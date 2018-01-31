<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "roles".
 *
 * @property integer $idrole
 * @property string $nombre
 * @property string $sincronizaciones
 *
 * @property RolOperacion[] $rolOperaciones
 * @property Operaciones[] $operaciones
 * @property Usuarios[] $usuarios
 */
class Roles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'roles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['sincronizaciones'], 'string'],
            [['nombre'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idrole' => 'Idrole',
            'nombre' => 'Nombre',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRolOperaciones()
    {
        return $this->hasMany(RolOperacion::className(), ['idrole' => 'idrole']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperaciones()
    {
        return $this->hasMany(Operaciones::className(), ['idoperacion' => 'idoperacion'])->viaTable('rol_operacion', ['idrole' => 'idrole']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarios()
    {
        return $this->hasMany(Usuarios::className(), ['idrole' => 'idrole']);
    }
}
