<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Ingreso */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="salida-form">

    <?php $form = ActiveForm::begin([
        'id'=>'form-salida',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
    ]); ?>
    <div class="modal-body">    
        

        <?= $form->field($model, 'motivo')->dropDownList([
            'T'=>'TRANSFERENCIA',
            'R'=>'REGULARIZACION'
        ],['prompt'=>'-SELECCIONE-']) ?>

        

    </div>
    <div class="modal-footer">
        <?= Html::submitButton($model->isNewRecord ? 'REGISTRAR' : 'ACTUALIZAR', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs('
    // obtener la id del formulario y establecer el manejador de eventos
        $("form#form-salida").on("beforeSubmit", function(e) {
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
                $.pjax.reload({container:"#salidas-grid-pjax"});
            });
            return false;
        }).on("submit", function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
    ');
?>