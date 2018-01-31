<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InventarioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inventarios Archivados';
$this->params['breadcrumbs'][] = ['label' => 'Inventarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

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
?>
<div class="inventario-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columnas,
        'pjax'=>true,
        'panel' => [
            'heading'=>'<i class="fa fa-adjust"></i> INVENTARIOS ARCHIVADOS',
            'type'=>'info',
        ],
        'toolbar'=>[]
    ]); ?>
</div>
