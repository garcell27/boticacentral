<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detalle_transferencia".
 *
 * @property integer $iddetalle
 * @property integer $idtransferencia
 * @property integer $idunidad
 * @property string $cantidad
 *
 * @property Transferencia $transferencia
 * @property Unidades $unidad
 */
class DetalleTransferencia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detalle_transferencia';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idtransferencia', 'idunidad', 'cantidad'], 'required'],
            [['idtransferencia', 'idunidad'], 'integer'],
            [['cantidad'], 'number'],
            [['idtransferencia'], 'exist', 'skipOnError' => true, 'targetClass' => Transferencia::className(), 'targetAttribute' => ['idtransferencia' => 'idtransferencia']],
            [['idunidad'], 'exist', 'skipOnError' => true, 'targetClass' => Unidades::className(), 'targetAttribute' => ['idunidad' => 'idunidad']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddetalle' => 'Iddetalle',
            'idtransferencia' => 'Idtransferencia',
            'idunidad' => 'Idunidad',
            'cantidad' => 'Cantidad',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransferencia()
    {
        return $this->hasOne(Transferencia::className(), ['idtransferencia' => 'idtransferencia']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnidad()
    {
        return $this->hasOne(Unidades::className(), ['idunidad' => 'idunidad']);
    }
}
