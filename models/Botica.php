<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "botica".
 *
 * @property integer $idbotica
 * @property string $nomrazon
 * @property string $ruc
 * @property string $direccion
 * @property integer $idclientecaja
 * @property integer $idinventario
 * @property integer $idcompped
 * @property integer $tipo_almacen
 * @property integer $create_by
 * @property integer $update_by
 * @property integer $conectado
 *
 * @property Ingreso[] $ingresos
 * @property Pedidos[] $pedidos
 * @property Stock[] $stocks
 * @property Talonarios[] $talonarios
 */

class Botica extends \yii\db\ActiveRecord {
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'botica';
	}

	public function behaviors() {
		return [
			'blameable'           => [
				'class'              => BlameableBehavior::className(),
				'createdByAttribute' => 'create_by',
				'updatedByAttribute' => 'update_by'
			]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['nomrazon', 'ruc', 'direccion'], 'required'],
			[['idclientecaja', 'idinventario', 'idcompped', 'tipo_almacen', 'create_by', 'update_by', 'conectado'], 'integer'],
			[['nomrazon', 'direccion'], 'string', 'max' => 250],
			[['ruc'], 'string', 'max' => 11],

		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'idbotica'      => 'ID',
			'nomrazon'      => 'RAZON SOCIAL',
			'ruc'           => 'RUC',
			'direccion'     => 'DIRECCION',
			'idclientecaja' => 'CLIENTE CAJA',
			'idinventario'  => 'PROV. INVENTARIO',
			'idcompped'     => 'COMPR. DE PEDIDO',
			'tipo_almacen'  => 'TIPO',
			'create_by'     => 'REGISTRADO POR',
			'update_by'     => 'MODIFICADO POR',

		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */

	public function getIngresos() {
		return $this->hasMany(Ingreso::className(), ['idbotica' => 'idbotica']);
	}

	public function getSalidas() {
		return $this->hasMany(Salida::className(), ['idbotica' => 'idbotica']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPedidos() {
		return $this->hasMany(Pedidos::className(), ['idbotica' => 'idbotica']);
	}

	public function getMovimientoStocks() {
		return $this->hasMany(MovimientoStock::className(), ['idbotica' => 'idbotica']);
	}
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getStocks() {
		return $this->hasMany(Stock::className(), ['idbotica' => 'idbotica']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTalonarios() {
		return $this->hasMany(Talonarios::className(), ['idbotica' => 'idbotica']);
	}

	public function getCboclicaja() {
		return ArrayHelper::map(Clientes::find()->where(['cajarapida' => 1])->asArray()->all(),
			'idcliente', 'nomcliente');
	}
	public function getCboprovinv() {
		return ArrayHelper::map(Proveedores::find()->where(['inventario' => 1])->asArray()->all(),
			'idcliente', 'nomproveedor');
	}
	public function getCbocomprobante() {
		return ArrayHelper::map(Comprobante::find()->where(['tipoventa' => 1])->asArray()->all(),
			'idcomprobante', 'descripcion');
	}

}
