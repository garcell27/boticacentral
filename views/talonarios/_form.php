<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Talonarios */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="talonarios-form">

    <?php $form = ActiveForm::begin([
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
    ]); ?>

    <?= $form->field($model, 'idcomprobante')->widget(Select2::className(),[
            'data' => $model->getCboComprobante(),
            'language'=>'es',
            'options' => ['placeholder' => 'Seleccione Comprobante'],
            'pluginOptions' => [
                'allowClear' => true
            ],
    ])?>

    <?= $form->field($model, 'serie')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'numero')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'maxitem')->textInput() ?>

    <?= $form->field($model, 'idbotica')->widget(Select2::className(),[
        'data' => $model->getCboBoticas(),
        'language'=>'es',
        'options' => ['placeholder' => 'Seleccione Botica'],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
