<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Pedidos */

$this->title = $model->idpedido;

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
            'idpedido',
            'idcliente',
            'fecha_registro',
            'idcomprobante',
            'ndocumento',
            'total',
            'estado',
            'entregado',
            'idbotica',
        ],
    ]) ?>

</div>
