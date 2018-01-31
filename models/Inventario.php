<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "inventarios".
 *
 * @property integer $idinventario
 * @property string $motivo
 * @property integer $idbotica
 * @property string $estado
 * @property integer $create_by
 * @property integer $update_by
 * @property string $create_at
 * @property string $update_at
 *
 * @property DetalleInventario[] $detalleInventarios
 * @property Botica $botica
 */
class Inventario extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inventarios';
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
    public function rules()
    {
        return [
            [['motivo', 'idbotica', 'estado'], 'required'],
            [['idbotica', 'create_by', 'update_by'], 'integer'],
            [['create_at', 'update_at'], 'safe'],
            [['motivo'], 'string', 'max' => 2],
            [['estado'], 'string', 'max' => 1],
            [['idbotica'], 'exist', 'skipOnError' => true, 'targetClass' => Botica::className(), 'targetAttribute' => ['idbotica' => 'idbotica']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idinventario' => 'ID',
            'motivo' => 'MOTIVO',
            'idbotica' => 'BOTICA',
            'estado' => 'ESTADO',
            'create_by' => 'Create By',
            'update_by' => 'Update By',
            'create_at' => 'REGISTRADO',
            'update_at' => 'Update At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleInventarios()
    {
        return $this->hasMany(DetalleInventario::className(), ['idinventario' => 'idinventario']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBotica()
    {
        return $this->hasOne(Botica::className(), ['idbotica' => 'idbotica']);
    }


    public function getCboBoticas(){
        return ArrayHelper::map(Botica::find()->asArray()->all(),
            'idbotica', 'nomrazon');
    }
}
