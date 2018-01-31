<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * StockSearch represents the model behind the search form about `app\models\Stock`.
 */
class StockSearch extends Stock
{
    /**
     * @inheritdoc
     */


    public function rules()
    {
        return [
            [['idstock', 'idunidad', 'idbotica'], 'integer'],
            [['fisico', 'separado', 'bloqueado','minimo'], 'number'],
            [['detalle'], 'safe'],
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
        $query = Stock::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'idstock' => $this->idstock,
            'idunidad' => $this->idunidad,
            'idbotica' => $this->idbotica,
            'fisico' => $this->fisico,
            'separado' => $this->separado,
            'bloqueado' => $this->bloqueado,
        ]);

        $query->andFilterWhere(['like', 'detalle', $this->detalle]);

        return $dataProvider;
    }



    public function searchRequerimientos($params){

        $query = Stock::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'idstock' => $this->idstock,
            'idunidad' => $this->idunidad,
            'idbotica' => $this->idbotica,
            'fisico' => $this->fisico,
            'separado' => $this->separado,
            'bloqueado' => $this->bloqueado,
        ]);
        $query->andFilterWhere(['IS NOT','minimo', new Expression('NULL')]);
        $query->andFilterWhere(['>','minimo', new Expression('fisico - bloqueado')]);
        $query->andFilterWhere(['like', 'detalle', $this->detalle]);

        return $dataProvider;
    }

    public function searchDisponible($params)
    {
        $query = Stock::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'idstock' => $this->idstock,
            'idunidad' => $this->idunidad,
            'idbotica' => $this->idbotica,
            'fisico' => $this->fisico,
            'separado' => $this->separado,
            'bloqueado' => $this->bloqueado,
        ]);

        $query->andFilterWhere(['like', 'detalle', $this->detalle]);
        $query->andWhere('fisico - bloqueado > 0');
        return $dataProvider;
    }



}
