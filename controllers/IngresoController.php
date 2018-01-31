<?php

namespace app\controllers;

use app\models\Botica;
use app\models\DetalleIngreso;
use app\models\DetalleTransferencia;
use app\models\Ingreso;
use app\models\IngresoSearch;
use app\models\MovimientoStock;
use app\models\Productos;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
/**
 * IngresoController implements the CRUD actions for Ingreso model.
 */

class IngresoController extends Controller {
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
	 * Lists all Ingreso models.
	 * @return mixed
	 */
	public function actionIndex() {

		$smcompra = new IngresoSearch();
		$sminventario = new IngresoSearch();
		$smcompra->tipo='C';
		$sminventario->tipo='I';
		$dpcompra = $smcompra->search(Yii::$app->request->queryParams);
		$dpcompra->pagination->pageParam = 'compra-page';
		$dpcompra->sort->sortParam = 'compra-sort';

		$dpinventario = $sminventario->search(Yii::$app->request->queryParams);
		$dpinventario->pagination->pageParam = 'inventario-page';
		$dpinventario->sort->sortParam = 'inventario-sort';

		return $this->render('index', [
			//'smcompra'  => $smcompra,
			'dpcompra' => $dpcompra,
			'dpinventario'=>$dpinventario
		]);

	}

	/**
	 * Displays a single Ingreso model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id) {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}


	/**
	 * Creates a new Ingreso model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate($submit = false) {
		$model         = new Ingreso();
		$model->conigv = 1;
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $submit == false) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($model);
		}
		if ($model->load(Yii::$app->request->post())) {
			$model->tipo       = 'C';
			$model->estado     = 'P';
			$model->total      = 0;
			$model->f_registro = date('Y-m-d H:i:s');
			$model->porcentaje = 0.18;
			if ($model->save()) {
				$model->refresh();
				Yii::$app->response->format = Response::FORMAT_JSON;
				return [
					'message'  => 'ok',
					'growl'    => [
						'message' => 'Se ha registrado correctamente un nuevo Ingreso',
						'options' => [
							'type'   => 'success',
						],
					],
					'idingreso' => $model->idingreso,
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

	public function actionCreateinventario($submit = false) {
		$model                = new Ingreso();
		$model->idcomprobante = 2;
		$model->n_comprobante = '000000';
		$model->tipo          = 'I';
		$model->f_registro    = date('Y-m-d');
		$model->f_emision     = date('Y-m-d H:i:s');
		$model->total         = 0;
		$model->conigv        = 0;
		$model->estado        = 'P';
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $submit == false) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($model);
		}
		if ($model->load(Yii::$app->request->post())) {
			$botica             = Botica::findOne($model->idbotica);
			$model->idproveedor = $botica->idinventario;
			if ($model->save()) {
				$model->refresh();
				Yii::$app->response->format = Response::FORMAT_JSON;
				return [
					'message'  => 'ok',
					'growl'    => [
						'message' => 'Ha iniciado la apertura de un inventario',
						'options' => [
							'type'   => 'success',
						],
					],
					'idingreso' => $model->idingreso,
				];
			} else {
				Yii::$app->response->format = Response::FORMAT_JSON;
				return ActiveForm::validate($model);
			}
		} else {
			return $this->renderAjax('_createinventario', [
					'model' => $model,
				]);
		}

	}

	public function actionVerproducto($id, $q = null) {
		$subconsulta = 'SELECT DISTINCT q.idproducto from productos q
                            INNER JOIN unidades v on q.idproducto=v.idproducto
                            INNER JOIN detalle_ingreso d on d.idunidad=v.idunidad
                            WHERE d.idingreso='.$id;
		$query   = new Query;
		$ingreso = Ingreso::findOne($id);
		if ($ingreso->tipo == 'I') {
			$query->select('p.descripcion, p.idproducto, l.nombre , count(u.idunidad) as nund ')
			      ->from('productos p')
			      ->innerJoin('laboratorio l', 'p.idlaboratorio=l.idlaboratorio')
			      ->innerJoin('unidades u', 'u.idproducto=p.idproducto')
			      ->groupBy('p.idproducto')
			      ->where('p.descripcion LIKE "%'.$q.'%" AND p.idproducto NOT IN ('.$subconsulta.')')
			      ->orderBy('p.descripcion');
		} else {
			$subconsulta2 = 'SELECT DISTINCT idlaboratorio from labproveedor
                            WHERE idproveedor='.$ingreso->idproveedor;
			$query->select('p.descripcion, p.idproducto, l.nombre , count(u.idunidad) as nund ')
			      ->from('productos p')
			      ->innerJoin('laboratorio l', 'p.idlaboratorio=l.idlaboratorio')
			      ->innerJoin('unidades u', 'u.idproducto=p.idproducto')
			      ->groupBy('p.idproducto')
			      ->where('p.descripcion LIKE "%'.$q.'%" AND l.idlaboratorio IN('.$subconsulta2.')')
			      ->orderBy('p.descripcion');
		}

		$command = $query->createCommand();
		$data    = $command->queryAll();
		$out     = [];
		foreach ($data as $d) {
			$out[] = [
				'value'       => $d['descripcion'],
				'laboratorio' => $d['nombre'],
				'idproducto'  => $d['idproducto']
			];
		}
		echo Json::encode($out);
	}
	public function actionActabladet($id) {
		$model = Ingreso::findOne($id);
		if ($model->tipo == 'I') {
			return $this->renderAjax('_listadetInventario', [
					'model' => $model,
				]);
		} else {
			return $this->renderAjax('_listadetCompra', [
					'model' => $model,
				]);
		}

	}



	public function actionCorregirimporte(){
		$ingresos=Ingreso::find()->where(['tipo'=>'C'])->all();
		foreach($ingresos as $ingreso){
			$ingreso->recalculaImporte();
		}
	}

	public function actionDemo($id) {
		$ingreso   = Ingreso::findOne($id);
		$productos = Productos::find()->all();
		foreach ($productos as $p) {
			$detalle            = new DetalleIngreso();
			$detalle->idingreso = $ingreso->idingreso;
			$detalle->idunidad  = $p->undpri->idunidad;
			$detalle->cantidad  = 500;
			$detalle->ingresado = 1;
			if ($detalle->save()) {
				$detalle->refresh();
				$movimiento                   = new MovimientoStock();
				$movimiento->idunidad         = $detalle->idunidad;
				$movimiento->fecha            = date('Y-m-d H:i:s');
				$movimiento->tipo_transaccion = 'I';
				$movimiento->cantidad         = $detalle->cantidad;
				$movimiento->idprocedencia    = $detalle->iddetalle;
				$movimiento->detalle          = null;
				$movimiento->idbotica         = $ingreso->idbotica;
				$movimiento->save();
				$p->recalculaStock($movimiento->idbotica);
			}
		}
		echo "terminado";
	}

	/**
	 * Updates an existing Ingreso model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id, $submit = false) {
		$model        = $this->findModel($id);
		$modelIngreso = new DetalleIngreso();
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $submit == false) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($model);
		}
		if ($model->load(Yii::$app->request->post())) {
			if ($model->save()) {
				$model->refresh();
				Yii::$app->response->format = Response::FORMAT_JSON;
				return [
					'message'  => 'ok',
					'growl'    => [
						'message' => 'Se ha actualizado la información del Ingreso : '.$id,
						'options' => [
							'type'   => 'info',
						],
					],
				];
			} else {
				Yii::$app->response->format = Response::FORMAT_JSON;
				return ActiveForm::validate($model);
			}
		} else {
			return $this->renderAjax('update', [
					'model'        => $model,
					'modelIngreso' => $modelIngreso
				]);
		}
	}

	/**
	 * Deletes an existing Ingreso model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id) {
		$this->findModel($id)->delete();
		if (!Yii::$app->request->isAjax) {
			return $this->redirect(['index']);
		} else {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return [
				'message'  => 'ok',
				'growl'    => [
					'message' => 'Se ha eliminado el ingreso',
					'options' => [
						'type'   => 'danger',
					],
				],
			];
		}
	}
	public function actionEliminadetalle($id) {
		$model    = DetalleIngreso::findOne($id);
		$producto = $model->unidad->producto;
		if ($model->ingreso->tipo == 'I' && $model->ingreso->estado == 'P') {
			$movimiento = MovimientoStock::find()->where([
					'tipo_transaccion' => 'I',
					'idprocedencia'    => $model->iddetalle,
				])                        ->one();
			$movimiento->tipo_transaccion = 'X';
            $movimiento->idlocal=null;
			if ($movimiento->save()) {
				$model->delete();
				$producto->recalculaStock($movimiento->idbotica);
				Yii::$app->response->format = Response::FORMAT_JSON;
				return [
					'message'    => 'ok',
					'movimiento' => $movimiento->attributes,
				];
			}
		} else if ($model->ingreso->tipo == 'C' && $model->ingreso->estado == 'P') {
			$ingreso = $model->ingreso;
			$model->delete();
			$ingreso->recalculaImporte();
			Yii::$app->response->format = Response::FORMAT_JSON;
			return [
				'message' => 'ok',
			];
		}

	}

	public function actionUpdatedetalle($id, $submit = false){
		$model=DetalleIngreso::findOne($id);
		if (Yii::$app->request->isAjax && $submit == true && $model->load(Yii::$app->request->post())) {
			$model->subtotal  = number_format($model->cantidad*$model->costound, 2, '.', '');
			if ($model->save()) {
				$model->ingreso->recalculaImporte();
				$movimiento=MovimientoStock::find()->where([
					'idunidad'=>$model->unidad->producto->undpri->idunidad,
					'idbotica'=>$model->ingreso->idbotica,
					'idprocedencia'=>$model->iddetalle,
					'tipo_transaccion'=>'I',
				])->one();
				$movimiento->cantidad=number_format($model->cantidad*$model->unidad->equivalencia, 2, '.', '');
				$movimiento->save();
				$model->unidad->producto->recalculaStock($movimiento->idbotica);
				Yii::$app->response->format = Response::FORMAT_JSON;
				return [
					'message' => 'ok',
				];
			}
		}else{
			return $this->renderAjax('_formCompra', [
				'model'     => $model
			]);
		}
	}

	public function actionCerrarinv($id) {
		$model         = $this->findModel($id);
		$model->estado = 'F';
		if ($model->save()) {
			$this->redirect(['view', 'id' => $id]);
		}
	}

	public function actionConfirmcompra($id) {
		$model = $this->findModel($id);
		if ($model->tipo == 'C' && $model->estado == 'P') {
			$transaccion = Yii::$app->db->beginTransaction();
			try {
				foreach ($model->detalleIngreso as $detalle) {
					$detalle->ingresado = 1;
					$producto           = $detalle->unidad->producto;
					$detalle->save();
					$movimiento                   = new MovimientoStock();
					$movimiento->idunidad         = $producto->undpri->idunidad;
					$movimiento->fecha            = date('Y-m-d H:i:s');
					$movimiento->tipo_transaccion = 'I';
					$movimiento->cantidad         = number_format($detalle->cantidad*$detalle->unidad->equivalencia, 2, '.', '');
					$movimiento->idprocedencia    = $detalle->iddetalle;
					$movimiento->detalle          = null;
					$movimiento->idbotica         = $model->idbotica;
					$movimiento->save();
					$producto->recalculaStock($movimiento->idbotica);

				}
				$model->estado = 'F';
				$model->save();
				$transaccion->commit();
				$this->redirect(['view', 'id' => $id]);
			} catch (\PDOException $e) {
				$transaccion->rollBack();
				throw $e;
			}

		}
	}

	/**
	 * Finds the Ingreso model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Ingreso the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = Ingreso::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
