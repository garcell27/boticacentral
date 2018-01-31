<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SugerenciaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
<div class="widget-box widget-color-dark" id="sugerencia-index">
    <div class="widget-header">
        <div class="widget-toolbar">
            <?= Html::a('Agregar', '#', [
                'class' => 'btn btn-success btn-xs widget-vinculado',
                'data-url'=>Url::to(['sugerencia/create','idproducto'=>$producto->idproducto]),
                'data-wmain'=>'#sugerencia-index',
                'data-remoto'=>'#registro-sugerencias'
            ]) ?>
        </div>
        <h3 class="widget-title">SUGERENCIAS DEL PRODUCTO</h3>

    </div>
    <div class="widget-body">
        <div class="widget-main">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute'=>'sugerido',
                        'value'=>function($model){
                            return $model->prodsugerido->descripcion;
                        },
                        'enableSorting'=>false,
                    ],

                    /*['class' => 'yii\grid\ActionColumn'],*/
                ],
            ]); ?>
        </div>
    </div>
</div>
<div id="registro-sugerencias"></div>

