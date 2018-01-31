<?php
/**
 * Created by PhpStorm.
 * User: cesar
 * Date: 10/04/17
 * Time: 08:06 PM
 */

namespace app\controllers;



use app\models\InfoVentas;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use Yii;


class InforankingController extends Controller
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
        $searchModel = new InfoVentas();
        $dataProvider = $searchModel->searchRanking();
        return $this->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}