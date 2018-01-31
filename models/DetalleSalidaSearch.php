<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DetalleSalida;

/**
 * DetalleSalidaSearch represents the model behind the search form about `app\models\DetalleSalida`.
 */
class DetalleSalidaSearch extends DetalleSalida
{
    /**
     * @inheritdoc
     */
    
    public function rules()
    {
        return [
            [['iddetallesalida', 'idsalida', 'idunidad'], 'integer'],
            [['cantidad', 'preciounit', 'subtotal'], 'number'],
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
        $query = DetalleSalida::find();

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
            'iddetallesalida' => $this->iddetallesalida,
            'idsalida' => $this->idsalida,
            'idunidad' => $this->idunidad,
            'cantidad' => $this->cantidad,
            'preciounit' => $this->preciounit,
            'subtotal' => $this->subtotal,
        ]);

        return $dataProvider;
    }
    

            
    
    
}
