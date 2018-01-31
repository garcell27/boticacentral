<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Proveedores */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="proveedores-form">

    <?php $form = ActiveForm::begin([
        'id'=>'form-proveedor',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
    ]); ?>
    <div class="modal-body">
        <?= $form->field($model, 'nomproveedor')->textInput(['maxlength' => true, 'class'=>'mayus']) ?>

        <?= $form->field($model, 'direccion')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tipodoc')->dropDownList(['DNI'=>'DNI','RUC'=>'RUC'],['prompt'=>'SELECCIONE']) ?>

        <?= $form->field($model, 'docidentidad')->textInput(['maxlength' => true]) ?>
    </div>


    <div class="modal-footer">
        <?= Html::submitButton($model->isNewRecord ? 'Registrar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs('
    // obtener la id del formulario y establecer el manejador de eventos
        $("form#form-proveedor").on("beforeSubmit", function(e) {
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
                $.pjax.reload({container:"#proveedores-grid-pjax"});
            });
            return false;
        }).on("submit", function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
    ');
?>