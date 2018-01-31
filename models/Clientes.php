<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "clientes".
 *
 * @property integer $idcliente
 * @property string $nomcliente
 * @property string $direccion
 * @property string $tipodoc
 * @property string $docidentidad
 * @property string $telefono
 * @property integer $cajarapida
 * @property integer $create_by
 * @property integer $update_by
 * @property string $sincronizaciones
 *
 * @property Pedidos[] $pedidos
 */
class Clientes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clientes';
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

    public function beforeSave($insert) {
        $this->nomcliente=mb_strtoupper($this->nomcliente,'utf-8');
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nomcliente'], 'required'],
            [['cajarapida', 'create_by', 'update_by'], 'integer'],
            [['sincronizaciones'], 'string'],
            [['nomcliente', 'direccion'], 'string', 'max' => 200],
            [['tipodoc'], 'string', 'max' => 10],
            [['docidentidad'], 'string', 'max' => 11],
            [['telefono'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idcliente' => 'ID',
            'nomcliente' => 'NOMBRES',
            'direccion' => 'DIRECCION',
            'tipodoc' => 'TIPO DOC.',
            'docidentidad' => 'NRO DOCUMENTO',
            'telefono' => 'TELF.',
            'cajarapida' => '¿Caja Rapida?',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPedidos()
    {
        return $this->hasMany(Pedidos::className(), ['idcliente' => 'idcliente']);
    }
}
