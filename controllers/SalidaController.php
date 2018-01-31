<?php


namespace app\controllers;
use app\models\Botica;
use app\models\Pedidos;
use app\models\SalidaSearch;
use app\models\Salida;
use app\models\DetalleSalida;
use app\models\MovimientoStock;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\db\Query;
use yii\helpers\Json;
use yii\filters\AccessControl;

class SalidaController extends \yii\web\Controller{
    
    public function behaviors()
    {
        return [
            'access'=>[
                'class'=> AccessControl::className(),
                'rules'=>[
                    [
                        'allow'=>true,
                        'roles'=>['@']
                    ]                    
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    public function actionIndex()
    {
        $botica=Botica::find()->one();
        if($botica){
            $searchModel = new SalidaSearch();
            $searchModel->idbotica=$botica->idbotica;            
            $dataProvider = $searchModel->searchSinventa(Yii::$app->request->queryParams);

            
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }else{
            return $this->render('../botica/nobotica');
        }
    }
    
    public function actionCreate($submit = false){
        $botica=Botica::find()->one();
        $model=  new Salida();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $submit == false) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            //return $this->redirect(['view', 'id' => $model->idcliente]);
            $model->fecha_registro=date('Y-m-d h:i:s');
            $model->idbotica=$botica->idbotica;
            $model->estado='A';
            $model->idusuario=  Yii::$app->user->identity->idusuario;
            if ($model->save()) {
                $model->refresh();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'message' => 'ok',
                    'growl'=>[
                        'message' => 'Se ha registrado correctamente una salida',
                        'options' => [
                            'type'=>'success',
                        ],
                    ],
                ];
            } else {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionVerproducto($id,$q=null){
            $query = new Query;    
            $query->select('p.descripcion, p.idproducto, l.nombre')
                ->from('productos p')
                ->innerJoin('laboratorio l', 'p.idlaboratorio=l.idlaboratorio')
                ->where('p.descripcion LIKE "%' . $q .'%"')
                ->orderBy('p.descripcion');
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out = [];
            foreach ($data as $d) {
                $out[] = [
                    'value' => $d['descripcion'],
                    'laboratorio' => $d['nombre'],
                    'idproducto'=>$d['idproducto']
                ];
            }
            echo Json::encode($out);
    }
    public function actionActabladet($id){
        $model=  Salida::findOne($id);
        return $this->renderAjax('_listadetSalida', [
            'model' => $model,
        ]);
    }

    public function actionEnlaces(){
        $errores=0;
        $salidas=Salida::find()->where(['motivo'=>'V'])->all();
        foreach($salidas as $salida){
            $pedido=Pedidos::find()->where([
                'idlocal'=>$salida->idpedlocal,
                'idbotica'=>$salida->idbotica
            ])->one();
            if($pedido->idpedido!=$salida->idpedido){
                $errores++;
                $salida->idpedido=$pedido->idpedido;
                $salida->save();
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'salidas'=>count($salidas),
            'errores'=>$errores,
        ];
    }
    
    public function actionEliminadetalle($id){
        $model= DetalleSalida::findOne($id);
        $producto=$model->unidad->producto;
        
        $movimiento= MovimientoStock::find()->where([
            'tipo_transaccion'=>'E',
            'idprocedencia'=>$model->iddetallesalida,
        ])->one();
        $movimiento->tipo_transaccion='Y';
        if($movimiento->save()){
            $model->delete();
            $producto->recalculaStock($movimiento->idbotica);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'message' => 'ok',
                'movimiento'=>$movimiento->attributes,
            ];
        }
        
        
    }
}
