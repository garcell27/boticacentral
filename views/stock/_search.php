<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\StockSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stock-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'idstock') ?>

    <?= $form->field($model, 'idunidad') ?>

    <?= $form->field($model, 'idbotica') ?>

    <?= $form->field($model, 'fisico') ?>

    <?= $form->field($model, 'separado') ?>

    <?php // echo $form->field($model, 'bloqueado') ?>

    <?php // echo $form->field($model, 'detalle') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
