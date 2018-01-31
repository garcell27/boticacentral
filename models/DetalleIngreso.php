<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "detalle_ingreso".
 *
 * @property integer $iddetalle
 * @property integer $idingreso
 * @property integer $idunidad
 * @property string $cantidad
 * @property string $costound
 * @property string $subtotal
 * @property integer $ingresado
 *
 * @property Ingreso $ingreso
 * @property Unidades $unidad
 */
class DetalleIngreso extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detalle_ingreso';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idingreso', 'idunidad', 'cantidad', 'ingresado'], 'required'],
            [['idingreso', 'idunidad', 'ingresado'], 'integer'],
            [['cantidad', 'costound', 'subtotal'], 'number'],
            [['idingreso'], 'exist', 'skipOnError' => true, 'targetClass' => Ingreso::className(), 'targetAttribute' => ['idingreso' => 'idingreso']],
            [['idunidad'], 'exist', 'skipOnError' => true, 'targetClass' => Unidades::className(), 'targetAttribute' => ['idunidad' => 'idunidad']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddetalle' => 'Iddetalle',
            'idingreso' => 'Idingreso',
            'idunidad' => 'UNIDAD',
            'cantidad' => 'CANTIDAD',
            'costound' => 'COSTO',
            'subtotal' => 'Subtotal',
            'ingresado' => 'Ingresado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIngreso()
    {
        return $this->hasOne(Ingreso::className(), ['idingreso' => 'idingreso']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnidad()
    {
        return $this->hasOne(Unidades::className(), ['idunidad' => 'idunidad']);
    }
    
    public function getCboUnidades($idproducto){
        return ArrayHelper::map(Unidades::find()->where(['idproducto'=>$idproducto])
                ->orderBy(['equivalencia'=>SORT_DESC])->asArray()->all(), 'idunidad', 'descripcion');
    }
}
