<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "uncmpred".
 *
 * @property integer $idproveedor
 * @property integer $idunidad
 *
 * @property Proveedores $proveedor
 * @property Unidades $unidad
 */
class Uncmpred extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'uncmpred';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idproveedor', 'idunidad'], 'required'],
            [['idproveedor', 'idunidad'], 'integer'],
            [['idproveedor'], 'exist', 'skipOnError' => true, 'targetClass' => Proveedores::className(), 'targetAttribute' => ['idproveedor' => 'idproveedor']],
            [['idunidad'], 'exist', 'skipOnError' => true, 'targetClass' => Unidades::className(), 'targetAttribute' => ['idunidad' => 'idunidad']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idproveedor' => 'Idproveedor',
            'idunidad' => 'Idunidad',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProveedor()
    {
        return $this->hasOne(Proveedores::className(), ['idproveedor' => 'idproveedor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnidad()
    {
        return $this->hasOne(Unidades::className(), ['idunidad' => 'idunidad']);
    }
}
