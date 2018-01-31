<?php
use miloschuman\highcharts\Highcharts;
use miloschuman\highcharts\HighchartsAsset;
use kartik\widgets\Alert;

HighchartsAsset::register($this)->withScripts([
    'highcharts-3d',
    'modules/exporting',
    'modules/data',
    'modules/drilldown',
]);

/* @var $this yii\web\View */

$this->title = 'Sistema de Botica';
$info        = $model->getInfo();
?>
<div class="site-index">
    <h2>BIENVENIDO AL SISTEMA DE BOTICA!</h2>
    <div class="row">
        <div class="col-sm-12 infobox-container">
            <div class="infobox infobox-blue">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-hospital-o"></i>
                </div>
                <div class="infobox-data">
                    <span class="infobox-data-number"><?=$info['nboticas'];?></span>
                    <div class="infobox-content">Sucursal<?=$info['nboticas'] > 1?'es':'';?></div>
                </div>
            </div>
            <div class="infobox infobox-orange">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-users"></i>
                </div>
                <div class="infobox-data">
                    <span class="infobox-data-number"><?=$info['nusuarios'];?></span>
                    <div class="infobox-content">Usuario<?=$info['nusuarios'] > 1?'s':'';?></div>
                </div>
            </div>
            <div class="infobox infobox-red">
                <div class="infobox-icon">
                    <i class="ace-icon icon ion-cube"></i>
                </div>
                <div class="infobox-data">
                    <span class="infobox-data-number"><?=$info['nproductos'];?></span>
                    <div class="infobox-content">Producto<?=$info['nproductos'] > 1?'s':'';?></div>
                </div>
            </div>
            <div class="infobox infobox-green">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-cubes"></i>
                </div>
                <div class="infobox-data">
                    <span class="infobox-data-number"><?=$info['ncategorias'];?></span>
                    <div class="infobox-content">Categoria<?=$info['ncategorias'] > 1?'s':'';?> </div>
                </div>
            </div>
            <div class="infobox infobox-grey">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-flask"></i>
                </div>
                <div class="infobox-data">
                    <span class="infobox-data-number"><?=$info['nlaboratorios'];?></span>
                    <div class="infobox-content">Laboratorio<?=$info['nlaboratorios'] > 1?'s':'';?> </div>
                </div>
            </div>
        </div>
    </div>
    <hr>

        <?php foreach($boticas as $b):?>

                <div class="widget-box">
                    <div class="widget-body">
                        <div class="widget-main">
                            <?php
                                $data=$ventas->dataGrafVMxBotica($b->idbotica);
                                if(count($data['categorias'])){
                                    echo Highcharts::widget([
                                        'setupOptions'=>[
                                            'lang'=>[
                                                'loading'=>'Cargando...',
                                                'exportButtonTitle'=>'Exportar',
                                                'printButtonTitle'=>'Importar',
                                                'downloadPNG'=> 'Descargar gráfica PNG',
                                                'downloadJPEG'=> 'Descargar gráfica JPEG',
                                                'downloadPDF'=> 'Descargar gráfica PDF',
                                                'downloadSVG'=> 'Descargar gráfica SVG',
                                                'printChart'=> 'Imprimir Gráfica',
                                            ]
                                        ],
                                        'options' => [
                                            'chart'=>[
                                                'type'=>'column',

                                            ],
                                            'title' => ['text' => 'VENTAS EN '.$b->nomrazon],
                                            'xAxis' => [
                                                'type' => 'category',
                                            ],
                                            'legend'=>[
                                                'enabled'=>true,
                                            ],
                                            'yAxis' => [
                                                'min'=> 0,
                                                'title' => ['text' => 'Importes (S/)'],
                                            ],
                                            'plotOptions'=>[
                                                'series'=> [
                                                    'borderWidth'=>0,
                                                    'dataLabels'=>[
                                                        'enabled'=>true,
                                                        'format'=>'S/ {point.y:.2f}',
                                                    ]
                                                ]
                                            ],
                                            'tooltip'=> [
                                                'headerFormat'=> '<span style="font-size:11px">{series.name}</span><br>',
                                                'pointFormat' => '<span style="color:{point.color}">{point.name}</span>: <b>S/ {point.y:.2f}</b> of total<br/>'
                                            ],

                                            'series' => $data['series'],
                                            'drilldown'=>$data['drilldown']
                                        ]
                                    ]);
                                }else{
                                    echo Alert::widget([
                                        'type' => Alert::TYPE_DEFAULT,
                                        'title' => 'Sin ventas registradas en '.$b->nomrazon,
                                        'icon' => 'glyphicon glyphicon-exclamation-sign',
                                        'body' => 'Actualmente no se han registrado ventas',
                                        'closeButton'=>false,
                                    ]);
                                }
                            ?>
                        </div>
                    </div>
                </div>


        <?php endforeach?>
    <div class="widget-box">
        <div class="widget-body">
            <div class="widget-main">
                <?php
                $data=$ventas->dataVentasxVendedor();
                if(count($data['categorias'])){
                    echo Highcharts::widget([
                        'setupOptions'=>[
                            'lang'=>[
                                'loading'=>'Cargando...',
                                'exportButtonTitle'=>'Exportar',
                                'printButtonTitle'=>'Importar',
                                'downloadPNG'=> 'Descargar gráfica PNG',
                                'downloadJPEG'=> 'Descargar gráfica JPEG',
                                'downloadPDF'=> 'Descargar gráfica PDF',
                                'downloadSVG'=> 'Descargar gráfica SVG',
                                'printChart'=> 'Imprimir Gráfica',
                            ]
                        ],
                        'options' => [
                            'chart'=>[
                                'type'=>'column',

                            ],
                            'title' => ['text' => 'VENTAS EN '.$b->nomrazon],
                            'xAxis' => [
                                'type' => 'category',
                            ],
                            'legend'=>[
                                'enabled'=>true,
                            ],
                            'yAxis' => [
                                'min'=> 0,
                                'title' => ['text' => 'Importes (S/)'],
                            ],
                            'plotOptions'=>[
                                'series'=> [
                                    'borderWidth'=>0,
                                    'dataLabels'=>[
                                        'enabled'=>true,
                                        'format'=>'S/ {point.y:.2f}',
                                    ]
                                ]
                            ],
                            'tooltip'=> [
                                'headerFormat'=> '<span style="font-size:11px">{series.name}</span><br>',
                                'pointFormat' => '<span style="color:{point.color}">{point.name}</span>: <b>S/ {point.y:.2f}</b> of total<br/>'
                            ],

                            'series' => $data['series'],
                            'drilldown'=>$data['drilldown']
                        ]
                    ]);
                }else{
                    echo Alert::widget([
                        'type' => Alert::TYPE_DEFAULT,
                        'title' => 'Sin ventas registradas ',
                        'icon' => 'glyphicon glyphicon-exclamation-sign',
                        'body' => 'Actualmente no se han registrado ventas',
                        'closeButton'=>false,
                    ]);
                }
                ?>
            </div>
        </div>
    </div>


</div>


