<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PedidosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pedidos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'idpedido') ?>

    <?= $form->field($model, 'idcliente') ?>

    <?= $form->field($model, 'fecha_registro') ?>

    <?= $form->field($model, 'idcomprobante') ?>

    <?= $form->field($model, 'ndocumento') ?>

    <?php // echo $form->field($model, 'total') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'entregado') ?>

    <?php // echo $form->field($model, 'idbotica') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
