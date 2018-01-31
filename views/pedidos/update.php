<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pedidos */

$this->title = 'Update Pedidos: ' . $model->idpedido;

?>
<div class="modal-header">
    <?= Html::button('<span aria-hidden="true">×</span>',[
        'class' =>'close',
        'data-dismiss'=>'modal',
        'aria-label'=>'Close'
    ])?>
<h2 class="modal-title"><?= Html::encode($this->title) ?></h2>
</div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
