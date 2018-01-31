<?php

use kartik\grid\GridView;


/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'PRODUCTOS CON UTILIDAD ESTIMADA';
$this->params['breadcrumbs'][] = $this->title;

$columnas=[
    ['class' => 'yii\grid\SerialColumn'],
    [
        'attribute'=>'descripcion',
        'value'=>function($model){
            return $model->descripcion.' ('.$model->undpri->descripcion.')';
        }
    ],
    [
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
        'header'=>'PRECIO COMPRA',
        'value'=>function($model){
            return $model->getPrecioCompraEst();
        },
        'hAlign'=>'right',
    ],
    [
        'header'=>'PRECIO VENTA',
        'value'=>function($model){
            return $model->getPrecioVentaEst();
        },
        'hAlign'=>'right',
    ],
    [
        'header'=>'UTILIDAD',
        'value'=>function($model){
            return number_format($model->getPrecioVentaEst()-$model->getPrecioCompraEst(),4,'.','');
        },
        'hAlign'=>'right',
    ],
    // 'caducidad',


];
?>
<div class="productos-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


 <?= GridView::widget([
        'id' => 'productos-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'resizableColumns'=>true,
        'pjax'=>true,
        'columns' => $columnas,
        'panel' => [
            'heading'=>'<i class="fa fa-line-char"></i> LISTA DE PRODUCTOS',
            'type'=>'success',
            
        ],
        'toolbar'=>[]
    ]); ?>
</div>
