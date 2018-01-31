<?php
/**
 * Created by PhpStorm.
 * User: cesar
 * Date: 21/02/17
 * Time: 06:57 PM
 */

namespace app\controllers;

use app\models\Botica;
use app\models\Laboratorio;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\Response;

class ReportesjsonController extends Controller
{
    public function actionIndex(){}

    public function actionStockReport(){
        $laboratorios=Laboratorio::find()->innerJoin('productos','productos.idlaboratorio=laboratorio.idlaboratorio')
            ->innerJoin('unidades','productos.idproducto=unidades.idproducto')
            ->innerJoin('stock','stock.idunidad=unidades.idunidad')
            ->groupBy('idlaboratorio')->orderBy('nombre')->all();
        $datos=[];
        foreach ($laboratorios as $ilab=>$lab){
            $productos=[];
            foreach ($lab->productos as $p){
                $infoalmacen=$p->verStock(1);
                $infoyanet=$p->verStock(2);
                $infosalud=$p->verStock(3);
                if($infoalmacen['existe']){
                    $stockalmacen= $infoalmacen['disponible'];
                }else{
                    $stockalmacen= 0;
                }
                if($infoyanet['existe']){
                    $stockyanet = $infoyanet['disponible'];
                }else{
                    $stockyanet = 0;
                }
                if($infosalud['existe']){
                    $stocksalud = $infosalud['disponible'];
                }else{
                    $stocksalud = 0;
                }
                $stocktotal=$stockalmacen+$stockyanet+$stocksalud;
                $productos[]=[
                    'descripcion'=>$p->descripcion,
                    'undpri'=>$p->undpri->descripcion,
                    'almacencentral'=>$stockalmacen,
                    'yanet'=>$stockyanet,
                    'salud'=>$stocksalud,
                    'total'=>$stocktotal
                ];
            }
            $datos[]=[
                'laboratorio'=>$lab->nombre,
                'productos'=>$productos
            ];

        }
        Yii::$app->response->format=Response::FORMAT_JSON;
        return $datos;
    }
    public function actionConsolidadoReport($anio,$mes){
        $query= new Query();
        $rows=$query->select([
            'laboratorio'=>'l.nombre',
            'producto' =>'p.descripcion',
            'idunidad'=>'u.idunidad',
            'unidad'=>'u.descripcion',
        ])->from('movimiento_stock m')
            ->innerJoin('unidades u','u.idunidad = m.idunidad')
            ->innerJoin('productos p','p.idproducto = u.idproducto')
            ->innerJoin('laboratorio l','l.idlaboratorio = p.idlaboratorio')
            ->groupBy(['m.idunidad'])
            ->where([
                'YEAR(m.fecha)'=>$anio,
                'MONTH(m.fecha)'=>$mes,
                'm.tipo_transaccion'=>'E'
            ])->orderBy(['l.nombre'=>SORT_ASC,'p.descripcion'=>SORT_ASC])->all();
        $datos=[];
        $i=0;
        foreach ($rows as $indice=>$r){
            // consultar yanet
            $consultay=$query->select([
                    'cantidades'=>'SUM(m.cantidad)',
                ])->from('movimiento_stock m')
                    ->groupBy(['m.idunidad'])
                    ->where([
                        'YEAR(m.fecha)'=>$anio,
                        'MONTH(m.fecha)'=>$mes,
                        'm.tipo_transaccion'=>'E',
                        'm.idunidad'=>$r['idunidad'],
                        'm.idbotica'=>2
                    ])->one();
            // consultar Salud
            $consultas=$query->select([
                'cantidades'=>'SUM(m.cantidad)',
            ])->from('movimiento_stock m')
                ->groupBy(['m.idunidad'])
                ->where([
                    'YEAR(m.fecha)'=>$anio,
                    'MONTH(m.fecha)'=>$mes,
                    'm.tipo_transaccion'=>'E',
                    'm.idunidad'=>$r['idunidad'],
                    'm.idbotica'=>3
                ])->one();

            if(!$consultay){
                $consultay['cantidades']=0;
            }
            if(!$consultas){
                $consultas['cantidades']=0;
            }
            $total=$consultay['cantidades']+$consultas['cantidades'];
            if($indice==0){
                $datos[$i]=[
                    'laboratorio'=>$r['laboratorio'],
                    'productos'=>[
                        [
                            'descripcion'=>$r['producto'],
                            'unidad'=>$r['unidad'],
                            'yanet'=>number_format($consultay['cantidades'],2,'.',''),
                            'salud'=>number_format($consultas['cantidades'],2,'.',''),
                            'total'=>number_format($total,2,'.','')
                        ]
                    ]
                ];
            }else{
                if($datos[$i]['laboratorio']==$r['laboratorio']){
                    $datos[$i]['productos'][]=[
                        'descripcion'=>$r['producto'],
                        'unidad'=>$r['unidad'],
                        'yanet'=>number_format($consultay['cantidades'],2,'.',''),
                        'salud'=>number_format($consultas['cantidades'],2,'.',''),
                        'total'=>number_format($total,2,'.','')
                    ];
                }else{
                    $i++;
                    $datos[$i]=[
                        'laboratorio'=>$r['laboratorio'],
                        'productos'=>[
                            [
                                'descripcion'=>$r['producto'],
                                'unidad'=>$r['unidad'],
                                'yanet'=>number_format($consultay['cantidades'],2,'.',''),
                                'salud'=>number_format($consultas['cantidades'],2,'.',''),
                                'total'=>number_format($total,2,'.','')
                            ]
                        ]
                    ];
                }
            }
        }
        Yii::$app->response->format=Response::FORMAT_JSON;
        return $datos;
    }

    public function actionMesesVentas(){
        $query= new Query();
        $rowanio=$query->select(['anio'=>'YEAR(fecha)'])->from('movimiento_stock')
            ->groupBy(['YEAR(fecha)'])->orderBy(['anio'=>SORT_DESC])->all();
        $datos=[];
        foreach ($rowanio as $a){
            $rowmeses=$query->select(['mes'=>'MONTH(fecha)'])->from('movimiento_stock')
                ->groupBy(['MONTH(fecha)'])->where(['YEAR(fecha)'=>$a['anio']])->orderBy(['mes'=>SORT_DESC])->all();
            $meses=[];
            foreach ($rowmeses as $m){
                $meses[]=$m['mes'];
            }
            $datos[]=[
                'anio'=>$a['anio'],
                'meses'=>$meses
            ];
        }
       Yii::$app->response->format=Response::FORMAT_JSON;
        return $datos;

    }




}