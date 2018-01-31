<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Comprobante */
/* @var $form yii\widgets\ActiveForm */
?>


<?php $form = ActiveForm::begin([
		'id'         => 'form-comprobante',
		'type'       => ActiveForm::TYPE_HORIZONTAL,
		'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
	]);?>
<div class="modal-body">
<?=$form->field($model, 'descripcion')->textInput(['maxlength' => true,'class'=>'mayusc'])?>

<?=$form->field($model, 'abreviatura')->textInput(['maxlength' => true,'class'=>'mayusc'])?>
<?= $form->field($model, 'tipocompra')->radioList(['No','Si'],['inline'=>true]) ?>
<?= $form->field($model, 'tipoventa')->radioList(['No','Si'],['inline'=>true]) ?>

</div>
	<div class="modal-footer">
<?=Html::submitButton($model->isNewRecord?'Registrar':'Actualizar', ['class' => $model->isNewRecord?'btn btn-success':'btn btn-primary'])?>
</div>

<?php ActiveForm::end();?>

<?php
$this->registerJs('
    // obtener la id del formulario y establecer el manejador de eventos
        $("form#form-comprobante").on("beforeSubmit", function(e) {
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
                $.pjax.reload({container:"#comprobantes-grid-pjax"});
            });
            return false;
        }).on("submit", function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
    ');
?>