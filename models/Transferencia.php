<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "transferencia".
 *
 * @property integer $idtransferencia
 * @property string $motivo
 * @property string $estado
 * @property integer $idbotorigen
 * @property integer $idbotdestino
 * @property integer $origen_conf
 * @property integer $destino_conf
 * @property string $create_at
 *
 * @property DetalleTransferencia[] $items
 * @property Botica $botorigen
 * @property Botica $botdestino
 */
class Transferencia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transferencia';
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
    public function rules()
    {
        return [
            [['estado', 'idbotorigen', 'idbotdestino'], 'required'],
            [['create_at','update_at'], 'safe'],
            [['idbotorigen', 'idbotdestino', 'origen_conf', 'destino_conf'], 'integer'],
            [['estado'], 'string', 'max' => 1],
            [['idbotorigen'], 'exist', 'skipOnError' => true, 'targetClass' => Botica::className(), 'targetAttribute' => ['idbotorigen' => 'idbotica']],
            [['idbotdestino'], 'exist', 'skipOnError' => true, 'targetClass' => Botica::className(), 'targetAttribute' => ['idbotdestino' => 'idbotica']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idtransferencia' => 'ID',
            'estado' => 'ESTADO',
            'idbotorigen' => 'ORIGEN',
            'idbotdestino' => 'DESTINO',
            'origen_conf' => 'Origen Conf',
            'destino_conf' => 'Destino Conf',
            'create_at'=>'REGISTRADO'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(DetalleTransferencia::className(), ['idtransferencia' => 'idtransferencia']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBotorigen()
    {
        return $this->hasOne(Botica::className(), ['idbotica' => 'idbotorigen']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBotdestino()
    {
        return $this->hasOne(Botica::className(), ['idbotica' => 'idbotdestino']);
    }
    public function getCboOrigen(){
        return ArrayHelper::map(Botica::find()->where('tipo_almacen=1')->asArray()->all(),
            'idbotica','nomrazon');
    }


    public function getLblEstado(){
        switch ($this->estado){
            case 'P':
                $valor='<label class="label label-danger">Sin Procesar</label>';
                break;
            case 'E':
                $valor='<label class="label label-warning">Enviado</label>';
                break;
            case 'R':
                $valor='<label class="label label-info">Entregado</label>';
                break;
            case 'F':
                $valor='<label class="label label-success">Procesado</label>';
                break;
        }
        return $valor;
    }





}
