<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Transferencia;

/**
 * TransferenciaSearch represents the model behind the search form about `app\models\Transferencia`.
 */
class TransferenciaSearch extends Transferencia
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idtransferencia', 'idbotorigen', 'idbotdestino', 'origen_conf', 'destino_conf'], 'integer'],
            [['create_at','estado'], 'safe'],
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
        $query = Transferencia::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder'=>['create_at'=>SORT_DESC]],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'idtransferencia' => $this->idtransferencia,
            'idbotorigen' => $this->idbotorigen,
            'idbotdestino' => $this->idbotdestino,
            'origen_conf' => $this->origen_conf,
            'destino_conf' => $this->destino_conf,
            'create_at'=>$this->create_at
        ]);

        $query->andFilterWhere(['like', 'estado', $this->estado]);

        return $dataProvider;
    }
}
