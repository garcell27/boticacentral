<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Inventario;

/**
 * InventarioSearch represents the model behind the search form about `app\models\Inventario`.
 */
class InventarioSearch extends Inventario
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idinventario', 'idbotica', 'create_by', 'update_by'], 'integer'],
            [['motivo', 'estado', 'create_at', 'update_at'], 'safe'],
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
        $query = Inventario::find();

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
            'idinventario' => $this->idinventario,
            'idbotica' => $this->idbotica,
            'create_by' => $this->create_by,
            'update_by' => $this->update_by,
            'create_at' => $this->create_at,
            'update_at' => $this->update_at,
        ]);

        $query->andFilterWhere(['like', 'motivo', $this->motivo])
            ->andFilterWhere(['like', 'estado', $this->estado]);

        return $dataProvider;
    }
}
