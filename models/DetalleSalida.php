<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detalle_salida".
 *
 * @property integer $iddetallesalida
 * @property integer $idsalida
 * @property integer $idunidad
 * @property string $cantidad
 * @property string $preciounit
 * @property string $subtotal
 *
 * @property Salida $salida
 * @property Unidades $unidad
 */
class DetalleSalida extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detalle_salida';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idsalida', 'idunidad', 'cantidad', 'preciounit', 'subtotal'], 'required'],
            [['idsalida', 'idunidad',  'idlocal'], 'integer'],
            [['cantidad', 'preciounit', 'subtotal'], 'number'],
            [['idsalida'], 'exist', 'skipOnError' => true, 'targetClass' => Salida::className(), 'targetAttribute' => ['idsalida' => 'idsalida']],
            [['idunidad'], 'exist', 'skipOnError' => true, 'targetClass' => Unidades::className(), 'targetAttribute' => ['idunidad' => 'idunidad']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddetallesalida' => 'Iddetallesalida',
            'idsalida' => 'Idsalida',
            'idunidad' => 'Idunidad',
            'cantidad' => 'Cantidad',
            'preciounit' => 'Preciounit',
            'subtotal' => 'Subtotal',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalida()
    {
        return $this->hasOne(Salida::className(), ['idsalida' => 'idsalida']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnidad()
    {
        return $this->hasOne(Unidades::className(), ['idunidad' => 'idunidad']);
    }
}
