<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CategoriasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categorias';
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
            return Yii::$app->controller->renderPartial('_detalleProductos',
                [
                    'model'=>$model
                ]);
        },
        'headerOptions'=>['class'=>'kartik-sheet-style'],
        'expandOneOnly'=>true,
        'expandIcon'=>'<span class="ace-icon fa fa-angle-double-down green"></span',  
        'collapseIcon'=>'<span class="ace-icon fa fa-angle-double-up red"></span',          
    ],
    'descripcion',
    [
        'header'=>'ACCIONES',
        'class' => 'yii\grid\ActionColumn',
        'template' => '{update} {delete}',
        'buttons' => [
//            'view' => function ($url, $model, $key) {
//                return Html::a('<span class="fa fa-eye"></span>', '#', [
//                    'id' => 'categorias-index-link',
//                    'class' => 'btn btn-primary btn-xs',
//                    'title' => Yii::t('app', 'View'),
//                    'data-toggle' => 'modal',
//                    'data-target' => '#modal',
//                    'data-url' => $url,
//                    'data-pjax' => '0',
//                ]);
//            },
            'update' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-pencil"></span>', '#', [
                    'id' => 'categorias-index-link',
                    'title' => Yii::t('app', 'Update'),
                    'class' => 'btn btn-info btn-xs',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'data-url' => $url,
                    'data-pjax' => '0',
                ]);
            },
            'delete' => function ($url, $model, $key) {
                if(count($model->productos)){
                    return '';
                }else{
                    return Html::a('<span class="fa fa-trash"></span>', '#', [
                        'id'=>'categorias-delete-link',
                        'title' => Yii::t('yii', 'Delete'),
                        'class' => 'btn btn-danger btn-xs',
                        'delete-url'=>$url,
                        'pjax-container'=>'categorias-grid-pjax',
                        'data-pjax' => '0',
                    ]);
                }

            },
        ],
    ],
];
?>
<div class="categorias-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


<?= GridView::widget([
        'id'=>'categorias-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'resizableColumns'=>true,
        'pjax'=>true,
        'columns' => $columnas,
        'panel' => [
            'heading'=>'<i class="fa fa-tasks"></i> LISTA DE CATEGORIAS',
            'type'=>'default',            
        ],
        'toolbar'=>[
            Html::a(' <i class="fa fa-plus"></i> Agregar','#',
                    [
                        'id'=>'categorias-index-link',
                        'class' => ' btn btn-xs btn-success',
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
    'header' => '<h4 class="modal-title">Registro de Categorias</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
]);

echo "<div class='well'></div>";

Modal::end();
?>
<?php
$this->registerJs(
    "$(document).on('click', '#categorias-index-link', (function() {
        $.get(
            $(this).data('url'),
            function (data) {
                $('.modal-content').html(data);
                $('#modal').modal();
            }
        );        
    }));
    $(document).on('click', '#categorias-delete-link', function() {
        var deleteUrl = $(this).attr('delete-url');
        var pjaxContainer = $(this).attr('pjax-container');
        if (confirm('ok?')) {
            $.ajax({
                        url: deleteUrl,
                        type: 'post',
                        dataType: 'json',
                        error: function(xhr, status, error) {
                            alert('There was an error with your request.' + xhr.responseText);
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