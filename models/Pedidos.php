<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pedidos".
 *
 * @property integer $idpedido
 * @property integer $idcliente
 * @property integer $datoscliente
 * @property string $fecha_registro
 * @property integer $idcomprobante
 * @property string $ndocumento
 * @property string $total
 * @property string $pago
 * @property string $detalles
 * @property string $estado
 * @property integer $entregado
 * @property integer $idbotica
 * @property integer $idlocal
 * @property integer $sincroniza
 *
 * @property DocumentoEmitido[] $documentosEmitidos
 * @property Clientes $cliente
 * @property Botica $botica
 * @property Comprobante $comprobante
 * @property Salida[] $salidas
 */
class Pedidos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pedidos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fecha_registro', 'idcomprobante', 'estado', 'entregado', 'idbotica', 'idlocal'], 'required'],
            [['idcliente', 'idcomprobante', 'entregado', 'idbotica', 'idlocal'], 'integer'],
            [['fecha_registro','datoscliente','detalles'], 'safe'],
            [['total','pago'], 'number'],
            [['ndocumento'], 'string', 'max' => 45],
            [['estado'], 'string', 'max' => 1],
            [['idcliente'], 'exist', 'skipOnError' => true, 'targetClass' => Clientes::className(), 'targetAttribute' => ['idcliente' => 'idcliente']],
            [['idbotica'], 'exist', 'skipOnError' => true, 'targetClass' => Botica::className(), 'targetAttribute' => ['idbotica' => 'idbotica']],
            [['idcomprobante'], 'exist', 'skipOnError' => true, 'targetClass' => Comprobante::className(), 'targetAttribute' => ['idcomprobante' => 'idcomprobante']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idpedido' => 'Idpedido',
            'idcliente' => 'Idcliente',
            'fecha_registro' => 'Fecha Registro',
            'idcomprobante' => 'Idcomprobante',
            'ndocumento' => 'Ndocumento',
            'total' => 'Total',
            'estado' => 'Estado',
            'entregado' => 'Entregado',
            'idbotica' => 'Idbotica',
            'idlocal' => 'Idlocal',
        ];
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentosEmitidos()
    {
        return $this->hasMany(DocumentoEmitido::className(), ['idpedido' => 'idpedido']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(Clientes::className(), ['idcliente' => 'idcliente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBotica()
    {
        return $this->hasOne(Botica::className(), ['idbotica' => 'idbotica']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComprobante()
    {
        return $this->hasOne(Comprobante::className(), ['idcomprobante' => 'idcomprobante']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalidas()
    {
        return $this->hasMany(Salida::className(), ['idpedido' => 'idpedido']);
    }
}
