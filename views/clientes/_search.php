<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ClientesSearch */
/* @var $form yii\widgets\ActiveForm */
?>



    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'type'=>ActiveForm::TYPE_INLINE,
        'method' => 'get',
    ]); ?>



    <?= $form->field($model, 'nomcliente') ?>


    <div class="form-group">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Limpiar', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

