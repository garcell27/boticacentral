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
use app\models\ProductosSearch;
use Yii;
use app\models\InfoDiario;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class InfoutilidadesController extends Controller
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
        $searchModel = new ProductosSearch();
        $dataProvider = $searchModel->searchConUtilidad(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}