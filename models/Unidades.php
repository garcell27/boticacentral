<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "unidades".
 *
 * @property integer $idunidad
 * @property string $descripcion
 * @property integer $idproducto
 * @property string $tipo
 * @property integer $paraventa
 * @property string $equivalencia
 * @property integer $idundprimaria
 * @property string $preciomin
 * @property string $preciomax
 * @property string $preciosug
 * @property integer $create_by
 * @property integer $update_by
 * @property string $sincronizaciones
 *
 * @property DetalleIngreso[] $detalleIngresos
 * @property DetalleSalida[] $detalleSalidas
 * @property MovimientoStock[] $movimientoStocks
 * @property Stock[] $stocks
 * @property Uncmpred[] $uncmpreds
 * @property Proveedores[] $proveedores
 * @property Productos $producto
 * @property Unidades $undprimaria
 * @property Unidades[] $unidades
 */
class Unidades extends \yii\db\ActiveRecord
{

    public $numequi;
    public $undequi;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unidades';
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

    public function beforeSave($insert)
    {
        $this->descripcion=mb_strtoupper($this->descripcion,'utf-8');
        return parent::beforeSave($insert);
    }
    

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descripcion', 'idproducto', 'tipo', 'paraventa'], 'required'],
            [['idproducto', 'paraventa', 'idundprimaria', 'create_by', 'update_by'], 'integer'],
            [['equivalencia', 'preciomin', 'preciomax', 'preciosug'], 'number'],
            [['descripcion'], 'string', 'max' => 45],
            [['tipo'], 'string', 'max' => 1],
            [['sincronizaciones'], 'string'],
            [['idproducto'], 'exist', 'skipOnError' => true, 'targetClass' => Productos::className(), 'targetAttribute' => ['idproducto' => 'idproducto']],
            [['idundprimaria'], 'exist', 'skipOnError' => true, 'targetClass' => Unidades::className(), 'targetAttribute' => ['idundprimaria' => 'idunidad']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idunidad' => 'ID',
            'descripcion' => 'DESCRIPCION',
            'idproducto' => 'PRODUCTO',
            'tipo' => 'TIPO',
            'paraventa' => '¿PARA VENTA?',
            'equivalencia' => 'EQUIVALE',
            'idundprimaria' => 'UND. PRIMARIA',
            'preciomin' => 'PRECIO MIN.',
            'preciomax' => 'PRECIO MAX.',
            'preciosug' => 'PRECIO SUG.',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleIngresos()
    {
        return $this->hasMany(DetalleIngreso::className(), ['idunidad' => 'idunidad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleSalidas()
    {
        return $this->hasMany(DetalleSalida::className(), ['idunidad' => 'idunidad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMovimientoStocks()
    {
        return $this->hasMany(MovimientoStock::className(), ['idunidad' => 'idunidad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStocks()
    {
        return $this->hasMany(Stock::className(), ['idunidad' => 'idunidad']);
    }

    public function getMistock($idbotica)
    {
        return $this->hasOne(Stock::className(), ['idunidad' => 'idunidad'])->andWhere(['idbotica'=>$idbotica]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUncmpreds()
    {
        return $this->hasMany(Uncmpred::className(), ['idunidad' => 'idunidad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProveedores()
    {
        return $this->hasMany(Proveedores::className(), ['idproveedor' => 'idproveedor'])->viaTable('uncmpred', ['idunidad' => 'idunidad']);
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
    public function getUndprimaria()
    {
        return $this->hasOne(Unidades::className(), ['idunidad' => 'idundprimaria']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnidades()
    {
        return $this->hasMany(Unidades::className(), ['idundprimaria' => 'idunidad']);
    }


    public function getCboUndEquiv(){
        if($this->isNewRecord){
            return ArrayHelper::map(
                Unidades::find()->where(['idproducto'=>$this->idproducto,])
                    ->orderBy(['idunidad'=>SORT_DESC])->asArray()->all(),
                'equivalencia','descripcion'
            );
        }else{
            return ArrayHelper::map(
                Unidades::find()->andFilterWhere([
                    'idproducto'=>$this->idproducto,
                ])->andFilterWhere(['<>','idunidad',$this->idunidad])
                    ->orderBy(['idunidad'=>SORT_DESC])->asArray()->all(),
                'equivalencia','descripcion'
            );
        }

    }
}
