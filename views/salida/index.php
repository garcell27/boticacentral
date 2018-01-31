
<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $searchModel app\models\PedidosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Salidas';
$this->params['breadcrumbs'][] = $this->title;

$columnas=[
    [
        'class'=>'kartik\grid\ExpandRowColumn',
        'width'=>'50px',
        'value'=>function ($model, $key, $index, $column) {
            return GridView::ROW_COLLAPSED;
        },
        'detail'=>function ($model, $key, $index, $column) {
            $sm=new \app\models\DetalleSalidaSearch();
            $sm->idsalida=$model->idsalida;
            $dataProvider = $sm->search(Yii::$app->request->queryParams);
            return Yii::$app->controller->renderPartial('_detalleSalida',
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
        'format'=>['date','php:d/m/Y']
    ],
    [
        'attribute'=>'motivo',
        'value'=>function($model){
            switch ($model->motivo){
                case 'T':
                    $campo='Transferencia';
                    break;
                case 'R':
                    $campo='Regularizacion';
                    break;
            }
            return $campo;
        },
        
    ],
    [
        'attribute'=>'estado',
                
    ],
    // 'estado',
    // 'entregado',
    // 'idbotica',
];
?>
<div class="salida-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<?= GridView::widget([
        'id' => 'salidas-grid',
        'dataProvider' => $dataProvider,
        'resizableColumns'=>true,
        'pjax'=>true,
        'columns' => $columnas,
        'panel' => [
            'heading'=>'<i class="fa fa-users"></i> SALIDA DE MERCADERIA',
            'type'=>'success',
        ],
        'toolbar'=>[
            Html::a('<i class="glyphicon glyphicon-plus"></i> Crear Salida','#',
                [
                    'id'=>'salida-index-link',
                    'class' => 'btn btn-xs btn-success',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'data-url' => Url::to(['create']),
                    'data-pjax' => '0',
                ]
            ),
        ]
    ]); ?>
</div>
<?php
Modal::begin([
    'id' => 'modal',
    'size'=>Modal::SIZE_SMALL,
    'header' => '<h4 class="modal-title">Registro de Salida</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
]);

echo "<div class='well text-center'><i class='fa fa-3x fa-spin fa-spinner'></i> <h2>Cargando Contenido</h2></div>";

Modal::end();
?>
<div id="plantilla" class="hidden"></div>
<?php
$this->registerJs(
    "
    $(document).ready(function(){
        $('#plantilla').html($('#modal .modal-content').html());
    });
    $(document).on('click', '#salida-index-link', (function() {
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
    
    $(document).on('click', '#delete-detalle-item', (function() {        
        var indexlink=$(this).data('url');
        var tabla=$(this).data('tabla');
        var idsalida=$(this).data('idsalida');
        if(confirm('¿Desea Eliminar el item asignado?')){
            $.ajax({
                url:indexlink,
                type:'get',                
                success:function(data){
                    actualizaTabla(tabla,idsalida);
                }
            });
        }       
        return false;
    }));

    function actualizaTabla(idtable,idsalida){
            $.ajax({
                url:'".Url::to(['salida/actabladet','id'=>''])."'+idsalida,
                type:'get',
                success:function(result){
                    $('#'+idtable).html(result);
                }
            });            
        }
    ", $this::POS_END
); ?>
