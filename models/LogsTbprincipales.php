<?php

namespace app\models;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "logs_tbprincipales".
 *
 * @property integer $idlog
 * @property string $tabla
 * @property integer $idclave
 * @property string $data
 * @property integer $estado
 * @property integer $create_by
 * @property integer $update_by
 *
 * @property Sincronizado[] $sincronizados
 * @property Botica[] $idboticas
 */

class LogsTbprincipales extends \yii\db\ActiveRecord {
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'logs_tbprincipales';
	}

	public function behaviors() {
		return [
                    'timestamp' =>[
                        'class'=>'yii\behaviors\TimestampBehavior',
                        'attributes'=>[
                            ActiveRecord::EVENT_BEFORE_INSERT=>['create_at','update_at'],
                            ActiveRecord::EVENT_BEFORE_UPDATE=>['update_at']
                        ],
                        'value'=> new Expression('NOW()'),
                    ],
                    'blameable' => [
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
			[['tabla', 'idclave', 'data', 'estado'], 'required'],
			[['idclave', 'estado'], 'integer'],
			[['data'], 'string'],
			[['tabla'], 'string', 'max' => 80],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'idlog'     => 'Idlog',
			'tabla'     => 'Tabla',
			'idclave'   => 'Idclave',
			'data'      => 'Data',
			'estado'    => 'Estado',
			'create_by' => 'Create By',
			'update_by' => 'Update By',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSincronizados() {
		return $this->hasMany(Sincronizado::className(), ['idlog' => 'idlog']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getBoticas() {
		return $this->hasMany(Botica::className(), ['idbotica' => 'idbotica'])->viaTable('sincronizado', ['idlog' => 'idlog']);
	}
}
