<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IngresoSearch represents the model behind the search form about `app\models\Ingreso`.
 */
class IngresoSearch extends Ingreso
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idingreso', 'idproveedor', 'idcomprobante', 'conigv', 'idbotica'], 'integer'],
            [['n_comprobante', 'tipo', 'f_emision', 'f_registro', 'estado'], 'safe'],
            [['total', 'total_igv', 'porcentaje'], 'number'],
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
        $query = Ingreso::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder'=>['f_registro'=>SORT_DESC]],
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
            'idingreso' => $this->idingreso,
            'idproveedor' => $this->idproveedor,
            'idcomprobante' => $this->idcomprobante,
            'f_emision' => $this->f_emision,
            'f_registro' => $this->f_registro,
            'total' => $this->total,
            'conigv' => $this->conigv,
            'total_igv' => $this->total_igv,
            'porcentaje' => $this->porcentaje,
            'idbotica' => $this->idbotica,
        ]);

        $query->andFilterWhere(['like', 'n_comprobante', $this->n_comprobante])
            ->andFilterWhere(['like', 'tipo', $this->tipo])
            ->andFilterWhere(['like', 'estado', $this->estado]);

        return $dataProvider;
    }

    public function getInventarioActivo(){
        $ingresos=Ingreso::find()
            ->andFilterWhere(['tipo'=>'I'])
            ->andFilterWhere(['estado'=>'P'])->all();
        switch (count($ingresos)){
            case 0:
                return [
                    'disponible'=>true,
                    'error'=>false,
                ];
                break;
            case 1:
                return [
                    'disponible'=>false,
                    'error'=>false,
                    'ingreso'=>$ingresos[0]->getAttributes(),
                ];
                break;
            default:
                return [
                    'disponible'=>false,
                    'error'=>true,
                ];
                break;
        }
    }
}
