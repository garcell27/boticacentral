<?php
/**
 * Created by PhpStorm.
 * User: cesar
 * Date: 20/04/17
 * Time: 03:23 AM
 */

namespace app\controllers;

use Yii;
use app\models\StockSearch;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class RequerimientosController extends Controller
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
        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchRequerimientos(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}