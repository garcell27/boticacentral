<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rol_operacion".
 *
 * @property integer $idrole
 * @property integer $idoperacion
 *
 * @property Operaciones $operacion
 * @property Roles $role
 */
class RolOperacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rol_operacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idrole', 'idoperacion'], 'required'],
            [['idrole', 'idoperacion'], 'integer'],
            [['idoperacion'], 'exist', 'skipOnError' => true, 'targetClass' => Operaciones::className(), 'targetAttribute' => ['idoperacion' => 'idoperacion']],
            [['idrole'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::className(), 'targetAttribute' => ['idrole' => 'idrole']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idrole' => 'Idrole',
            'idoperacion' => 'Idoperacion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperacion()
    {
        return $this->hasOne(Operaciones::className(), ['idoperacion' => 'idoperacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Roles::className(), ['idrole' => 'idrole']);
    }
}
