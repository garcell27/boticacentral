<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "logs_movimientos".
 *
 * @property integer $idlogcentral
 * @property integer $idlog
 * @property integer $idbotica
 * @property string $tabla
 * @property integer $idlocal
 * @property string $data
 * @property string $data_detalle
 * @property integer $sincronizado
 * @property integer $create_by
 * @property integer $update_by
 */
class LogsMovimientos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logs_movimientos';
    }



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idbotica', 'tabla', 'data', 'data_detalle','sincronizado','idlog','create_by', 'update_by'], 'required'],
            [['idlog', 'idbotica', 'sincronizado', 'create_by', 'update_by'], 'integer'],
            [['data', 'data_detalle'], 'string'],
            [['tabla'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idlogcentral' => 'Idlogcentral',
            'idlog' => 'Idlog',
            'idbotica' => 'Idbotica',
            'tabla' => 'Tabla',
            'idlocal' => 'Idlocal',
            'data' => 'Data',
            'data_detalle' => 'Data Detalle',
            'sincronizado' => 'Sincronizado',
            'create_by' => 'Create By',
            'update_by' => 'Update By',
        ];
    }
}
