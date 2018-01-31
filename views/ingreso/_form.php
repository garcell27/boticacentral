<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Ingreso */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ingreso-form">

    <?php $form = ActiveForm::begin([
        'id'=>'form-ingreso',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
    ]); ?>
    <div class="modal-body">
    <?= $form->field($model, 'idproveedor')->textInput() ?>

    <?= $form->field($model, 'idcomprobante')->textInput() ?>

    <?= $form->field($model, 'n_comprobante')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tipo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'f_emision')->textInput() ?>

    <?= $form->field($model, 'f_registro')->textInput() ?>

    <?= $form->field($model, 'total')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'conigv')->textInput() ?>

    <?= $form->field($model, 'total_igv')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'porcentaje')->textInput() ?>

    <?= $form->field($model, 'idbotica')->textInput() ?>

    <?= $form->field($model, 'estado')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'estado')->textInput(['maxlength' => true]) ?>

    </div>
    <div class="modal-footer">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs('
    // obtener la id del formulario y establecer el manejador de eventos
        $("form#form-ingreso").on("beforeSubmit", function(e) {
            var form = $(this);
            $.post(
                form.attr("action")+"?submit=true",
                form.serialize()
            )
            .done(function(result) {
                if(result.message=="ok"){
                     $.notify(result.growl.message,result.growl.options);
                     $("#modal").modal("hide");
                }
                $.pjax.reload({container:"#ingreso-grid-pjax"});
            });
            return false;
        }).on("submit", function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
    ');
?>