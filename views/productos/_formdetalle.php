<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\select2\Select2;

$this->title = 'Detalle Producto: ' . $model->idproducto;
?>
<div class="modal-header">
    <?= Html::button('<span aria-hidden="true">×</span>',[
        'class' =>'close',
        'data-dismiss'=>'modal',
        'aria-label'=>'Close'
    ])?>
    <h2 class="modal-title">
        <?= Html::encode($this->title) ?>
    </h2>
</div>

<div class="detalle-producto-form">
    <h3 class="text-center"><?= $model->descripcion;?></h3>
    <?php $form = ActiveForm::begin([
        'id'=>'form-detalleproducto',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
    ]); ?>
    <div class="modal-body">
        <?= $form->field($detalle, 'informacion')->textarea() ?>
        <?= $form->field($detalle, 'presentacion')->textInput(['maxlength' => true]) ?>
        <?= $form->field($detalle, 'concentracion')->textInput(['maxlength' => true]) ?>
    </div>


    <div class="modal-footer">
        <?= Html::submitButton( 'GRABAR', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$script= <<<JS

    // obtener la id del formulario y establecer el manejador de eventos
        $("form#form-detalleproducto").on("beforeSubmit", function(e) {
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
                //$.pjax.reload({container:"#productos-grid-pjax"});
            });
            return false;
        }).on("submit", function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
JS;

$this->registerJs($script);
?>