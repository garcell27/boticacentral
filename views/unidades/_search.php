<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UnidadesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="unidades-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'idunidad') ?>

    <?= $form->field($model, 'descripcion') ?>

    <?= $form->field($model, 'idproducto') ?>

    <?= $form->field($model, 'tipo') ?>

    <?= $form->field($model, 'paraventa') ?>

    <?php // echo $form->field($model, 'equivalencia') ?>

    <?php // echo $form->field($model, 'idundprimaria') ?>

    <?php // echo $form->field($model, 'preciomin') ?>

    <?php // echo $form->field($model, 'preciomax') ?>

    <?php // echo $form->field($model, 'preciosug') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
