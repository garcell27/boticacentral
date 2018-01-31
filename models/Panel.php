<?php
/**
 * Created by PhpStorm.
 * User: cesar
 * Date: 20/12/16
 * Time: 06:12 AM
 */

namespace app\models;


use yii\base\Model;

class Panel extends Model
{
    public function getInfo(){
        $boticas=Botica::find()->count();
        $usuarios=Usuarios::find()->count();
        $productos=Productos::find()->count();
        $categorias=Categorias::find()->count();
        $laboratorios=Laboratorio::find()->count();
        return [
            'nboticas'=>$boticas,
            'nusuarios'=>$usuarios,
            'nproductos'=>$productos,
            'ncategorias'=>$categorias,
            'nlaboratorios'=>$laboratorios
        ];
    }
}