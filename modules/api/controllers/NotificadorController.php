<?php
/**
 * Created by PhpStorm.
 * User: cesar
 * Date: 23/01/18
 * Time: 02:33 PM
 */

namespace app\modules\api\controllers;


use app\models\Categorias;
use app\models\Clientes;
use app\models\Comprobante;
use app\models\Laboratorio;
use app\models\MovimientoStock;
use app\models\Productos;
use app\models\Roles;
use app\models\Sugerencia;
use app\models\Transferencia;
use app\models\Unidades;
use app\models\Usuarios;

class NotificadorController extends DefaultController
{
    public function actionIndex()
    {

        $tablas=[
            [
                'tabla'=>'roles',
                'datos'=>Roles::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->idbotica])->all(),
                'icon'=>'fa fa-users',
            ],
            [
                'tabla'=>'usuarios',
                'datos'=>Usuarios::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->idbotica])->all(),
                'icon'=>'fa fa-user',
            ],
            [
                'tabla'=>'clientes',
                'datos'=>Clientes::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->idbotica])->all(),
                'icon'=>'fa fa-user-o',
            ],
            [
                'tabla'=>'comprobantes',
                'datos'=>Comprobante::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->idbotica])->all(),
                'icon'=>'fa fa-book',
            ],
            [
                'tabla'=>'categorias',
                'datos'=>Categorias::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->idbotica])->all(),
                'icon'=>'fa fa-th',
            ],
            [
                'tabla'=>'laboratorios',
                'datos'=>Laboratorio::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->idbotica])->all(),
                'icon'=>'fa fa-flask',
            ],
            [
                'tabla'=>'productos',
                'datos'=>Productos::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->idbotica])->all(),
                'icon'=>'fa fa-cubes',
            ],
            [
                'tabla'=>'unidades',
                'datos'=>Unidades::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->idbotica])->all(),
                'icon'=>'fa fa-cube',
            ],
            [
                'tabla'=>'sugerencias',
                'datos'=>Sugerencia::find()->where('sincronizaciones is null')
                    ->orWhere(['not like','sincronizaciones','"botica":'.$this->idbotica])->all(),
                'icon'=>'fa fa-external-link',
            ],
            [
                'tabla'=>'movimientos',
                'datos'=>MovimientoStock::find()
                    ->where('idbotica='.$this->idbotica.' AND idlocal is null')->all(),
                'icon'=>'fa fa-retweet',
            ],
            [
                'tabla'=>'transfxrecibir',
                'datos'=>Transferencia::find()->where([
                    'destino_conf'=>0,'estado'=>'E','idbotdestino'=>$this->idbotica
                ])->all(),
                'icon'=>'fa fa-level-down',
            ],
            [
                'tabla'=>'transfxfinalizar',
                'datos'=>Transferencia::find()->where([
                    'origen_conf'=>0,'estado'=>'F','idbotorigen'=>$this->idbotica
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

}