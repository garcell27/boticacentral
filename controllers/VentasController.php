<?php

namespace app\controllers;
use app\models\Botica;
use app\models\Salida;
use app\models\VentasSearch;
use app\models\UnidConsolidadas;
use app\models\MovimientoStock;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;

class VentasController extends \yii\web\Controller
{
    
    public function behaviors()
    {
        return [
            'access'=>[
                'class'=> AccessControl::className(),
                'rules'=>[
                    [
                        'allow'=>true,
                        'roles'=>['@']
                    ]                    
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    public function actionIndex()
    {
        $botica=Botica::find()->one();
        if($botica){
            $smventas = new VentasSearch();
            $smcon=new UnidConsolidadas();
            $smventas->idbotica=$botica->idbotica;
            $smventas->fecha_registro= date('Y-m-d');
            $smcon->fecha_registro= date('Y-m-d');
            $get=$get2=Yii::$app->request->queryParams;
            if(count($get)){
                $get2['UnidConsolidadas']=$get['VentasSearch'];
            }
            $dpventas = $smventas->searchVentas($get);
            $dpconsolidado = $smcon->search($get2);
            //$dpconsolidado->query;
            $dpventas->pagination=false;
            //$dpconsolidado = $smventas->searchConsolidado(Yii::$app->request->queryParams);
            return $this->render('index', [
                'smventas' => $smventas,
                'dpventas' => $dpventas,
                'dpconsolidado'=>$dpconsolidado
            ]);
        }else{
            return $this->render('../botica/nobotica');
        }
    }
    public function actionAnular($id) {
        $salida= Salida::findOne($id);
        $salida->estado='X';
        $salida->pedido->estado='X';
        $salida->pedido->total=0;
        if($salida->save() && $salida->pedido->save()){
            foreach ($salida->detalleSalida as $detalle){
                $movimiento= MovimientoStock::find()->where([
                    'tipo_transaccion'=>'E',
                    'idprocedencia'=>$detalle->iddetallesalida,
                ])->one();
                $movimiento->tipo_transaccion='Y';
                if($movimiento->save()){
                    $detalle->unidad->producto->recalculaStock($salida->idbotica);
                }
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'message' => 'ok',
            'growl'=>[
                'message' => 'Se ha eliminado la venta',
                'options' => [
                    'type'=>'danger',
                ],
            ],
        ];
    }

}
