<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
/**
 * This is the model class for table "movimiento_stock".
 *
 * @property integer $idmovimiento_stock
 * @property integer $idunidad
 * @property string $fecha
 * @property string $tipo_transaccion
 * @property integer $idprocedencia
 * @property string $cantidad
 * @property string $detalle
 * @property integer $idbotica
 * @property integer $create_by
 * @property integer $update_by
 * @property integer $idlocal
 *
 * @property Botica $botica
 * @property Unidades $unidad
 */

class MovimientoStock extends \yii\db\ActiveRecord {
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'movimiento_stock';
	}

	public function behaviors() {
		return [
            'timestamp' =>[
                'class'=>'yii\behaviors\TimestampBehavior',
                'attributes'=>[
                    ActiveRecord::EVENT_BEFORE_INSERT=>['fecha'],
                    ActiveRecord::EVENT_BEFORE_UPDATE=>['fechamod']
                ],
                'value'=> new Expression('NOW()'),
            ],
                    'blameable'=>[
                        'class'=> BlameableBehavior::className(),
                        'createdByAttribute'=>'create_by',
                        'updatedByAttribute'=>'update_by'
                    ]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['idunidad', 'tipo_transaccion', 'cantidad', 'idbotica'], 'required'],
			[['idunidad', 'idprocedencia', 'idbotica'], 'integer'],
			[['fecha','create_by','update_by'], 'safe'],
			[['cantidad'], 'number'],
			[['detalle'], 'string'],
			[['tipo_transaccion'], 'string', 'max' => 1],
			[['idbotica'], 'exist', 'skipOnError'  => true, 'targetClass'  => Botica::className(), 'targetAttribute'  => ['idbotica'  => 'idbotica']],
			[['idunidad'], 'exist', 'skipOnError'  => true, 'targetClass'  => Unidades::className(), 'targetAttribute'  => ['idunidad'  => 'idunidad']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'idmovimiento_stock' => 'Idmovimiento Stock',
			'idunidad'           => 'Idunidad',
			'fecha'              => 'Fecha',
			'tipo_transaccion'   => 'Tipo Transaccion',
			'idprocedencia'      => 'Idprocedencia',
			'cantidad'           => 'Cantidad',
			'detalle'            => 'Detalle',
			'idbotica'           => 'Idbotica',
            'create_by'          =>'Creado Por',
            'update_by'          =>'Actualizado Por',
                    
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getBotica() {
		return $this->hasOne(Botica::className(), ['idbotica' => 'idbotica']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUnidad() {
		return $this->hasOne(Unidades::className(), ['idunidad' => 'idunidad']);
	}
}
