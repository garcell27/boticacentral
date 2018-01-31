<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documento_emitido".
 *
 * @property integer $iddocumento
 * @property integer $idpedido
 * @property integer $idcomprobante
 * @property string $ndocumento
 * @property string $total
 * @property integer $conigv
 * @property string $total_igv
 * @property double $porcentaje
 * @property integer $idbotica
 * @property integer $estado
 *
 * @property Comprobante $idcomprobante0
 * @property Pedidos $idpedido0
 */
class DocumentoEmitido extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'documento_emitido';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idpedido', 'idcomprobante', 'estado','idbotica'], 'required'],
            [['idpedido', 'idcomprobante', 'conigv', 'estado','idbotica'], 'integer'],
            [['total', 'total_igv', 'porcentaje'], 'number'],
            [['ndocumento'], 'string', 'max' => 45],
            [['idcomprobante'], 'exist', 'skipOnError' => true, 'targetClass' => Comprobante::className(), 'targetAttribute' => ['idcomprobante' => 'idcomprobante']],
            [['idpedido'], 'exist', 'skipOnError' => true, 'targetClass' => Pedidos::className(), 'targetAttribute' => ['idpedido' => 'idpedido']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddocumento' => 'Iddocumento',
            'idpedido' => 'Idpedido',
            'idcomprobante' => 'Idcomprobante',
            'ndocumento' => 'Ndocumento',
            'total' => 'Total',
            'conigv' => 'Conigv',
            'total_igv' => 'Total Igv',
            'porcentaje' => 'Porcentaje',
            'estado' => 'Estado',
            'idbotica'=>'Botica',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdcomprobante0()
    {
        return $this->hasOne(Comprobante::className(), ['idcomprobante' => 'idcomprobante']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdpedido0()
    {
        return $this->hasOne(Pedidos::className(), ['idpedido' => 'idpedido']);
    }
}
