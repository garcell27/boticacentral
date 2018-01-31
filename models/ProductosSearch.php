<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * ProductosSearch represents the model behind the search form about `app\models\Productos`.
 */
class ProductosSearch extends Productos
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idproducto', 'idcategoria', 'idlaboratorio', 'caducidad'], 'integer'],
            [['descripcion', 'detalle'], 'safe'],
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
        $query = Productos::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'idproducto' => $this->idproducto,
            'idcategoria' => $this->idcategoria,
            'idlaboratorio' => $this->idlaboratorio,
            'caducidad' => $this->caducidad,
        ]);

        $query->andFilterWhere(['like', 'descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'detalle', $this->detalle]);

        return $dataProvider;
    }

    public function searchCatalogo($params){
        $query = Productos::find();
        $query->innerJoin('unidades','productos.idproducto = unidades.idproducto');

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
            'idproducto' => $this->idproducto,
            'idcategoria' => $this->idcategoria,
            'idlaboratorio' => $this->idlaboratorio,
            'caducidad' => $this->caducidad,
        ]);

        $query->andFilterWhere(['like', 'descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'detalle', $this->detalle]);

        return $dataProvider;
    }


    public function getAllCboProductos(){
        $productos=Productos::find()->select('productos.*')
            ->innerJoin('unidades','productos.idproducto = unidades.idproducto')
            ->innerJoin('stock','stock.idunidad = unidades.idunidad')
            ->groupBy('productos.idproducto')->asArray()->all();

        return ArrayHelper::map($productos,'idproducto','descripcion');
    }


    public function searchConUtilidad($params){
        $query = Productos::find()->distinct();
        $query->innerJoin('unidades','unidades.idproducto = productos.idproducto')
            ->innerJoin('detalle_ingreso','detalle_ingreso.idunidad=unidades.idunidad')
            ->innerJoin('ingreso','ingreso.idingreso=detalle_ingreso.idingreso')
            ->andFilterWhere(['ingreso.tipo'=>'C','ingreso.estado'=>'F']);
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'defaultOrder'=>[
                    'descripcion'=>SORT_ASC
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
            'idproducto' => $this->idproducto,
            'idlaboratorio' => $this->idlaboratorio,
        ]);

        $query->andFilterWhere(['like', 'productos.descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'detalle', $this->detalle]);

        return $dataProvider;
    }

/*
    public function searchUltimosMovSemana($params){
        $this->load($params);
        if($this->idproducto==null){
            return [];
        }else{
            $producto=ProductoStock::findOne($this->idproducto);

            $boticas=Botica::find()->where(['tipo_almacen'=>0])->all();
            foreach($boticas as $indice=>$botica){
                $stock=Stock::find()->where([
                    'idunidad'=>$producto->undpri->idunidad,
                    'idbotica'=>$botica->idbotica
                ])->one();
                if($stock){
                    $cantidad=$stock->fisico;
                }else{
                    $cantidad=0;
                }
                $fecha=date('Y-m-d');
                $datos[$indice]=[
                    'botica'=>$botica->nomrazon,
                    'stock'=>number_format($cantidad,2,'.',''),
                    'unidades'=>$producto->undpri->descripcion,
                ];
                for($i=0;$i<7;$i++){
                    $diferenciar=strtotime('-'.$i.' day',strtotime($fecha));
                    $nfecha=date('Y-m-d',$diferenciar);
                    $label=date('d/m',$diferenciar);
                    $query=new Query();
                    $consultar= $query->select([
                        'm.create_by',
                        'u.namefull',
                        'm.tipo_transaccion',
                        'cantidad'=>'sum(m.cantidad)'
                    ])->from('movimiento_stock m')->where(['like','fecha',$nfecha])
                        ->innerJoin('usuarios u','m.create_by=u.idusuario')
                        ->groupBy(['m.create_by','m.tipo_transaccion'])
                        ->andFilterWhere(['idbotica'=>$botica->idbotica])
                        ->andFilterWhere(['idunidad'=>$producto->undpri->idunidad])->all();
                    $datos[$indice]['date'][$label]=$consultar;
                }
            }
            return $datos;
        }
    }

*/
}
