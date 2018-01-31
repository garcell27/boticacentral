<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $searchModel app\models\PedidosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pedidos';
$this->params['breadcrumbs'][] = $this->title;

$columnas=[
    ['class' => 'yii\grid\SerialColumn'],

            'idpedido',
            'idcliente',
            'fecha_registro',
            'idcomprobante',
            'ndocumento',
            // 'total',
            // 'estado',
            // 'entregado',
            // 'idbotica',
    [
        'header'=>'ACCIONES',
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {update} {delete}',
        'buttons' => [
            'view' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-eye"></span>', '#', [
                    'id' => 'pedidos-index-link',
                    'class' => 'btn btn-primary btn-sm',
                    'title' => Yii::t('app', 'View'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'data-url' => $url,
                    'data-pjax' => '0',
                ]);
            },
            'update' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-pencil"></span>', '#', [
                    'id' => 'pedidos-index-link',
                    'title' => Yii::t('app', 'Update'),
                    'class' => 'btn btn-info btn-sm',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'data-url' => $url,
                    'data-pjax' => '0',
                ]);
            },
            'delete' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-trash"></span>', '#', [
                    'id'=>'pedidos-delete-link',
                    'class' => 'btn btn-danger btn-sm',
                    'title' => Yii::t('yii', 'Delete'),
                    'delete-url'=>$url,
                    'pjax-container'=>'pedidos-grid-pjax',
                    'data-pjax' => '0',
                ]);
            },
        ]
    ],
];
?>
<div class="pedidos-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id'=>'pedidos-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columnas,
        'pjax'=>true,
        'panel' => [
            'heading'=>'<i class="fa fa-users"></i> Lista de Pedidos',
            'type'=>'success',
        ],
        'toolbar'=>[
            Html::a('<i class="glyphicon glyphicon-plus"></i> Crear Pedidos','#',
                [
                    'id'=>'pedidos-index-link',
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
    'header' => '<h4 class="modal-title">Registro de Pedidos</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
]);

echo "<div class='well'></div>";

Modal::end();
?>

<?php
$this->registerJs(
    "$(document).on('click', '#pedidos-index-link', (function() {
        var indexlink=$(this).data('url');
        $.ajax({
            url:indexlink,
            type:'get',
            success:function(data){
                $('.modal-content').html(data);
                $('#modal').modal();
            }
        });
    }));
    $(document).on('click', '#pedidos-delete-link', function() {
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

