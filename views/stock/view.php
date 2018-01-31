<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Stock */

$this->title = $model->idstock;

?>
<div class="modal-header">
    <?= Html::button('<span aria-hidden="true">×</span>',[
        'class' =>'close',
        'data-dismiss'=>'modal',
        'aria-label'=>'Close'
    ])?>
    <h2 class="modal-title"><?= Html::encode($this->title) ?></h2>
</div>

<div class="modal-body">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'idstock',
            'idunidad',
            'idbotica',
            'fisico',
            'separado',
            'bloqueado',
            'detalle:ntext',
        ],
    ]) ?>

</div>
