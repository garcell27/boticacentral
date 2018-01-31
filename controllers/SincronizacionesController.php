<?php

namespace app\controllers;

use app\models\Botica;
use app\models\Categorias;
use app\models\Clientes;
use app\models\Comprobante;
use app\models\Laboratorio;
use app\models\LogsTbprincipales;
use app\models\Productos;
use app\models\Roles;
use app\models\Sincronizado;
use app\models\Sugerencia;
use app\models\Unidades;
use app\models\Usuarios;
use kartik\alert\Alert;

class SincronizacionesController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $boticasjs=json_encode(Botica::find()->where(['tipo_almacen'=>0])->asArray()->all());

        return $this->render('index',['boticasjs'=>$boticasjs]);
    }
    public function actionVerificamodificaciones($tabla){
        $contador=0;
        switch ($tabla){
            case "roles":
                $rows=Roles::find()->all();
                break;
            case "clientes":
                $rows=Clientes::find()->all();
                break;
            case "comprobante":
                $rows=Comprobante::find()->all();
                break;
            case "sugerencia":
                $rows=Sugerencia::find()->all();
                break;
        }
        foreach ($rows as $row){
            $log=LogsTbprincipales::find()->where(['tabla'=>$tabla,'idclave'=>$row->primaryKey])->one();
            if($log){
                if($log->data!=json_encode($row->getAttributes())){
                    $log->data=json_encode($row->getAttributes());
                    if($log->save()){
                        $sincronizaciones=Sincronizado::find()->where(['idlog'=>$log->idlog])->all();
                        foreach ($sincronizaciones as $sinc){
                            $sinc->estado=0;
                            $sinc->save();
                        }
                        $contador++;
                    }
                }
            }else{
                $log=new LogsTbprincipales();
                $log->tabla=$tabla;
                $log->idclave=$row->primaryKey;
                $log->data=json_encode($row->getAttributes());
                $log->estado=1;
                if($log->save()){
                    $contador++;
                }
            }
        }
        if($contador==0){
             return Alert::widget([
                 'type' => Alert::TYPE_INFO,
                 'title' => 'Sin actualizaciones',
                 'icon' => 'fa fa-info',
                 'body' => 'No se han encontrado ninguna actualizacion en la tabla '.$tabla,
                 'showSeparator' => true
             ]);
        }else{
            return Alert::widget([
                'type' => Alert::TYPE_WARNING,
                'title' => 'Actualizacion',
                'icon' => 'fa fa-exclamation-triangle',
                'body' => 'Se necesitan actualizar '.$contador.' registro(s) en la tabla '.$tabla,
                'showSeparator' => true
            ]);
        }
    }

    public function actionVeriModifica(){
        $tbprincipales=[
            ['tabla'=>'roles','rows'=>Roles::find()->all()],
            ['tabla'=>'unidades','rows'=>Unidades::find()->all()],
            ['tabla'=>'clientes','rows'=>Clientes::find()->all()],
            ['tabla'=>'comprobante','rows'=>Comprobante::find()->all()],
            ['tabla'=>'sugerencia','rows'=>Sugerencia::find()->all()],
        ];
        foreach ($tbprincipales as $tb){
            foreach ($tb['rows'] as $row){
                $log=LogsTbprincipales::find()->where(['tabla'=>$tb['tabla'],'idclave'=>$row->primaryKey])->one();
                if($log){
                    if($log->data!=json_encode($row->getAttributes())){
                        $log->data=json_encode($row->getAttributes());
                        if($log->save()){
                            $sincronizaciones=Sincronizado::find()->where(['idlog'=>$log->idlog])->all();
                            foreach ($sincronizaciones as $sinc){
                                $sinc->estado=0;
                                $sinc->save();
                            }
                        }
                    }
                }else{
                    $log=new LogsTbprincipales();
                    $log->tabla=$tb['tabla'];
                    $log->idclave=$row->primaryKey;
                    $log->data=json_encode($row->getAttributes());
                    $log->estado=1;
                    $log->save();
                }
            }
        }
        return 'Finalizado';
    }

    public function actionSincronizabotica($id){
        $contador=0;
        $botica=Botica::findOne($id);
        $logs=LogsTbprincipales::find()->all();
        foreach ($logs as $log){
            $sincroniza=Sincronizado::findOne(['idbotica'=>$id,'idlog'=>$log->idlog]);
            if($sincroniza){
                if($sincroniza->estado==0){
                    $contador++;
                }
            }else{
                $sincroniza= new Sincronizado();
                $sincroniza->idlog=$log->idlog;
                $sincroniza->idbotica=$id;
                $sincroniza->estado=0;
                if($sincroniza->save()){
                    $contador++;
                }
            }
        }
        if($contador==0){
            return Alert::widget([
                'type' => Alert::TYPE_INFO,
                'title' => 'Botica : '.$botica->nomrazon,
                'icon' => 'fa fa-info',
                'body' => 'No se han encontrado ninguna actualizacion en la botica',
                'showSeparator' => true
            ]);
        }else{
            return Alert::widget([
                'type' => Alert::TYPE_WARNING,
                'title' =>  'Botica : '.$botica->nomrazon,
                'icon' => 'fa fa-exclamation-triangle',
                'body' => 'Se necesitan actualizar '.$contador.' registro(s) en la botica ',
                'showSeparator' => true
            ]);
        }
    }
}
