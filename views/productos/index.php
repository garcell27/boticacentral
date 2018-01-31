<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Productos';
$this->params['breadcrumbs'][] = $this->title;

$columnas=[
    ['class' => 'yii\grid\SerialColumn'],
    [
        'attribute'=>'idcategoria',
        'value'=>function($model){
            return $model->categoria->descripcion;
        },
        'filterType'=>GridView::FILTER_SELECT2,
        'filter'=> $searchModel->getCboCategorias(),
        'filterWidgetOptions'=>[
            'pluginOptions'=>['allowClear'=>true],
        ],
        'filterInputOptions'=>['placeholder'=>'Todas las categorias']
    ],
    [
        'attribute'=>'idlaboratorio',
        'value'=>function($model){
            return $model->laboratorio->nombre;
        },
        'filterType'=>GridView::FILTER_SELECT2,
        'filter'=> $searchModel->getCboLaboratorios(),
        'filterWidgetOptions'=>[
            'pluginOptions'=>['allowClear'=>true],
        ],
        'filterInputOptions'=>['placeholder'=>'Todas los laboratorios']
    ],
    'descripcion',
    // 'caducidad',

    [
        'header'=>'ACCIONES DE PRODUCTO',
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {update} {reg-detalle-producto} {recalcular} {delete}',
        'buttons' => [
            'view' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-eye"></span>', '#', [
                    'id' => 'productos-index-modal',
                    'title' => Yii::t('app', 'View'),
                    'class' => 'btn btn-primary btn-xs',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'data-url' => $url,
                    'data-pjax' => '0',
                ]);
            },
            'update' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-pencil"></span>', '#', [
                    'id' => 'productos-index-modal',
                    'title' => Yii::t('app', 'Update'),
                    'class' => 'btn btn-info btn-xs',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'data-url' => $url,
                    'data-pjax' => '0',
                ]);
            },
            'reg-detalle-producto' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-newspaper-o"></span>', '#', [
                    'id' => 'productos-index-modal',
                    'class' => 'btn btn-purple btn-xs',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'data-url' => $url,
                    'data-pjax' => '0',
                ]);
            },
            'recalcular' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-refresh"></span>', '#', [
                    'id' => 'recalcular-link',
                    'title' => Yii::t('app', 'View'),
                    'class' => 'btn btn-warning btn-xs',
                    'data-url' => $url,
                    'data-pjax' => '0',
                ]);
            },
            'delete' => function ($url, $model, $key) {
                $unds=$model->unidades;
                $count=0;
                foreach ($unds as $und){
                    $count+=count($und->movimientoStocks);
                }
                if($count){
                    return '';
                }else{
                    return Html::a('<span class="fa fa-trash"></span>', '#', [
                        'id'=>'productos-delete-link',
                        'title' => Yii::t('yii', 'Delete'),
                        'class' => 'btn btn-danger btn-xs',
                        'delete-url'=>$url,
                        'pjax-container'=>'productos-grid-pjax',
                        'data-pjax' => '0',
                    ]);
                }

            },
        ]
    ],
];
?>
<div class="productos-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


 <?= GridView::widget([
        'id' => 'productos-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'resizableColumns'=>true,
        'pjax'=>true,
        'columns' => $columnas,
        'rowOptions' => function($model,$key,$index){
           $style='success';
           if(count($model->unidades)==0){
               $style='danger';
           }else{
               $error=false;
               foreach ($model->unidades as $u){
                   if($u->tipo=='S' && ($u->equivalencia==0 || $u->equivalencia==1)){
                        $error=true;
                        break;
                    }
                    if($u->paraventa==1 && ($u->preciosug==0 || $u->preciosug==null)){
                        $error=true;
                        break;
                    }
               }
               if($error){$style='warning';}
           }
           return ['class'=>$style];
        },
        'panel' => [
            'heading'=>'<i class="fa fa-cube"></i> LISTA DE PRODUCTOS',
            'type'=>'success',
            
        ],
        'toolbar'=>[
            Html::a('<i class="glyphicon glyphicon-plus"></i> Crear Producto','#',
                    [
                        'id'=>'productos-index-modal',
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
    'size'=>'modal-fullscreen',
    'header' => '<h4 class="modal-title">MODULO DE ACCESO A PRODUCTOS</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => false]
]);?>

<div class='well'>
    <h3>INICIANDO MODULO</h3>
    <p>Mensaje de Bienvenida</p>
</div>

<?php Modal::end();?>
<template>

</template>
<?php

$script=<<<JS
    $(document).on('click', '#productos-index-modal', (function() {
        var indexlink=$(this).data('url');
        $.ajax({
            url:indexlink,
            type:'get',
            beforeSend:function(){
                $('#modal .modal-body').append('<div class="widget-box-overlay">'+
                    '<i class="ace-icon loading-icon fa fa-spin fa-circle-o-notch fa-5x white"></i>'+
                '</div>');
                $('#modal .modal-footer').hide();
            },
            success:function(data){
                $('.modal-content').html(data);
                //$('#modal').modal();
            }
        });
    }));
    //Widget Vinculador actualizar
    $(document).on('click', '.widget-vinculado', (function() {
        var indexlink=$(this).data('url');
        var widget_main=$(this).data('wmain');
        var widget_remoto=$(this).data('remoto');
        $.ajax({
            url:indexlink,
            type:'get',
            beforeSend:function(){
                $(widget_remoto).slideUp('slow');
                $(widget_main+' .widget-box').append('<div class="widget-box-overlay">'+
                    '<i class="ace-icon loading-icon fa fa-spin fa-spinner fa-2x white"></i>'+
                '</div>');
                $(widget_main+' .widget-box').addClass('position-relative');
            },
            success:function(data){
                $(widget_remoto).html(data).slideDown('slow');
                $(widget_main).slideUp('slow');
            }
        });
        return false;
    }));
    // cancela widget
    $(document).on('click', '.cancela-widget-vinculado', (function() {
        var widget_main=$(this).data('wmain');
        var widget_secondary=$(this).data('wsecondary');
        $(widget_secondary+' .widget-box-overlay').remove();
        $(widget_secondary+' .widget-box').removeClass('position-relative');
        $(widget_main).slideUp('slow');
        $(widget_secondary).slideDown('slow');
        return false;
    }));

     $(document).on('click', '#productos-delete-link', function() {
        var deleteUrl = $(this).attr('delete-url');
        var pjaxContainer = $(this).attr('pjax-container');
        if (confirm('¿Desea Eliminar este producto?')) {
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
        return false;
    });
    $(document).on('click', '#recalcular-link', function() {
        var indexlink=$(this).data('url');
        if (confirm('¿Desea recalcular este producto en todos los almacenes?')) {
            $.ajax({
                url: indexlink,
                type: 'post',
                dataType: 'json',
                error: function(xhr, status, error) {
                    alert('Hubo un error: ' + xhr.responseText);
                },
            }).done(function(result) {
                if(result.message=='ok'){
                    $.notify(result.growl.message,result.growl.options);
                }

            });

        }
        return false;
    });
    $('#modal').removeAttr('tabindex');
JS;

$this->registerJs($script); ?>
