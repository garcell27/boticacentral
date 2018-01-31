<?php

namespace app\controllers;

use app\models\Botica;
use app\models\DetalleTransferencia;

use app\models\Transferencia;
use app\models\TransferenciaSearch;
use app\models\Productos;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\Json;

/**
 * TransferenciaController implements the CRUD actions for Transferencia model.
 */

class TransferenciaController extends Controller {
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
	 * Lists all Transferencia models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel  = new TransferenciaSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
				'searchModel'  => $searchModel,
				'dataProvider' => $dataProvider,
			]);
	}

	/**
	 * Displays a single Transferencia model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id) {
		return $this->render('view', [
				'model' => $this->findModel($id),
			]);
	}

	/**
	 * Creates a new Transferencia model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate($submit = false) {
		$model = new Transferencia();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $submit == false) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
		if ($model->load(Yii::$app->request->post())) {
		    $model->estado='P';
            if ($model->save()) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'message' => 'ok',
                    'growl'=>[
                        'message' => 'Se ha registrado una nueva transferencia, registre los productos que va transferir',
                        'options' => [
                            'type'=>'success',
                        ],
                    ],
                ];
            }else{
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
		} else {
			return $this->renderAjax('create', [
					'model' => $model,
				]);
		}
	}
	public function actionVerproducto($id, $q = null){
        $subconsulta = 'SELECT DISTINCT q.idproducto from productos q
                            INNER JOIN unidades v on q.idproducto=v.idproducto
                            INNER JOIN detalle_transferencia d on d.idunidad=v.idunidad
                            WHERE d.idtransferencia='.$id;
        $query   = new Query();
        $transferencia = Transferencia::findOne($id);
        $query->select('p.descripcion, p.idproducto, l.nombre , sum(s.fisico-s.separado) as stock')
            ->from('productos p')
            ->innerJoin('laboratorio l', 'p.idlaboratorio=l.idlaboratorio')
            ->innerJoin('unidades u', 'u.idproducto=p.idproducto')
            ->innerJoin('stock s', 'u.idunidad=s.idunidad')
            ->groupBy('p.idproducto')
            ->where('p.descripcion LIKE "%'.$q.'%" AND p.idproducto NOT IN ('.$subconsulta.') AND s.idbotica='.$transferencia->idbotorigen)
            ->orderBy('p.descripcion');
        $command = $query->createCommand();
        $data    = $command->queryAll();
        $out     = [];
        foreach ($data as $d) {
            $out[] = [
                'value'       => $d['descripcion'],
                'laboratorio' => $d['nombre'],
                'stock'=>$d['stock'],
                'idproducto'  => $d['idproducto']
            ];
        }
        echo Json::encode($out);
    }

	public function actionAddItem($idtransferencia, $idproducto, $submit = false){
        $producto=Productos::findOne($idproducto);
        $transferencia=Transferencia::findOne($idtransferencia);
        $busqueda=false;
        foreach ($transferencia->items as $item){
            if($item->unidad->producto->idproducto==$producto->idproducto){
                $detalle=$item;
                $busqueda=true;
                $accion='actualiza';
            }
        }
        if(!$busqueda){
            $detalle=new DetalleTransferencia();
            $detalle->idtransferencia=$transferencia->idtransferencia;
            $detalle->cantidad=1;
            $accion='nuevo';
        }
        if (Yii::$app->request->isAjax && $detalle->load(Yii::$app->request->post()) && $submit == false) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($detalle);
        }
        if ($detalle->load(Yii::$app->request->post())) {
            if ($detalle->save()) {
                $detalle->refresh();
                $detalle->unidad->producto->recalculaStock($transferencia->idbotorigen);
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'message' => 'ok',
                    'growl'=>[
                        'message' => $accion=='nuevo'?'Se ha agregado un item a la transferencia':'Se ha actualizado el item',
                        'options' => ['type'=> $accion=='nuevo'?'success':'info',],
                    ],
                ];
            }else{
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($detalle);
            }
        }else{
            return $this->renderAjax('additem', [
                'producto' => $producto,
                'model'=>$detalle,
                'botica'=>$transferencia->botorigen
            ]);
        }
    }

	/**
	 * Updates an existing Transferencia model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	public function actionUpdate($id,$submit = false) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->idtransferencia]);
		} else {
			return $this->render('update', [
					'model' => $model,
				]);
		}
	}

	/**
	 * Deletes an existing Transferencia model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	public function actionDelete($id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the Transferencia model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Transferencia the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
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
                    'message' => 'Se ha eliminado el registro',
                    'options' => [
                        'type'   => 'danger',
                    ],
                ],
            ];
        }
    }
	protected function findModel($id) {
		if (($model = Transferencia::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

    public function actionEliminadetalle($id) {
        $model    = DetalleTransferencia::findOne($id);
        $producto=$model->unidad->producto;
        $idbotica=$model->transferencia->idbotorigen;
        if ($model->transferencia->estado == 'P') {
            $model->delete();
            $producto->recalculaStock($idbotica);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'message'    => 'ok',
            ];
        }

    }

    public function actionEnviar($id){
        $model=Transferencia::findOne($id);
        $model->estado='E';
        $model->save();
        /*$model->refresh();

        $log=new LogsMovimientos();
        $log->idbotica=$model->idbotdestino;
        $data=$model->getAttributes();

        $data['botorigen']=$model->botorigen->nomrazon;
        $data['botdestino']=$model->botdestino->nomrazon;

        $data_detalle=[];
        foreach ($model->items as $item){
            $data_detalle[]=$item->getAttributes();
        }
        $log->tabla='transferencia';
        $log->data=Json::encode($data);
        $log->data_detalle=Json::encode($data_detalle);
        $log->sincronizado=0;
        $log->save();*/
        $this->redirect(['view','id'=>$id]);
    }

	public function actionCbOrigen(){
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $idorigen = $parents[0];
                $boticas= Botica::find()->where(['<>','idbotica',$idorigen])->all();
                foreach ($boticas as $botica){
                    $out[]=[
                        'id'=>$botica->idbotica,
                        'name'=>$botica->nomrazon
                    ];
                }
                // the getSubCatList function will query the database based on the
                // cat_id and return an array like below:
                // [
                //    ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
                //    ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
                // ]
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>$out,'selected'=>'']);
    }
}
