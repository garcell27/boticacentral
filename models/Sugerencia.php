<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "sugerencia".
 *
 * @property integer $idsugerencia
 * @property integer $idproducto
 * @property integer $sugerido
 * @property string $sincronizaciones
 *
 * @property Productos $producto
 * @property Productos $prodsugerido
 */
class Sugerencia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sugerencia';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idproducto', 'sugerido'], 'required'],
            [['idproducto', 'sugerido'], 'integer'],
            [['sincronizaciones'], 'string'],
            [['idproducto'], 'exist', 'skipOnError' => true, 'targetClass' => Productos::className(), 'targetAttribute' => ['idproducto' => 'idproducto']],
            [['sugerido'], 'exist', 'skipOnError' => true, 'targetClass' => Productos::className(), 'targetAttribute' => ['sugerido' => 'idproducto']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idsugerencia' => 'ID',
            'idproducto' => 'PRODUCTO',
            'sugerido' => 'SUGERIDO',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducto()
    {
        return $this->hasOne(Productos::className(), ['idproducto' => 'idproducto']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProdsugerido()
    {
        return $this->hasOne(Productos::className(), ['idproducto' => 'sugerido']);
    }

    public function getCboProductos(){


        return ArrayHelper::map(Productos::find()->select(['productos.idproducto','productos.descripcion','laboratorio.nombre'])
            ->innerJoin('laboratorio','laboratorio.idlaboratorio = productos.idlaboratorio')
            ->orderBy('descripcion')->asArray()->all(),'idproducto',function($model){
            return $model['descripcion'].' - '.$model['nombre'];
        });
    }
}
