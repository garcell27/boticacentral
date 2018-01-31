<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Botica */
/* @var $form yii\widgets\ActiveForm */
?>



    <?php $form = ActiveForm::begin([
        'id'=>'form-botica',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
    ]); ?>
    <div class="modal-body">
        <?= $form->field($model, 'nomrazon')->textInput(['maxlength' => true, 'class'=>'mayusc']) ?>
        <?= $form->field($model, 'ruc')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'direccion')->textInput(['maxlength' => true]) ?>
        <?php if($model->isNewRecord):?>
            <?= $form->field($model, 'idclientecaja')->dropDownList($model->getCboclicaja()) ?>
            <?= $form->field($model, 'idinventario')->dropDownList($model->getCboprovinv()) ?>
            <?= $form->field($model, 'tipo_almacen')->dropDownList(['Punto de Venta','Almacen'],['prompt'=>'SELECCIONE']) ?>
            <?= $form->field($model, 'idcompped')->dropDownList($model->getCbocomprobante(),['prompt'=>'SELECCIONE']) ?>
        <?php endif;?>
    </div>


    <div class="modal-footer">
        <?= Html::submitButton($model->isNewRecord ? 'Registrar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

 <?php
$this->registerJs('
    // obtener la id del formulario y establecer el manejador de eventos
        $("form#form-botica").on("beforeSubmit", function(e) {
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
                $.pjax.reload({container:"#boticas-grid-pjax"});
            });
            return false;
        }).on("submit", function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
    ');
?>
