<?php

namespace app\controllers;

use app\models\DetalleIngreso;
use app\models\DetalleInventario;
use app\models\DetalleInventarioSearch;
use app\models\DetalleSalida;
use app\models\MovimientoStock;
use app\models\Ingreso;
use app\models\Productos;
use app\models\Salida;
use app\models\Stock;
use kartik\mpdf\Pdf;
use Yii;
use app\models\Inventario;
use app\models\InventarioSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\db\Query;
use yii\helpers\Json;

/**
 * InventarioController implements the CRUD actions for Inventario model.
 */
class InventarioController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
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
     * Lists all Inventario models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InventarioSearch();
        $searchModel->estado='A';
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionArchivados()
    {
        $searchModel = new InventarioSearch();
        $searchModel->estado='C';
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('archivados', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }



    /**
     * Displays a single Inventario model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model=$this->findModel($id);
        $stocks=Stock::find()->where(['idbotica'=>$model->idbotica])->count();
        $smdetalles = new DetalleInventarioSearch();
        $smdetalles->idinventario=$model->idinventario;
        $dpdetalles = $smdetalles->search(Yii::$app->request->queryParams);
        //$vacios=0;
        //$data    = $this->consultaStocks($id);
        //foreach ($data as $d) {
        //    $disponible=$d['fisico']-$d['separado']-$d['bloqueado'];
        //    if($disponible==0){
        //        $vacios++;
        //    }
        //}
        return $this->render('view', [
            'model' => $model,
            'stocks'=>$stocks,
            'smdetalles'=>$smdetalles,
            'dpdetalles'=>$dpdetalles,
        ]);
    }

    public function actionVerproducto($id, $q = null) {
        $data    = $this->consultaStocks($id,$q);
        $out     = [];
        foreach ($data as $d) {
            $disponible=$d['fisico']-$d['separado']-$d['bloqueado'];
            //if($disponible>0){
                $out[] = [
                    'value'       => $d['descripcion'],
                    'laboratorio' => $d['nombre'],
                    'idproducto'  => $d['idproducto'],
                    'disponible' =>  number_format($disponible,2)
                ];
            //}

        }
        echo Json::encode($out);
    }

    public function actionVaciar($id){
        $model=$this->findModel($id);
        $data    = $this->consultaStocks($id);
        foreach ($data as $d) {
            $disponible=$d['fisico']-$d['separado']-$d['bloqueado'];
            if($disponible==0){
                $producto=Productos::findOne($d['idproducto']);
                $detalle= new DetalleInventario();
                $detalle->idinventario=$id;
                $detalle->idunidad=$producto->undpri->idunidad;
                $detalle->cantestimada=0;
                $detalle->cantinventariada=0;
                $detalle->cantvendida=0;
                $detalle->estado=0;
                $detalle->save();
            }
        }
        return $this->redirect(['view', 'id' => $model->idinventario]);
    }



    public function actionReporteDetalle($id,$tipo){
        $model=$this->findModel($id);
        $detalles=[];
        foreach($model->detalleInventarios as $item){
            switch($tipo){
                case 'conforme':
                    $titulo='LISTA DE PRODUCTOS INVENTARIADOS CONFORMES';
                    if($item->cantestimada==($item->cantinventariada+$item->cantvendida)){
                        $detalles[]=$item;
                    }
                    break;
                case 'excedente':
                    $titulo='LISTA DE PRODUCTOS INVENTARIADOS EXCEDENTES';
                    if($item->cantestimada<($item->cantinventariada+$item->cantvendida)){
                        $detalles[]=$item;
                    }
                    break;
                case 'faltante':
                    $titulo='LISTA DE PRODUCTOS INVENTARIADOS FALTANTES';
                    if($item->cantestimada>($item->cantinventariada+$item->cantvendida)){
                        $detalles[]=$item;
                    }
                    break;
            }


        }
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'content' => $this->renderPartial('reporte',[
                'model'=>$model,
                'tipo'=>$tipo,
                'titulo'=>$titulo,
                'detalles'=>$detalles
            ]),
            'options' => [
                'title' => 'Reporte - Botica :'.$model->botica->nomrazon,
                'subject' => 'Sistema de Reporte de Botica'
            ],
            'methods' => [
                'SetHeader' => ['Reporte de Inventario - Botica: '.$model->botica->nomrazon.'||Generado :' . date("d/m/Y h:i ")],
                'SetFooter' => ['|Página {PAGENO}|'],
            ]
        ]);
        return $pdf->render();
    }

    public function consultaStocks($idinventario,$q=null){
        $subconsulta = 'SELECT DISTINCT q.idproducto from productos q
                            INNER JOIN unidades v on q.idproducto=v.idproducto
                            INNER JOIN detalle_inventario d on d.idunidad=v.idunidad
                            WHERE d.idinventario='.$idinventario;
        $query   = new Query;
        $inventario = Inventario::findOne($idinventario);
        $query->select('p.descripcion, p.idproducto, l.nombre , s.fisico, s.separado, s.bloqueado')
            ->from('productos p')
            ->innerJoin('laboratorio l', 'p.idlaboratorio=l.idlaboratorio')
            ->innerJoin('unidades u', 'u.idproducto=p.idproducto')
            ->innerJoin('stock s','u.idunidad = s.idunidad')
            ->groupBy('p.idproducto, s.fisico, s.separado, s.bloqueado')
            ->where('(p.descripcion LIKE "%'.$q.'%" OR p.idproducto="'.$q.'") AND p.idproducto NOT IN ('.$subconsulta.') AND s.idbotica ='.$inventario->idbotica)
            ->orderBy('p.descripcion, s.fisico desc');


        $command = $query->createCommand();
        return $command->queryAll();
    }
    public function actionAgregadetalle($idinventario,$idproducto,$submit = false){
        $producto    = Productos::findOne($idproducto);
        $inventario =Inventario::findOne($idinventario);
        $verificadet=DetalleInventario::find()->where(['idinventario' => $idinventario, 'idunidad' => $producto->undpri->idunidad])->count();
        if ($verificadet == 0) {
            $model=new DetalleInventario();
            $model->idinventario=$idinventario;
            $model->idunidad=$producto->undpri->idunidad;
            if (Yii::$app->request->isAjax && $submit == true &&$model->load(Yii::$app->request->post())) {
                $cantinventariada = 0;
                $cantvendida=0;
                for($i=0;$i<count($_POST['cantinventariada']);$i++) {
                    $cantinventariada += $_POST['cantinventariada'][$i]['cantidad']*$_POST['cantinventariada'][$i]['equivalencia'];
                    $cantvendida+= $_POST['cantvendida'][$i]['cantidad']*$_POST['cantvendida'][$i]['equivalencia'];
                }
                $model->cantinventariada=$cantinventariada;
                $model->cantvendida=$cantvendida;
                $model->estado=0;
                if ($model->save()) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'message' => 'ok',
                    ];
                }
            }else{
                return $this->renderAjax('adddetalle', [
                    'producto'  => $producto,
                    'inventario' => $inventario,
                    'model'=>$model
                ]);
            }
        }
    }


    /**
     * Creates a new Inventario model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($submit = false)
    {
        $model = new Inventario();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $submit == false) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->estado='A';
            if ($model->save()) {
                $model->refresh();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'message'  => 'ok',
                    'growl'    => [
                        'message' => 'Se ha registrado un nuevo Inventario Activo',
                        'options' => [
                            'type'   => 'success',
                        ],
                    ],
                    'idinventario' => $model->idinventario,
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

    /**
     * Updates an existing Inventario model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->idinventario]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Inventario model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeletedetalle($id){
        $model    = DetalleInventario::findOne($id);
        $idinventario=$model->idinventario;
        $model->delete();
        return $this->redirect(['view', 'id' => $idinventario]);
    }

    public function actionRegularizaingreso($id){
        $inventario=Inventario::findOne($id);

        $detalles=[];
        foreach($inventario->detalleInventarios as $item){
            if($item->cantestimada<($item->cantinventariada+$item->cantvendida) && $item->accion==null){
                $detalles[]=$item;
            }
        }
        if(count($detalles)>0){
            $ingreso= new Ingreso();
            $ingreso->idproveedor=1;
            $ingreso->idbotica=$inventario->idbotica;
            $ingreso->idcomprobante = 2;
            $ingreso->n_comprobante = '000000';
            $ingreso->tipo          = 'I';
            $ingreso->f_registro    = date('Y-m-d');
            $ingreso->f_emision     = date('Y-m-d H:i:s');
            $ingreso->total         = 0;
            $ingreso->conigv        = 0;
            $ingreso->estado        = 'F';
            if($ingreso->save()){
                $ingreso->refresh();
                foreach($detalles as $detalle){
                    $detalleIng  = new DetalleIngreso();
                    $detalleIng->idingreso = $ingreso->idingreso;
                    $detalleIng->idunidad  = $detalle->idunidad;
                    $detalleIng->cantidad  = number_format($detalle->cantinventariada+$detalle->cantvendida-$detalle->cantestimada,2,'.','');
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
                        $detalle->accion='I';
                        $detalle->save();
                        $detalle->unidad->producto->recalculaStock($movimiento->idbotica);
                    }
                }
            }
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionRegularizasalida($id){
        $inventario=Inventario::findOne($id);
        $detalles=[];
        foreach($inventario->detalleInventarios as $item){
            if($item->cantestimada>($item->cantinventariada+$item->cantvendida) && $item->accion==null){
                $detalles[]=$item;
            }
        }
        $salida= new Salida();
        $salida->motivo='R';
        $salida->idbotica=$inventario->idbotica;
        $salida->estado='A';
        $salida->sincroniza=0;
        $salida->create_by=$inventario->create_by;
        $salida->update_by=$inventario->update_by;
        $salida->create_at=date('Y-m-d H:i:s');
        $salida->update_at=date('Y-m-d H:i:s');
        if($salida->save()){
            $salida->refresh();
            foreach($detalles as $detalle){
                $diferencia=$detalle->cantestimada-$detalle->cantinventariada-$detalle->cantvendida;
                $detalleSal  = new DetalleSalida();
                $detalleSal->idsalida = $salida->idsalida;
                $detalleSal->idunidad  = $detalle->idunidad;
                $detalleSal->cantidad  = number_format($diferencia,2,'.','');
                $detalleSal->preciounit = $detalle->unidad->preciosug;
                $detalleSal->subtotal=number_format($diferencia*$detalle->unidad->preciosug,2,'.','');
                if ($detalleSal->save()) {
                    $detalleSal->refresh();
                    $movimiento                   = new MovimientoStock();
                    $movimiento->idunidad         = $detalleSal->idunidad;
                    $movimiento->fecha            = date('Y-m-d H:i:s');
                    $movimiento->tipo_transaccion = 'E';
                    $movimiento->cantidad         = $detalleSal->cantidad;
                    $movimiento->idprocedencia    = $detalleSal->iddetallesalida;
                    $movimiento->detalle          = null;
                    $movimiento->idbotica         = $salida->idbotica;
                    $movimiento->save();
                    $detalle->accion='E';
                    $detalle->save();
                    $detalle->unidad->producto->recalculaStock($movimiento->idbotica);
                }
            }
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    public function  actionArchivar($id){
        $inventario=$this->findModel($id);
        foreach($inventario->detalleInventarios as $item){
            if($item->accion==null && $item->cantestimada==($item->cantinventariada+$item->cantvendida)){
                $item->accion='X';
                $item->save();
            }
        }
        $inventario->estado='C';
        $inventario->save();
        return $this->redirect(['view', 'id' => $id]);
    }
    /**
     * Finds the Inventario model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Inventario the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Inventario::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
