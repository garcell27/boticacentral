<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "operaciones".
 *
 * @property integer $idoperacion
 * @property string $nombre
 *
 * @property RolOperacion[] $rolOperaciones
 * @property Roles[] $roles
 */
class Operaciones extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operaciones';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idoperacion' => 'Idoperacion',
            'nombre' => 'Nombre',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRolOperaciones()
    {
        return $this->hasMany(RolOperacion::className(), ['idoperacion' => 'idoperacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Roles::className(), ['idrole' => 'idrole'])->viaTable('rol_operacion', ['idoperacion' => 'idoperacion']);
    }
}
