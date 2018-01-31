<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detalle_inventario".
 *
 * @property integer $iddetalle_inventario
 * @property integer $idinventario
 * @property integer $idunidad
 * @property string $cantestimada
 * @property string $cantinventariada
 * @property string $cantvendida
 * @property string $observaciones
 * @property integer $estado
 * @property string $accion
 *
 * @property Inventarios $idinventario0
 * @property Unidades $idunidad0
 */
class DetalleInventarioJson extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detalle_inventario';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idinventario', 'idunidad', 'cantestimada', 'estado'], 'required'],
            [['idinventario', 'idunidad', 'estado'], 'integer'],
            [['cantestimada', 'cantinventariada', 'cantvendida'], 'number'],
            [['observaciones'], 'string'],
            [['accion'], 'string', 'max' => 1],
            [['idinventario'], 'exist', 'skipOnError' => true, 'targetClass' => Inventarios::className(), 'targetAttribute' => ['idinventario' => 'idinventario']],
            [['idunidad'], 'exist', 'skipOnError' => true, 'targetClass' => Unidades::className(), 'targetAttribute' => ['idunidad' => 'idunidad']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddetalle_inventario' => 'Iddetalle Inventario',
            'idinventario' => 'Idinventario',
            'idunidad' => 'Idunidad',
            'cantestimada' => 'Cantestimada',
            'cantinventariada' => 'Cantinventariada',
            'cantvendida' => 'Cantvendida',
            'observaciones' => 'Observaciones',
            'estado' => 'Estado',
            'accion' => 'Accion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdinventario0()
    {
        return $this->hasOne(Inventarios::className(), ['idinventario' => 'idinventario']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdunidad0()
    {
        return $this->hasOne(Unidades::className(), ['idunidad' => 'idunidad']);
    }
}
