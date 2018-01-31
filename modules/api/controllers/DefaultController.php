<?php

namespace app\modules\api\controllers;

use yii\rest\Controller;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\HttpException;

/**
 * Default controller for the `api` module
 */
class DefaultController extends Controller
{

    public $idbotica=null;

    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
    }

    public function beforeAction($action){
        $header=Yii::$app->request->getHeaders();
        $before=parent::beforeAction($action);
        if($header->get('idbotica')==null){
            throw new HttpException(401,'Falta de informacion');
        }else{
            $this->idbotica=$header->get('idbotica');
            return $before;
        }
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['options'],
        ];
        $behaviors['corsFilter']=[
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Allow-Origin' => ['*'],
                'Access-Control-Request-Headers' => ['idbotica'],
                'Access-Control-Allow-Credentials' => true,
                // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
            ]
        ];
        return $behaviors;
    }

    public function actionIndex()
    {

        return [
            'mensaje'=>'Hola Mundo de nuevo'
        ];
    }
}
