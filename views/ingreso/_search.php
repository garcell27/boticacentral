<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\IngresoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ingreso-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'idingreso') ?>

    <?= $form->field($model, 'idproveedor') ?>

    <?= $form->field($model, 'idcomprobante') ?>

    <?= $form->field($model, 'n_comprobante') ?>

    <?= $form->field($model, 'tipo') ?>

    <?php // echo $form->field($model, 'f_emision') ?>

    <?php // echo $form->field($model, 'f_registro') ?>

    <?php // echo $form->field($model, 'total') ?>

    <?php // echo $form->field($model, 'conigv') ?>

    <?php // echo $form->field($model, 'total_igv') ?>

    <?php // echo $form->field($model, 'porcentaje') ?>

    <?php // echo $form->field($model, 'idbotica') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
