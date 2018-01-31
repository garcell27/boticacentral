<?php

namespace app\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "conexiones".
 *
 * @property integer $idconexion
 * @property string $enlace
 * @property string $fecha_ini
 * @property string $fecha_fin
 * @property integer $create_by
 * @property string $messages
 * @property string $idbotica
 */
class Conexiones extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return 'conexiones';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enlace', 'fecha_ini','create_by','messages'], 'required'],
            [['fecha_ini', 'fecha_fin'], 'safe'],
            [['create_by','idbotica'], 'integer'],
            [['messages'], 'string'],
            [['enlace'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idconexion' => 'Idconexion',
            'idbotica' => 'Botica',
            'enlace' => 'Enlace',
            'fecha_ini' => 'Fecha Ini',
            'fecha_fin' => 'Fecha Fin',
            'create_by' => 'Create By',
            'messages' => 'Messages',
        ];
    }

    public function getMensajes(){
        return Json::decode($this->messages,true);
    }

    public function setMensajes($mensajes){
        $this->messages=Json::encode($mensajes);
    }

    public function validarVigencia(){
        $fechacomp=explode(' ',$this->fecha_ini);
        if($fechacomp[0]!=date('Y-m-d')) {
            return false;
        }else{
            return true;
        }
    }

    public function cerrarMensajes(){
        $mensajes=$this->getMensajes();
        $mensajes[] = [
            'date' => date('Y-m-d H:i:s'),
            'mensaje' => 'La conexion ha caducado',
            'tipo'=>'info',
        ];
        $this->setMensajes($mensajes);
        $this->fecha_fin=date('Y-m-d H:i:s');
    }

    public function iniciaMensaje(){
        $newmensajes =[
            [
                'date' => date('Y-m-d H:i:s'),
                'mensaje' => 'Se ha Iniciado una nueva conexion',
                'tipo'=>'info',
            ]
        ];
        $this->messages = Json::encode($newmensajes);
    }
}
