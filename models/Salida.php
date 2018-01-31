<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "salida".
 *
 * @property integer $idsalida
 * @property integer $idpedido
 * @property integer $idpedlocal
 * @property string $fecha_registro
 * @property string $motivo
 * @property string $estado
 * @property integer $idbotica
 * @property integer $idusuario
 *
 * @property DetalleSalida[] $detalleSalida
 * @property Pedidos $pedido
 * @property Botica $botica
 * @property Usuarios $usuario
 */
class Salida extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salida';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idpedido', 'idpedlocal', 'idbotica', 'idlocal', 'sincroniza', 'create_by', 'update_by'], 'integer'],
            [['motivo', 'estado', 'idbotica', 'create_by', 'update_by', 'create_at', 'update_at'], 'required'],
            [['create_at', 'update_at'], 'safe'],
            [['motivo', 'estado'], 'string', 'max' => 1],
            [['idbotica'], 'exist', 'skipOnError' => true, 'targetClass' => Botica::className(), 'targetAttribute' => ['idbotica' => 'idbotica']],
            [['idpedido'], 'exist', 'skipOnError' => true, 'targetClass' => Pedidos::className(), 'targetAttribute' => ['idpedido' => 'idpedido']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idsalida' => 'ID',
            'idpedido' => 'Idpedido',
            'motivo' => 'MOTIVO',
            'estado' => 'ESTADO',
            'idbotica' => 'BOTICA',
            'idusuario' => 'USUARIO'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleSalida()
    {
        return $this->hasMany(DetalleSalida::className(), ['idsalida' => 'idsalida']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPedido()
    {
        return $this->hasOne(Pedidos::className(), ['idpedido' => 'idpedido']);
    }
    public function getUsuario(){
        return $this->hasOne(Usuarios::className(), ['idusuario' => 'idusuario']);
    }

    public function getBotica()
    {
        return $this->hasOne(Botica::className(), ['idbotica' => 'idbotica']);
    }




}
