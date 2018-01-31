<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProveedoresSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Proveedores';
$this->params['breadcrumbs'][] = $this->title;

$columnas=[
    ['class' => 'yii\grid\SerialColumn'],
    [
        'class'=>'kartik\grid\ExpandRowColumn',
        'width'=>'50px',
        'value'=>function ($model, $key, $index, $column) {
            return GridView::ROW_COLLAPSED;
        },
        'detail'=>function ($model, $key, $index, $column) {
            return Yii::$app->controller->renderPartial('_viewProveedor',
                ['model'=>$model]);
        },
        'headerOptions'=>['class'=>'kartik-sheet-style'],
        'expandOneOnly'=>true,
        'expandIcon'=>'<span class="ace-icon fa fa-angle-double-down green"></span>',
        'collapseIcon'=>'<span class="ace-icon fa fa-angle-double-up red"></span>',
    ],
    'nomproveedor',
    'direccion',
    'tipodoc',
    'docidentidad',
];

?>
<div class="proveedores-index">

    <?= GridView::widget([
        'id' => 'proveedores-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax'=>true,
        'columns' => $columnas,
        'panel' => [
            'heading'=>'<i class="fa fa-users"></i> Lista de Proveedores',
            'type'=>'success',
            
        ],
        'toolbar'=>[
            Html::a('<i class="glyphicon glyphicon-plus"></i> Crear Proveedor','#',
                    [
                        'id'=>'proveedores-index-link',
                        'class' => 'btn btn-xs btn-success',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                        'data-url' => Url::to(['create']),
                        'data-pjax' => '0',
                    ]
                )
        ]
    ]); ?>
</div>
<?php
Modal::begin([
    'id' => 'modal',
    'size'=>Modal::SIZE_LARGE,
    'header' => '<h4 class="modal-title">Registro de Proveedor</h4>',
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
    $(document).on('click', '#proveedores-index-link', (function() {
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
                $('.modal-content').html(data);
                $('#modal').modal();
            }        
        });        
    }));
    $(document).on('click', '#proveedores-delete-link', function() {
        var deleteUrl = $(this).data('url');
        var pjaxContainer = $(this).attr('pjax-container');
        if (confirm('Desea Eliminar el siguiente proveedor?')) {
            $.ajax({
                url: deleteUrl,
                type: 'post',
                dataType: 'json',
                error: function(xhr, status, error) {
                    alert('Hubo un error: ' + xhr.responseText);
                },
            }).done(function(result) {
                if(result.message=='ok'){
                    $.notify(result.growl.message,result.growl.options);                         
                }                
                $.pjax.reload({container: '#' + $.trim(pjaxContainer)});
            });
        
        }                                 
    });
    $(document).on('click', '#proveedor-delete-link', function() {
        var deleteUrl = $(this).data('url');
        var idprov = $(this).attr('idprov');
        if (confirm('Desea Retirar el siguiente laboratorio?')) {
            $.ajax({
                url: deleteUrl,
                type: 'post',
                dataType: 'json',
                error: function(xhr, status, error) {
                    alert('Hubo un error: ' + xhr.responseText);
                },
            }).done(function(result) {
                if(result.message=='ok'){
                    $.notify(result.growl.message,result.growl.options);                         
                }                
                reload(idprov);
            });
        
        }                                 
    });
    $('#modal').removeAttr('tabindex');
    function reload(id){
        $.ajax({
            url:'".Url::to(['view','id'=>''])."'+id,
            type:'get',
            beforeSend:function(){
                $('#detalle-prov-'+id).append('<div class=\"widget-box-overlay\">'+
                    '<i class=\"ace-icon fa fa-spinner fa-spin fa-5x middle\"></i>'+
                '</div>');
            },
            success:function(html){
                $('#detalle-prov-'+id).html(html);
            }
        });
    }

    ",  $this::POS_END
); ?>


