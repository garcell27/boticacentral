<?php
/**
 * Created by PhpStorm.
 * User: cesar
 * Date: 13/02/17
 * Time: 02:57 PM
 */

namespace app\controllers;

use app\models\Botica;
use app\models\DetalleTransferencia;
use app\models\LogsMovimientos;
use kartik\form\ActiveForm;
use Yii;
use app\models\Funciones;
use yii\helpers\Json;
use yii\web\Controller;
use app\models\Transferencia;
use app\models\TransfExterna;
use yii\web\Response;

class TransfjsonController extends Controller
{
    public function actionIndex()
    {
        //return $this->render('index');
    }
    private function devolverTransferencias($idbotica){
        $transferencias=Transferencia::find()->where([
            'destino_conf'=>0,'estado'=>'E','idbotdestino'=>$idbotica
        ])->all();
        $datos=[];
        foreach ($transferencias as $i=>$t){
            $datos[$i]=$t->getAttributes();
            foreach ($t->items as $item){
                $datos[$i]['items'][]=$item->getAttributes();
            }
            $datos[$i]['botorigen']=$t->botorigen->nomrazon;
            $datos[$i]['botdestino']=$t->botdestino->nomrazon;
        }
        return $datos;
    }

    private function devolverTransfxfinalizar($idbotica){
        $transferencias=Transferencia::find()->where([
            'origen_conf'=>0,'estado'=>'F','idbotorigen'=>$idbotica
        ])->all();
        $datos=[];
        foreach ($transferencias as $i=>$t){
            $datos[$i]=$t->getAttributes();
            $datos[$i]['botorigen']=$t->botorigen->nomrazon;
            $datos[$i]['botdestino']=$t->botdestino->nomrazon;
        }
        return $datos;
    }


    public function actionTransferenciasxrecibir($id,$enlace){
        $respuesta=Funciones::establecerConexion($id,$enlace);
        if($respuesta['mensaje']){
            $conexion=$respuesta['conexion'];
            $respuesta['transferenciasRecibidas']=$this->devolverTransferencias($conexion->idbotica);
            $respuesta['transferenciasxFinalizar']=$this->devolverTransfxfinalizar($conexion->idbotica);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $respuesta;
        }else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $respuesta;
        }
    }

    public function actionObtenerBoticas($id,$enlace){
        $respuesta=Funciones::establecerConexion($id,$enlace);
        if($respuesta['mensaje']){
            $conexion=$respuesta['conexion'];
            $respuesta['boticas']=$this->infoBoticas($conexion->idbotica);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $respuesta;
        }else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $respuesta;
        }
    }

    public function actionTranferenciaxsubir($id,$enlace){
        $respuesta=Funciones::establecerConexion($id,$enlace);
        if($respuesta['mensaje']){
            $transferencia= new TransfExterna();
            if(Yii::$app->request->post()){
                $post=Yii::$app->request->post();
                $datostransf=Json::decode($post['data'],true);
                $itemsdatatransf=Json::decode($post['itemsdata'],true);

                    $transferencia->attributes=[
                        'estado'=>'E',
                        'idbotorigen'=>$datostransf['idbotorigen'],
                        'idbotdestino'=>$datostransf['idbotdestino'],
                        'origen_conf'=>$datostransf['origen_conf'],
                        'destino_conf'=>$datostransf['destino_conf'],
                        'create_by'=>$datostransf['create_by'],
                        'update_by'=>$datostransf['update_by'],
                        'create_at'=>$datostransf['create_at'],
                        'update_at'=>date('Y-m-d H:i:s'),
                    ];
                    if($transferencia->save()){
                        $transferencia->refresh();
                        foreach ($itemsdatatransf as $item){
                            $detalle = new DetalleTransferencia();
                            $detalle->idtransferencia = $transferencia->idtransferencia;
                            $detalle->idunidad = $item['idunidad'];
                            $detalle->cantidad = $item['cantidad'];
                            $detalle->save();
                        }
                        $respuesta['transferencia']=$transferencia->getAttributes();
                    }else{
                        $respuesta['transferencia']=false;
                        $respuesta['error']=ActiveForm::validate($transferencia);

                    }

            }else{
                $respuesta['transferencia']=false;
                $respuesta['error']=ActiveForm::validate($transferencia);
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $respuesta;
        }else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $respuesta;
        }
    }

    public function actionRecibirTransferencia($id,$enlace,$idtransferencia){
        $respuesta=Funciones::establecerConexion($id,$enlace);
        if($respuesta['mensaje']){
            $transferencia=TransfExterna::findOne($idtransferencia);
            $transferencia->estado='E';
            $transferencia->update_at=date('Y-m-d H:i:s');
            if($transferencia->save()){
                $respuesta['transferencia']=$transferencia->getAttributes();
            }else{
                $respuesta['transferencia']=false;
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $respuesta;
        }else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $respuesta;
        }
    }

    public function actionConfirmarTransferencia($id,$enlace,$idtransferencia){
        $respuesta=Funciones::establecerConexion($id,$enlace);
        if($respuesta['mensaje']){
            $conexion=$respuesta['conexion'];
            $transferencia=TransfExterna::findOne($idtransferencia);
            $transferencia->destino_conf=1;
            $transferencia->estado='F';
            if($transferencia->botorigen->tipo_almacen==1){
                $transferencia->origen_conf=1;
            }
            $transferencia->update_at=date('Y-m-d H:i:s');
            if($transferencia->save()){
                if($transferencia->botorigen->tipo_almacen==1){
                    $transferencia->genMovIngresos($conexion->idconexion);
                    $transferencia->genMovSalidas($conexion->idconexion);
                    $respuesta['movingresos']=$transferencia->movIngresos();
                }else{
                    $transferencia->genMovIngresos($conexion->idconexion);
                    $respuesta['movingresos']=$transferencia->movIngresos();
                }
                $respuesta['transferencia']=$transferencia->getAttributes();
            }else{
                $respuesta['transferencia']=false;
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $respuesta;
        }else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $respuesta;
        }
    }

    public function actionProbarpost(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'post'=> Yii::$app->request->post(),
            'get'=> Yii::$app->request->get()
        ];
    }
    public function actionFinalizaTransferencia($id,$enlace,$idtransferencia){
        $respuesta=Funciones::establecerConexion($id,$enlace);
        if($respuesta['mensaje']){
            $conexion=$respuesta['conexion'];
            $transferencia=TransfExterna::findOne($idtransferencia);
            $transferencia->origen_conf=1;
            $transferencia->update_at=date('Y-m-d H:i:s');
            if($transferencia->save()){
                $transferencia->genMovSalidas($conexion->idconexion);
                $respuesta['movsalidas']=$transferencia->movSalidas();
                $respuesta['transferencia']=$transferencia->getAttributes();
            }else{
                $respuesta['transferencia']=false;
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $respuesta;
        }else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $respuesta;
        }
    }




    private function infoBoticas($idbotica){
        $boticas=Botica::find()->where(['<>','idbotica',$idbotica])
            ->andWhere(['tipo_almacen'=>0])->all();
        $datos=[];
        foreach ($boticas as $b){
            $datos[]=$b->getAttributes();
        }
        return $datos;
    }


}