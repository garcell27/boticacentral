<?php

namespace app\modules\api\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use Yii;

class ProveedoresController extends ActiveController
{
    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];
        return $behaviors;
    }
    public $modelClass = 'app\models\Proveedores';

}
