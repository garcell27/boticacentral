<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\widgets\Alert;
use miloschuman\highcharts\Highcharts;
use kartik\export\ExportMenu;
use miloschuman\highcharts\HighchartsAsset;

HighchartsAsset::register($this)->withScripts([
    'highcharts-3d', 
    'modules/exporting',
    ]);
/* @var $this yii\web\View */
/* @var $searchModel app\models\PedidosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'VENTAS REGISTRADAS';
$this->params['breadcrumbs'][] = $this->title;

$columnas=[
    [
        'class'=>'kartik\grid\ExpandRowColumn',
        'width'=>'50px',
        'value'=>function ($model, $key, $index, $column) {
            return GridView::ROW_COLLAPSED;
        },
        'detail'=>function ($model, $key, $index, $column) {
            $sm=new \app\models\DetallepedidoSearch();
            $sm->idpedido=$model->idpedido;
            $dataProvider = $sm->search(Yii::$app->request->queryParams);
            return Yii::$app->controller->renderPartial('_detalleventa',
                [
                   'model'=>$model,
                   'searchModel' => $sm,
                   'dataProvider' => $dataProvider,                   
                ]);
        },
        'headerOptions'=>['class'=>'kartik-sheet-style'],
        'expandOneOnly'=>true,
        'expandIcon'=>'<span class="ace-icon fa fa-angle-double-down green"></span',  
        'collapseIcon'=>'<span class="ace-icon fa fa-angle-double-up red"></span',         
    ],
    [
        'attribute'=>'fecha_registro',
        'format'=>['date','php:h:i A']
    ],
    [
        'header'=>'CLIENTE',
        'value'=>function($model){
            return $model->pedido->cliente->nomcliente;
        },
        'pageSummary'=>'TOTAL (S/)',
    ],
    [
        'header'=>'IMPORTE (S/)',
        'value'=>function($model){
            return $model->pedido->total;
        },
        'hAlign'=> GridView::ALIGN_RIGHT,
        'format'=>['decimal', 2],        
        'pageSummary'=>true,        
    ],
    // 'estado',
    // 'entregado',
    // 'idbotica',
];
$colexport=$columnas;
unset($colexport[0]);        
$export= ExportMenu::widget([
    'dataProvider' => $dpventas,
    'columns' => $colexport,
    'target' => ExportMenu::TARGET_BLANK,
    'fontAwesome' => true,
]);
$total=0;
foreach ($dpventas->getModels() as $item){
    $total+=$item->pedido->total;
}
?>
<div class="text-right">
    <?php echo $this->render('_search', ['model' => $smventas]); ?>
</div>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active">
            <?= Html::a('REPORTE DE CAJA','#tab-caja',['data-toggle'=>'tab']);?>
        </li>
         <li>
            <?= Html::a('CONSOLIDADO','#tab-consolidado',['data-toggle'=>'tab']);?>
        </li>
        <li>
            <?= Html::a('INFORMES','#tab-informe',['data-toggle'=>'tab']);?>
        </li>
        
    </ul>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="tab-content">        
        <div class="tab-pane active" id="tab-caja">
                               
                    <h3 class="header text-right">VENTA TOTAL: S/ <?= number_format($total,2)?></h3>
               
            
            
            <?= GridView::widget([
                'id'=>'pedidos-grid',
                'dataProvider' => $dpventas,
                'columns' => $columnas,
                 'panel' => [
                    'heading'=>'<i class="fa fa-money"></i> INFORME CAJA DIARIA',
                    'type'=>'success',            
                ],
                'pjax'=>true,
                'toolbar'=>[
                    $export,
                ],
                'showPageSummary' => true
                
            ]); 
            $sumconsolidada=0;
            ?>
        </div>
        <div class="tab-pane" id="tab-consolidado">
            <table class="table table-bordered">
                <tr>
                    <th>PRODUCTO</th>
                    <th>LABORATORIO</th>
                    <th>CANTIDADES VENDIDAS</th>
                    <th>IMPORTE</th>
                </tr>
                <?php foreach ($dpconsolidado as $row) {?>
                <?php $sumconsolidada+=$row['subtotal']?>
                    <tr>
                        <td><?= $row['producto']?></td>
                        <td><?= $row['laboratorio']?></td>
                        <td><?= $row['labelCantidad']?></td>
                        <td class="text-right"><?= $row['subtotal']?></td>
                    </tr>
                <?php }?>
                    <tr>
                        <th colspan="3" class="text-right">IMPORTE TOTAL</th>
                        <th class="text-right"><?= 'S/ '.number_format($sumconsolidada,2);?></th>
                    </tr>    
            </table>
            
        </div>
        <div class="tab-pane" id="tab-informe">
           
                <?php 
                    if(Yii::$app->user->identity->idrole>2){
                        $dt=$smventas->searchMensual(Yii::$app->user->identity->idusuario);
                        $dx=$smventas->searchAnual(Yii::$app->user->identity->idusuario);
                    }else{
                        $dt=$smventas->searchMensual();
                        $dx=$smventas->searchAnual();
                    }
                    
                ?>
            <div class="row">
                <div class="col-md-6">
                    <?php
                    if(count($dx['categorias'])){
                        echo Highcharts::widget([
                            'options' => [
                               'chart'=>[
                                   'type'=>'column',
                                   'options3d'=>[
                                       'enabled'=>true,
                                       'alpha'=>0,
                                       'beta'=>0,
                                       'depth'=>50,
                                       'viewDistance'=>10
                                   ]
                                ],
                               'title' => ['text' => 'Ventas registradas por el mes de '.$dt['mes']],
                               'xAxis' => [
                                  'categories' => $dt['categorias'],
                               ],
                               'yAxis' => [
                                  'min'=> 0,
                                  'title' => ['text' => 'Importes(S/)'],
                                   'stackLabels'=> [
                                       'enabled'=> true,
                                       'style'=>[
                                           'fontWeight'=> 'bold',
                                           'color'=>new yii\web\JsExpression('(Highcharts.theme && Highcharts.theme.textColor) || "gray"'),
                                       ]
                                   ]
                               ],
                               
                               'plotOptions'=>[
                                   'column'=> [
                                       'depth'=>25,
                                       'stacking'=> 'normal',
                                       'dataLabels'=>[
                                           'enable'=>true,
                                           'color'=>new yii\web\JsExpression('(Highcharts.theme && Highcharts.theme.dataLabelsColor)|| "white"'),
                                       ]
                                   ]
                               ], 
                               'series' => $dt['series'],
                            ]
                         ]);
                    }else{
                        echo Alert::widget([
                            'type' => Alert::TYPE_DEFAULT,
                            'title' => 'Sin ventas en el mes',
                            'icon' => 'glyphicon glyphicon-exclamation-sign',
                            'body' => 'Actualmente no se han registrado ventas en el mes',
                            'closeButton'=>false,
                        ]);
                    }

                    ?>
                </div>
                <div class="col-md-6">
                    <?php
                    if(count($dx['categorias'])){
                        echo Highcharts::widget([
                            'options' => [
                               'chart'=>[
                                   'type'=>'column',
                                   'options3d'=>[
                                       'enabled'=>true,
                                       'alpha'=>0,
                                       'beta'=>0,
                                       'depth'=>50,
                                       'viewDistance'=>25
                                   ]
                                ],
                               'title' => ['text' => 'REPORTE GENERAL DE VENTAS'],
                               'xAxis' => [
                                  'categories' => $dx['categorias'],
                               ],
                               'yAxis' => [
                                  'min'=> 0,
                                  'title' => ['text' => 'Importes(S/)'],
                                   'stackLabels'=> [
                                       'enabled'=> true,
                                       'style'=>[
                                           'fontWeight'=> 'bold',
                                           'color'=>new yii\web\JsExpression('(Highcharts.theme && Highcharts.theme.textColor) || "gray"'),
                                       ]
                                   ]
                               ],
                               
                               'plotOptions'=>[
                                   'column'=> [
                                       'depth'=>50,
                                       'stacking'=> 'normal',
                                       'dataLabels'=>[
                                           'enable'=>true,
                                           'color'=>new yii\web\JsExpression('(Highcharts.theme && Highcharts.theme.dataLabelsColor)|| "white"'),
                                       ]
                                   ]
                               ], 
                               'series' => $dx['series'],
                            ]
                         ]);
                    }else{
                        echo Alert::widget([
                            'type' => Alert::TYPE_DEFAULT,
                            'title' => 'Sin ventas',
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

</div>
<?php
$this->registerJs("
    $(document).on('click', '#anular-venta', (function() {
        var indexlink=$(this).data('url');
        var pjaxContainer = $(this).attr('pjax-container');
        if (confirm('¿Desea Anular la Venta?')) {
            $.ajax({
                url:indexlink,
                type:'get',
                success:function(data){
                    if(data.message=='ok'){
                        $.notify(data.growl.message,data.growl.options);
                        $.pjax.reload({container: '#pedidos-grid-pjax'});
                    }else{                     
                        alert(JSON.stringify(data.errores));
                    }
                }
            });
        }
        
        return false;
    }));

        "
); ?>


