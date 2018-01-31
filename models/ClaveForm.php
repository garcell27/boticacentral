<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\base\Model;
use yii\helpers\Json;

/**
 * Description of ClaveForm
 *
 * @author cesar
 */
class ClaveForm extends Model{
    //put your code here
    public $idusuario;
    public $clave;
    public $nclave;
    //public $rclave;
    
    private $_user = false;
    
    public function rules()
    {
        return [
            [['idusuario','clave', 'nclave'], 'required'],
            ['clave', 'validaclave'],
        ];
    }
    public function validaclave($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->clave)) {
                $this->addError($attribute, 'Clave Incorrecta');
            }
        }
    }
    
    public function modificarPass()
    {
        $user = $this->getUser();
        if ($this->validate()) {
            $user->setPassword($this->nclave);
            if ($user->save()) {
                $user->refresh();
                $log=LogsTbprincipales::find()->where([
                    'tabla'=>'usuarios',
                    'idclave'=>$user->idusuario,
                ])->one();
                $log->data=Json::encode($user->getAttributes());
                $log->save();
                if($log->save()){
                    $sincronizaciones=Sincronizado::find()->where(['idlog'=>$log->idlog])->all();
                    foreach ($sincronizaciones as $sinc){
                        $sinc->estado=0;
                        $sinc->save();
                    }
                }
                return true;
            }else{
                return $user;
            }
        }
        return $user;
    }
    
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Usuarios::findIdentity($this->idusuario);
        }

        return $this->_user;
    }
    
    
    public function attributeLabels()
    {
        return [
            'clave' => 'ACTUAL CONTRASEÑA',
            'nclave'=>'NUEVA CONTRASEÑA',
            //'rclave' => 'REPITA CONTRASEÑA',
        ];
    }
    
}
