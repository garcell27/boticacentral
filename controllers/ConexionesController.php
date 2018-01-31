<?php

namespace app\controllers;
use app\models\Conexiones;
use app\models\Funciones;
use app\models\LogsTbprincipales;
use app\models\Botica;
use app\models\MovimientosSubidos;
use app\models\MovimientoStock;
use app\models\SincroLocal;
use app\models\Sincronizado;
use app\models\Transferencia;
use app\models\TransfExterna;
use app\models\Usuarios;
use Yii;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;


class ConexionesController extends Controller
{
    public function actionIndex()
    {
        //return $this->render('index');
    }

    public function actionInicializartabla(){
        $post=Yii::$app->request->post();
        $usuarios=Usuarios::find()->where([
            'idusuario'=>$post['idusuario'],
            'password_hash'=>$post['password_hash']
        ])->count();
        if($usuarios==1){
            $query= new Query();
            $datos=$query->select('l.data')->from('logs_tbprincipales l')
                ->where("l.tabla ='".$post['tabla']."' AND s.estado=0 AND s.idbotica=".$post['idbotica'])
                ->innerJoin('sincronizado s','l.idlog = s.idlog')->column();
            $respuesta=[
                'conexion'=>true,
                'datos'=>$datos,
            ];

        }else{
            $respuesta=[
                'conexion'=>false,
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $respuesta;
    }
    private function devolverLogs($idbotica){
        $query= new Query();
        $datos=$query->select('l.*')->from('logs_tbprincipales l')
            ->where(" s.estado=0 AND s.idbotica=".$idbotica)
            ->innerJoin('sincronizado s','l.idlog = s.idlog')->all();
        return $datos;
    }



    private function infoBoticas($idbotica){
        $boticas=Botica::find()->where(['<>','idbotica',$idbotica])
            ->andWhere(['tipo_almacen'=>0])->all();
        $datos=[];
        foreach ($boticas as $b){
            $datos[]=$b->getAttributes();
        }
        return $datos;
    }


    private function devolverMovimientos($idbotica){
        $datos=MovimientoStock::find()->where('idbotica='.$idbotica.' AND idlocal is null')->asArray()->all();

        return $datos;
    }
    

    public function actionIniciarConexion($idbotica,$user,$pass){
        $user=Usuarios::find()->where([
            'username'=>$user,
            'password_hash'=>$pass,
        ])->one();
        $conpend=Conexiones::find()->andFilterWhere([
            'idbotica'=>$idbotica,
            'create_by'=>$user->idusuario,
        ])->andWhere('fecha_fin is null')->all();
        $transaccion=Yii::$app->db->beginTransaction();
        try{
            if(count($conpend)>1){
                foreach ($conpend as $con){
                    $mensajes=Json::decode($con->messages,true);
                    $mensajes[]=[
                        'date'=>date('Y-m-d H:i:s'),
                        'mensaje'=>'Se ha cerrado la conexion de manera forzosa',
                        'tipo'=>'info',
                    ];
                    $con->fecha_fin=date('Y-m-d H:i:s');
                    $con->messages=Json::encode($mensajes);
                    $con->save();
                }
            }elseif(count($conpend)==1){
                $con=$conpend[0];
                $mensajes=Json::decode($con->messages,true);
                $mensajes[]=[
                    'date'=>date('Y-m-d H:i:s'),
                    'mensaje'=>'Se ha cerrado la conexion de manera forzosa',
                    'tipo'=>'info',
                ];
                $con->fecha_fin=date('Y-m-d H:i:s');
                $con->messages=Json::encode($mensajes);
                $con->save();
            }
            $conexion=new Conexiones();
            $conexion->idbotica=$idbotica;
            $conexion->create_by=$user->idusuario;
            $conexion->enlace=Funciones::Random(32);
            $conexion->fecha_ini=date('Y-m-d H:i:s');
            $conexion->messages=Json::encode([
                ['date'=>date('Y-m-d H:i:s'),'mensaje'=>'Se ha Iniciado una nueva conexion','tipo'=>'info',]
            ]);
            $conexion->save();
            $conexion->refresh();
            $transaccion->commit();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'mensaje'=>true,
                'conexion'=>$conexion->getAttributes(),
            ];
        }catch (\Exception $e){
            $transaccion->rollBack();
            return [
                'mensaje'=>false,
            ];
        }

    }

    public function actionConectar($id,$enlace){
        $respuesta=Funciones::establecerConexion($id,$enlace);
        if($respuesta['mensaje']){
            $conexion=$respuesta['conexion'];
            $respuesta['logspendientes']=$this->devolverLogs($conexion['idbotica']);
            $respuesta['movimientospendientes']=$this->devolverMovimientos($conexion['idbotica']);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $respuesta;
        }else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $respuesta;
        }
    }


    public function actionEnlazar($username,$password,$botica){
        $usuario=Usuarios::findByUsername($username);
        if($usuario->validatePassword($password) && $usuario->idrole<=2){
            $botica=Botica::findOne($botica);
            $respuesta=[
                'mensaje'=>true,
                'usuario'=>$usuario->getAttributes(),
                'botica'=>$botica->getAttributes(),
                'logs'=>$this->devolverLogs($botica->idbotica),
            ];
        }else{
            $respuesta=[
                'mensaje'=>false,
                'growl'=>[
                    'message' => 'Parametros incorrectos',
                    'options' => [
                        'type'=>'danger',
                    ],
                ],
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $respuesta;
    }



    public function actionSincronizarall($id,$enlace,$num){
        $conexion=Conexiones::find()->where([
            'idconexion'=>$id,
            'enlace'=>$enlace
        ])->one();
        if($conexion){
            $logpend=$this->devolverLogs($conexion->idbotica);
            if(count($logpend)==$num){
                $consulta=Yii::$app->db->createCommand()->update(
                    'sincronizado',[
                        'estado'=>1,
                        'update_by'=>$conexion->create_by,'update_at'=>date('Y-m-d H:i:s')
                    ],'idbotica='.$conexion->idbotica)->execute();
                $mensajes=Json::decode($conexion->messages,true);
                $mensajes[]=[
                    'date' => date('Y-m-d H:i:s'),
                    'mensaje' => 'Se han sincronizado '.$consulta.' registros de las tablas principales',
                    'tipo'=>'success',
                ];
                $conexion->messages=Json::encode($mensajes);
                $conexion->save();
                $conexion->refresh();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'mensaje'=>'Sincronizado',
                    'consulta'=>$consulta,
                    'conexion'=>$conexion->getAttributes()
                ];
            }

        }

    }

    public function actionSincronizarMovimiento($id,$enlace,$idmovimiento,$idlocal){
        $conexion=Conexiones::find()->where([
            'idconexion'=>$id,
            'enlace'=>$enlace
        ])->one();
        if($conexion){
            $movimiento=MovimientosSubidos::findOne($idmovimiento);
            $movimiento->idlocal=$idlocal;
            $movimiento->update_by=$conexion->create_by;
            $movimiento->fechamod=date('Y-m-d H:i:s');
            if($movimiento->save()){
                $mensajes=Json::decode($conexion->messages,true);
                $regmov=false;
                foreach ($mensajes as $i=>$m){
                    if($m['tipo']=='reg-movimientos'){
                        $regmov=true;
                        $mensajes[$i]['update']=date('Y-m-d H:i:s');
                        $mensajes[$i]['contador']=$m['contador']+1;
                        $mensajes[$i]['mensaje']='Se han sincronizado '.($m['contador']+1).' registros de movimientos del Stock';
                    }
                }
                if($regmov==false){
                    $mensajes[]=[
                        'date' => date('Y-m-d H:i:s'),
                        'update'=>date('Y-m-d H:i:s'),
                        'mensaje' => 'Se han sincronizado 1 registro de movimientos del Stock',
                        'tipo'=>'reg-movimientos',
                        'contador'=>1
                    ];
                }
                $conexion->messages=Json::encode($mensajes);
                $conexion->save();
                $conexion->refresh();
                $result=[
                    'mensaje'=>1,
                    'conexion'=>$conexion
                ];
            }else{
                $result=[
                    'mensaje'=>0,
                    'conexion'=>$conexion
                ];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionSubirMovimiento($id,$enlace,$idlocal,$movdata){
        $conexion=Conexiones::find()->where([
            'idconexion'=>$id,
            'enlace'=>$enlace
        ])->one();
        if($conexion){
            $movimiento=MovimientosSubidos::find()->where([
                'idlocal'=>$idlocal,'idbotica'=>$conexion->idbotica
            ])->one();
            if(!$movimiento){
                $movinfo=Json::decode($movdata,true);
                $movimiento= new MovimientosSubidos();
                $movimiento->idunidad=$movinfo['idunidad'];
                $movimiento->cantidad=$movinfo['cantidad'];
                $movimiento->tipo_transaccion=$movinfo['tipo_transaccion'];
                $movimiento->fecha=$movinfo['fecha'];
                $movimiento->idprocedencia=$movinfo['idprocedencia'];
                $movimiento->idbotica=$movinfo['idbotica'];
                $movimiento->idlocal=$idlocal;
                $movimiento->create_by=$movinfo['create_by'];
                $movimiento->update_by=$conexion->create_by;
            }else{
                $movimiento->idlocal=$idlocal;
                $movimiento->update_by=$conexion->create_by;
                $movimiento->fechamod=date('Y-m-d H:i:s');
            }
            if($movimiento->save()){
                $movimiento->refresh();
                $movimiento->unidad->producto->recalculaStock($movimiento->idbotica);
                $mensajes=Json::decode($conexion->messages,true);
                $regmov=false;
                foreach ($mensajes as $i=>$m){
                    if($m['tipo']=='reg-movimientos'){
                        $regmov=true;
                        $mensajes[$i]['update']=date('Y-m-d H:i:s');
                        $mensajes[$i]['contador']=$m['contador']+1;
                        $mensajes[$i]['mensaje']='Se han sincronizado '.($m['contador']+1).' registros de movimientos del Stock';
                    }
                }
                if($regmov==false){
                    $mensajes[]=[
                        'date' => date('Y-m-d H:i:s'),
                        'update'=>date('Y-m-d H:i:s'),
                        'mensaje' => 'Se han sincronizado 1 registro de movimientos del Stock',
                        'tipo'=>'reg-movimientos',
                        'contador'=>1
                    ];
                }
                $conexion->messages=Json::encode($mensajes);
                $conexion->save();
                $conexion->refresh();
                $result=[
                    'mensaje'=>1,
                    'conexion'=>$conexion,
                    'movimiento'=>$movimiento->getAttributes()
                ];
            }else{
                $result=[
                    'mensaje'=>0,
                    'conexion'=>$conexion
                ];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionSincronizarTransferencia($id,$enlace,$idtransferencia){
        $conexion=Conexiones::find()->where([
            'idconexion'=>$id,
            'enlace'=>$enlace
        ])->one();
        if($conexion){
            $transferencia=TransfExterna::findOne($idtransferencia);
            $transferencia->destino_conf=1;
            $transferencia->estado='R';
            $transferencia->update_by=$conexion->create_by;
            $transferencia->update_at=date('Y-m-d H:i:s');
            if($transferencia->save()){
                $mensajes=Json::decode($conexion->messages,true);
                $regt=false;
                foreach ($mensajes as $i=>$m){
                    if($m['tipo']=='reg-transferencia'){
                        $regt=true;
                        $mensajes[$i]['update']=date('Y-m-d H:i:s');
                        $mensajes[$i]['contador']=$m['contador']+1;
                        $mensajes[$i]['mensaje']='Se han sincronizado '.($m['contador']+1).' registros de transferencia';
                    }
                }
                if($regt==false){
                    $mensajes[]=[
                        'date' => date('Y-m-d H:i:s'),
                        'update'=>date('Y-m-d H:i:s'),
                        'mensaje' => 'Se han sincronizado 1 registro de transferencia',
                        'tipo'=>'reg-transferencia',
                        'contador'=>1
                    ];
                }
                $conexion->messages=Json::encode($mensajes);
                $conexion->save();
                $conexion->refresh();
                $result=[
                    'mensaje'=>1,
                    'conexion'=>$conexion,
                    'transferencia'=>$transferencia->attributes
                ];
            }else{
                $result=[
                    'mensaje'=>0,
                    'conexion'=>$conexion
                ];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionConfirmaTransferencia($id,$enlace,$idtransferencia){
        $conexion=Conexiones::find()->where([
            'idconexion'=>$id,
            'enlace'=>$enlace
        ])->one();
        if($conexion){
            $transferencia=TransfExterna::findOne($idtransferencia);
            if($transferencia->estado!='F'){
                $transferencia->estado='F';
            }
            if($transferencia->botorigen->tipo_almacen==1){
                $transferencia->origen_conf=1;
                $transferencia->genMovSalidas($conexion->idconexion);
            }
            $transferencia->update_by=$conexion->create_by;
            $transferencia->update_at=date('Y-m-d H:i:s');
            if($transferencia->save()){
                $transferencia->genMovIngresos($conexion->idconexion);
                $mensajes=Json::decode($conexion->messages,true);
                $regt=false;
                foreach ($mensajes as $i=>$m){
                    if($m['tipo']=='reg-transferencia'){
                        $regt=true;
                        $mensajes[$i]['update']=date('Y-m-d H:i:s');
                        $mensajes[$i]['contador']=$m['contador']+1;
                        $mensajes[$i]['mensaje']='Se han sincronizado '.($m['contador']+1).' registros de transferencia';
                    }
                }
                if($regt==false){
                    $mensajes[]=[
                        'date' => date('Y-m-d H:i:s'),
                        'update'=>date('Y-m-d H:i:s'),
                        'mensaje' => 'Se han sincronizado 1 registro de transferencia',
                        'tipo'=>'reg-transferencia',
                        'contador'=>1
                    ];
                }
                $conexion->messages=Json::encode($mensajes);
                $conexion->save();
                $conexion->refresh();
                $result=[
                    'mensaje'=>1,
                    'conexion'=>$conexion,
                    'transferencia'=>$transferencia->attributes,
                    'movingresos'=>$transferencia->movingresos(),
                ];
            }else{
                $result=[
                    'mensaje'=>0,
                    'conexion'=>$conexion
                ];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    public function actionSincronizarLog($id,$enlace,$idlog){
        $conexion=Conexiones::find()->where([
            'idconexion'=>$id,
            'enlace'=>$enlace
        ])->one();

        if($conexion){
            $sincronizado=SincroLocal::findOne([
                'idlog'=>$idlog,
                'idbotica'=>$conexion->idbotica
            ]);
            $sincronizado->estado=1;
            $sincronizado->update_by=$conexion->create_by;
            $sincronizado->update_at=date('Y-m-d H:i:s');
            if($sincronizado->save()){
                $mensajes=Json::decode($conexion->messages,true);
                $regmov=false;
                foreach ($mensajes as $i=>$m){
                    if($m['tipo']=='reg-logs-principales'){
                        $regmov=true;
                        $mensajes[$i]['update']=date('Y-m-d H:i:s');
                        $mensajes[$i]['contador']=$m['contador']+1;
                        $mensajes[$i]['mensaje']='Se han sincronizado '.($m['contador']+1).' registros de las tablas principales';
                    }
                }
                if($regmov==false){
                    $mensajes[]=[
                        'date' => date('Y-m-d H:i:s'),
                        'update'=>date('Y-m-d H:i:s'),
                        'mensaje' => 'Se han sincronizado 1 registro de las tablas principales',
                        'tipo'=>'reg-logs-principales',
                        'contador'=>1
                    ];
                }
                $conexion->messages=Json::encode($mensajes);
                $conexion->save();
                $conexion->refresh();
                $result=[
                    'mensaje'=>1,
                    'conexion'=>$conexion
                ];
            }else{
                $result=[
                    'mensaje'=>0,
                    'conexion'=>$conexion
                ];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;

        }
    }


    public function actionConfirmamodificacion(){
        $post=Yii::$app->request->post();
        $usuarios=Usuarios::find()->where([
            'idusuario'=>$post['idusuario'],
            'password_hash'=>$post['password_hash']
        ])->count();
        if($usuarios==1){
            $sincronizado= Sincronizado::findOne([
                'idlog'=>$post['idlog'],
                'idbotica'=>$post['idbotica']
            ]);
            $sincronizado->estado=1;
            $sincronizado->save();
        }
    }

    public function actionConsultalog($user,$pass){
        $usuario=Usuarios::findByUsername($user);
        if($usuario->password_hash==$pass){
            $logs=LogsTbprincipales::find()->all();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $logs;
        }
    }


}
