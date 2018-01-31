<?php

namespace app\controllers;

use Yii;
use app\models\Botica;
use app\models\Sincronizado;
use app\models\Clientes;
use app\models\ClientesSearch;
use app\models\LogsTbprincipales;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use yii\helpers\Json;

/**
 * ClientesController implements the CRUD actions for Clientes model.
 */
class ClientesController extends Controller
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
     * Lists all Clientes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Clientes model.
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
     * Creates a new Clientes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($submit = false)
    {
        $model = new Clientes();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $submit == false) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {

                if ($model->save()) {
                    $model->refresh();
                    $transaction->commit();
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'message' => 'ok',
                        'growl'=>[
                            'message' => 'Se ha registrado correctamente un nuevo cliente',
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

    /**
     * Updates an existing Clientes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id,$submit = false)
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
                            'message' => 'Se ha actualizado la información del cliente',
                            'options' => [
                                'type'=>'info',
                            ],
                        ],
                    ];
                } else {
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

    /**
     * Deletes an existing Clientes model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        $log=LogsTbprincipales::find()->where(['tabla'=>'clientes','idclave'=>$id])->one();
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
                    'message' => 'Se ha eliminado el cliente',
                    'options' => [
                        'type'=>'danger',
                    ],
                ],
            ];
        }
    }

    /**
     * Finds the Clientes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Clientes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Clientes::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
