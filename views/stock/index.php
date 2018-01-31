<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Stocks';
$this->params['breadcrumbs'][] = $this->title;
$columnas=[
    [
        'attribute'=>'descripcion',
        'value'=>function($model){
            return $model->descripcion.' ('.$model->undpri->descripcion.')';
        },
        'enableSorting'=>false,
        'filterInputOptions'=>['class'=>'form-control mayusc'],
    ],
    [
        'header'=>'LABORATORIO',
        'attribute'=>'idlaboratorio',
        'value'=>function($model){
            return $model->laboratorio->nombre;
        },
        'filterType'=>GridView::FILTER_SELECT2,
        'filter'=> $searchModel->getCboLaboratorios(),
        'filterWidgetOptions'=>[
            'pluginOptions'=>['allowClear'=>true],
        ],
        'filterInputOptions'=>['placeholder'=>'Todas los laboratorios']
    ],
    [
        'header'=>'ALMACEN',
        'value'=>function($model){
            $info=$model->verStock(1);
            if($info['existe']){
                return $info['disponible'];
            }else{
                return 0;
            }

        },
        'hAlign'=> GridView::ALIGN_RIGHT,
        'format'=>['decimal', 2],
    ],
    [
        'header'=>'YANET',
        'value'=>function($model){
            $info=$model->verStock(2);
            if($info['existe']){
                return $info['disponible'];
            }else{
                return 0;
            }

        },
        'hAlign'=> GridView::ALIGN_RIGHT,
        'format'=>['decimal', 2],
    ],
    [
        'header'=>'SALUD',
        'value'=>function($model){
            $info=$model->verStock(3);
            if($info['existe']){
                return $info['disponible'];
            }else{
                return 0;
            }

        },
        'hAlign'=> GridView::ALIGN_RIGHT,
        'format'=>['decimal', 2],
    ],
    [
        'header'=>'TOTAL',
        'value'=>function($model){
            return $model->verAllDisponible();
        },
        'hAlign'=> GridView::ALIGN_RIGHT,
        'format'=>['decimal', 2],
    ],
    [
        'header'=>'ACCIONES',
        'class' => 'yii\grid\ActionColumn',
        'template' => '{verdetalle}',
        'buttons' => [
            'verdetalle' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-line-chart"></span>', $url, [
                    'class' => 'btn btn-success btn-xs',
                    'title' => Yii::t('app', 'View'),
                ]);
            },

        ]
    ],
];

/*$exportConfig=[
    GridView::EXCEL => [
        'label' => 'Excel',
        'icon' => 'file-excel-o',
        'iconOptions' => ['class' => 'text-success'],
        'showHeader' => true,
        'showPageSummary' => true,
        'showFooter' => true,
        'showCaption' => true,
        'filename' =>  'grid-export',
        'alertMsg' =>  'The EXCEL export file will be generated for download.',
        'options' => ['title' => 'Microsoft Excel 95+'],
        'mime' => 'application/vnd.ms-excel',
        'config' => [
            'worksheet' => 'ExportWorksheet',
            'cssFile' => ''
        ]
    ],
];
$export= ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $columnas,
    'target' => ExportMenu::TARGET_BLANK,
    'fontAwesome' => true,
    'showColumnSelector'=>false,
    'exportConfig'=>[
        ExportMenu::FORMAT_TEXT => false,
        ExportMenu::FORMAT_HTML=>false,
        ExportMenu::FORMAT_CSV=>false,
        ExportMenu::FORMAT_EXCEL=>false,

    ],
    'dropdownOptions' => [
        'label' => 'Exportar Informacion',
        'class' => 'btn btn-default btn-xs',

    ],
]);                
*/
?>
<div class="stock-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id'=>'stock-grid',
        'dataProvider' => $dataProvider,
        'filterModel'=>$searchModel,
        'columns' => $columnas,
        //'showPageSummary'=>true,
        'pjax'=>true,
        'panel' => [
            'heading'=>'<i class="fa fa-users"></i> Lista de Stocks</h3>',
            'type'=>'success',
            'before'=> '',
        ],
        /*'autoXlFormat'=>true,
        'export' => [
            'label' => 'Exportar',
            'fontAwesome' => true,
            'showConfirmAlert'=>false,
            'target'=>GridView::TARGET_BLANK
        ],*/
        'toolbar'=>[
           /*Html::a('<i class="fa fa-file-o"></i> Reportes',['reportes'],[
               'data-pjax' => '0',
               'class'=>'btn btn-primary btn-xs'
           ]),*/
        ],
        //'exportConfig'=>$exportConfig,
    ]); ?>
</div>




