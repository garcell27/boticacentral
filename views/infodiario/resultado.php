<?php
use kartik\widgets\Alert;
use miloschuman\highcharts\Highcharts;
use miloschuman\highcharts\HighchartsAsset;
HighchartsAsset::register($this)->withScripts([
    'highcharts-3d',
    'modules/exporting',
    'modules/data',
    'modules/drilldown',
]);

$botica=$data['botica'];
$principal=$data['principal'];
?>
<h3 class="center">
    RESULTADO EN BOTICA : <?= $botica->nomrazon;?>
</h3>
<?php if($principal['cantidad']==0):?>
<?php echo Alert::widget([
        'type' => Alert::TYPE_DEFAULT,
        'title' => 'Sin ventas registradas ',
        'icon' => 'glyphicon glyphicon-exclamation-sign',
        'body' => 'Actualmente no se han registrado ventas en esta fecha',
        'closeButton'=>false,
    ]);?>
<?php else:?>
    <? $series=[];?>
    <div class="widget-box">
        <div class="widget-header">
            <h4 class="widget-title">
                DETALLE DE VENTAS POR VENDEDOR
            </h4>
        </div>
        <div class="widget-body">
            <div class="widget-main">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr><th>VENDEDOR</th><th>N° PEDIDOS</th><th>IMPORTE (S/.)</th></tr>
                            <?php foreach($data['participacion'] as $vendedor){ ?>
                                <?php
                                $porcentaje=round($vendedor['importe']*1000/$principal['importe'])/10;
                                $series[]=[
                                    'name'=> $vendedor['namefull'],
                                    'y'=>$porcentaje
                                ];?>
                                <tr>
                                    <td><?= $vendedor['namefull']?></td>
                                    <td class="text-right"><?= $vendedor['total']?></td>
                                    <td class="text-right"><?= $vendedor['importe']?></td>
                                </tr>
                            <?php }?>
                            <tr>
                                <th>TOTAL</th>
                                <th class="text-right"><?= $principal['cantidad']?></th>
                                <th class="text-right"><?= $principal['importe']?></th>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <?php
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
                                    'plotBackgroundColor'=>null,
                                    'plotBorderWidth'=>null,
                                    'plotShadow'=>false,
                                    'type'=>'pie',

                                ],
                                'title' => ['text' => 'VENTAS POR VENDEDOR'],
                                'tooltip'=>[
                                    'pointFormat'=>'{series.name}: <b>{point.percentage:.1f}%</b>'
                                ],
                                'plotOptions'=>[
                                    'pie'=>[
                                        'allowPointSelect'=>true,
                                        'cursor'=>'pointer',
                                        'dataLabels'=>[
                                            'enabled'=>true,
                                            'format'=>'{point.name}: <b>{point.percentage:.1f}%</b>',

                                        ]
                                    ]
                                ],
                                'series' => [[
                                    'name'=>'Porcentaje',
                                    'colorByPoint'=>true,
                                    'data'=>$series
                                ]],
                            ]
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="widget-box">
        <div class="widget-header">
            <h4 class="widget-title">
                CONSOLIDADOS POR VENDEDOR
            </h4>
        </div>
        <div class="widget-body">
            <div class="widget-main">
                <div id="accordion" class="panel-group accordion-style1">
                    <?php foreach($data['consolidados'] as $consolidado){ ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#consolidado-<?= $consolidado['idvendedor']?>">
                                        <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down"
                                           data-icon-show="ace-icon fa fa-angle-right"></i>
                                        <?= $consolidado['nomvendedor']?>
                                    </a>
                                </h4>
                            </div>
                            <div class="panel-collapse collapse" id="consolidado-<?= $consolidado['idvendedor']?>">
                                <div class="panel-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>PRODUCTO</th><th>LABORATORIO</th><th>CANTIDAD</th><th>IMPORTE</th>
                                        </tr>
                                        <?php foreach($consolidado['data'] as $detalle){?>
                                            <tr>
                                                <td><?= $detalle['descripcion']?></td>
                                                <td><?= $detalle['laboratorio'];?></td>
                                                <td class="text-right"><?= $detalle['cantidad']?></td>
                                                <td class="text-right"><?= $detalle['precio']?></td>
                                            </tr>
                                        <?php }?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
<?php endif;?>
