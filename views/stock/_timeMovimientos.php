<?php

use yii\helpers\Html;
use yii\helpers\Url;
$hoy=date('Y-m-d');
$ayer = date( "Y-m-d", strtotime( "-1 day", strtotime( $hoy ) ) );
$stock=$stockini;
foreach($dataMovimientos as $data):
    if($data['fecha']==$hoy){
        $labelfecha='HOY';
    }elseif($data['fecha']==$ayer){
        $labelfecha='AYER';
    }else{
        $labelfecha=Yii::$app->formatter->asDate($data['fecha'],'dd/MM/YY');
    }
?>


    <div class="timeline-label">
        <span class="label label-primary arrowed-in-right"><?= $labelfecha?></span>
    </div>
    <div class="timeline-items">
        <?php
        foreach($data['movimientos'] as $mov):
            $stinicio=$stock;
            switch($mov['tipo_transaccion']){
                case 'I':
                    $icono='download';
                    $color='success';
                    $movimiento='INGRESOS';
                    $cantidad=$model->creaTexto($mov['cantidad']);
                    $contenido='<p>REGISTRADO POR '.$mov['namefull'].'</p>';
                    $stock=$stock-$mov['cantidad'];
                    $tipowidget='transparent';
                    break;
                case 'J':
                    $icono='download';
                    $color='success';
                    $movimiento='INGRESO POR TRANSFERENCIA';
                    $cantidad=$model->creaTexto($mov['cantidad']);
                    $contenido='<p>REGISTRADO POR '.$mov['namefull'].'</p>';
                    $stock=$stock-$mov['cantidad'];
                    $tipowidget='transparent';
                    break;
                case 'E':
                    $icono='upload';
                    $color='warning';
                    $movimiento='SALIDAS';
                    $cantidad=$model->creaTexto($mov['cantidad']);
                    $contenido='<p>REGISTRADO POR '.$mov['namefull'].'</p>';
                    $tipowidget=$stinicio==0?'widget-color-red':'transparent';
                    if($stockmin>$stinicio && $tipowidget=='transparent'){
                        $tipowidget='widget-color-orange';
                    }
                    $stock=$stock+$mov['cantidad'];
                    break;
                case 'F':
                    $icono='download';
                    $color='success';
                    $movimiento='SALIDAS POR TRANSFERENCIA';
                    $cantidad=$model->creaTexto($mov['cantidad']);
                    $contenido='<p>REGISTRADO POR '.$mov['namefull'].'</p>';
                    $stock=$stock+$mov['cantidad'];
                    $tipowidget='transparent';
                    break;
                case 'X':
                    $icono='download';
                    $color='danger';
                    $cantidad='widget-color-grey';
                    $movimiento='INGRESOS ANULADAS';
                    $contenido='<p>REGISTRADO POR '.$mov['namefull'].'</p>';
                    $tipowidget='';
                    break;
                case 'Y':
                    $icono='upload';
                    $color='danger';
                    $cantidad='';
                    $movimiento='SALIDAS ANULADAS';
                    $contenido='<p>REGISTRADO POR '.$mov['namefull'].'</p>';
                    $tipowidget='widget-color-grey';
                    break;
            }?>
            <div class="timeline-item clearfix">
                <div class="timeline-info">
                    <i class="timeline-indicator ace-icon fa fa-<?= $icono?> btn btn-<?= $color?> no-hover green"></i>
                </div>
                <div class="widget-box <?= $tipowidget;?>">
                    <div class="widget-header widget-header-small">
                        <h5 class="widget-title smaller"><?= $movimiento?></h5>
                        <span class="widget-toolbar">
                            <?= $cantidad;?>
                        </span>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <?= $contenido;?>
                            <?php if($mov['tipo_transaccion']!='X' && $mov['tipo_transaccion']!='Y'){?>
                            <div class="widget-toolbox clearfix">
                                <strong>STOCK PREVIO: <?= number_format($stock,2,'.','')?></strong>
                                <strong class="pull-right">STOCK FINAL: <?= number_format($stinicio,2,'.','')?></strong>
                            </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>

        <?php
        endforeach;
        ?>
    </div>
<?php
endforeach;
if(count($model->dataDetalleMov($botica->idbotica,$pagina+1))):
    echo Html::button('VER MAS',[
        'id'=>'btn-vermas',
        'class'=>'btn btn-info',
        'data-url'=>Url::to(['time-movimientos']),
        'data-idbotica'=>$botica->idbotica,
        'data-stockini'=>$stock,
        'data-stockmin'=>$stockmin==null?-1:$stockmin,
        'data-pagina'=>$pagina+1,
    ]);
endif

?>