<?php
/**
 * Created by PhpStorm.
 * User: cesar
 * Date: 12/01/17
 * Time: 12:21 PM
 */

namespace app\models;


use yii\base\Model;

class Security extends Model
{
    public static function mcrypt($text){
        if(!$text==null){
            $algorithm= MCRYPT_RIJNDAEL_192;
            $mode=MCRYPT_MODE_NOFB;
            $iv_size=mcrypt_get_iv_size($algorithm,$mode);
            $iv= mcrypt_create_iv($iv_size,MCRYPT_RAND);
            $key="boticasyanetFarma-ginohb";
            return base64_encode(MCRYPT_ENCRYPT($algorithm,$key,$text,$mode,$iv));
        }
    }

    public static function decrypt($string){
        $algorithm= MCRYPT_RIJNDAEL_192;
        $mode=MCRYPT_MODE_NOFB;
        $iv_size=mcrypt_get_iv_size($algorithm,$mode);
        $iv= mcrypt_create_iv($iv_size,MCRYPT_RAND);
        $key="boticasyanetFarma-ginohb";
        $encrypt=base64_decode($string);
        return  MCRYPT_DECRYPT($algorithm,$key,$encrypt,$mode,$iv);
    }
}