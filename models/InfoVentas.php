<?php
/**
 * Created by PhpStorm.
 * User: cesar
 * Date: 08/04/17
 * Time: 04:26 PM
 */

namespace app\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class InfoVentas extends Model
{

    public function principal($fecha,$idbotica){
        $query = new Query();
        $query->select([
            'cantidad'=>'count(s.idsalida)',
            'importe'=>'sum(p.total)'
        ])->from('salida s')
            ->innerJoin('pedidos p', 's.idpedido=p.idpedido');
        $query->andFilterWhere([
            's.idbotica' => $idbotica,
            's.motivo'=>'V'
        ]);
        $rangos=Funciones::rangoDia($fecha);
        $query->andFilterWhere(['between','s.create_at',$rangos['ini'],$rangos['fin']]);
        return $query->one();
    }
    public function dtpartdiaria($fecha,$idbotica){
        $query = new Query();
        $query->select([
                's.create_by',
                'u.namefull',
                'total'=>'count(s.idsalida)',
                'importe'=>'sum(p.total)'
            ])->from('salida s')
            ->innerJoin('usuarios u','s.create_by = u.idusuario')
            ->innerJoin('pedidos p', 's.idpedido=p.idpedido')
            ->groupBy('s.create_by');
        $query->andFilterWhere([
            's.idbotica' => $idbotica,
            's.motivo'=>'V'
        ]);

        $rangos=Funciones::rangoDia($fecha);
        $query->andFilterWhere(['between','create_at',$rangos['ini'],$rangos['fin']]);
        return $query->all();
    }

    public function consolidadosxVendedor($fecha,$idbotica){
        $query = new Query();
        $query->select([
            's.create_by',
            'u.namefull',
        ])->from('salida s')
            ->innerJoin('usuarios u','s.create_by = u.idusuario')
            ->groupBy('s.create_by');
        $query->andFilterWhere([
            's.idbotica' => $idbotica,
            's.motivo'=>'V'
        ]);
        $rangos=Funciones::rangoDia($fecha);
        $query->andFilterWhere(['between','create_at',$rangos['ini'],$rangos['fin']]);
        $filas=$query->all();
        $consolidado=[];
        foreach($filas as $indice=>$fila){
            $q= new Query();
            $q->select([
                'd.idunidad',
                'x.descripcion',
                'laboratorio'=>'l.nombre',
                'cantidad'=>'sum(d.cantidad)',
                'precio'=>'sum(d.subtotal)'
            ])->from('detalle_salida d')->innerJoin('salida s','d.idsalida =s.idsalida')
                ->innerJoin('pedidos p','p.idpedido = s.idpedido')->innerJoin('unidades u','u.idunidad=d.idunidad')
                ->innerJoin('productos x', 'x.idproducto=u.idproducto')->innerJoin('laboratorio l','l.idlaboratorio=x.idlaboratorio')
                ->groupBy('d.idunidad');
            $q->andFilterWhere([
                's.idbotica' => $idbotica,
                's.motivo'=>'V',
                's.create_by'=>$fila['create_by']
            ]);
            $rangos=Funciones::rangoDia($fecha);
            $q->andFilterWhere(['between','s.create_at',$rangos['ini'],$rangos['fin']]);
            $q->orderBy('x.descripcion');
            $consolidado[]=[
                'idvendedor'=>$fila['create_by'],
                'nomvendedor'=>$fila['namefull'],
                'data'=>$q->all()
            ];
        }
        return $consolidado;
    }

    public function searchRanking($fecha=''){
        $query= new Query();
        $query->select([
            'idunidad'=>'d.idunidad',
            'unidad'=>'u.descripcion',
            'producto'=>'p.descripcion',
            'laboratorio'=>'l.nombre',
            'npedidos'=>'count(d.iddetallesalida)',
            'tcantidad'=>'sum(cantidad)',
            'timporte'=>'sum(subtotal)'
        ])->from('detalle_salida d')->innerJoin('salida s','s.idsalida=d.idsalida')
            ->innerJoin('unidades u','u.idunidad=d.idunidad')->innerJoin('productos p','p.idproducto=u.idproducto')
            ->innerJoin('laboratorio l','l.idlaboratorio= p.idlaboratorio')
            ->groupBy('d.idunidad')->orderBy('npedidos DESC, timporte DESC, tcantidad')
        ->andFilterWhere(['s.motivo'=>'V']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }
}