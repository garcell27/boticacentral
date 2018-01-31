<?php
/**
 * Created by PhpStorm.
 * User: cesar
 * Date: 13/01/17
 * Time: 10:51 AM
 */

namespace app\models;


use yii\base\Model;

class Funciones extends  Model
{
    public static function Random($length){
        $chars ="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $string =''; // define variable with empty value
        // we generate a random integer first, then we are getting corresponding character , then append the character to $string variable. we are repeating this cycle until it reaches the given length
        for($i=0;$i<$length; $i++)
        {
            $string .= $chars[rand(0,strlen($chars)-1)];
        }
        return $string ;
    }

    public static function nomMes($nmes)
    {
        $mes = [
            1 => 'Enero', 2 => 'Febrero',
            3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto',
            9 => 'Setiembre', 10 => 'Octubre',
            11 => 'Noviembre', 12 => 'Diciembre',
        ];
        return $mes[$nmes];
    }

    public static function zerofill($entero, $largo) {
        // Limpiamos por si se encontraran errores de tipo en las variables
        $entero  = (int) $entero;
        $largo   = (int) $largo;
        $relleno = '';
        if (strlen($entero) < $largo) {
            $relleno = str_repeat('0', $largo-strlen($entero));
        }
        return $relleno.$entero;

    }

    public static function establecerConexion($idconexion,$enlace){
        $conexion=$conexion=Conexiones::find()->where([
            'idconexion'=>$idconexion,
            'enlace'=>$enlace
        ])->one();
        if($conexion){
            if($conexion->validarVigencia()){
                return [
                    'mensaje'=>true,
                    'conexion'=>$conexion,
                ];
            }else{
                $conexion->cerrarMensajes();
                $conexion->save();
                $newcon=Conexiones::find()->where("fecha_ini like '".date('Y-m-d')."%' AND idbotica=".$conexion->idbotica)->one();
                if(!$newcon){
                    $newcon = new Conexiones();
                    $newcon->idbotica = $conexion->idbotica;
                    $newcon->fecha_ini = date('Y-m-d H:i:s');
                    $newcon->enlace = Funciones::Random(32);
                    $newcon->create_by = $conexion->create_by;
                    $newcon->iniciaMensaje();
                    $newcon->save();
                }
                return [
                    'mensaje'=>true,
                    'conexion'=>$newcon,
                    'conexionvencida'=>$conexion,
                ];
            }

        }else{
            return [
                'mensaje'=>false,
                'growl'=>[
                    'message' => 'Parametros de conexion incorrectos',
                    'options' => [
                        'type'=>'danger',
                    ],
                ],
            ];
        }

    }

    public static function rangoDia($fecha){
        $inicio = date('Y-m-d H:i:s', strtotime($fecha . "+6 hours"));
        $fin = date('Y-m-d H:i:s', strtotime($inicio . "+23 hours"));
        return [
            'ini'=>$inicio,
            'fin'=>$fin,
        ];
    }

    public static function groupArray($array,$groupkey)
    {
        if (count($array)>0)
        {
            $keys = array_keys($array[0]);
            $removekey = array_search($groupkey, $keys);
            if ($removekey===false)
                return array("Clave \"$groupkey\" no existe");
            else
                unset($keys[$removekey]);
            $groupcriteria = array();
            $return=array();
            foreach($array as $value)
            {
                    $item=null;
                    foreach ($keys as $key)
                    {
                        $item[$key] = $value[$key];
                    }
                    $busca = array_search($value[$groupkey], $groupcriteria);
                    if ($busca === false)
                    {
                        $groupcriteria[]=$value[$groupkey];
                        $return[]=array($groupkey=>$value[$groupkey],'agrupados'=>array());
                        $busca=count($return)-1;
                    }
                    $return[$busca]['agrupados'][]=$item;
            }
            return $return;
        }
        else
            return array();
    }

}