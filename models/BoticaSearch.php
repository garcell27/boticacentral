<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Botica;

/**
 * BoticaSearch represents the model behind the search form about `app\models\Botica`.
 */
class BoticaSearch extends Botica
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idbotica', 'idclientecaja', 'idinventario', 'idcompped', 'tipo_almacen'], 'integer'],
            [['nomrazon', 'ruc', 'direccion'], 'safe'],
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
        $query = Botica::find();

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
            'idbotica' => $this->idbotica,
            'idclientecaja' => $this->idclientecaja,
            'idinventario' => $this->idinventario,
            'idcompped' => $this->idcompped,
            'tipo_almacen' => $this->tipo_almacen,
        ]);

        $query->andFilterWhere(['like', 'nomrazon', $this->nomrazon])
            ->andFilterWhere(['like', 'ruc', $this->ruc])
            ->andFilterWhere(['like', 'direccion', $this->direccion]);

        return $dataProvider;
    }
}
