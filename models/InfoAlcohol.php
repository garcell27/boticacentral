<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\db\Query;

class InfoAlcohol extends Model{


    public function getAlldata(){
        $boticas =Botica::find()->where(['tipo_almacen'=>0])->all();
        $data=[];
        foreach($boticas as $botica){

            $query=(new Query())->select([
                'anio'=>'YEAR(s.create_at)',
                'mes'=>'MONTH(s.create_at)',
                'idunidad'=>'d.idunidad',
                'producto'=>'p.descripcion',
                'unidad'=>'u.descripcion',
                'idcomprobante'=>'c.idcomprobante',
                'idusuario'=>'x.idusuario',
                'usuario'=>'x.namefull',
                'comprobante'=>'c.descripcion',
                'numdocumentos'=>'count(e.iddocumento)',
                'ptotal'=>'sum(d.subtotal)'
            ])->from('detalle_salida d')
                ->innerJoin('salida s','s.idsalida =d.idsalida')
                ->innerJoin('pedidos q','q.idpedido = s.idpedido')
                ->innerJoin('documento_emitido e','q.idpedido =e.idpedido')
                ->innerJoin('comprobante c','e.idcomprobante=c.idcomprobante')
                ->innerJoin('unidades u','u.idunidad =d.idunidad')
                ->innerJoin('productos p','u.idproducto =p.idproducto')
                ->innerJoin('usuarios x','s.create_by=x.idusuario')
                ->groupBy(['idunidad','anio','mes','idcomprobante','idusuario'])
                ->where('p.idcategoria = 7 and s.motivo ="V" and s.idbotica='.$botica->idbotica)
                ->orderBy(['anio'=>SORT_DESC,'mes'=>SORT_DESC,'idunidad'=>SORT_ASC,'idcomprobante'=>SORT_ASC]);
            $consulta=$query->all();
            $agrupaanio=Funciones::groupArray($consulta,'anio');
            $datos=[];
            foreach($agrupaanio as $aganio){
                $agrupmes=Funciones::groupArray($aganio['agrupados'],'mes');
                foreach($agrupmes as $agmes){
                    $lproductos=[];
                    $ndocumentos=0;
                    $ntickets=0;
                    $nboletas=0;
                    $ncomprobantes=0;
                    $agrupaprod=Funciones::groupArray($agmes['agrupados'],'idunidad');
                    foreach($agrupaprod as $agprod){

                        $detalles=[];
                        foreach($agprod['agrupados'] as $detalle){
                            $detalles[]=[
                                'idcomprobante' => $detalle['idcomprobante'],
                                'idusuario' => $detalle['idusuario'],
                                'usuario' => $detalle['usuario'],
                                'comprobante' => $detalle['comprobante'],
                                'numdocumentos' => $detalle['numdocumentos'],
                                'ptotal' => $detalle['ptotal']
                            ];
                        }
                        $agrupacomp=Funciones::groupArray($detalles,'idcomprobante');
                        $compr=[];
                        $nprod=0;
                        $nimprod=0;
                        $nticketsxprod=0;
                        $nboletasxprod=0;
                        foreach($agrupacomp as $agcomp){
                            $ncompro=0;
                            $nimpcompr=0;
                            $detscomp=[];
                            foreach($agcomp['agrupados'] as $detcomp){
                                $detscomp[]=[
                                    'idusuario'=>$detcomp['idusuario'],
                                    'usuario'=>$detcomp['usuario'],
                                    'numdocumentos'=>$detcomp['numdocumentos'],
                                    'ptotal'=>$detcomp['ptotal'],
                                ];
                                $ncompro+=$detcomp['numdocumentos'];
                                $nimpcompr+=$detcomp['ptotal'];
                            }
                            $compr[]=[
                                'idcomprobante'=>$agcomp['idcomprobante'],
                                'comprobante'=>$agcomp['agrupados'][0]['comprobante'],
                                'numdocumentos'=>$ncompro,
                                'ptotal'=>number_format($nimpcompr,2,'.',''),
                                'detalles'=>$detscomp,
                            ];
                            if($agcomp['idcomprobante']==1){
                                $nticketsxprod+=$ncompro;
                            }elseif($agcomp['idcomprobante']==3){
                                $nboletasxprod+=$ncompro;
                            }
                            $nprod+=$ncompro;
                            $nimprod+=$nimpcompr;
                        }
                        $lproductos[]=[
                            'idunidad'=>$agprod['idunidad'],
                            'producto'=>$agprod['agrupados'][0]['producto'],
                            'unidad'=>$agprod['agrupados'][0]['unidad'],
                            'numdocumentos'=>$nprod,
                            'ntickets'=>$nticketsxprod,
                            'nboletas'=>$nboletasxprod,
                            'ptotal'=>number_format($nimprod,2,'.',''),
                            'detalles'=>$compr,
                        ];
                        $ndocumentos+=$nprod;
                        $ncomprobantes+=$nimprod;
                        $ntickets+=$nticketsxprod;
                        $nboletas+=$nboletasxprod;
                    }
                    $datos[]=[
                        'mes'=>$agmes['mes'],
                        'anio'=>$aganio['anio'],
                        'periodo'=>Funciones::nomMes($agmes['mes']).' del '.$aganio['anio'],
                        'numdocumentos'=>$ndocumentos,
                        'ntickets'=>$ntickets,
                        'nboletas'=>$nboletas,
                        'ptotal'=>number_format($ncomprobantes,2,'.',''),
                        'datos'=>$lproductos,
                    ];
                }
            }
            $data[]=[
                'botica'=>[
                    'idbotica'=>$botica->idbotica,
                    'nomrazon'=>$botica->nomrazon,
                ],
                'datos'=>$datos,
            ];
        }

        return $data;
    }

    public function reportegresogrupo($anio,$meses,$idbotica){
        $query=(new Query())->select([
            'fecha'=>'date(s.create_at)',
            'idunidad'=>'d.idunidad',
            'producto'=>'p.descripcion',
            'detalle'=>'p.detalle',
            'unidad'=>'u.descripcion',
            'idcomprobante'=>'c.idcomprobante',
            'ndocumento'=>'e.ndocumento',
            'comprobante'=>'c.descripcion',
            'cliente'=>'q.datoscliente',
            'cantidad'=> 'd.cantidad'

        ])->from('detalle_salida d')
            ->innerJoin('salida s','s.idsalida = d.idsalida')
            ->innerJoin('pedidos q','q.idpedido = s.idpedido')
            ->innerJoin('documento_emitido e','q.idpedido = e.idpedido')
            ->innerJoin('comprobante c','e.idcomprobante = c.idcomprobante')
            ->innerJoin('unidades u','u.idunidad = d.idunidad')
            ->innerJoin('productos p','u.idproducto = p.idproducto')
            ->where([
                'p.idcategoria' => 7 ,
                's.motivo' =>'V',
                's.idbotica'=>$idbotica,
                'c.idcomprobante'=>3,
                'YEAR(s.create_at)'=>$anio
            ])->andWhere('MONTH(s.create_at) BETWEEN '.$meses[0].' AND '.$meses[1])
            ->orderBy(['fecha'=>SORT_ASC]);
        $consulta=$query->all();
        return $consulta;
    }
}