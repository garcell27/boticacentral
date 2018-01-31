<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'GESTION DE CLIENTES';
$this->params['breadcrumbs'][] = $this->title;

$columnas=[
    ['class' => 'yii\grid\SerialColumn'],
    'nomcliente',
    'direccion',
    'tipodoc',
    'docidentidad',
    // 'telefono',
    // 'cajarapida',

    [
        'header'=>'ACCIONES',
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {update} {delete}',
        'buttons' => [
            'view' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-eye"></span>', '#', [
                    'id' => 'clientes-index-link',
                    'class' => 'btn btn-primary btn-xs',
                    'title' => Yii::t('app', 'View'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'data-url' => $url,
                    'data-pjax' => '0',
                ]);
            },
            'update' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-pencil"></span>', '#', [
                    'id' => 'clientes-index-link',
                    'title' => Yii::t('app', 'Update'),
                    'class' => 'btn btn-info btn-xs',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'data-url' => $url,
                    'data-pjax' => '0',
                ]);
            },
            'delete' => function ($url, $model, $key) {
                if(count($model->pedidos)|| ($model->cajarapida==1)){
                    return '';
                }else{
                    return Html::a('<span class="fa fa-trash"></span>', '#', [
                        'id'=>'clientes-delete-link',
                        'class' => 'btn btn-danger btn-xs',
                        'title' => Yii::t('yii', 'Delete'),
                        'delete-url'=>$url,
                        'pjax-container'=>'clientes-grid-pjax',
                        'data-pjax' => '0',
                    ]);
                }

            },
        ]
    ],
];

?>
<div class="clientes-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<?= GridView::widget([
        'id' => 'clientes-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'resizableColumns'=>true,
        'pjax'=>true,
        'columns' => $columnas,
        'panel' => [
            'heading'=>'<i class="fa fa-users"></i> LISTA DE CLIENTES',
            'type'=>'success',
            
        ],
        'toolbar'=>[
            Html::a('<i class="glyphicon glyphicon-plus"></i> AGREGAR','#',
                    [
                        'id'=>'clientes-index-link',
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
    'header' => '<h4 class="modal-title">Registro de Clientes</h4>',
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
    $(document).on('click', '#clientes-index-link', (function() {
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
    $(document).on('click', '#clientes-delete-link', function() {
        var deleteUrl = $(this).attr('delete-url');
        var pjaxContainer = $(this).attr('pjax-container');
        if (confirm('ok?')) {
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
    "
); ?>

