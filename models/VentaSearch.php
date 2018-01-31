<?php
/**
 * Created by PhpStorm.
 * User: cesar
 * Date: 15/03/17
 * Time: 05:21 AM
 */

namespace app\models;
use yii\data\ActiveDataProvider;

class VentaSearch extends Salida
{
    public $fecha_registro;
    public function rules()
    {
        return [
            [['idsalida', 'idpedido', 'idbotica'], 'integer'],
            [['create_by','create_at', 'motivo', 'estado','fecha_registro'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function searchVentas($fecha,$idbotica)
    {
        $query = Salida::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        // grid filtering conditions
        $query->andFilterWhere([
            'idbotica' => $idbotica,
            'motivo'=>'V'
        ]);

        $rangos=Funciones::rangoDia($fecha);
        $query->andFilterWhere(['between','create_at',$rangos['ini'],$rangos['fin']]);


        return $dataProvider;
    }

    public function dataGrafVMxBotica($idbotica,$all=false){
        if($all){
            $qc=Salida::find()->distinct()->select([
                'YEAR(salida.create_at) AS anio',
                'MONTH(salida.create_at) AS mes'
            ])->innerJoin('pedidos', 'salida.idpedido = pedidos.idpedido')
                ->where(['salida.idbotica'=>$idbotica])
                ->groupBy('anio, mes')->orderBy(['anio'=>SORT_DESC,'mes'=>SORT_DESC])
                ->asArray()->all();
        }else{
            $qc=Salida::find()->distinct()->select([
                'YEAR(salida.create_at) AS anio',
                'MONTH(salida.create_at) AS mes'
            ])->innerJoin('pedidos', 'salida.idpedido = pedidos.idpedido')
                ->where(['salida.idbotica'=>$idbotica])
                ->groupBy('anio, mes')->orderBy(['anio'=>SORT_DESC,'mes'=>SORT_DESC])
                ->limit(12)->asArray()->all();
        }

        $qc= array_reverse($qc);
        $total=[];
        $drilldown=[];
        $cat=[];
        foreach ($qc as $c){
            $cat[]=Funciones::nomMes($c['mes']).' DEL '.$c['anio'];
            $qt=Salida::find()->distinct()->select([
                'SUM(total) AS total',
                'YEAR(salida.create_at) AS anio',
                'MONTH(salida.create_at) AS mes'
            ])->innerJoin('pedidos', 'salida.idpedido = pedidos.idpedido')
                ->where([
                    'MONTH(salida.create_at)'=>$c['mes'],
                    'salida.idbotica'=>$idbotica,
                    'YEAR(salida.create_at)'=>$c['anio']
                ])->groupBy('mes, anio')->asArray()->one();
            $qd=Salida::find()->distinct()->select([
                'DAY(salida.create_at) AS dia',
                'SUM(total) AS total',
            ])->innerJoin('pedidos', 'salida.idpedido = pedidos.idpedido')
                ->where([
                    'MONTH(salida.create_at)'=>$c['mes'],
                    'salida.idbotica'=>$idbotica,
                    'YEAR(salida.create_at)'=>$c['anio']
                ])->groupBy('dia')->asArray()->all();
            $total[]=[
                'name'=>Funciones::nomMes($c['mes']).' DEL '.$c['anio'],
                'y'=>(float)$qt['total'],
                'drilldown'=>Funciones::nomMes($c['mes']).' DEL '.$c['anio'],
            ];
            $datadrill=[];
            foreach($qd as $x){
                $datadrill[]=[Funciones::zerofill($x['dia'],2).'-'.Funciones::zerofill($c['mes'],2),(float)$x['total']];
            }
            $drilldown['series'][]=[
                'name'=>  Funciones::nomMes($c['mes']).' DEL '.$c['anio'],
                'id'=>Funciones::nomMes($c['mes']).' DEL '.$c['anio'],
                'data'=>$datadrill,
            ];
        }
        $series[]=[
            'name'=>'Ventas',
            'colorByPoint'=>true,
            'data'=>$total,
        ];
        return [
            'categorias'=>$cat,
            'series'=>$series,
            'drilldown'=>$drilldown,
        ];
    }
    public function dataGrafixVendedor(){
        $qc=Salida::find()->distinct()->select([
            'YEAR(salida.create_at) AS anio',
            'MONTH(salida.create_at) AS mes'
        ])->innerJoin('pedidos', 'salida.idpedido = pedidos.idpedido')
            ->groupBy('anio, mes')->orderBy(['anio'=>SORT_DESC,'mes'=>SORT_DESC])
            ->limit(12)->asArray()->all();
        $qc=array_reverse($qc);
        $qseries=Salida::find()->distinct()->select([
            'salida.create_by',
            'SUM(total) AS total',
        ])->innerJoin('pedidos', 'salida.idpedido = pedidos.idpedido')
            ->groupBy('salida.create_by')->asArray()->all();
        $series=[];
        foreach($qseries as $qs){
            $usuario=Usuarios::findOne($qs['create_by']);
            $total=0;
            $data=[];
            foreach ($qc as $c){
                $qdata=Salida::find()->distinct()->select([
                    'SUM(total) AS total',
                    'salida.create_by',
                ])->innerJoin('pedidos', 'salida.idpedido = pedidos.idpedido')
                    ->where([
                        'MONTH(salida.create_at)'=>$c['mes'],
                        'salida.create_by'=>$qs['create_by'],
                        'YEAR(salida.create_at)'=>$c['anio']
                    ])->groupBy('salida.create_by')->asArray()->one();
                $total+=(float)$qdata['total'];
                $data[]=(float)$qdata['total'];
            }
            if($total>0 && $usuario->idrole>2 && $usuario->status==10){
                $series[]=[
                    'name'=>$usuario->namefull,
                    'data'=>$data
                ];
            }

        }
        $cat=[];
        foreach ($qc as $c){
            $cat[]=Funciones::nomMes($c['mes']).' DEL '.$c['anio'];
        }
        return [
            'categorias'=>$cat,
            'series'=>$series,
        ];
    }

    public function dataVentasxVendedor(){
        $qc=Salida::find()->distinct()->select([
            'YEAR(salida.create_at) AS anio',
            'MONTH(salida.create_at) AS mes'
        ])->innerJoin('pedidos', 'salida.idpedido = pedidos.idpedido')
            ->groupBy('anio, mes')->orderBy(['anio'=>SORT_DESC,'mes'=>SORT_DESC])
            ->limit(12)->asArray()->all();
        $qc=array_reverse($qc);
        $total=[];
        $drilldown=[];
        $cat=[];
        foreach ($qc as $c) {
            $cat[] = Funciones::nomMes($c['mes']) . ' DEL ' . $c['anio'];
            $qt=Salida::find()->distinct()->select([
                'SUM(total) AS total',
                'YEAR(salida.create_at) AS anio',
                'MONTH(salida.create_at) AS mes'
            ])->innerJoin('pedidos', 'salida.idpedido = pedidos.idpedido')

                ->where([
                    'MONTH(salida.create_at)'=>$c['mes'],
                    'YEAR(salida.create_at)'=>$c['anio']
                ])->groupBy('mes, anio')->asArray()->one();
            $qu=Salida::find()->distinct()->select([
                'usuarios.username AS usuario',
                'SUM(total) AS total',
            ])->innerJoin('pedidos', 'salida.idpedido = pedidos.idpedido')
                ->innerJoin('usuarios','usuarios.idusuario = salida.create_by')
                ->where([
                    'MONTH(salida.create_at)'=>$c['mes'],
                    'YEAR(salida.create_at)'=>$c['anio']
                ])->groupBy('usuario')->asArray()->all();

            $total[]=[
                'name'=>Funciones::nomMes($c['mes']).' DEL '.$c['anio'],
                'y'=>(float)$qt['total'],
                'drilldown'=>Funciones::nomMes($c['mes']).' DEL '.$c['anio'],
            ];
            $datadrill=[];
            foreach($qu as $x){
                $datadrill[]=[$x['usuario'],(float)$x['total']];
            }
            $drilldown['series'][]=[
                'name'=>  Funciones::nomMes($c['mes']).' DEL '.$c['anio'],
                'id'=>Funciones::nomMes($c['mes']).' DEL '.$c['anio'],
                'data'=>$datadrill,
            ];
        }

        $series[]=[
            'name'=>'Ventas',
            'colorByPoint'=>true,
            'data'=>$total,
        ];
        return [
            'categorias'=>$cat,
            'series'=>$series,
            'drilldown'=>$drilldown,
        ];
    }


}