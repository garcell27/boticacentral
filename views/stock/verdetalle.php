<?php
use miloschuman\highcharts\Highstock;
use kartik\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;

$this->title = 'Detalle Stock : '.$model->descripcion.' - '.$model->laboratorio->nombre;
$this->params['breadcrumbs'][] = ['label'=>'Stocks','url'=>['index']];
$this->params['breadcrumbs'][] = $model->idproducto;
$datosHistorial=$model->dataHistorial();

if(isset($datosHistorial['grafica'])) {
    $boticas=$datosHistorial['boticas'];
    ?>
    <input type="hidden" id="idproducto" value="<?= $model->idproducto?>"/>
    <div class="widget-box transparent">
        <div class="widget-header">
            <h4 class="widget-title">HISTORIAL</h4>
        </div>
        <div class="widget-body">
            <div class="widget-main">
                <?php
                echo Highstock::widget([
                    'setupOptions' => [
                        'lang' => [
                            'shortMonths' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                            'weekdays' => ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'],
                            'loading' => 'Cargando...',
                            'exportButtonTitle' => 'Exportar',
                            'printButtonTitle' => 'Importar',
                            'rangeSelectorZoom' => 'Periodo',
                            'downloadPNG' => 'Descargar gráfica PNG',
                            'downloadJPEG' => 'Descargar gráfica JPEG',
                            'downloadPDF' => 'Descargar gráfica PDF',
                            'downloadSVG' => 'Descargar gráfica SVG',
                            'printChart' => 'Imprimir Gráfica',
                        ]
                    ],
                    'options' => [
                        'rangeSelector' => [
                            'selected' => 4
                        ],
                        'yAxis' => [
                            'plotLines' => [[
                                'value' => 0,
                                'width' => 2,
                                'color' => 'silver'
                            ]]
                        ],
                        'tooltip' => [
                            'pointFormat' => '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.change})<br/>',
                            'valueDecimals' => 2
                        ],
                        'series' => $datosHistorial['grafica']
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>
    <h3 class="page text-center">DETALLE DE MOVIMIENTOS</h3>
    <div class="row">
        <?php foreach($boticas as $b):?>
            <div class="col-md-6">
                <div class="widget-box">
                    <div class="widget-header">
                        <h4 class="widget-title"><?=$b->nomrazon;?></h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main" >
                            <?php
                                $stock=$model->verStockBotica($b->idbotica);
                                if($stock){
                            ?>
                            <h4>STOCK MINIMO :
                                <?= $stock->minimo==null?'NO TIENE':$stock->minimo.' '.$model->undpri->descripcion;?>
                                <small><?= Html::button('<i class="fa fa-pencil"></i>',[
                                        'class'=>'btn btn-xs btn-info',
                                        'id'=>'reg-stock-min',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal',
                                        'data-url'=>Url::to(['update-min','id'=>$stock->idstock])
                                    ])?></small>
                            </h4>

                            <div class="timeline-container" id="movimientos-<?=$b->idbotica;?>">
                            <?php
                                echo $this->render('_timeMovimientos',[
                                    'dataMovimientos'=>$model->dataDetalleMov($b->idbotica),
                                    'stockini'=>$stock->fisico,
                                    'stockmin'=>$stock->minimo,
                                    'botica'=>$b,
                                    'pagina'=>1,
                                    'model'=>$model
                                ]);
                            ?>
                            </div>
                            <?php }else{
                                    echo Alert::widget([
                                        'type' => Alert::TYPE_INFO,
                                        'title' => 'SIN MOVIMIENTOS',
                                        'icon' => 'glyphicon glyphicon-exclamation-sign',
                                        'body' => 'ESTE PRODUCTO NO HA PRESENTADO NINGUN MOVIMIENTO EN ESTA BOTICA',
                                        'closeButton'=>false,
                                    ]);
                            }?>
                        </div>
                    </div>
                </div>

            </div>
        <?php endforeach;?>
    </div>
    <div id="plantilla" class="hidden"></div>
<?php
    Modal::begin([
        'id' => 'modal',
        'header' => '<h4 class="modal-title">STOCK MINIMO</h4>',
        'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
    ]);

    echo "<div class='well text-center'><i class='fa fa-3x fa-spin fa-spinner'></i> <h2>Cargando Contenido</h2></div>";

    Modal::end();

$script=<<<JS
    $(document).on('click', '#btn-vermas', (function() {
        var idproducto=$('#idproducto').val();
        var idbotica=$(this).data('idbotica');
        var stockini=$(this).data('stockini');
        var stockmin=$(this).data('stockmin');
        var pagina=$(this).data('pagina');
        var indexlink=$(this).data('url');
        var button=$(this);
        $.ajax({
            url:indexlink,
            type:'post',
            data:'idproducto='+idproducto+'&idbotica='+idbotica+'&stockini='+stockini+
            '&stockmin='+stockmin+'&pagina='+pagina,
            beforeSend:function(){
                button.slideUp();
            },
            success:function(data){
                $('#movimientos-'+idbotica).append(data);
            }
        });
    }));
     $(document).on('click', '#reg-stock-min', (function() {
        var indexlink=$(this).data('url');
        $.ajax({
            url:indexlink,
            type:'get',
            beforeSend:function(){
                var html= $('#plantilla').html();
                if($('#modal .modal-content').html()!==html){
                    $('#modal .modal-content').html(html);
                }
            },
            success:function(data){
                $('#modal .modal-content').html(data);
                $('#modal').modal();
            }
        });
    }));

JS;

    $this->registerJs($script);
}else{
    echo Alert::widget([
        'type' => Alert::TYPE_WARNING,
        'title' => 'INACTIVO',
        'icon' => 'glyphicon glyphicon-exclamation-sign',
        'body' => 'ESTE PRODUCTO NO HA PRESENTADO NINGUN MOVIMIENTO',
        'closeButton'=>false,
    ]);
}