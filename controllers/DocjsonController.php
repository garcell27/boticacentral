<?php
/**
 * Created by PhpStorm.
 * User: cesar
 * Date: 03/03/17
 * Time: 03:10 PM
 */

namespace app\controllers;

use app\models\Detallepedido;
use app\models\DetalleSalida;
use app\models\Pedidos;
use app\models\Salida;
use Yii;
use yii\web\Response;
use app\models\Funciones;
use yii\web\Controller;
use yii\widgets\ActiveForm;
use yii\helpers\Json;

class DocjsonController extends Controller
{
    public function actionIndex(){

    }
    public function actionRegistrarPedido($id,$enlace){
        $respuesta=Funciones::establecerConexion($id,$enlace);
        $pedido=new Pedidos();
        if($respuesta['mensaje']){
            if(Yii::$app->request->post()){
                $post=Yii::$app->request->post();
                $datos=Json::decode($post['data'],true);
                $itemsdata=Json::decode($post['itemsdata'],true);
                $pedido->attributes=[
                    'idcliente'=>$datos['idcliente'],
                    'datoscliente'=>$datos['datoscliente'],
                    'fecha_registro'=>$datos['fecha_registro'],
                    'ndocumento'=>$datos['ndocumento'],
                    'total'=>$datos['total'],
                    'idcomprobante'=>$datos['idcomprobante'],
                    'estado'=>$datos['estado'],
                    'entregado'=>$datos['entregado'],
                    'idbotica'=>$datos['idbotica'],
                    'idlocal'=>$datos['idpedido']
                ];
                if($pedido->save()){
                    $pedido->refresh();
                    foreach ($itemsdata as $it){
                        $item = new Detallepedido();
                        $item->attributes=[
                            'idpedido'=>$pedido->idpedido,
                            'idunidad'=>$it['idunidad'],
                            'cantidad'=>$it['cantidad'],
                            'preciounit'=>$it['preciounit'],
                            'subtotal'=>$it['subtotal']
                        ];
                        $item->save();
                    }
                    $respuesta['pedido']=$pedido->getAttributes();
                }else{
                    $respuesta['pedido']=false;
                    $respuesta['error']=ActiveForm::validate($pedido);
                }
            }else{
                $respuesta['pedido']=false;
                $respuesta['error']=ActiveForm::validate($pedido);
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $respuesta;
    }
    public function actionRegistrarSalida($id,$enlace){
        $respuesta=Funciones::establecerConexion($id,$enlace);
        $salida=new Salida();
        if($respuesta['mensaje']){
            if(Yii::$app->request->post()){
                $post=Yii::$app->request->post();
                $datos=Json::decode($post['data'],true);
                $itemsdata=Json::decode($post['itemsdata'],true);
                if($datos['motivo']=='V'){
                    $pedido=Pedidos::find()->where(['idlocal'=>$datos['idpedido'],'idbotica'=>$datos['idbotica']])->one();
                    $idpedido=$pedido->idpedido;
                }else{
                    $idpedido=null;
                }
                $salida->attributes=[
                    'idpedido'=>$idpedido,
                    'idpedlocal'=>$datos['idpedido'],
                    'motivo'=>$datos['motivo'],
                    'estado'=>$datos['estado'],
                    'idbotica'=>$datos['idbotica'],
                    'idlocal'=>$datos['idsalida'],
                    'sincroniza'=>$datos['sincroniza'],
                    'create_by'=>$datos['create_by'],
                    'update_by'=>$datos['update_by'],
                    'create_at'=>$datos['create_at'],
                    'update_at'=>$datos['update_at'],
                ];
                if($salida->save()){
                    $salida->refresh();
                    foreach ($itemsdata as $it){
                        $item = new DetalleSalida();
                        $item->attributes=[
                            'idsalida'=>$salida->idsalida,
                            'idunidad'=>$it['idunidad'],
                            'cantidad'=>$it['cantidad'],
                            'preciounit'=>$it['preciounit'],
                            'subtotal'=>$it['subtotal']
                        ];
                        $item->save();
                    }
                    $respuesta['salida']=$salida->getAttributes();
                }else{
                    $respuesta['salida']=false;
                    $respuesta['error']=ActiveForm::validate($salida);
                }
            }else{
                $respuesta['salida']=false;
                $respuesta['error']=ActiveForm::validate($salida);
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $respuesta;
    }
}