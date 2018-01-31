<?php

namespace app\modules\api\models;

use app\models\Usuarios;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class UsuariosApi extends Usuarios{

    public function fields()
    {
        $fields = parent::fields();
        // remove fields that contain sensitive information
        unset($fields['auth_key'], $fields['password_hash'], $fields['password_reset_token'],$fields['sincronizaciones']);
        return $fields;
    }
}