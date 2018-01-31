<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "proveedores".
 *
 * @property integer $idproveedor
 * @property string $nomproveedor
 * @property string $direccion
 * @property string $tipodoc
 * @property string $docidentidad
 * @property integer $inventario
 * @property integer $create_by
 * @property integer $update_by
 * 
 * @property Ingreso[] $ingresos
 * @property Labproveedor[] $labproveedores
 * @property Uncmpred[] $uncmpreds
 * @property Unidades[] $Unidades
 */
class Proveedores extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'proveedores';
    }

    public function behaviors() {
        return [
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'create_by',
                'updatedByAttribute' => 'update_by'
            ]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nomproveedor'], 'required'],
            [['inventario'], 'integer'],
            [['nomproveedor'], 'string', 'max' => 150],
            [['direccion'], 'string', 'max' => 250],
            [['tipodoc'], 'string', 'max' => 3],
            [['docidentidad'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idproveedor' => 'ID',
            'nomproveedor' => 'NOMBRE DEL PROVEEDOR',
            'direccion' => 'DIRECCION',
            'tipodoc' => 'TIPO DOCUMENTO',
            'docidentidad' => 'N° DOCUMENTO',
            'inventario' => '¿Para Inventario?',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIngresos()
    {
        return $this->hasMany(Ingreso::className(), ['idproveedor' => 'idproveedor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLabproveedores()
    {
        return $this->hasMany(Labproveedor::className(), ['idproveedor' => 'idproveedor']);
    }
    public function getLaboratorios(){
        return $this->hasMany(Laboratorio::className(), ['idlaboratorio'=>'idlaboratorio'])
                ->viaTable('labproveedor', ['idproveedor'=>'idproveedor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUncmpreds()
    {
        return $this->hasMany(Uncmpred::className(), ['idproveedor' => 'idproveedor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnidades()
    {
        return $this->hasMany(Unidades::className(), ['idunidad' => 'idunidad'])->viaTable('uncmpred', ['idproveedor' => 'idproveedor']);
    }
}
