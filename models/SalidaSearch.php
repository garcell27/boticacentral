<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SalidaSearch represents the model behind the search form about `app\models\Salida`.
 */
class SalidaSearch extends Salida
{
    /**
     * @inheritdoc
     */

    public $meses=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];
    public function rules()
    {
        return [
            [['idsalida', 'idpedido', 'idbotica'], 'integer'],
            [['fecha_registro', 'motivo', 'estado'], 'safe'],
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
        $query = Salida::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>['defaultOrder' => ['fecha_registro' => SORT_DESC,],]            
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'idsalida' => $this->idsalida,
            'idpedido' => $this->idpedido,
            'fecha_registro' => $this->fecha_registro,
            'idbotica' => $this->idbotica,
        ]);

        $query->andFilterWhere(['like', 'motivo', $this->motivo])
            ->andFilterWhere(['like', 'estado', $this->estado]);

        return $dataProvider;
    }

    
    public function searchSinventa($params)
    {
        $query = Salida::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'defaultOrder' => [
                    'fecha_registro' => SORT_DESC,
                ],
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
            'idsalida' => $this->idsalida,
            'idpedido' => $this->idpedido,
            'fecha_registro' => $this->fecha_registro,
            'idbotica' => $this->idbotica,
        ]);

        $query->andFilterWhere(['<>', 'motivo', 'V'])
            ->andFilterWhere(['like', 'estado', $this->estado]);

        return $dataProvider;
    }
    
    
    
    
}
