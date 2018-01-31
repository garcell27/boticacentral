<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "ingreso".
 *
 * @property integer $idingreso
 * @property integer $idproveedor
 * @property integer $idcomprobante
 * @property string $n_comprobante
 * @property string $tipo
 * @property string $f_emision
 * @property string $f_registro
 * @property string $total
 * @property integer $conigv
 * @property string $total_igv
 * @property double $porcentaje
 * @property integer $idbotica
 * @property string $estado
 *
 * @property DetalleIngreso[] $detalleIngreso
 * @property Proveedores $proveedor
 * @property Botica $botica
 * @property Comprobante $comprobante
 */
class Ingreso extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ingreso';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idproveedor', 'idcomprobante', 'n_comprobante', 'tipo', 'f_emision', 'f_registro', 'total', 'conigv', 'idbotica', 'estado'], 'required'],
            [['idproveedor', 'idcomprobante', 'conigv', 'idbotica'], 'integer'],
            [['f_emision', 'f_registro'], 'safe'],
            [['total', 'total_igv', 'porcentaje'], 'number'],
            [['n_comprobante'], 'string', 'max' => 45],
            [['tipo', 'estado'], 'string', 'max' => 1],
            [['idproveedor'], 'exist', 'skipOnError' => true, 'targetClass' => Proveedores::className(), 'targetAttribute' => ['idproveedor' => 'idproveedor']],
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
            'idingreso' => 'ID',
            'idproveedor' => 'PROVEEDOR',
            'idcomprobante' => 'COMPROBANTE',
            'n_comprobante' => 'NRO',
            'tipo' => 'TIPO',
            'f_emision' => 'F. EMISION',
            'f_registro' => 'F REGISTRO',
            'total' => 'TOTAL',
            'conigv' => ' CON IGV?',
            'total_igv' => 'IGV',
            'porcentaje' => '%',
            'idbotica' => 'BOTICA',
            'estado' => 'ESTADO',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleIngreso()
    {
        return $this->hasMany(DetalleIngreso::className(), ['idingreso' => 'idingreso']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProveedor()
    {
        return $this->hasOne(Proveedores::className(), ['idproveedor' => 'idproveedor']);
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


    public function getLabeltipo(){
        $label='';
        switch ($this->tipo){
            case 'I':
                $label='INVENTARIO';
                break;
            case 'C':
                $label='COMPRA';
                break;
            default:
                break;
        }
        return $label;
    }
    public function getLabelestado(){
        $label='';
        switch($this->estado){
            case 'P':
                $label='<span class="label label-warning">Pendiente</span>';
                break;
            case 'F':
                $label='<span class="label label-success">Finalizado</span>';
                break;
            default:
                break;
        }
        return $label;
    }
    public function getCboProveedores(){
        return ArrayHelper::map(Proveedores::find()->orderBy('nomproveedor')->asArray()->all(), 'idproveedor', 'nomproveedor');
    }
    public function getCboComprobante(){
        return ArrayHelper::map(Comprobante::find()
                ->orderBy('descripcion')->where(['tipocompra'=>1])
                ->asArray()->all(), 'idcomprobante', 'descripcion');
    }
    
    public function getCboBoticaAlmacen(){
        return ArrayHelper::map(Botica::find()->where(['tipo_almacen'=>1])
                ->asArray()->all(), 
                'idbotica', 'nomrazon');
    }
    public function recalculaImporte(){
        if($this->tipo=='C' && count($this->detalleIngreso)){
            $total=0;
            foreach ($this->detalleIngreso as $detalle){
                $total+=$detalle->subtotal;
            }

            if($this->conigv==1){
                $this->total= number_format($total,2,'.','');
                $subtotal=$total/(1+$this->porcentaje);
                $this->total_igv= number_format($total-$subtotal,2,'.','');
            }else{
                $this->total_igv= number_format($total*$this->porcentaje,2,'.','');
                $this->total= number_format($total+$this->total_igv,2,'.','');
            }
            $this->save();
        }
    }
    
    public function getCboInvDisponible(){
        $data=[];
        $boticas= Botica::find()->all();
        foreach ($boticas as $b){
            $c=$this->find()->where(['tipo'=>'I','estado'=>'P','idbotica'=>$b->idbotica])->count();
            if($c==0){
                $data[$b->idbotica]=$b->nomrazon;
            }
        }
        return $data;
    }
}
