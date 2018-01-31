<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\behaviors\BlameableBehavior;
use yii\helpers\Json;

/**
 * This is the model class for table "productos".
 *
 * @property integer $idproducto
 * @property integer $idcategoria
 * @property integer $idlaboratorio
 * @property string $descripcion
 * @property string $tipo
 * @property string $detalle
 * @property integer $caducidad 
 * @property integer $create_by
 * @property integer $update_by
 * @property string $sincronizaciones
 *
 * @property Categorias $categoria
 * @property Laboratorio $laboratorio
 * @property Unidades[] $unidades
 * @property Unidades[] $undventas
 * @property Unidades $undpri
 */
class Productos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */


    public static function tableName()
    {
        return 'productos';
    }
    public function behaviors() {
        return [
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'create_by',
                'updatedByAttribute' => 'update_by'
            ]
        ];
    }
    public function beforeSave($insert)
    {
        $this->descripcion=mb_strtoupper($this->descripcion,'utf-8');
        return parent::beforeSave($insert);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idcategoria', 'idlaboratorio', 'descripcion', 'caducidad'], 'required'],
            [['idcategoria', 'idlaboratorio', 'caducidad', 'create_by', 'update_by'], 'integer'],
            [['detalle', 'sincronizaciones'], 'string'],
            [['descripcion'], 'string', 'max' => 250],
            [['tipo'], 'string', 'max' => 1],
            [['idcategoria'], 'exist', 'skipOnError' => true, 'targetClass' => Categorias::className(), 'targetAttribute' => ['idcategoria' => 'idcategoria']],
            [['idlaboratorio'], 'exist', 'skipOnError' => true, 'targetClass' => Laboratorio::className(), 'targetAttribute' => ['idlaboratorio' => 'idlaboratorio']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idproducto' => 'ID',
            'idcategoria' => 'CATEGORIA',
            'idlaboratorio' => 'LABORATORIO',
            'descripcion' => 'DESCRIPCION',
            'detalle' => 'DETALLE',
            'caducidad' => 'CADUCIDAD',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoria()
    {
        return $this->hasOne(Categorias::className(), ['idcategoria' => 'idcategoria']);
    }

    public function getNombrelaboratorio(){

        return $this->descripcion.' - '.$this->laboratorio->nombre;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLaboratorio()
    {
        return $this->hasOne(Laboratorio::className(), ['idlaboratorio' => 'idlaboratorio']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnidades()
    {
        return $this->hasMany(Unidades::className(), ['idproducto' => 'idproducto']);
    }

    public function getUndventas(){
        return $this->hasMany(Unidades::className(),['idproducto'=>'idproducto'])->andWhere(['paraventa'=>1]);
    }
    public function getUndpri(){
        return $this->hasOne(Unidades::className(),['idproducto'=>'idproducto'])->andWhere(['idundprimaria'=>null]);
    }



    public function getCboCategorias(){
        return ArrayHelper::map(Categorias::find()->orderBy('descripcion')->asArray()->all(),'idcategoria','descripcion');
    }

    public function getCboLaboratorios(){
        return ArrayHelper::map(Laboratorio::find()->orderBy('nombre')->asArray()->all(),'idlaboratorio','nombre');
    }

    public function verStock($idbotica){
        $undpri=Unidades::find()->where(['idproducto'=>$this->idproducto,'tipo'=>'P'])->one();
        if($undpri){
            $stock=Stock::find()->where(['idunidad'=>$undpri->idunidad,'idbotica'=>$idbotica])->one();
            if($stock){
                $allund=Unidades::find()->where(['idproducto'=>$this->idproducto])->orderBy(['equivalencia'=>SORT_DESC])->all();
                $lbl=[];
                $info='';
                $infotext='';
                $disp=$stock->fisico-$stock->separado-$stock->bloqueado;
                if($disp==0){
                  $info='<span class="label label-sm arrowed arrowed-right label-danger">Sin Stock</span>';
                  $infotext= "0 ".$undpri->descripcion;
                }else{
                    $residuo=$disp;
                    foreach ($allund as $i=>$und){
                        $cociente=floor($residuo/$und->equivalencia);
                        $lbl[]=['cantidad'=>$cociente,'unidad'=>$und->descripcion];
                        if($cociente>0){                        
                            $info.='<span class="label label-sm label-inverse arrowed-in arrowed-in-right">'.$cociente.' '.$und->descripcion.'</span> ';
                            $infotext.=$cociente.' '.$und->descripcion;
                            $infotext.= $und->tipo=='P'?'.':'; ';
                        }

                        $residuo=$residuo%$und->equivalencia;
                    }
                }
                
                return [
                    'existe'=>true,
                    'fisico'=>$stock->fisico,
                    'separado'=>$stock->separado,
                    'bloqueado'=>$stock->bloqueado,
                    'disponible'=>$disp,
                    'label'=>$lbl,
                    'info'=>$info,
                    'infotext'=>$infotext
                ];
            }else{
                return ['existe'=>false];
            }
        }else{
            return ['existe'=>false];
        }
    }


    
    public function creaLabel($cantidad){
        $allund=Unidades::find()->where(['idproducto'=>$this->idproducto])->orderBy(['equivalencia'=>SORT_DESC])->all();
        $lbl=[];
        $info='<span class="label label-sm arrowed arrowed-right label-danger">Sin Valor</span>';
        if(count($allund) &&$cantidad>0){
            $info='';
            $residuo=$cantidad;
            foreach ($allund as $i=>$und){
                $cociente=floor($residuo/$und->equivalencia);
                $lbl[]=['cantidad'=>$cociente,'unidad'=>$und->descripcion];
                if($cociente>0){                        
                    $info.='<span class="label label-sm label-inverse arrowed-in arrowed-in-right">'.$cociente.' '.$und->descripcion.'</span> ';
                }
                $residuo=$residuo%$und->equivalencia;
            }
        }
        return $info;
        
    }


    public function recalculaStock($idbotica){
        $undpri=$this->undpri;
        $stock=Stock::find()->where(['idunidad'=>$undpri->idunidad,'idbotica'=>$idbotica])->one();
        $ingresos=MovimientoStock::find()->where([
            'idunidad'=>$undpri->idunidad,'idbotica'=>$idbotica,'tipo_transaccion'=>'I'
        ])->sum('cantidad');
        $tingresos=MovimientoStock::find()->where([
            'idunidad'=>$undpri->idunidad,'idbotica'=>$idbotica,'tipo_transaccion'=>'J'
        ])->sum('cantidad');
        $egresos=MovimientoStock::find()->where([
            'idunidad'=>$undpri->idunidad,'idbotica'=>$idbotica,'tipo_transaccion'=>'E'
        ])->sum('cantidad');
        $tegresos=MovimientoStock::find()->where([
            'idunidad'=>$undpri->idunidad,'idbotica'=>$idbotica,'tipo_transaccion'=>'F'
        ])->sum('cantidad');
        $fisico=$ingresos+$tingresos-$egresos-$tegresos;
        if($stock){
            //calculo fisico
            $stock->fisico=number_format($fisico,2,'.','');
            //calculo pendiente
            $detTransf=DetalleTransferencia::find()
                ->innerJoin('transferencia t','t.idtransferencia = detalle_transferencia.idtransferencia')
                ->andWhere("t.idbotorigen = ".$idbotica." and t.estado in ('P','E','R')")->all();
            $sumpendiente=0;
            foreach ($detTransf as $dt){
                foreach ($this->unidades as $u){
                    if($dt->idunidad==$u->idunidad){
                        $sumpendiente+=$dt->cantidad*$u->equivalencia;
                    }
                }
            }
            $stock->separado=$sumpendiente;
        }else{
            $stock = new Stock();
            $stock->idbotica=$idbotica;
            $stock->idunidad=$undpri->idunidad;
            $stock->fisico=number_format($fisico,2,'.','');
            $stock->separado=0;
            $stock->bloqueado=0;
            $stock->detalle='';
        }
        $stock->save();
    }


    public function getPrecioCompraEst(){
        $unidades=$this->unidades;
        $preciocompra=0;
        $fechacompra='';
        foreach($unidades as $und){
            $query= new Query();
            $consulta=$query->select(['d.idunidad','i.conigv','i.porcentaje','d.costound','i.f_emision'])
                ->from('detalle_ingreso d')->innerJoin('ingreso i','d.idingreso =i.idingreso')
                ->where(['d.idunidad'=>$und->idunidad,'i.tipo'=>'C','i.estado'=>'F'])->orderBy('i.f_emision DESC')
                ->one();
            if($consulta){
                if($fechacompra!=''){
                    if($consulta['f_emision']>$fechacompra){
                        if($consulta['conigv']){
                            $preciocompra=$consulta['costound']/$und->equivalencia;
                        }else{
                            $preciocompra=($consulta['costound']*(1+$consulta['porcentaje']))/$und->equivalencia;
                        }
                        $fechacompra=$consulta['f_emision'];
                    }
                }else{
                    if($consulta['conigv']){
                        $preciocompra=$consulta['costound']/$und->equivalencia;
                    }else{
                        $preciocompra=($consulta['costound']*(1+$consulta['porcentaje']))/$und->equivalencia;
                    }
                    $fechacompra=$consulta['f_emision'];
                }

            }
        }
        return number_format($preciocompra,4,'.','');
    }

    public function getPrecioVentaEst(){
        $subtotales=0;
        $cantidades=0;
        foreach($this->undventas as $und){
            $query=new Query();
            $consulta =$query->select(['d.idunidad','cantidades'=>'SUM(d.cantidad)','subtotales'=>'sum(d.subtotal)'])->from('detalle_salida d')
                ->innerJoin('salida s','s.idsalida= d.idsalida')->groupBy('d.idunidad')
                ->where(['d.idunidad'=>$und->idunidad,'s.estado'=>'A','s.motivo'=>'V'])->one();
            $subtotales+=$consulta['subtotales'];
            $cantidades+=$consulta['cantidades']*$und->equivalencia;
        }
        if($cantidades>0){
            $precioventa=$subtotales/$cantidades;
        }else{
            $precioventa=$this->undpri->preciosug;
        }
        return number_format($precioventa,4,'.','');
    }

    public function getCboUndVentas(){
        return ArrayHelper::map(
            Unidades::find()->where(['paraventa'=>1,'idproducto'=>$this->idproducto])
                ->asArray()->all(),'idunidad','descripcion'
        );
    }

}
