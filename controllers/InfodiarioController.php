<?php
/**
 * Created by PhpStorm.
 * User: cesar
 * Date: 27/03/17
 * Time: 05:00 PM
 */

namespace app\controllers;

use app\models\Botica;
use app\models\InfoVentas;
use Yii;
use app\models\InfoDiario;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class InfodiarioController extends Controller
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

    public function actionIndex(){
        $model= new InfoDiario();
        if($model->load(Yii::$app->request->post())){
            $informes=new InfoVentas();
            $principal=$informes->principal($model->fecha,$model->botica);

            $botica=Botica::findOne($model->botica);
            $data['botica']=$botica;
            $data['principal']=$principal;
            if($principal['cantidad']>0){
                $data['participacion']=$informes->dtpartdiaria($model->fecha,$model->botica);
                $data['consolidados']=$informes->consolidadosxVendedor($model->fecha,$model->botica);
            }
            /*return $this->render('resultado',[
                'dataProvider'=>$dataProvider,
                'botica'=>$botica,
            ]);*/
        }else{
            $data=[];
        }
        return $this->render('index',[
            'model'=>$model,
            'data'=>$data,
        ]);
    }
}