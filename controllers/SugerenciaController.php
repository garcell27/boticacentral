<?php

namespace app\controllers;

use app\models\Productos;
use Yii;
use app\models\Sugerencia;
use app\models\SugerenciaSearch;
use app\models\LogsTbprincipales;
use app\models\Sincronizado;
use app\models\Botica;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
use yii\web\Response;


/**
 * SugerenciaController implements the CRUD actions for Sugerencia model.
 */
class SugerenciaController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Sugerencia models.
     * @return mixed
     */
    public function actionIndex($idproducto)
    {
        $searchModel = new SugerenciaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->renderAjax('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'producto'=>Productos::findOne($idproducto)
        ]);
    }

    /**
     * Displays a single Sugerencia model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Sugerencia model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($idproducto, $submit = false)
    {
        $model = new Sugerencia();
        $model->idproducto=$idproducto;
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
                    $searchModel = new SugerenciaSearch();
                    $searchModel->idproducto=$idproducto;
                    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'message'        => 'ok',
                        'listas'         => $this->renderAjax('index', [
                            'searchModel'  => $searchModel,
                            'dataProvider' => $dataProvider,
                            'producto'     => Productos::findOne($idproducto)
                        ]),

                    ];
                }
            }catch (\Exception $e){
                $transaction->rollBack();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $e;
            }
        }else{
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Sugerencia model.
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
            $model->sincronizaciones=null;
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->idsugerencia]);
            }

        } else {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Sugerencia model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model=$this->findModel($id);
        $log=LogsTbprincipales::find()->where(['tabla'=>'sugerencia','idclave'=>$id])->one();
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
     * Finds the Sugerencia model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sugerencia the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sugerencia::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
