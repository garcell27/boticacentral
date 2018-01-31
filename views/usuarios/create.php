<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'REGISTRO DE USUARIOS';

?>
<div class="modal-header">
    <?= Html::button('<span aria-hidden="true">×</span>',[
        'class' =>'close',
        'data-dismiss'=>'modal',
        'aria-label'=>'Close'
    ])?>
    <h2 class="modal-title"><?=Html::encode($this->title) ?></h2>
</div>
<?php $form = ActiveForm::begin([
    'id'=>'form-usuario',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
]); ?>
    <div class="modal-body">
        <?= $form->field($model, 'username') ?>

        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'namefull') ?>
        <?= $form->field($model, 'idrol')->dropDownList($model->cboRol(),['prompt'=>'Seleccione']) ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
    </div>
    <div class="modal-footer">
        <?= Html::submitButton('Registrar', ['class' => 'btn btn-success']) ?>
    </div>

<?php ActiveForm::end(); ?>
<?php
$this->registerJs('
    // obtener la id del formulario y establecer el manejador de eventos
        $("form#form-usuario").on("beforeSubmit", function(e) {
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
                $.pjax.reload({container:"#usuarios-grid-pjax"});
            });
            return false;
        }).on("submit", function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
    ');
?>
