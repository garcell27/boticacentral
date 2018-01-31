<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "talonarios".
 *
 * @property integer $idtalonario
 * @property integer $idcomprobante
 * @property string $serie
 * @property string $numero
 * @property integer $maxitem
 * @property integer $idbotica
 *
 * @property Botica $botica
 * @property Comprobante $comprobante
 */

class Talonarios extends \yii\db\ActiveRecord {
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'talonarios';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['idcomprobante', 'serie', 'numero', 'idbotica'], 'required'],
			[['idcomprobante', 'maxitem', 'numero', 'idbotica'], 'integer'],
			[['serie'], 'string', 'max' => 3],

			[['idbotica'], 'exist', 'skipOnError'      => true, 'targetClass'      => Botica::className(), 'targetAttribute'      => ['idbotica'      => 'idbotica']],
			[['idcomprobante'], 'exist', 'skipOnError' => true, 'targetClass' => Comprobante::className(), 'targetAttribute' => ['idcomprobante' => 'idcomprobante']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'idtalonario'   => 'ID',
			'idcomprobante' => 'COMPROBANTE',
			'serie'         => 'SERIE',
			'numero'        => 'NUMERO',
			'maxitem'       => 'MAXITEM',
			'idbotica'      => 'BOTICA',
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
	public function getComprobante() {
		return $this->hasOne(Comprobante::className(), ['idcomprobante' => 'idcomprobante']);
	}

	public function getCboBoticas() {
		return ArrayHelper::map(Botica::find()->asArray()->all(), 'idbotica', 'nomrazon');
	}

	public function getCboComprobante() {
		return ArrayHelper::map(Comprobante::find()->filterWhere(['tipoventa' => 1])->asArray()->all(), 'idcomprobante', 'descripcion');
	}

	public function getNdocumento() {

		return $this->serie.'-'.$this->zerofill($this->numero, 7);
	}

	private function zerofill($entero, $largo) {
		// Limpiamos por si se encontraran errores de tipo en las variables
		$entero  = (int) $entero;
		$largo   = (int) $largo;
		$relleno = '';

		if (strlen($entero) < $largo) {
			$relleno = str_repeat('0', $largo-strlen($entero));
		}
		return $relleno.$entero;

	}

}
