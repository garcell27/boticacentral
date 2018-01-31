<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $searchModel app\models\IngresoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'INGRESOS';
$this->params['breadcrumbs'][] = $this->title;

$columnas=[
    [
        'attribute'=>'f_registro',
        'value'=>function($model){
            return Yii::$app->formatter->asDate($model->f_registro,'dd/MM/yyyy');
        }
    ],
    [
        'attribute'=>'idproveedor',
        'value'=>function($model){
            return $model->proveedor->nomproveedor;
        }
    ],
    [
        'attribute'=>'n_comprobante',
        'value'=>function($model){
            return $model->comprobante->abreviatura.': '.$model->n_comprobante;
        }
    ],
    [
        'attribute'=>'idbotica',
        'value'=>function($model){
            return $model->botica->nomrazon;
        }
    ],        
    [
        'header'=>'ITEMS',
        'value'=>function($model){
            return count($model->detalleIngreso);
        },
        'hAlign'=>'right',
    ],
    [
        'attribute'=>'total',
        'format'=>['decimal', 2],
        'hAlign'=>'right',
    ],        
    
    [
        'header'=>'ACCIONES',
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {delete}',
        'buttons' => [
            'view' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-eye"></span>', $url, [                   
                    'class' => 'btn btn-primary btn-xs',
                    'title' => Yii::t('app', 'View'),
                ]);
            },
            'delete' => function ($url, $model, $key) {
                if(count($model->detalleIngreso)){
                    return '';
                }else{
                    return Html::a('<span class="fa fa-trash"></span>', '#', [
                        'id'=>'ingreso-delete-link',
                        'class' => 'btn btn-danger btn-xs',
                        'title' => Yii::t('yii', 'Delete'),
                        'delete-url'=>$url,
                        'pjax-container'=>'ingreso-grid-pjax',
                        'data-pjax' => '0',
                    ]);
                }

            },
        ]
    ],        
            // 'f_emision',

            // 'conigv',
            // 'total_igv',
            // 'porcentaje',
            // 'idbotica',
            // 'estado',

];
?>
<div id="ingreso-index" class="widget-box transparent">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="widget-header">
        <h4 class="widget-title">
            <i class="ion-android-list orange"></i> LISTADOS
        </h4>
        <div class="widget-toolbar no-border">
            <ul class="nav nav-tabs" id="lista-tab">
                <li class="active">
                    <a data-toggle="tab" href="#list-compra" class="green">
                        <i class="fa fa-shopping-bag"></i> COMPRAS
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#list-inv" class="red2">
                        <i class="fa fa-bar-chart"></i> INVENTARIO
                    </a>
                </li>

            </ul>
        </div>
    </div>
    <div class="widget-body">
        <div class="widget-main no-padding">
            <div class="tab-content">
                <div id="list-compra" class="tab-pane active">
                   <?= $this->render('_listacompras',[
                       'dpcompra'=>$dpcompra,
                   ]);?>
                </div>
                <div id="list-inv" class="tab-pane">
                    <?= $this->render('_listainventarios',[
                        'dpinventario'=>$dpinventario,
                    ]);?>
                </div>
            </div>
        </div>
    </div>

</div>
<?php
Modal::begin([
    'id' => 'modal',
    'size'=> Modal::SIZE_LARGE,
    'header' => '<h4 class="modal-title">Registro de Ingresos</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
]);

echo "<div class='well'></div>";

Modal::end();
?>
<?php
Modal::begin([
    'id' => 'modal2',
    'header' => '<h4 class="modal-title">REGISTRO</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
]);

echo "<div class='well'></div>";

Modal::end();
?>
<?php
$script= "

     $(document).on('click', '#ingreso-index-link', (function() {
        var indexlink=$(this).data('url');
        $.ajax({
            url:indexlink,
            type:'get',
            success:function(data){
                $('#modal .modal-content').html(data);
                $('#modal').modal();
            }
        });
    }));
    $(document).on('click', '#ingreso-view-link', (function() {
        var indexlink=$(this).data('url');
        $.ajax({
            url:indexlink,
            type:'get',
            success:function(data){                
                $('#ingreso-view').slideUp('slow',function(){
                    $('#ingreso-view').html(data).slideDown();
                    $('#ingreso-index').slideUp();
                });
            }
        });
        return false;
    }));
    $(document).on('click', '#ingreso-delete-link', function() {
        var deleteUrl = $(this).attr('delete-url');
        var pjaxContainer = $(this).attr('pjax-container');
        if(confirm('Desea Eliminar el registro?')){
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
    $(document).on('click', '#inventario-link', (function() {
        var indexlink=$(this).data('url');
        $.ajax({
            url:indexlink,
            type:'get',
            success:function(data){
                $('#modal2 .modal-content').html(data);
                $('#modal2').modal();
            }
        });
    }));
    $(document).on('click', '#ingreso-cerrar-link', (function() {
        var indexlink=$(this).data('url');
        var pjaxContainer = $(this).attr('pjax-container');
        if (confirm('¿Desea Cerrar el Inventario?')) {
            $.ajax({
                url:indexlink,
                type:'get',
                success:function(data){
                    if(data.message=='ok'){
                        $.notify(data.growl.message,data.growl.options);
                        $.pjax.reload({container: '#ingreso-grid-pjax'});
                    }else{                     
                        alert(JSON.stringify(data.errores));
                    }
                }
            });
        }
        
        return false;
    }));
    
    function actualizaTabla(idtable,idingreso){
        $.ajax({
            url:'".Url::to(['ingreso/actabladet','id'=>''])."'+idingreso,
            type:'get',
            success:function(result){
                $('#'+idtable).html(result);
            }
        });            
    }
    $('#modal').removeAttr('tabindex');   
";

$this->registerJs($script,  $this::POS_END); 
?>

