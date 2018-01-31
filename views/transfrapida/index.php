<?php
/* @var $this yii\web\View */
use app\widgets\AceGridView;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;

$this->title = 'TRANSFERENCIA RAPIDA';
$this->params['breadcrumbs'][] = $this->title;
$columnas=[
    [
        'class'=>'kartik\grid\ExpandRowColumn',
        'width'=>'50px',
        'value'=>function ($model, $key, $index, $column) {
            return AceGridView::ROW_COLLAPSED;
        },
        'detailUrl'=>Url::to(['detalletransferencia']),
        'headerOptions'=>['class'=>'kartik-sheet-style'],
        'expandOneOnly'=>true,
        'expandIcon'=>'<span class="ace-icon fa fa-angle-double-down green"></span>',
        'collapseIcon'=>'<span class="ace-icon fa fa-angle-double-up red"></span>',
    ],
    [
        'header'=>'PRODUCTO',
        'value'=>function($model){
            return $model->unidad->producto->descripcion;
        },
    ],
    [
        'header'=>'LABORATORIO',
        'value'=>function($model){
            return $model->unidad->producto->laboratorio->nombre;
        }
    ],
    [
        'header'=>'DISPONIBLE',
        'value'=>function($model){
            return $model->verStock();
        },
        'format'=>'raw'
    ],
    [
        'header'=>'ACCIONES',
        'class' => 'yii\grid\ActionColumn',
        'template' => '{transferir}',
        'buttons' => [
            'transferir' => function ($url, $model, $key) {
                if($model->getDisponible()>0){
                    return Html::a('<span class="fa fa-exchange"></span>', '#', [
                        'id'=>'transferencia-index-link',
                        'class' => 'btn btn-primary btn-xs',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                        'data-pjax' => '0',
                        'data-url'=>$url,
                    ]);
                }else{
                    return '';
                }

            },
        ],
    ]
];

?>

<div class="stock-index">
    <?= AceGridView::widget([
        'id'=>'stock-grid',
        'dataProvider' => $dataprovider,
        'columns' => $columnas,
        'pjax'=>true,
        'panel' => [
            'heading'=>'<i class="fa fa-exchange"></i> Lista de Stocks</h3>',
            'type'=>AceGridView::TYPE_SUCCESS,
            'before'=> '',
        ],

        'toolbar'=>[],
    ]); ?>
</div>

<?php
Modal::begin([
    'id' => 'modal',
    'size'=> Modal::SIZE_DEFAULT,
    'header' => '<h4 class="modal-title">Registro de Transferencia</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
]);

echo "<div class='well text-center'><i class='fa fa-3x fa-spin fa-spinner'></i> <h2>Cargando Contenido</h2></div>";

Modal::end();
?>
<div id="plantilla" class="hidden"></div>

<?php
$script=<<<JS
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
JS;

$this->registerJs($script);
?>