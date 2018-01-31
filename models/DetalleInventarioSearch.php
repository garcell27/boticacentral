<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DetalleInventario;

/**
 * DetalleInventarioSearch represents the model behind the search form about `app\models\DetalleInventario`.
 */
class DetalleInventarioSearch extends DetalleInventario
{
    /**
     * @inheritdoc
     */
    public $codproducto;
    public $producto;
    public function rules()
    {
        return [
            [['iddetalle_inventario', 'idinventario', 'idunidad', 'estado'], 'integer'],
            [['cantestimada', 'cantinventariada', 'cantvendida'], 'number'],
            [['observaciones', 'accion', 'codproducto', 'producto'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'iddetalle_inventario' => 'ID',
            'idinventario' => 'ID INVENTARIO',
            'idunidad' => 'UNIDAD',

            'cantestimada' => 'CANT. ESTIMADA',
            'cantinventariada' => 'CANT. INV.',
            'cantvendida' => 'CANT. VENDIDA',
            'observaciones' => 'OBSERVACIONES',
            'estado' => 'ESTADO',
            'accion' => 'ACCION',
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
        $query = DetalleInventario::find()
            ->innerJoin('unidades','detalle_inventario.idunidad = unidades.idunidad')
            ->innerJoin('productos','productos.idproducto = unidades.idproducto');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'codproducto' => SORT_ASC,
                ],
                'attributes'=>[
                    'cantestimada',
                    'cantinventariada',
                    'cantvendida',
                    'codproducto'=>[
                        'asc' => ['productos.idproducto' => SORT_ASC],
                        'desc' => ['productos.idproducto' => SORT_DESC],
                        'label' => 'CODIGO',
                        'default'=>SORT_ASC,
                    ],
                    'producto'=>[
                        'asc' => ['productos.descripcion' => SORT_ASC],
                        'desc' => ['productos.descripcion' => SORT_DESC],
                        'label' => 'PRODUCTO',
                        'default'=>SORT_ASC,
                    ]
                ]
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
            'iddetalle_inventario' => $this->iddetalle_inventario,
            'idinventario' => $this->idinventario,
            'idunidad' => $this->idunidad,
            'cantestimada' => $this->cantestimada,
            'cantinventariada' => $this->cantinventariada,
            'cantvendida' => $this->cantvendida,
            'estado' => $this->estado,
            'productos.idproducto' =>$this->codproducto,
        ]);

        $query->andFilterWhere(['like', 'observaciones', $this->observaciones])
            ->andFilterWhere(['like', 'accion', $this->accion])
            ->andFilterWhere(['like', 'productos.descripcion', $this->producto]);

        return $dataProvider;
    }


}
