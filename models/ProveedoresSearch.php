<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Proveedores;

/**
 * ProveedoresSearch represents the model behind the search form about `app\models\Proveedores`.
 */
class ProveedoresSearch extends Proveedores
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idproveedor'], 'integer'],
            [['nomproveedor', 'direccion', 'tipodoc', 'docidentidad'], 'safe'],
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
        $query = Proveedores::find();

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
            'idproveedor' => $this->idproveedor,
            'inventario' =>0
        ]);

        $query->andFilterWhere(['like', 'nomproveedor', $this->nomproveedor])
            ->andFilterWhere(['like', 'direccion', $this->direccion])
            ->andFilterWhere(['like', 'tipodoc', $this->tipodoc])
            ->andFilterWhere(['like', 'docidentidad', $this->docidentidad]);

        return $dataProvider;
    }
}
