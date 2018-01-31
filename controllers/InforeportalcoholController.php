<?php

namespace app\controllers;

use app\models\Botica;
use kartik\mpdf\Pdf;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\InfoAlcohol;

class InforeportalcoholController extends Controller
{

    public function behaviors()
    {
        return [
            'access'=>[
                'class'=> AccessControl::className(),
                'rules'=>[
                    [
                        'allow'=>true,
                        'roles'=>['@']
                    ]
                ],
            ],

        ];
    }

    public function actionIndex()
    {
        $infoalcohol= new InfoAlcohol();
        return $this->render('index',[
            'alldata'=>$infoalcohol->getAlldata(),
        ]);
    }

    public function actionReportegrupal($grupo,$anio,$idbotica){
        if($grupo==1){
            $meses=[1,3];
        }elseif($grupo==2){
            $meses=[4,6];
        }elseif($grupo==3){
            $meses=[7,9];
        }else{
            $meses=[10,12];
        }
        $botica=Botica::findOne($idbotica);
        $infoalcohol=new InfoAlcohol();
        $data=$infoalcohol->reportegresogrupo($anio,$meses,$idbotica);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'content' => $this->renderPartial('contenido',[
                'datos'=>$data,
                'botica'=>$botica
            ]),
            'marginLeft'=>11,
            'marginTop'=>43,
            'marginRight'=>11,
            'cssFile' => Url::to('css/reportalcohol.css'),
            'options' => [
                'title' => 'Reporte - Botica :'.$botica->nomrazon,
                'subject' => 'Sistema de Reporte de Botica',
                'defaultheaderline' => 0,
                'defaultfooterline' => 0,
                'setAutoTopMargin' => false
            ],
            'methods' => [
                'SetHeader' => [$this->renderPartial('header_repo')],
                'SetFooter' => [$this->renderPartial('footer_repo')],
            ]
        ]);
        return $pdf->render();
        /*return $this->renderPartial('contenido',[
            'datos'=>$data,
        ]);*/
    }

}
