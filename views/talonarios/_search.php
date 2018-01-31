<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TalonariosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="talonarios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'idtalonario') ?>

    <?= $form->field($model, 'idcomprobante') ?>

    <?= $form->field($model, 'serie') ?>

    <?= $form->field($model, 'numero') ?>

    <?= $form->field($model, 'maxitem') ?>

    <?php // echo $form->field($model, 'idbotica') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
