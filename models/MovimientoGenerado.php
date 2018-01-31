<?php
/**
 * Created by PhpStorm.
 * User: cesar
 * Date: 14/01/17
 * Time: 02:44 PM
 */

namespace app\models;

use Yii;

class MovimientoGenerado extends MovimientoStock
{
    public function behaviors() { return [];}

    public function rules() {
        return [
            [['idunidad', 'tipo_transaccion', 'cantidad', 'idbotica','create_by','update_by','fecha'], 'required'],
            [['idunidad', 'idprocedencia', 'idbotica'], 'integer'],
            [['fecha','create_by','update_by'], 'safe'],
            [['cantidad'], 'number'],
            [['detalle'], 'string'],
            [['tipo_transaccion'], 'string', 'max' => 1],
        ];
    }
}