<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stock".
 *
 * @property integer $idstock
 * @property integer $idunidad
 * @property integer $idbotica
 * @property string $fisico
 * @property string $separado
 * @property string $bloqueado
 * @property string $detalle
 * @property string $minimo
 *
 *
 * @property Botica $botica
 * @property Unidades $unidad
 */
class Stock extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public $disponible;
    public $valorVentaRef;


    public static function tableName()
    {
        return 'stock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idunidad', 'idbotica', 'fisico', 'separado', 'bloqueado'], 'required'],
            [['idunidad', 'idbotica'], 'integer'],
            [['fisico', 'separado', 'bloqueado','minimo'], 'number'],
            [['detalle'], 'string'],
            [['idbotica'], 'exist', 'skipOnError' => true, 'targetClass' => Botica::className(), 'targetAttribute' => ['idbotica' => 'idbotica']],
            [['idunidad'], 'exist', 'skipOnError' => true, 'targetClass' => Unidades::className(), 'targetAttribute' => ['idunidad' => 'idunidad']],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idstock' => 'ID',
            'idunidad' => 'UNIDAD',
            'idbotica' => 'BOTICA',
            'fisico' => 'FISICO',
            'separado' => 'SEPARADO',
            'bloqueado' => 'BLOQUEADO',
            'detalle' => 'DETALLE',
            'minimo' => 'MINIMO'
        ];
    }


    public function getDisponible(){
        $disponible=$this->fisico - $this->separado - $this->bloqueado;
        return number_format($disponible,2,'.','');
    }


    public function getDispVenta($idunidad){
        $unidad=Unidades::findOne($idunidad);
        $disponible=$this->getDisponible();
        return floor($disponible/$unidad->equivalencia);
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
    public function getUnidad()
    {
        return $this->hasOne(Unidades::className(), ['idunidad' => 'idunidad']);
    }

    public function getValorVentaRef(){
        $producto= $this->unidad->producto;
        $eq=0;
        $valor=0;
        foreach ($producto->undventas as $uv){
            if(floatval($uv->equivalencia)>$eq){
                $eq=floatval($uv->equivalencia);
                $valor= floatval($uv->preciosug);                
            }
        }
        if($eq==0){
            return 0;
        }else{
            return $valor/$eq;
        }
        
    }

    public function verStock(){
        $allund=Unidades::find()
            ->where(['idproducto'=>$this->unidad->idproducto])
            ->orderBy(['equivalencia'=>SORT_DESC])->all();
        $lbl='';
        $disp=$this->fisico-$this->separado-$this->bloqueado;
        if($disp>0){
            $residuo=$disp;
            $total=count($allund);
            if($total){
                foreach ($allund as $i=>$und){
                    $cociente=floor($residuo/$und->equivalencia);
                    if($cociente>0){
                        $lbl.='<span class="label label-sm label-inverse">'.$cociente." ".$und->descripcion.'</span> ';
                    }
                    $residuo=$residuo%$und->equivalencia;
                }
            }else{
                $lbl='Nothing';
            }
        }else{
            $lbl='<span class="label label-sm label-danger">Vacio</span>';
        }


        return $lbl;
    }

    public function verStockRaw(){
        $allund=Unidades::find()
            ->where(['idproducto'=>$this->unidad->idproducto])
            ->orderBy(['equivalencia'=>SORT_DESC])->all();
        $lbl=[];
        $disp=$this->fisico-$this->separado-$this->bloqueado;
        if($disp>0){
            $residuo=$disp;
            $total=count($allund);
            if($total){
                foreach ($allund as $i=>$und){
                    $cociente=floor($residuo/$und->equivalencia);
                    if($cociente>0){
                        $lbl[]=[
                            'cantidad'=>$cociente,
                            'descripcion'=>$und->descripcion
                        ];
                    }
                    $residuo=$residuo%$und->equivalencia;
                }
            }else{
                $lbl=null;
            }
        }else{
            $lbl=[];
        }


        return $lbl;
    }


}
