<?php

namespace app\controllers;

use app\models\DetalleTransferencia;
use app\models\Stock;
use app\models\Transferencia;
use app\models\Transfrapida;
use Yii;
use app\models\Botica;
use app\models\StockSearch;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\helpers\Json;


class TransfrapidaController extends \yii\web\Controller
{
    /*public function actionIndex2()
    {
        $boticas=Botica::find()->where(['tipo_almacen'=>1])->all();
        return $this->render('index2',[
            'botcentrals'=>$boticas,
        ]);
    }*/

    public function actionListaStock($id){
        $stocks=Stock::find()->where(['idbotica'=>$id])
            ->andWhere(['>','fisico',0])->all();
        $data=[];
        foreach($stocks as $i=>$stock){
            $data[$i]=$stock->attributes;
            $data[$i]['disponible']=number_format($stock->fisico-$stock->separado,2,'.','');
            $data[$i]['nomunidad']=$stock->unidad->descripcion;
            $data[$i]['producto']=$stock->unidad->producto->descripcion;
            $data[$i]['laboratorio']=$stock->unidad->producto->laboratorio->nombre;
            $data[$i]['lblstock']=$stock->verStockRaw();
        }
        Yii::$app->response->format=Response::FORMAT_JSON;

        return $data;
    }

    public function actionIndex()
    {
        $boticacentral=Botica::find()->where(['tipo_almacen'=>1])->one();
        $searchstock=new StockSearch();
        $searchstock->idbotica=$boticacentral->idbotica;
        $dataprovider=$searchstock->searchDisponible(Yii::$app->request->queryParams);
        return $this->render('index',[
            'boticacentral'=>$boticacentral,
            'searchstock'=>$searchstock,
            'dataprovider'=>$dataprovider,
        ]);
    }

    public function actionDetalletransferencia(){
        if (isset($_POST['expandRowKey'])) {
            $stock=Stock::findOne($_POST['expandRowKey']);
            $stocks=Stock::find()->where(['idunidad'=>$stock->idunidad])->all();
            $idunidades=[];
            foreach($stock->unidad->producto->unidades as $unidad){
                $idunidades[]=$unidad->idunidad;
            }
            $consulta=DetalleTransferencia::find()
                ->innerJoin('transferencia t','detalle_transferencia.idtransferencia = t.idtransferencia')
                ->where(['in','t.estado',['P','E']])
                ->andWhere(['in','detalle_transferencia.idunidad',$idunidades])->all();

            return $this->renderPartial('_viewDetalle',
                [
                    'stock'=>$stock,
                    'stocks'=>$stocks,
                    'consulta'=>$consulta,
                ]);
        } else {
            return '<div class="alert alert-danger">No data found</div>';
        }
    }

    public function actionTransferir($id,$submit = false){
        $stock=Stock::findOne($id);

        $model = new Transfrapida();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $submit == false) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $transferencia=Transferencia::find()->where([
                'estado'=>'P',
                'idbotorigen'=>$stock->idbotica,
                'idbotdestino'=>$model->idbotica,
            ])->orderBy(['idtransferencia'=>SORT_DESC])->one();
            if(!$transferencia) {
                $transferencia= new Transferencia();
                $transferencia->idbotorigen=$stock->idbotica;
                $transferencia->idbotdestino=$model->idbotica;
                $transferencia->estado='P';
                $transferencia->save();
                $transferencia->refresh();
            }
            $detalle= new DetalleTransferencia();
            $detalle->idtransferencia=$transferencia->idtransferencia;
            $detalle->idunidad=$model->idunidad;
            $detalle->cantidad=$model->cantidad;
            $detalle->save();
            $detalle->refresh();
            $detalle->unidad->producto->recalculaStock($transferencia->idbotorigen);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'message' => 'ok',
                'growl'=>[
                    'message' => 'Se ha realizado la transferencia de manera exitosa',
                    'options' => ['type'=>'success'],
                ],
            ];
        }else{
            $boticas=$this->obtenerBoticas($stock->idbotica,$stock->unidad->producto->idproducto);
            $model->cantidad=1;
            return $this->renderAjax('additem', [
                'stock'=>$stock,
                'model'=>$model,
                'boticas'=>$boticas
            ]);
        }

    }
    public function obtenerBoticas($idbotica,$idproducto){
        $boticas=Botica::find()->where(['<>','idbotica',$idbotica])->asArray()->all();
        $lista=[];
        foreach($boticas as $botica){
            $transferencia=Transferencia::find()->where([
                'estado'=>'P',
                'idbotorigen'=>$idbotica,
                'idbotdestino'=>$botica['idbotica'],
            ])->orderBy(['idtransferencia'=>SORT_DESC])->one();
            if($transferencia){
                $busqueda=false;
                foreach ($transferencia->items as $item){
                    if($item->unidad->producto->idproducto==$idproducto){
                        $busqueda=true;
                    }
                }
                if(!$busqueda){
                    $lista[]=$botica;
                }
            }else{
                $lista[]=$botica;
            }
        }
        return $lista;
    }
}
