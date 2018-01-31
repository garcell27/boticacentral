<?php
/**
 * Created by PhpStorm.
 * User: cesar
 * Date: 15/01/17
 * Time: 10:47 AM
 */

namespace app\models;


class SincroLocal extends  Sincronizado
{
    public function behaviors() {
        return [];
    }

    public function rules() {
        return [
            [['idlog', 'idbotica', 'estado','update_at','update_by'], 'required'],
            [['idlog', 'idbotica', 'estado'], 'integer'],
            [['idbotica'], 'exist', 'skipOnError' => true, 'targetClass' => Botica::className(), 'targetAttribute' => ['idbotica' => 'idbotica']],
            [['idlog'], 'exist', 'skipOnError'    => true, 'targetClass'    => LogsTbprincipales::className(), 'targetAttribute'    => ['idlog'    => 'idlog']],
        ];
    }
}