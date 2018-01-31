<?php

use yii\helpers\Html;
use app\widgets\AceGridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InventarioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$columnas=[
    ['class' => 'yii\grid\SerialColumn'],
    [
        'attribute'=>'create_at',
        'format'=>['date','php:d/m/Y']
    ],
    [
        'attribute'=>'motivo',
        'value'=>function($model){
            switch($model->motivo){
                case "01":
                    $value='REVISION';
                    break;
                case "02":
                    $value='AJUSTES';
                    break;
                case "03":
                    $value='OTROS';
                    break;
            }
            return $value;
        }
    ],
    [
        'attribute'=>'idbotica',
        'value'=>function($model){
            return $model->botica->nomrazon;
        }
    ],

    // 'update_by',
    // 'create_at',
    // 'update_at',

    [
        'header'=>'ACCIONES',
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view}',
        'buttons' => [
            'view' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-eye"></span>', $url, [
                    'class' => 'btn btn-primary btn-xs',
                    'title' => Yii::t('app', 'View'),
                ]);
            },
        ]
    ],
];


$this->title = 'Inventarios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventario-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= AceGridView::widget([
        'id'=>'inventario-grid',
        'dataProvider' => $dataProvider,
        'columns' => $columnas,
        'pjax'=>true,
        'panel' => [
            'heading'=>'<i class="fa fa-adjust"></i> INVENTARIOS ACTIVOS',
            'type'=>AceGridView::TYPE_INFO,
        ],
        'toolbar'=>[
            Html::a('<i class="glyphicon glyphicon-plus"></i> Crear Inventario','#',
                [
                    'id'=>'inventario-index-link',
                    'class' => 'btn btn-xs btn-success',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'data-url' => Url::to(['create']),
                    'data-pjax' => '0',
                ]
            ),
            Html::a('<i class="glyphicon glyphicon-book"></i> Archivados',['archivados'],
                [
                    'class' => 'btn btn-xs btn-inverse',
                    'data-pjax' => '0',
                ]
            ),
        ]
    ]); ?>
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
        'header' => '<h4 class="modal-title">Registro de Ingresos</h4>',
        'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
    ]);

    echo "<div class='well'></div>";

    Modal::end();
?>

<?php
$script= <<<JS
     $(document).on('click', '#inventario-index-link', (function() {
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
    $(document).on('click', '#inventario-view-link', (function() {
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
    $(document).on('click', '#inventario-delete-link', function() {
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
    $('#modal').removeAttr('tabindex');
JS;

$this->registerJs($script,  $this::POS_END);
?>


