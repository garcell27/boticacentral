<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BoticaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="botica-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'idbotica') ?>

    <?= $form->field($model, 'nomrazon') ?>

    <?= $form->field($model, 'ruc') ?>

    <?= $form->field($model, 'direccion') ?>

    <?= $form->field($model, 'idclientecaja') ?>

    <?php // echo $form->field($model, 'idinventario') ?>

    <?php // echo $form->field($model, 'idcompped') ?>

    <?php // echo $form->field($model, 'tipo_almacen') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
