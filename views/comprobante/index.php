<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ComprobanteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Comprobantes';
$this->params['breadcrumbs'][] = $this->title;
$columnas                      = [
    ['class' => 'yii\grid\SerialColumn'],
    [
            'attribute' => 'descripcion',
            'value'     => function ($model) {
                    return $model->descripcion.' - '.$model->abreviatura;
            },
    ],
    'tipocompra:boolean',
    'tipoventa:boolean',
    [
        'template' =>'ACCIONES',
        'class' => 'yii\grid\ActionColumn',
        'template'=>'{update} {delete}',
        'buttons'=>[
            'update' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-pencil"></span>','#', [
                    'id' => 'comprobante-index-link',
                    'title' => Yii::t('app', 'Update'),
                    'class' => 'btn btn-info btn-xs',
                    'data-url'  => $url,
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'data-pjax' => '0',
                ]);
            },
            'delete' => function ($url, $model, $key) {
                if($model->validaEliminar()){
                    return Html::a('<span class="fa fa-trash"></span>', '#', [
                        'id'=>'comprobante-delete-link',
                        'class' => 'btn btn-danger btn-xs',
                        'title' => Yii::t('yii', 'Delete'),
                        'delete-url'=>$url,
                        'pjax-container'=>'comprobantes-grid-pjax',
                        'data-pjax' => '0',
                    ]);
                }else{
                    return '';
                }

            },
        ]
    ],
];

?>
<div class="comprobante-index">
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?=GridView::widget([
    'id'               => 'comprobantes-grid',
    'dataProvider'     => $dataProvider,
    'columns'          => $columnas,
    'resizableColumns' => true,
    'pjax'             => true,
    'panel'=>[
        'heading'=>'<i class="fa fa-book"></i> LISTA DE COMPROBANTE'
    ],
    'toolbar'=>[
        Html::a('<i class="ace-icon fa fa-plus"></i> AGREGAR',['create'],
            [
                'id'=>'comprobante-index-link',
                'class' => 'btn btn-xs btn-success',
                'data-toggle' => 'modal',
                'data-target' => '#modal',
                'data-pjax' => '0',
                'data-url'=> Url::to(['create']),
            ]
        )
    ]

]);?>


</div>
<?php
Modal::begin([
    'id' => 'modal',
    'size'=>Modal::SIZE_LARGE,
    'header' => '<h4 class="modal-title">REGISTRO DE COMPROBANTE</h4>',
]);
echo "<div class='well text-center'><i class='fa fa-3x fa-spin fa-spinner'></i> <h2>Cargando Contenido</h2></div>";
Modal::end();
?>
<div id="plantilla" class="hidden"></div>
<?php
$script=<<<JS
    $(document).ready(function(){
        $("#plantilla").html($('#modal .modal-content').html());
    });
    $(document).on("click", "#comprobante-index-link", (function() {
        var indexlink=$(this).data("url");
        $.ajax({
            url:indexlink,
            type:"get",
            beforeSend:function(){
                var html= $("#plantilla").html();
                if($("#modal .modal-content").html()!==html){
                    $("#modal .modal-content").html(html);
                }
            },            
            success:function(data){
                $("#modal .modal-content").html(data);
                $("#modal").modal();
            }        
        });        
    }));
    $(document).on("click", "#comprobante-delete-link", function() {
        var deleteUrl = $(this).attr("delete-url");
        var pjaxContainer = $(this).attr("pjax-container");
        if (confirm('ok?')) {
            $.ajax({
                url: deleteUrl,
                type: "post",
                dataType: "json",
                error: function(xhr, status, error) {
                    alert("Hubo un error: " + xhr.responseText);
                },
            }).done(function(result) {
                if(result.message=="ok"){
                    $.notify(result.growl.message,result.growl.options);                         
                }                
                $.pjax.reload({container: "#" + $.trim(pjaxContainer)});
            });
        
        }                                 
    });
JS;

$this->registerJs($script); ?>

