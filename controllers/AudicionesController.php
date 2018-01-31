<?php

namespace app\controllers;

use app\models\Botica;
use Yii;
use app\models\Pedidos;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\Response;

class AudicionesController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $datos=$this->verinfopedidos();
        return $this->render('index',[
            'datos'=>$datos
        ]);
    }

    public function actionComprimirPedido($id){
        Yii::$app->response->format = Response::FORMAT_JSON;

        $pedido=Pedidos::findOne($id);
        $pedido->detalles=Json::encode($pedido->items);
        if($pedido->save()){
            $datos=$this->verinfopedidos();
            return [
                'consulta'=>true,
                'datos'=>$datos
            ];
        }else{
            return ['consulta'=>false];
        }


    }



    public function verinfopedidos(){
        $npedidos= Pedidos::find()->count();
        $npedidosnull=Pedidos::find()->where('detalles is null')->count();

        $boticas=Botica::find()->where(['tipo_almacen'=>0])->all();
        $detalle=[];
        foreach($boticas as $i=> $b){
            $detalle[$i]=[
                'info'=>$b,
                'pedidos'=>Pedidos::find()->where(['idbotica'=>$b->idbotica])->count(),
                'nulos'=>Pedidos::find()
                    ->where('detalles is null and idbotica='.$b->idbotica)->count()
            ];
        }
        return [
            'npedidos'=>$npedidos,
            'npedidosnull'=>$npedidosnull,
            'detalles'=>$detalle
        ];
    }

}
