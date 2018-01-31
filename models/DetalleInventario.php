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
 * @property Inventario $inventario
 * @property Unidades $unidad
 */
class DetalleInventario extends \yii\db\ActiveRecord
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
            [['idinventario'], 'exist', 'skipOnError' => true, 'targetClass' => Inventario::className(), 'targetAttribute' => ['idinventario' => 'idinventario']],
            [['idunidad'], 'exist', 'skipOnError' => true, 'targetClass' => Unidades::className(), 'targetAttribute' => ['idunidad' => 'idunidad']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddetalle_inventario' => 'ID',
            'idinventario' => 'ID INVENTARIO',
            'idunidad' => 'UNIDAD',
            'cantestimada' => 'CANT. ESTIMADA',
            'cantinventariada' => 'CANT. INV.',
            'cantvendida' => 'CANT. VENDIDA',
            'observaciones' => 'OBSERVACIONES',
            'codproducto' => 'CODIGO',
            'producto' => 'PRODUCTO',
            'estado' => 'ESTADO',
            'accion' => 'ACCION',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventario()
    {
        return $this->hasOne(Inventario::className(), ['idinventario' => 'idinventario']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnidad()
    {
        return $this->hasOne(Unidades::className(), ['idunidad' => 'idunidad']);
    }
}
