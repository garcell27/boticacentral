<?php
use kartik\grid\GridView;

?>

<div id="requerimiento-index">

    <?= GridView::widget([
        'id'=>'requerimiento-grid',
        'dataProvider' => $dataProvider,
        'panelPrefix'=>'widget-box widget-',
        'pjax'=>true,
        'columns'=>[
            [
                'header'=>'PRODUCTO',
                'value'=>function($model){
                    return $model->unidad->producto->descripcion.' ('.$model->unidad->descripcion.')';
                }
            ],
            [
                'header'=>'LABORATORIO',
                'value'=>function($model){
                    return $model->unidad->producto->laboratorio->nombre;
                }
            ],
            [
                'attribute'=>'idbotica',
                'value'=>function($model){
                    return $model->botica->nomrazon;
                }
            ],
            [
                'attribute'=>'minimo',
                'hAlign'=>'right'
            ],
            [
                'header'=>'DISPONIBLE',
                'value'=>function($model){
                    return number_format($model->fisico-$model->bloqueado,2,'.','');
                },
                'hAlign'=>'right'
            ],
            [
                'header'=>'SOLICITAR',
                'value'=>function($model){
                    return number_format($model->minimo - $model->fisico-$model->bloqueado,2,'.','');
                },
                'hAlign'=>'right'
            ]
        ],
        'panel' => [
            'heading'=>'<i class="fa fa-users"></i> LISTA DE INGRESOS',
            'type'=>'info',
        ],
        'toolbar'=>[]
    ]); ?>
</div>