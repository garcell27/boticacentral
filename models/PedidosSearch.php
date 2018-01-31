<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pedidos;

/**
 * PedidosSearch represents the model behind the search form about `app\models\Pedidos`.
 */
class PedidosSearch extends Pedidos
{
    /**
     * @inheritdoc
     */

    public $fecha_salida;

    public function rules()
    {
        return [
            [['idpedido', 'idcliente', 'idcomprobante', 'entregado', 'idbotica'], 'integer'],
            [['fecha_registro', 'ndocumento', 'estado','fecha_salida'], 'safe'],
            [['total'], 'number'],
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
        $query = Pedidos::find();

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
            'idpedido' => $this->idpedido,
            'idcliente' => $this->idcliente,
            'fecha_registro' => $this->fecha_registro,
            'idcomprobante' => $this->idcomprobante,
            'total' => $this->total,
            'entregado' => $this->entregado,
            'idbotica' => $this->idbotica,
        ]);

        $query->andFilterWhere(['like', 'ndocumento', $this->ndocumento])
            ->andFilterWhere(['like', 'estado', $this->estado]);

        return $dataProvider;
    }
    public function searchVendidos($params)
    {
        $query = Pedidos::find()->innerJoinWith(['documentosEmitidos','salidas']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'fecha_salida' => SORT_ASC,
                ],
                'attributes'=>[
                    'idpedido',
                    'idcliente',
                    'idcomprobante',
                    'total',
                    'fecha_salida'=>[
                        'asc' => ['salida.fecha_registro' => SORT_ASC],
                        'desc' => ['salida.fecha_registro' => SORT_DESC],
                        'label' => 'Fecha Salida',
                        'default'=>SORT_ASC,
                    ]
                ]
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
            'idpedido' => $this->idpedido,
            'idcliente' => $this->idcliente,
            'idcomprobante' => $this->idcomprobante,
            'total' => $this->total,
            'entregado' => $this->entregado,
            'pedidos.idbotica' => $this->idbotica,
        ]);

        $query->andFilterWhere(['like', 'ndocumento', $this->ndocumento])
            ->andFilterWhere(['like', 'salida.fecha_registro', $this->fecha_salida])
            ->andFilterWhere(['like', 'estado', $this->estado]);

        return $dataProvider;
    }

}
