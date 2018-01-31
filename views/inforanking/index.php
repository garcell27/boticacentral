<?php
use kartik\grid\GridView;


?>

<?= GridView::widget([
    'id' => 'usuarios-grid',
    'dataProvider' => $dataProvider,
    'resizableColumns'=>true,
    'pjax'=>true,
    'panel' => [
        'heading'=>'<i class="fa fa-star"></i> LISTA DE PRODUCTOS RANKEADOS',
        'type'=>'success',
    ],
    'toolbar'=>[],
    'columns'=>[
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute'=>'producto',
            'header'=>'PRODUCTO'
        ],
        [
            'attribute'=>'laboratorio',
            'header'=>'LABORATORIO'
        ],
        [
            'attribute'=>'npedidos',
            'header'=>'N° PEDIDOS',
            'hAlign'=>'right',
        ],
        [
            'attribute'=>'timporte',
            'header'=>'IMPORTE(S/)',
            'hAlign'=>'right',
        ],
        [
            'attribute'=>'tcantidad',
            'header'=>'CANTIDADES',
            'hAlign'=>'right',
        ]

    ]

]); ?>
