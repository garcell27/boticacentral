<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductosSearch represents the model behind the search form about `app\models\Productos`.
 */
class CatalogoSearch extends Productos
{
    /**
     * @inheritdoc
     */

    public $idbotica;
    public function rules()
    {
        return [
            [['idproducto', 'idcategoria','idlaboratorio'], 'integer'],
            [['descripcion', 'detalle','idbotica','idcategoria','idlaboratorio'], 'safe'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Productos::find();
        $query->select(['productos.*', 'unds'=>'COUNT(unidades.idunidad)', 
            'fisico'=>'sum(stock.fisico)', 'separado'=>'sum(stock.separado)', 
            'bloqueado'=>'sum(stock.bloqueado)'
        ]);
        $query->innerJoin('unidades','productos.idproducto = unidades.idproducto');
        $query->innerJoin('stock','unidades.idunidad = stock.idunidad');
        // add conditions that should always apply here
        $query->groupBy('productos.idproducto');
        $query->andFilterWhere(['unidades.paraventa' => 1,]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere(['idproducto' => $this->idproducto,]);
        $query->andFilterWhere(['idlaboratorio' => $this->idlaboratorio,]);
        $query->andFilterWhere(['stock.idbotica' => $this->idbotica,]);
        $query->andFilterWhere(['like', 'productos.descripcion', $this->descripcion]);
        return $dataProvider;
    }


}
