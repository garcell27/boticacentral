<?php
use kartik\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use kartik\widgets\Alert;
if($model->estado=='A'){
?>
    <?= GridView::widget([
        'id' => 'catalogo-grid',
        'dataProvider' => $dataProvider,
        'showPageSummary'=>true,
        'columns'=>[
            [
                'header'=>'PRODUCTO',
                'value'=>function($model){
                    return $model->unidad->producto->descripcion;
                },
                'pageSummary'=>'TOTAL (S/)',
            ],
            [
                'header'=>'LABORATORIO',
                'value'=>function($model){
                    return $model->unidad->producto->laboratorio->nombre;
                },
                'pageSummary'=>'',
            ],            
            [
                'header'=>'UNIDAD',
                'value'=>function($model){
                    return $model->unidad->descripcion;
                }
            ],
            [
                'attribute'=>'cantidad',
                'enableSorting'=>false,
                'hAlign'=>GridView::ALIGN_RIGHT,
            ],
            [
                'attribute'=>'preciounit',
                'enableSorting'=>false,
                'hAlign'=>GridView::ALIGN_RIGHT,
            ],
            [
                'attribute'=>'subtotal',
                'enableSorting'=>false,
                'hAlign'=>GridView::ALIGN_RIGHT,
                'format'=>['decimal', 2],
                'pageSummary'=>true
            ]

        ],
        'pjax'=>true
    ])?>
    <?php if (Yii::$app->user->identity->idrole<=2){
      echo Html::a('<i class="fa fa-times"></i> ANULAR','#',[
            'class'=>'btn btn-danger',
            'id' => 'anular-venta',
            'data-url'  => Url::to(['anular', 'id'  => $model->idsalida]),
            'data-pjax' => '0',
      ]);  
    }
}else{
    echo Alert::widget([
        'type' => Alert::TYPE_DANGER,
        'title' => 'Venta Anulada',
        'icon' => 'glyphicon glyphicon-exclamation-sign',
        'body' => 'La venta se encuentra anulada',
        'closeButton'=>false,
    ]);
}?>