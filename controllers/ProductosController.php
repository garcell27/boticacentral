<?php

namespace app\controllers;

use app\models\DetalleProducto;
use Yii;
use app\models\Productos;
use app\models\ProductosSearch;
use app\models\LogsTbprincipales;
use app\models\Botica;
use app\models\Sincronizado;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use yii\helpers\Json;
/**
 * ProductosController implements the CRUD actions for Productos model.
 */
class ProductosController extends Controller
{
    /**
     * @inheritdoc
     */
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

    /**
     * Lists all Productos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Productos model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Productos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($submit = false)
    {

        $model = new Productos();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $submit == false) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->sincronizaciones=null;
                if ($model->save()) {
                    $model->refresh();

                    $transaction->commit();
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'message' => 'ok',
                        'growl'=>[
                            'message' => 'Se ha registrado correctamente un nuevo producto',
                            'options' => [
                                'type'=>'success',
                            ],
                        ],
                    ];
                } else {
                    $transaction->commit();
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                }
            }catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $e;
            }
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionActualizastock($id){
        $model=$this->findModel($id);
        return $this->renderAjax('_viewStock', [
            'model'=>$model
        ]);
    }



    /**
     * Updates an existing Productos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $submit = false)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $submit == false) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->sincronizaciones=null;
                if ($model->save()) {
                    $model->refresh();

                    $transaction->commit();
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'message' => 'ok',
                        'growl'=>[
                            'message' => 'Se ha actualizado correctamente el producto : '.$model->idproducto,
                            'options' => [
                                'type'=>'success',
                            ],
                        ],
                    ];
                } else {
                    $transaction->commit();
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                }
            }catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $e;
            }
        } else {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionRegDetalleProducto($id, $submit = false){
        $model = $this->findModel($id);
        $detalle= new DetalleProducto();

        if ($detalle->load(Yii::$app->request->post()) && $submit=true) {
            $model->detalle=$detalle->concentrar();
            if ($model->save()) {
                $model->refresh();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'message' => 'ok',
                    'growl'=>[
                        'message' => 'Se ha actualizado correctamente los detalles del producto : '.$model->idproducto,
                        'options' => [
                            'type'=>'success',
                        ],
                    ],
                ];
            }
        }else{
            $detalle->procesar($model->detalle);
            return $this->renderAjax('_formdetalle', [
                'model' => $model,
                'detalle'=>$detalle
            ]);
        }

    }



    public function actionRecalcular($id){
        $boticas= Botica::find()->all();
        $producto=$this->findModel($id);
        foreach ($boticas as $b){
            $producto->recalculaStock($b->idbotica);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'message' => 'ok',
            'growl'=>[
                'message' => 'Se ha recalculado todos los stock del producto ',
                'options' => [
                    'type'=>'success',
                ],
            ],
        ];
    }

    /**
     * Deletes an existing Productos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model=$this->findModel($id);
        if(count($model->unidades)){
            foreach ($model->unidades as $und){
                if($und->tipo!='P'){
                    $logund=LogsTbprincipales::find()->where([
                        'tabla'=>'unidades','idclave'=>$und->idunidad
                    ])->one();
                    $und->delete();
                    $logund->estado=0;
                    $logund->save();
                    foreach ($logund->sincronizados as $sincronizado) {
                        $sincronizado->estado=0;
                        $sincronizado->save();
                    }
                }            
            }
            $logundpri=LogsTbprincipales::find()->where([
                'tabla'=>'unidades','idclave'=>$model->undpri->idunidad
            ])->one();
            $model->undpri->delete();
            $logundpri->estado=0;
            $logundpri->save();
            foreach ($logundpri->sincronizados as $sincronizado) {
                $sincronizado->estado=0;
                $sincronizado->save();
            }
        }
        $model->delete();
        $log=LogsTbprincipales::find()->where(['tabla'=>'productos','idclave'=>$id])->one();
        $log->estado=0;
        if($log->save()){
            foreach ($log->sincronizados as $sinc){
                $sinc->estado=0;
                $sinc->save();
            }
        }
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }else{
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'message' => 'ok',
                'growl'=>[
                    'message' => 'Se ha eliminado el producto : '.$id,
                    'options' => [
                        'type'=>'danger',
                    ],
                ],
            ];
        }
    }

    /**
     * Finds the Productos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Productos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Productos::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
