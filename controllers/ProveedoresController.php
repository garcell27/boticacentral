<?php

namespace app\controllers;

use Yii;
use app\models\Proveedores;
use app\models\ProveedoresSearch;
use app\models\Labproveedor;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;

/**
 * ProveedoresController implements the CRUD actions for Proveedores model.
 */
class ProveedoresController extends Controller
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
     * Lists all Proveedores models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProveedoresSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Proveedores model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    
    public function actionDeleteAsiglab($id) {
       $model= Labproveedor::findOne($id);
       $laboratorio=$model->laboratorio;
       if(Yii::$app->request->isAjax){
           $model->delete();
           Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'message' => 'ok',
                'growl'=>[
                    'message' => 'Se ha retirado el laboratorio :'.$laboratorio->nombre,
                    'options' => [
                        'type'=>'danger',
                    ],
                ],
            ];
       }
        
    }

    /**
     * Creates a new Proveedores model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($submit = false)
    {
        $model = new Proveedores();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $submit == false) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->nomproveedor=mb_strtoupper($model->nomproveedor,'utf-8');
            if ($model->save()) {
                $model->refresh();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'message' => 'ok',
                    'growl'=>[
                        'message' => 'Se ha registrado correctamente un nuevo proveedor',
                        'options' => [
                            'type'=>'success',
                        ],
                    ],
                    'proveedor'=>$model->attributes,
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

    public function actionAddlaboratorio($id, $submit = false){
        $proveedor = $this->findModel($id);
        $model= new Labproveedor();
        $model->idproveedor=$proveedor->idproveedor;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $submit == false) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $model->refresh();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'message' => 'ok',
                    'growl'=>[
                        'message' => 'Se ha Asignado un laboratorio',
                        'options' => [
                            'type'=>'success',
                        ],
                    ],
                    'proveedor'=>$model->attributes,
                ];
            } else {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }else {
            return $this->renderAjax('_formlaboratorio', [
                'model' => $model,
            ]);
        }
    }
    
    /**
     * Updates an existing Proveedores model.
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
            $model->nomproveedor=mb_strtoupper($model->nomproveedor,'utf-8');
            if ($model->save()) {
                $model->refresh();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'message' => 'ok',
                    'growl'=>[
                        'message' => 'Se ha actualizado la información del proveedor : '.$id,
                        'options' => [
                            'type'=>'info',
                        ],
                    ],
                    'proveedor'=>$model->attributes
                ];
            } else {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        } else {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Proveedores model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }else{
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'message' => 'ok',
                'growl'=>[
                    'message' => 'Se ha eliminado el proveedor :'.$id,
                    'options' => [
                        'type'=>'danger',
                    ],
                ],
            ];
        }
    }

    /**
     * Finds the Proveedores model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Proveedores the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Proveedores::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
