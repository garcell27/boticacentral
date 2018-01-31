<?php

namespace app\controllers;

use app\models\Botica;
use app\models\Sincronizado;
use app\models\Comprobante;
use app\models\ComprobanteSearch;
use app\models\LogsTbprincipales;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\Json;

/**
 * ComprobanteController implements the CRUD actions for Comprobante model.
 */

class ComprobanteController extends Controller {
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@']
					]
				],
			],
			'verbs'    => [
				'class'   => VerbFilter::className(),
				'actions' => [
					'delete' => ['POST'],
				],
			],
		];
	}

	/**
	 * Lists all Comprobante models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel  = new ComprobanteSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
				'searchModel'  => $searchModel,
				'dataProvider' => $dataProvider,
			]);
	}

	/**
	 * Displays a single Comprobante model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id) {
		return $this->render('view', [
				'model' => $this->findModel($id),
			]);
	}

	/**
	 * Creates a new Comprobante model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate($submit = false) {
		$model = new Comprobante();
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $submit == false) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($model);
		}
		if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
			$model->sincronizaciones=null;
            try {
                if ($model->save()) {
                    $model->refresh();
                    $transaction->commit();
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'message'  => 'ok',
                        'growl'    => [
                            'message' => 'Se ha registrado correctamente un nuevo compronbante',
                            'options' => [
                                'type'   => 'success',
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
	 * Updates an existing Comprobante model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id, $submit = false) {
		$model = $this->findModel($id);
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $submit == false) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($model);
		}
		if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
			$model->sincronizaciones=null;
            try {
                if ($model->save()) {
                    $model->refresh();

                    $transaction->commit();
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'message'  => 'ok',
                        'growl'    => [
                            'message' => 'Se ha actualizado la información del Comprobante',
                            'options' => [
                                'type'   => 'info',
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
	 * Deletes an existing Comprobante model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id) {
		$this->findModel($id)->delete();
        $log=LogsTbprincipales::find()->where(['tabla'=>'comprobante','idclave'=>$id])->one();
        $log->estado=0;
        if($log->save()){
            foreach ($log->sincronizados as $sinc){
                $sinc->estado=0;
                $sinc->save();
            }
        }
		if (!Yii::$app->request->isAjax) {
			return $this->redirect(['index']);
		} else {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return [
				'message'  => 'ok',
				'growl'    => [
					'message' => 'Se ha eliminado el comprobante',
					'options' => [
						'type'   => 'danger',
					],
				],
			];
		}
	}

	/**
	 * Finds the Comprobante model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Comprobante the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = Comprobante::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
