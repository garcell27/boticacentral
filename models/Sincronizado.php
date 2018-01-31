<?php

namespace app\models;
use yii\behaviors\BlameableBehavior;
use Yii;
use yii\db\Expression;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sincronizado".
 *
 * @property integer $idlog
 * @property integer $idbotica
 * @property integer $estado
 * @property integer $create_by
 * @property integer $update_by
 *
 * @property Botica $botica
 * @property LogsTbprincipales $log
 */

class Sincronizado extends \yii\db\ActiveRecord {
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'sincronizado';
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
			[['idlog', 'idbotica', 'estado'], 'required'],
			[['idlog', 'idbotica', 'estado'], 'integer'],
			[['idbotica'], 'exist', 'skipOnError' => true, 'targetClass' => Botica::className(), 'targetAttribute' => ['idbotica' => 'idbotica']],
			[['idlog'], 'exist', 'skipOnError'    => true, 'targetClass'    => LogsTbprincipales::className(), 'targetAttribute'    => ['idlog'    => 'idlog']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'idlog'     => 'Idlog',
			'idbotica'  => 'Idbotica',
			'estado'    => 'Estado',
			'create_by' => 'Create By',
			'update_by' => 'Update By',
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
	public function getLog() {
		return $this->hasOne(LogsTbprincipales::className(), ['idlog' => 'idlog']);
	}
}
