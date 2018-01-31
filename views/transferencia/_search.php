<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TransferenciaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transferencia-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'idtransferencia') ?>

    <?= $form->field($model, 'fecha_registro') ?>

    <?= $form->field($model, 'motivo') ?>

    <?= $form->field($model, 'estado') ?>

    <?= $form->field($model, 'idbotorigen') ?>

    <?php // echo $form->field($model, 'idbotdestino') ?>

    <?php // echo $form->field($model, 'origen_conf') ?>

    <?php // echo $form->field($model, 'destino_conf') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
