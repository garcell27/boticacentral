<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use Yii;
use yii\base\Model;
use app\models\Unidades;

class UnidConsolidadas extends Unidades{
    //put your code here
    
    public $fecha_registro;
    public $total;
    
    public function rules()
    {
        return [
            [['descripcion', 'tipo','fecha_registro','total','importe'], 'safe'],
        ];
    }

    
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Unidades::find()->alias('u')
                ->innerJoin('detalle_salida d', 'u.idunidad= d.idunidad')
                ->innerJoin('salida s', 'd.idsalida= s.idsalida')
                ->select(['u.*','SUM(d.cantidad) as total','SUM(d.subtotal) as subtotal'])
                ->groupBy(['u.idunidad']);       
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return [];
        }
        $query->andFilterWhere(['s.motivo'=>'V','s.estado'=>'A']);
        $query->andFilterWhere(['like','s.fecha_registro',$this->fecha_registro]);
        $result=$query->asArray()->all();
        $valor=[];
        foreach ($result as $i=>$row){
           $und= Unidades::findOne($row['idunidad']); 
           $valor[$i]=$row;
           $valor[$i]['producto']=$und->producto->descripcion;
           $valor[$i]['laboratorio']=$und->producto->laboratorio->nombre;
           $valor[$i]['labelCantidad']=$und->producto->creaLabel($row['total']);
        }
        return $valor;
    }
}
