<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\helpers\Url;
use app\models\IngresoSearch;
use app\models\DetalleIngreso;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UnidadesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'UNIDADES DEL PRODUCTO';

?>
<div class="widget-box widget-color-purple">
    <div class="widget-header">
        <div class="widget-toolbar">
            <?= Html::a('<i class="ace-icon fa fa-plus"></i>', '#',[
                'class' => 'btn btn-success btn-xs widget-vinculado',
                'data-url'=>Url::to(['unidades/create','idproducto'=>$producto->idproducto]),
                'data-wmain'=>'#lista-unidades',
                'data-remoto'=>'#registro-unidades'
            ]) ?>

            
        </div>
        <h4 class="widget-title">
            <?= Html::encode($this->title) ?>
        </h4>

    </div>
    <div class="widget-body">
        <div class="widget-main">
            <div class="row">
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemOptions' => ['class' => 'col-md-3 col-sm-6'],
                'summaryOptions'=>['class'=>'summary col-xs-12'],
                'itemView' => '_myview',
            ]) ?>
            </div>
        </div>    
    </div>
</div>
