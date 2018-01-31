<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class InfoDiario extends Model{
    public $fecha;
    public $botica;


    public function rules(){
        return [
            [['fecha','botica'],'required'],
        ];
    }

    public function getCboBotica(){
        return ArrayHelper::map(Botica::find()->where(['tipo_almacen'=>0])->asArray()->all(),'idbotica','nomrazon');
    }

    public function getStartDate(){
        $query= new Query();
        $result=$query->select('create_at')->from('salida')->orderBy('create_at')->scalar();
        return Yii::$app->formatter->asDate($result,'dd/MM/yyyy');
    }
    public function getEndDate(){
        $query= new Query();
        $result=$query->select('create_at')->from('salida')->orderBy('create_at DESC')->scalar();
        return Yii::$app->formatter->asDate($result,'dd/MM/yyyy');
    }

}