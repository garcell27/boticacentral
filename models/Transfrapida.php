<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Transfrapida extends Model
{
    public $idbotica;
    public $idunidad;
    public $cantidad;

    public function rules()
    {
        return [
            [['idbotica', 'idunidad', 'cantidad'], 'required'],
            [['idbotica', 'idunidad'], 'integer'],
            [['cantidad'], 'number'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'idbotica' => 'BOTICA',
            'idunidad' => 'UNIDAD',
            'cantidad' => 'CANTIDAD',
        ];
    }
}