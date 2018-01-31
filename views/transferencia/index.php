<?php

use yii\helpers\Html;
use app\widgets\AceGridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TransferenciaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Transferencias';
$this->params['breadcrumbs'][] = $this->title;

$columnas=[
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute'=>'create_at',
            'value'=>function($model){
                return Yii::$app->formatter->asDatetime($model->create_at,'dd/MM/yyyy hh:mm a ');
            }
        ],

        [
            'attribute'=>'idbotorigen',
            'value'=>function($model){
                return $model->botorigen->nomrazon;
            }
        ],
        [
            'attribute'=>'idbotdestino',
            'value'=>function($model){
                return $model->botdestino->nomrazon;
            }
        ],
    [
        'attribute'=>'estado',
        'value'=>function($model){
            return $model->getLblEstado();
        },
        'format'=>'raw'
    ],
        // 'origen_conf',
        // 'destino_conf',
    [
        'header'=>'ACCIONES',
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {delete}',
        'buttons' => [
            'view' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-eye"></span>', $url, [
                    'class' => 'btn btn-primary btn-xs',
                    'title' => Yii::t('app', 'View'),
                    'data-pjax' => '0',
                ]);
            },
            'delete' => function ($url, $model, $key) {
                if(count($model->items)){
                    return '';
                }else{
                    return Html::a('<span class="fa fa-trash"></span>', '#', [
                        'id'=>'transferencia-delete-link',
                        'class' => 'btn btn-danger btn-xs',
                        'title' => Yii::t('yii', 'Delete'),
                        'delete-url'=>$url,
                        'pjax-container'=>'transferencias-grid-pjax',
                        'data-pjax' => '0',
                    ]);
                }

            },
        ]
    ],

];
?>
<div class="transferencia-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= AceGridView::widget([
        'id'=>'transferencias-grid',
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => $columnas,
        'pjax'=>true,
        'panel' => [
            'heading'=>'<i class="fa fa-exchange"></i> LISTA DE TRANSFERENCIAS',
            'type'=>'color-blue',
        ],
        'toolbar'=>[
            Html::a('<i class="glyphicon glyphicon-plus"></i> Registrar Transferencia','#',
                [
                    'id'=>'transferencia-index-link',
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
    'size'=> Modal::SIZE_LARGE,
    'header' => '<h4 class="modal-title">Registro de Transferencia</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
]);

echo "<div class='well text-center'><i class='fa fa-3x fa-spin fa-spinner'></i> <h2>Cargando Contenido</h2></div>";

Modal::end();
?>
<div id="plantilla" class="hidden"></div>
<?php
$script= <<<JS
    $(document).ready(function(){
        $('#plantilla').html($('#modal .modal-content').html());
    });
    $(document).on('click', '#transferencia-index-link', (function() {
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

    $(document).on('click', '#transferencia-delete-link', function() {
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
JS;
$this->registerJs($script);
?>