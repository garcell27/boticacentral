<?php
use app\widgets\AceGridView;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?= AceGridView::widget([
    'id'=>'inventario-grid',
    'dataProvider' => $dpinventario,
    'columns' => [
        [
            'attribute'=>'f_registro',
            'value'=>function($model){
                return Yii::$app->formatter->asDate($model->f_registro,'dd/MM/yyyy');
            }
        ],
        [
            'attribute'=>'n_comprobante',
            'value'=>function($model){
                return $model->comprobante->abreviatura.': '.$model->n_comprobante;
            }
        ],
        [
            'attribute'=>'idbotica',
            'value'=>function($model){
                return $model->botica->nomrazon;
            }
        ],
        [
            'attribute'=>'estado',
            'value'=>function($model){
                return $model->getLabelestado();
            },
            'format'=>'raw'
        ],
        [
            'header'=>'ITEMS',
            'value'=>function($model){
                return count($model->detalleIngreso);
            },
            'hAlign'=>'right',
        ],
        [
            'attribute'=>'total',
            'format'=>['decimal', 2],
            'hAlign'=>'right',
        ],

        [
            'header'=>'ACCIONES',
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {delete}',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('<span class="fa fa-eye"></span>', $url, [
                        'class' => 'btn btn-primary btn-xs',
                        'title' => Yii::t('app', 'View'),
                    ]);
                },
                'delete' => function ($url, $model, $key) {
                    if(count($model->detalleIngreso)){
                        return '';
                    }else{
                        return Html::a('<span class="fa fa-trash"></span>', '#', [
                            'id'=>'ingreso-delete-link',
                            'class' => 'btn btn-danger btn-xs',
                            'title' => Yii::t('yii', 'Delete'),
                            'delete-url'=>$url,
                            'pjax-container'=>'inventario-grid-pjax',
                            'data-pjax' => '0',
                        ]);
                    }

                },
            ]
        ],
    ],
    'pjax'=>true,
    'panel' => [
        'heading'=>'<i class="fa fa-bar-chart"></i> LISTA DE INVENTARIOS',
        'type'=>AceGridView::TYPE_PRIMARY,
    ],
    'toolbar'=>[
        Html::a('<i class="ion-android-add-circle"></i> Aperturar Inventario','#',
            [
                'id'=>'inventario-link',
                'class' => 'btn btn-xs btn-success',
                'data-url' => Url::to(['createinventario']),
                'data-toggle' => 'modal',
                'data-target' => '#modal2',
                'pjax-container'=>'inventario-grid-pjax',
            ]
        )
    ]
]); ?>