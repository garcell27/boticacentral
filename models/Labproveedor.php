<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "labproveedor".
 *
 * @property integer $idlabprov
 * @property integer $idproveedor
 * @property integer $idlaboratorio
 *
 * @property Laboratorio $laboratorio
 * @property Proveedores $proveedor
 */
class Labproveedor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'labproveedor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idproveedor', 'idlaboratorio'], 'required'],
            [['idproveedor', 'idlaboratorio'], 'integer'],
            [['idlaboratorio'], 'exist', 'skipOnError' => true, 'targetClass' => Laboratorio::className(), 'targetAttribute' => ['idlaboratorio' => 'idlaboratorio']],
            [['idproveedor'], 'exist', 'skipOnError' => true, 'targetClass' => Proveedores::className(), 'targetAttribute' => ['idproveedor' => 'idproveedor']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idlabprov' => 'Idlabprov',
            'idproveedor' => 'Idproveedor',
            'idlaboratorio' => 'Idlaboratorio',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLaboratorio()
    {
        return $this->hasOne(Laboratorio::className(), ['idlaboratorio' => 'idlaboratorio']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProveedor()
    {
        return $this->hasOne(Proveedores::className(), ['idproveedor' => 'idproveedor']);
    }
    public function getCboLaboratorios($idproveedor){
        $subquery= Labproveedor::find()->select('idlaboratorio')->where(['idproveedor'=>$idproveedor]);
        return ArrayHelper::map(
                Laboratorio::find()
                ->where(['not in','idlaboratorio',$subquery])
                ->orderBy('nombre')->asArray()->all(),
                'idlaboratorio','nombre');
    }
    
}
