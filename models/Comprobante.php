<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "comprobante".
 *
 * @property integer $idcomprobante
 * @property string $descripcion
 * @property string $abreviatura
 * @property integer $tipocompra
 * @property integer $tipoventa
 * @property integer $create_by
 * @property integer $update_by
 * @property string $sincronizaciones
 *
 * @property DocumentoEmitido[] $documentoEmitidos
 * @property Ingreso[] $ingresos
 * @property Pedidos[] $pedidos
 * @property Talonarios[] $talonarios
 */

class Comprobante extends \yii\db\ActiveRecord {
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'comprobante';
	}

	public function behaviors() {
		return [
			'blameable' => [
				'class'              => BlameableBehavior::className(),
				'createdByAttribute' => 'create_by',
				'updatedByAttribute' => 'update_by'
			]
		];
	}

    public function beforeSave($insert)
    {
        $this->descripcion=mb_strtoupper($this->descripcion,'utf-8');
        $this->abreviatura=mb_strtoupper($this->abreviatura,'utf-8');
        return parent::beforeSave($insert);
    }

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['descripcion', 'abreviatura', 'tipocompra', 'tipoventa'], 'required'],
			[['tipocompra', 'tipoventa', 'create_by', 'update_by'], 'integer'],
			[['sincronizaciones'], 'string'],
			[['descripcion'], 'string', 'max' => 100],
			[['abreviatura'], 'string', 'max' => 5],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'idcomprobante' => 'ID',
			'descripcion'   => 'DESCRIPCION',
			'abreviatura'   => 'ABREV.',
			'tipocompra'    => '¿DE COMPRA?',
			'tipoventa'     => '¿DE VENTA?',
		];
	}

	public function validaEliminar(){
	    $cuenta=count($this->documentoEmitidos)+count($this->ingresos)+
            count($this->pedidos)+count($this->talonarios);
        if($cuenta==0){
            return true;
        }else{
            return false;
        }
    }

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDocumentoEmitidos() {
		return $this->hasMany(DocumentoEmitido::className(), ['idcomprobante' => 'idcomprobante']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIngresos() {
		return $this->hasMany(Ingreso::className(), ['idcomprobante' => 'idcomprobante']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPedidos() {
		return $this->hasMany(Pedidos::className(), ['idcomprobante' => 'idcomprobante']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTalonarios() {
		return $this->hasMany(Talonarios::className(), ['idcomprobante' => 'idcomprobante']);
	}
}
