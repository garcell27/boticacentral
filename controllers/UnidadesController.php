<?php

namespace app\controllers;

use app\models\DetalleIngreso;
use app\models\DetalleSalida;
use app\models\Ingreso;
use app\models\MovimientoStock;
use app\models\Productos;
use app\models\LogsTbprincipales;
use app\models\Botica;
use app\models\Sincronizado;
use app\models\Salida;
use app\models\Unidades;
use app\models\UnidadesSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\Json;

/**
 * UnidadesController implements the CRUD actions for Unidades model.
 */

class UnidadesController extends Controller {
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
	 * Lists all Unidades models.
	 * @return mixed
	 */
	public function actionIndex($idproducto) {
		$searchModel  = new UnidadesSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$producto     = Productos::findOne($idproducto);
		return $this->renderAjax('index', [
				'searchModel'  => $searchModel,
				'dataProvider' => $dataProvider,
				'producto'     => $producto
			]);
	}

	/**
	 * Displays a single Unidades model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id) {
		return $this->render('view', [
				'model' => $this->findModel($id),
			]);
	}

	/**
	 * Creates a new Unidades model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate($idproducto, $submit = false) {

		$model            = new Unidades();
		$model->paraventa = 0;
		$producto         = Productos::findOne($idproducto);
		if (count($producto->unidades)) {
			$model->tipo          = 'S';
			$undpri               = Unidades::find()->where(['tipo' => 'P', 'idproducto' => $idproducto])->one();
			$model->idundprimaria = $undpri->idunidad;
		} else {
			$model->tipo         = 'P';
			$model->equivalencia = 1;
			$undpri              = false;
		}
		$model->idproducto = $idproducto;
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $submit == false) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($model);
		}
		if ($model->load(Yii::$app->request->post())) {
			$model->idproducto  = $idproducto;
			$model->descripcion = mb_strtoupper($model->descripcion, 'utf-8');
			if ($model->tipo == 'S') {
				$datos               = Yii::$app->request->post();
				$model->equivalencia = $datos['Unidades']['numequi']*$datos['Unidades']['undequi'];
			}
			if ($model->paraventa == 0) {
				$model->preciomin = null;
				$model->preciosug = null;
				$model->preciomax = null;
			}
            $transaction = Yii::$app->db->beginTransaction();
            try {
				$model->sincronizaciones=null;
                if ($model->save()) {
                    $model->refresh();

                    $transaction->commit();
                    $searchModel             = new UnidadesSearch();
                    $searchModel->idproducto = $idproducto;
                    $dataProvider            = $searchModel->search(Yii::$app->request->queryParams);

                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'message'        => 'ok',
                        'listas'         => $this->renderAjax('_listaund', [
                                'searchModel'  => $searchModel,
                                'dataProvider' => $dataProvider,
                                'producto'     => $producto
                            ]),
                        'vista' => $this->renderAjax('_confirmacion'),

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
					'model'    => $model,
					'undpri'   => $undpri,
					'producto' => $producto,
				]);
		}
	}

	/**
	 * Updates an existing Unidades model.
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
			if ($model->tipo == 'S') {
				$datos               = Yii::$app->request->post();
				$model->equivalencia = $datos['Unidades']['numequi']*$datos['Unidades']['undequi'];
			}
            $transaction = Yii::$app->db->beginTransaction();
            try {
				$model->sincronizaciones=null;
                if ($model->save()) {
                    $model->refresh();

                    $transaction->commit();
                    $searchModel             = new UnidadesSearch();
                    $searchModel->idproducto = $model->idproducto;
                    $dataProvider            = $searchModel->search(Yii::$app->request->queryParams);

                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'message'        => 'ok',
                        'listas'         => $this->renderAjax('_listaund', [
                                'searchModel'  => $searchModel,
                                'dataProvider' => $dataProvider,
                                'producto'     => $model->producto
                            ]),
                        'vista' => $this->renderAjax('_confirmacion'),

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

	/**
	 * Deletes an existing Unidades model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id) {
		$this->findModel($id)->delete();
		return $this->redirect(['index']);
	}

	public function actionAddinventario($idingreso, $idproducto, $submit = false) {
		$producto    = Productos::findOne($idproducto);
		$ingreso     = Ingreso::findOne($idingreso);
		$verificadet = DetalleIngreso::find()->where(['idingreso' => $idingreso, 'idunidad' => $producto->undpri->idunidad])->count();
		if ($verificadet == 0) {
			if (Yii::$app->request->isAjax && $submit == true) {
				$cantidad = 0;
				foreach ($_POST['ingreso'] as $item) {
					$cantidad += $item['cantidad']*$item['equivalencia'];
				}
				if ($cantidad > 0) {
					$undpri                = Unidades::find()->where(['idproducto' => $idproducto, 'tipo' => 'P'])->one();
					$detalleIng            = new DetalleIngreso();
					$detalleIng->idingreso = $idingreso;
					$detalleIng->idunidad  = $undpri->idunidad;
					$detalleIng->cantidad  = $cantidad;
					$detalleIng->ingresado = 1;
					if ($detalleIng->save()) {
						$detalleIng->refresh();
						$movimiento                   = new MovimientoStock();
						$movimiento->idunidad         = $detalleIng->idunidad;
						$movimiento->fecha            = date('Y-m-d H:i:s');
						$movimiento->tipo_transaccion = 'I';
						$movimiento->cantidad         = $detalleIng->cantidad;
						$movimiento->idprocedencia    = $detalleIng->iddetalle;
						$movimiento->detalle          = null;
						$movimiento->idbotica         = $ingreso->idbotica;
						$movimiento->save();
						$producto->recalculaStock($movimiento->idbotica);
						Yii::$app->response->format = Response::FORMAT_JSON;
						return [
							'message' => 'ok',
						];
					}
				}
			} else {
				return $this->renderAjax('_formInventario', [
						'producto' => $producto,
					]);
			}
		}
	}
	public function actionAddinventarioing($idingreso, $idproducto, $submit = false) {
		$producto    = Productos::findOne($idproducto);
		$ingreso     = Ingreso::findOne($idingreso);
		$verificadet = DetalleIngreso::find()->where(['idingreso' => $idingreso, 'idunidad' => $producto->undpri->idunidad])->count();
		if ($verificadet == 0) {
			if (Yii::$app->request->isAjax && $submit == true) {
				$cantidad = 0;
				foreach ($_POST['ingreso'] as $item) {
					$cantidad += $item['cantidad']*$item['equivalencia'];
				}
				if ($cantidad > 0) {
					$undpri                = Unidades::find()->where(['idproducto' => $idproducto, 'tipo' => 'P'])->one();
					$detalleIng            = new DetalleIngreso();
					$detalleIng->idingreso = $idingreso;
					$detalleIng->idunidad  = $undpri->idunidad;
					$detalleIng->cantidad  = $cantidad;
					$detalleIng->ingresado = 1;
					if ($detalleIng->save()) {
						$detalleIng->refresh();
						$movimiento                   = new MovimientoStock();
						$movimiento->idunidad         = $detalleIng->idunidad;
						$movimiento->fecha            = date('Y-m-d H:i:s');
						$movimiento->tipo_transaccion = 'I';
						$movimiento->cantidad         = $detalleIng->cantidad;
						$movimiento->idprocedencia    = $detalleIng->iddetalle;
						$movimiento->detalle          = null;
						$movimiento->idbotica         = $ingreso->idbotica;
						$movimiento->save();
						$producto->recalculaStock($movimiento->idbotica);
						Yii::$app->response->format = Response::FORMAT_JSON;
						return [
							'message' => 'ok',
						];
					}
				}
			} else {
				return $this->renderAjax('_formInventarioIng', [
						'producto'  => $producto,
						'idingreso' => $idingreso
					]);
			}
		}
	}

	public function actionAddcompra($idingreso, $idproducto, $submit = false) {
		$producto = Productos::findOne($idproducto);
		$ingreso  = Ingreso::findOne($idingreso);
		$model    = new DetalleIngreso();
		if (Yii::$app->request->isAjax && $submit == true && $model->load(Yii::$app->request->post())) {
			$model->idingreso = $idingreso;
			$model->subtotal  = number_format($model->cantidad*$model->costound, 2, '.', '');
			$model->ingresado = 0;
			if ($model->save()) {
				$ingreso->recalculaImporte();
				$model->refresh();
				Yii::$app->response->format = Response::FORMAT_JSON;
				return [
					'message' => 'ok',
				];
			}

		} else {
			return $this->renderAjax('_formCompra', [
					'producto'  => $producto,
					'idingreso' => $idingreso,
					'model'     => $model
				]);
		}

	}

	public function actionAddsalida($idsalida, $idproducto, $submit = false) {
		$producto    = Productos::findOne($idproducto);
		$salida      = Salida::findOne($idsalida);
		$verificadet = DetalleSalida::find()->where(['idsalida' => $idsalida, 'idunidad' => $producto->undpri->idunidad])->count();
		if ($verificadet == 0) {
			if (Yii::$app->request->isAjax && $submit == true) {
				$cantidad = 0;
				foreach ($_POST['salida'] as $item) {
					$cantidad += $item['cantidad']*$item['equivalencia'];
				}
				if ($cantidad > 0) {
					$undpri              = Unidades::find()->where(['idproducto' => $idproducto, 'tipo' => 'P'])->one();
					$detalle             = new DetalleSalida();
					$detalle->idsalida   = $idsalida;
					$detalle->idunidad   = $undpri->idunidad;
					$detalle->cantidad   = $cantidad;
					$detalle->preciounit = 0;
					$detalle->subtotal   = 0;
					if ($detalle->save()) {
						$detalle->refresh();
						$movimiento                   = new MovimientoStock();
						$movimiento->idunidad         = $detalle->idunidad;
						$movimiento->fecha            = date('Y-m-d H:i:s');
						$movimiento->tipo_transaccion = 'E';
						$movimiento->cantidad         = $detalle->cantidad;
						$movimiento->idprocedencia    = $detalle->iddetallesalida;
						$movimiento->detalle          = null;
						$movimiento->idbotica         = $salida->idbotica;
						$movimiento->save();
						$producto->recalculaStock($movimiento->idbotica);
						Yii::$app->response->format = Response::FORMAT_JSON;
						return [
							'message' => 'ok',
						];
					}
				}
			} else {
				return $this->renderAjax('_formSalida', [
						'producto' => $producto,
						'idsalida' => $idsalida
					]);
			}
		}
	}

	/**
	 * Finds the Unidades model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Unidades the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = Unidades::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
