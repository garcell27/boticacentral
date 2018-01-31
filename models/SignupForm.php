<?php
/**
 * Created by PhpStorm.
 * User: cesar
 * Date: 19/08/16
 * Time: 05:54 PM
 */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $namefull;
    public $idrol;

    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => Usuarios::className(), 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => Usuarios::className(), 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            [['namefull','idrol'],'required']
        ];
    }
    public function attributeLabels()
    {
        return [
            'username' => 'LOGIN',
            'namefull'=>'APELLIDOS Y NOMBRES',
            'email' => 'EMAIL',
            'idrol' => 'ROL',
            'password' => 'CLAVE',
        ];
    }



    public function signup()
    {
        if ($this->validate()) {
            $user = new Usuarios();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->namefull=$this->namefull;
            $user->idrole=$this->idrol;
            $user->status=10;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save()) {
                $log=new LogsTbprincipales();
                $log->tabla='usuarios';
                $log->estado=1;
                $log->idclave=$user->idusuario;
                $log->data=Json::encode($user->getAttributes());
                if($log->save()){
                    $log->refresh();
                    $boticas=Botica::find()->where(['tipo_almacen'=>0])->all();
                    foreach ($boticas as $b){
                        $sinc=new Sincronizado();
                        $sinc->idlog=$log->idlog;
                        $sinc->idbotica=$b->idbotica;
                        $sinc->estado=0;
                        $sinc->save();
                    }
                }
                return $user;
            }else{
                return ActiveForm::validate($user);
            }
        }

        return null;
    }


    public function cboRol(){
        return ArrayHelper::map(
                Roles::find()->where(['>','idrole', Yii::$app->user->identity->idrole])->asArray()->all(),
                'idrole','nombre'
        );
    }


}