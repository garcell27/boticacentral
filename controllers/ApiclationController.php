<?php

namespace app\controllers;

use app\models\Botica;
use app\models\Categorias;
use app\models\Clientes;
use app\models\Comprobante;
use app\models\DetalleSalida;
use app\models\DocumentoEmitido;
use app\models\Laboratorio;
use app\models\MovimientoGenerado;
use app\models\MovimientosSubidos;
use app\models\MovimientoStock;
use app\models\Pedidos;
use app\models\Productos;
use app\models\Roles;
use app\models\Salida;
use app\models\Sugerencia;
use app\models\Transferencia;
use app\models\Unidades;
use app\models\Usuarios;
use kartik\form\ActiveForm;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;



class ApiclationController extends Controller{

    private $user=null;
    private $botica=null;
    public function beforeAction($action){
        $header=Yii::$app->request->getHeaders();
        if (!parent::beforeAction($action)) {
            return false;
        }
        elseif($header->get('iduser')==null || $header->get('authkey')==null || $header->get('idbotica')==null){
            return false;
        }else{
            $user=Usuarios::findOne($header->get('iduser'));
            if($user){
                if($user->validateAuthKey($header->get('authkey'))){
                    $this->user=$user;
                    $this->botica=Botica::findOne($header->get('idbotica'));
                }else{
                    return false;
                }

            }
            return parent::beforeAction($action);
        }
    }
    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    // restrict access to
                    'Origin' => ['*'],

                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Allow-Origin' => ['*'],

                    'Access-Control-Request-Headers' => ['iduser','idbotica','authkey'],
                    // Allow only headers 'X-Wsse'
                    'Access-Control-Allow-Credentials' => true,

                    // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                    'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
                ],
            ],
        ];
    }

    public function actionIndex(){


        $datos=$this->sincronizaTablas();
        Yii::$app->response->format=Response::FORMAT_JSON;
        return $datos;

    }


    public function actionMostrarBoticas(){

        $datospost=Yii::$app->request->post();
        $info=[];
        if($datospost){
            $info=Botica::find()->where(['tipo_almacen'=>0])->all();
        }
        Yii::$app->response->format=Response::FORMAT_JSON;
        return $info;
    }



    public function sincronizaTablas(){
        $tablas=[
            [
                'tabla'=>'roles',
                'datos'=>Roles::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->botica->idbotica])->all(),
                'icon'=>'fa fa-users',
            ],
            [
                'tabla'=>'usuarios',
                'datos'=>Usuarios::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->botica->idbotica])->all(),
                'icon'=>'fa fa-user',
            ],
            [
                'tabla'=>'clientes',
                'datos'=>Clientes::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->botica->idbotica])->all(),
                'icon'=>'fa fa-user-o',
            ],
            [
                'tabla'=>'comprobantes',
                'datos'=>Comprobante::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->botica->idbotica])->all(),
                'icon'=>'fa fa-book',
            ],
            [
                'tabla'=>'categorias',
                'datos'=>Categorias::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->botica->idbotica])->all(),
                'icon'=>'fa fa-th',
            ],
            [
                'tabla'=>'laboratorios',
                'datos'=>Laboratorio::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->botica->idbotica])->all(),
                'icon'=>'fa fa-flask',
            ],
            [
                'tabla'=>'productos',
                'datos'=>Productos::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->botica->idbotica])->all(),
                'icon'=>'fa fa-cubes',
            ],
            [
                'tabla'=>'unidades',
                'datos'=>Unidades::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->botica->idbotica])->all(),
                'icon'=>'fa fa-cube',
            ],
            [
                'tabla'=>'sugerencias',
                'datos'=>Sugerencia::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->botica->idbotica])->all(),
                'icon'=>'fa fa-external-link',
            ],
            [
                'tabla'=>'movimientos',
                'datos'=>MovimientoStock::find()
                    ->where('idbotica='.$this->botica->idbotica.' AND idlocal is null')->all(),
                'icon'=>'fa fa-retweet',
            ],
            [
                'tabla'=>'transfxrecibir',
                'datos'=>Transferencia::find()->where([
                    'destino_conf'=>0,'estado'=>'E','idbotdestino'=>$this->botica->idbotica
                ])->all(),
                'icon'=>'fa fa-level-down',
            ],
            [
                'tabla'=>'transfxfinalizar',
                'datos'=>Transferencia::find()->where([
                    'origen_conf'=>0,'estado'=>'F','idbotorigen'=>$this->botica->idbotica
                ])->all(),
                'icon'=>'fa fa-level-up',
            ],
        ];
        $datos=[];
        foreach($tablas as $registro){
            if(count($registro['datos'])>0){
                $datos[]=$registro;
            }
        }
        return $datos;
    }



    public function actionTbsincronizado(){
        $datospost=Yii::$app->request->post();
        if($datospost){
            $tabla=$datospost['tabla'];
            $idtabla=$datospost['idtabla'];
            switch ($tabla){
                case "roles":
                    $row= Roles::findOne($idtabla);
                    break;
                case "usuarios":
                    $row= Usuarios::findOne($idtabla);
                    break;
                case "clientes":
                    $row= Clientes::findOne($idtabla);
                    break;
                case "comprobantes":
                    $row= Comprobante::findOne($idtabla);
                    break;
                case "categorias":
                    $row= Categorias::findOne($idtabla);
                    break;
                case "laboratorios":
                    $row= Laboratorio::findOne($idtabla);
                    break;
                case "productos":
                    $row= Productos::findOne($idtabla);
                    break;
                case "unidades":
                    $row= Unidades::findOne($idtabla);
                    break;
                case "sugerencias":
                    $row= Sugerencia::findOne($idtabla);
                    break;
            }

            $sincronizacion=[];
            if($row->sincronizaciones!=null){
                $sincronizacion=Json::decode($row->sincronizaciones);
            }
            $sincronizacion[]=['botica'=>$this->botica->idbotica,'fecha'=>date('Y-m-d h:i:s')];
            $row->sincronizaciones=Json::encode($sincronizacion);
            if($row->save()){
                Yii::$app->response->format=Response::FORMAT_JSON;
                return [
                    'save'=>true
                ];
            }
        }
    }

    public function actionSubirmovimiento(){
        $datospost=Yii::$app->request->post();
        if($datospost){
            $movinfo=Json::decode(urldecode($datospost['datos']));
            $movimiento=MovimientosSubidos::find()->where([
                'idlocal'=>$movinfo['idmovimiento_stock'],'idbotica'=>$this->botica->idbotica
            ])->one();
            if(!$movimiento){
                $movimiento= new MovimientosSubidos();
                $movimiento->idunidad=$movinfo['idunidad'];
                $movimiento->cantidad=$movinfo['cantidad'];
                $movimiento->tipo_transaccion=$movinfo['tipo_transaccion'];
                $movimiento->fecha=$movinfo['fecha'];
                $movimiento->idbotica=$movinfo['idbotica'];
                $movimiento->idlocal=$movinfo['idmovimiento_stock'];
                $movimiento->create_by=$movinfo['create_by'];
                $movimiento->update_by=$this->user->idusuario;
            }else{
                $movimiento->update_by=$this->user->idusuario;
                $movimiento->idunidad=$movinfo['idunidad'];
                $movimiento->cantidad=$movinfo['cantidad'];
                $movimiento->tipo_transaccion=$movinfo['tipo_transaccion'];
                $movimiento->fechamod=date('Y-m-d H:i:s');
            }
            Yii::$app->response->format=Response::FORMAT_JSON;
            if($movimiento->save()){
                $movimiento->refresh();
                $movimiento->unidad->producto->recalculaStock($movimiento->idbotica);
                return [
                    'save'=>true,
                    'idcentral'=>$movimiento->idmovimiento_stock
                ];
            }else{
                return [
                    'save'=>false,
                ];
            }
        }
    }
    public function actionSubirPedido(){
        $datospost=Yii::$app->request->post();
        if($datospost) {
            $pedinfo = Json::decode(urldecode($datospost['datos']));
            $detalles = urldecode($datospost['items']);
            $pedido = Pedidos::find()->where([
                'idlocal' => $pedinfo['idpedido'], 'idbotica' => $this->botica->idbotica
            ])->one();
            if (!$pedido) {
                $pedido = new Pedidos();
                $pedido->idcliente = $pedinfo['idcliente'];
                $pedido->datoscliente = $pedinfo['datoscliente'];
                $pedido->fecha_registro = $pedinfo['fecha_registro'];
                $pedido->idcomprobante=$pedinfo['idcomprobante'];
                $pedido->ndocumento = $pedinfo['ndocumento'];
                $pedido->total = $pedinfo['total'];
                $pedido->pago = $pedinfo['pago'];
                $pedido->estado = $pedinfo['estado'];
                $pedido->entregado = $pedinfo['entregado'];
                $pedido->idbotica = $pedinfo['idbotica'];
                $pedido->idlocal = $pedinfo['idpedido'];
            } else {
                $pedido->fecha_registro = $pedinfo['fecha_registro'];
                $pedido->ndocumento = $pedinfo['ndocumento'];
                $pedido->total = $pedinfo['total'];
                $pedido->pago = $pedinfo['pago'];
                $pedido->estado = $pedinfo['estado'];
                $pedido->entregado = $pedinfo['entregado'];
            }
            $pedido->detalles = $detalles;
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($pedido->save()) {
                $pedido->refresh();
                return [
                    'save' => true,
                    'idcentral' => $pedido->idpedido
                ];
            } else {
                return [
                    'save' => false,
                    'errorvalid'=>ActiveForm::validate($pedido)
                ];
            }
        }
    }
    public function actionSubirSalida(){
        $datospost=Yii::$app->request->post();
        if($datospost) {
            $salinfo = Json::decode(urldecode($datospost['datos']));
            $detalles = Json::decode(urldecode($datospost['items']));
            $salida = Salida::find()->where([
                'idlocal' => $salinfo['idsalida'], 'idbotica' => $this->botica->idbotica
            ])->one();
            if($salinfo['motivo']=='V'){
                $pedido=Pedidos::find()->where([
                    'idlocal'=>$salinfo['idpedido'],
                    'idbotica'=>$this->botica->idbotica
                ])->one();
                $idpedido=$pedido->idpedido;
            }else{
                $idpedido=null;
            }
            if (!$salida) {
                $salida = new Salida();
            }
            $salida->attributes=[
                'idpedido'=>$idpedido,
                'idpedlocal'=>$salinfo['idpedido'],
                'motivo'=>$salinfo['motivo'],
                'estado'=>$salinfo['estado'],
                'idbotica'=>$salinfo['idbotica'],
                'idlocal'=>$salinfo['idsalida'],
                'sincroniza'=>$salinfo['sincroniza'],
                'create_by'=>$salinfo['create_by'],
                'update_by'=>$salinfo['update_by'],
                'create_at'=>$salinfo['create_at'],
                'update_at'=>$salinfo['update_at'],
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($salida->save()){
                $salida->refresh();
                if(count($salida->detalleSalida)>0){
                    DetalleSalida::deleteAll('idsalida ='.$salida->idsalida);
                }
                foreach ($detalles as $it){
                    $item = new DetalleSalida();
                    $item->attributes=[
                        'idsalida'=>$salida->idsalida,
                        'idunidad'=>$it['idunidad'],
                        'cantidad'=>$it['cantidad'],
                        'preciounit'=>$it['preciounit'],
                        'subtotal'=>$it['subtotal'],
                        'idlocal'=>$it['iddetallesalida']
                    ];
                    $item->save();
                }
                return [
                    'save' => true,
                    'idcentral' => $salida->idsalida
                ];
            }else{
                return [
                    'save' => false,
                    'errorvalid'=>ActiveForm::validate($salida)
                ];
            }
        }
    }
    public function actionSubirDocumento(){
        $datospost=Yii::$app->request->post();
        if($datospost) {
            $docinfo = Json::decode(urldecode($datospost['datos']));
            $documento = DocumentoEmitido::find()->where([
                'idlocal' => $docinfo['iddocumento'], 'idbotica' => $this->botica->idbotica
            ])->one();
            if (!$documento) {
                $documento = new DocumentoEmitido();
            }
            $pedido=Pedidos::find()->where([
                'idlocal'=>$docinfo['idpedido'],
                'idbotica'=>$this->botica->idbotica
            ])->one();
            $idpedido=$pedido->idpedido;
            $documento->attributes=[
                'idpedido'=>$idpedido,
                'idcomprobante'=>$docinfo['idcomprobante'],
                'ndocumento'=>$docinfo['ndocumento'],
                'total'=>$docinfo['total'],
                'conigv'=>$docinfo['conigv'],
                'total_igv'=>$docinfo['total_igv'],
                'idbotica'=>$this->botica->idbotica,
                'estado'=>$docinfo['estado'],
                'porcentaje'=>$docinfo['porcentaje'],
                'idlocal'=>$docinfo['iddocumento'],
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($documento->save()){
                $documento->refresh();
                return [
                    'save' => true,
                    'idcentral' => $documento->iddocumento
                ];
            }else{
                return [
                    'save' => false,
                    'errorvalid'=>ActiveForm::validate($documento)
                ];
            }
        }
    }
    public function actionMovimientoSincronizado(){
        $datospost=Yii::$app->request->post();
        $info=[];
        if($datospost){
            $row=MovimientoGenerado::findOne($datospost['idcentral']);
            $row->idlocal=$datospost['idlocal'];
            $row->fechamod=date('Y-m-d h:i:s');
            $row->save();
            $info=[
                'save'=>true,
            ];
        }
        Yii::$app->response->format=Response::FORMAT_JSON;
        return $info;
    }

}

?>