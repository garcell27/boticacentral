<?php
namespace app\models;

use yii\base\Model;
use yii\helpers\Json;

class DetalleProducto extends  Model
{
    public $presentacion;
    public $concentracion;
    public $informacion;

    public function rules()
    {
        return [
            ['presentacion', 'filter', 'filter' => 'trim'],
            ['concentracion', 'filter', 'filter' => 'trim'],
            ['informacion', 'filter', 'filter' => 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'presentacion' => 'PRESENTACION',
            'concentracion' => 'CONCENTRACION',
            'informacion' => 'INFORMACION',
        ];
    }
    public function procesar($jsdetalle){
        if($jsdetalle!=null || $jsdetalle!=''){
            $detalle=Json::decode($jsdetalle,true);
            foreach($detalle as $campo =>$valor){
                $this->$campo=$valor;
            }
        }
    }

    public function concentrar(){
        $datos=[];
        foreach($this->attributes as $campo =>$valor){
            if($valor!='' || $valor!=null){
                $datos[$campo]=$valor;
            }
        }
        if(count($datos)){
            return Json::encode($datos);
        }else{
            return null;
        }
    }

}
?>