<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Unidades;

/**
 * UnidadesSearch represents the model behind the search form about `app\models\Unidades`.
 */
class UnidadesSearch extends Unidades
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idunidad', 'idproducto', 'paraventa', 'idundprimaria'], 'integer'],
            [['descripcion', 'tipo'], 'safe'],
            [['equivalencia', 'preciomin', 'preciomax', 'preciosug'], 'number'],
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
        $query = Unidades::find();

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
            'idunidad' => $this->idunidad,
            'idproducto' => $this->idproducto,
            'paraventa' => $this->paraventa,
            'equivalencia' => $this->equivalencia,
            'idundprimaria' => $this->idundprimaria,
            'preciomin' => $this->preciomin,
            'preciomax' => $this->preciomax,
            'preciosug' => $this->preciosug,
        ]);

        $query->andFilterWhere(['like', 'descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'tipo', $this->tipo]);

        return $dataProvider;
    }
}
