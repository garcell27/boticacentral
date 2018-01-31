<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "productos".
 *
 * @property integer $idproducto
 * @property integer $idcategoria
 * @property integer $idlaboratorio
 * @property string $descripcion
 * @property string $detalle
 * @property integer $caducidad 
 * @property integer $create_by
 * @property integer $update_by
 *
 * @property Categorias $categoria
 * @property Laboratorio $laboratorio
 * @property Unidades[] $unidades
 * @property Unidades[] $undventas
 * @property Unidades $undpri
 */
class ProductoStock extends Productos
{


    public function verAllDisponible(){
        $disp=0;
        $fisico=0;
        $separado=0;
        $bloqueado=0;
        $stocks=Stock::find()->where(['idunidad'=>$this->undpri->idunidad])->all();
        foreach ($stocks as $s){
            $fisico+=$s->fisico;
            $separado+=$s->separado;
            $bloqueado+=$s->bloqueado;
        }
        $disp=$fisico-$separado-$bloqueado;
        return $disp;
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

    public function creaTexto($cantidad){
        $allund=Unidades::find()->where(['idproducto'=>$this->idproducto])->orderBy(['equivalencia'=>SORT_DESC])->all();
        $info='0';
        if(count($allund) && $cantidad>0){
            $info='';
            $residuo=$cantidad;
            foreach ($allund as $i=>$und){

                $cociente=floor($residuo/$und->equivalencia);
                if($cociente>0){
                    if($info!=''){
                        $info=$info.', ';
                    }
                    $info.=$cociente.' '.$und->descripcion;
                }

                $residuo=$residuo%$und->equivalencia;
            }
        }
        return $info;

    }

    public function dataHistorial(){
        $unidad=$this->undpri;
        $data['boticas']=Botica::find()->where(['tipo_almacen'=>0])->all();
        $query= new Query();
        $qcat=$query->select(['categoria'=>'DATE(m.fecha)'])->distinct()->from('movimiento_stock m')
            ->innerJoin('botica b','b.idbotica= m.idbotica')
            ->where(['m.idunidad'=>$unidad->idunidad,'b.tipo_almacen'=>0])->orderBy('categoria')->all();
        foreach($data['boticas'] as $bot){
            $inicio=0;
            $dthistorial=[];
            foreach($qcat as $cat){
                $ingresos=MovimientoStock::find()->where([
                    'idunidad'=>$unidad->idunidad,'idbotica'=>$bot->idbotica,'tipo_transaccion'=>'I','DATE(fecha)'=>$cat['categoria']
                ])->sum('cantidad');
                $tingresos=MovimientoStock::find()->where([
                    'idunidad'=>$unidad->idunidad,'idbotica'=>$bot->idbotica,'tipo_transaccion'=>'J','DATE(fecha)'=>$cat['categoria']
                ])->sum('cantidad');
                $egresos=MovimientoStock::find()->where([
                    'idunidad'=>$unidad->idunidad,'idbotica'=>$bot->idbotica,'tipo_transaccion'=>'E','DATE(fecha)'=>$cat['categoria']
                ])->sum('cantidad');
                $tegresos=MovimientoStock::find()->where([
                    'idunidad'=>$unidad->idunidad,'idbotica'=>$bot->idbotica,'tipo_transaccion'=>'F','DATE(fecha)'=>$cat['categoria']
                ])->sum('cantidad');
                $stock=$inicio+$ingresos+$tingresos-$egresos-$tegresos;
                $fdate=strtotime($cat['categoria'])*1000;
                $dthistorial[]=[$fdate,$stock];
                $inicio=$stock;
            }
            if(count($dthistorial)){
                $data['grafica'][]=[
                    'name'=>$bot->nomrazon,
                    'step'=>true,
                    'data'=>$dthistorial
                ];
            }
        }

        return $data;
    }

    public function dataDetalleMov($idbotica,$pag=1){
        $unidad=$this->undpri;

        $query= new Query();
        $query->select(['fecha'=>'DATE(m.fecha)'])->distinct()->from('movimiento_stock m')
            ->where(['m.idunidad'=>$unidad->idunidad,'m.idbotica'=>$idbotica])
            ->orderBy('fecha desc')->limit(10);
        if($pag>1){
            $inicia=($pag-1)*10;
            $query->offset($inicia);
        }

        $filas=$query->all();
        $data=$filas;
        foreach($filas as $indice=>$fila){
            //consultar Movimientos
            $qm=new Query();
            $qm->select([
                'm.create_by',
                'u.namefull',
                'm.tipo_transaccion',
                'cantidad'=>'sum(m.cantidad)'
            ])->from('movimiento_stock m')->where(['like','fecha',$fila['fecha']])
                ->innerJoin('usuarios u','m.create_by=u.idusuario')
                ->groupBy(['m.create_by','m.tipo_transaccion'])
                ->andFilterWhere(['idbotica'=>$idbotica])
                ->andFilterWhere(['idunidad'=>$this->undpri->idunidad]);
            $data[$indice]['movimientos']=$qm->all();
        }
        return $data;
    }

    public function verStockBotica($idbotica){
        $stock=Stock::find()->where([
            'idunidad'=>$this->undpri->idunidad,
            'idbotica'=>$idbotica
        ])->one();

        return $stock;
    }

    public function verStockMin(){

    }


    public function getCboUndVentas(){
        return ArrayHelper::map(
            Unidades::find()->where(['paraventa'=>1,'idproducto'=>$this->idproducto])
                ->asArray()->all(),'idunidad','descripcion'
        );
    }

}
