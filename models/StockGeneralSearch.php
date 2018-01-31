<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductosSearch represents the model behind the search form about `app\models\Productos`.
 */
class StockGeneralSearch extends Productos
{
    /**
     * @inheritdoc
     */

    public $laboratorio;

    public function rules()
    {
        return [
            [['idproducto', 'idcategoria', 'idlaboratorio', 'caducidad'], 'integer'],
            [['descripcion', 'detalle','laboratorio'], 'safe'],
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
        $query = ProductoStock::find();
        $query->innerJoin('unidades','productos.idproducto = unidades.idproducto');
        $query->innerJoin('laboratorio','productos.idlaboratorio = laboratorio.idlaboratorio');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'defaultOrder'=>[
                    'descripcion'=>SORT_ASC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'idproducto' => $this->idproducto,
            'idcategoria' => $this->idcategoria,
            'productos.idlaboratorio' => $this->idlaboratorio,
            'caducidad' => $this->caducidad,
        ]);

        $query->andFilterWhere(['like', 'productos.descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'detalle', $this->detalle]);

        return $dataProvider;
    }


}
