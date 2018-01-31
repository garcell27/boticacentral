<?php

namespace app\controllers;

use app\models\Botica;
use app\models\InventarioJson;
use yii\web\Response;
use Yii;

class InventariojsonController extends \yii\web\Controller
{
    public function actionIndex()
    {

        $invActi=InventarioJson::find()->where(['estado'=>'A'])->all();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'inventarios'=>$invActi,
        ];
    }

    public function actionNuevoinventario(){
        $boticas=Botica::find()->where(['tipo_almacen'=>0])->all();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'boticas'=>$boticas,
        ];


    }


    public function actionGrabarinventario(){
        $mispost=Yii::$app->request->post();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $mispost;
    }
    public function actionAgregardetalle(){

    }
    public function actionCerrarinventario($id){
        $inventario=InventarioJson::findOne($id);
        $inventario->estado='C';
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'inventario'=>$inventario,
        ];
    }



}
