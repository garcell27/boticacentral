<?php
use kartik\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;
?>

<div>
    <?php $form = ActiveForm::begin([
        'action' => ['consultar-movimiento'],
        'method' => 'get',
        'type'=>ActiveForm::TYPE_INLINE,
    ]); ?>
    <?= $form->field($model, 'idproducto')->widget(Select2::className(),[
        'data' => $model->getAllCboProductos(),
        'options' => ['placeholder' => 'Elija un Producto ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>
    <div class="form-group">
        <?= Html::submitButton('Consultar', ['class' => 'btn btn-primary btn-sm']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<hr>
<div class="row">
<?php if(count($datos)):?>


    <?php foreach($datos as $dt){?>
        <?php $stock_local=$dt['stock'];?>
        <div class="col-lg-6">
            <div class="timeline-container">
                <div class="timeline-label">
                    <span class="label label-primary arrowed-in-right"><?= $dt['botica']?></span>
                </div>
                <div class="timeline-items">
                    <div class="timeline-item clearfix">
                        <div class="timeline-info">
                            <i class="timeline-indicator ace-icon fa fa-cubes btn btn-sucess"></i>
                        </div>
                        <div class="widget-box transparent">
                            <div class="widget-body">
                                <div class="widget-main">
                                    STOCK ACTUAL: <?= $dt['stock'].' '.$dt['unidades']?>
                                </div>
                            </div>
                        </div>

                    </div>
                    <?php foreach ($dt['date'] as $fecha=>$movimientos){
                        if(count($movimientos)){?>
                            <div class="timeline-item clearfix">
                                <div class="timeline-info">
                                    <span class="label label-info "><?= $fecha;?></span>
                                </div>
                                <div class="widget-box transparent">
                                    <div class="widget-header widget-header-small">
                                        <h5 class="widget-title smaller">MOVIMIENTOS</h5>
                                    </div>
                                    <div class="widget-body">
                                        <div class="widget-main">
                                            <?php foreach($movimientos as $mov){
                                                switch($mov['tipo_transaccion']){
                                                    case 'I':
                                                        $movimiento='INGRESOS';
                                                        $stock_local=$stock_local-$mov['cantidad'];
                                                        break;
                                                    case 'J':
                                                        $movimiento='INGRESO POR TRANSFERENCIA';
                                                        $stock_local=$stock_local-$mov['cantidad'];
                                                        break;
                                                    case 'E':
                                                        $movimiento='SALIDAS';
                                                        $stock_local=$stock_local+$mov['cantidad'];
                                                        break;
                                                    case 'F':
                                                        $movimiento='SALIDAS POR TRANSFERENCIA';
                                                        $stock_local=$stock_local+$mov['cantidad'];
                                                        break;
                                                    case 'Y':
                                                        $movimiento='SALIDAS ANULADAS';
                                                        break;
                                                }
                                                echo 'EL USUARIO '.$mov['namefull'].' REGISTRO '.$movimiento.' DE '.$mov['cantidad'].' '.$dt['unidades'].'<br>';

                                            }?>
                                            <div class="widget-toolbox clearfix">
                                                <strong>STOCK INICIAL: <?= number_format($stock_local,2,'.','')?></strong>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        <?php }
                    }?>
                </div>
            </div>
        </div>
    <?php }?>

<?php endif;?>
</div>
