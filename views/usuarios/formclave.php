<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'ACTUALIZAR CLAVE ('.$usuario->namefull.')';

?>
<div class="modal-header">
    <?= Html::button('<span aria-hidden="true">×</span>',[
        'class' =>'close',
        'data-dismiss'=>'modal',
        'aria-label'=>'Close'
    ])?>
    <h2 class="modal-title"><?=Html::encode($this->title) ?></h2>
</div>
<div class="usuarios-form">

    <?php $form = ActiveForm::begin([
        'id'=>'form-usuario',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
    ]); ?>
    <div class="modal-body">


        <?= $form->field($model, 'idusuario')->hiddenInput(['maxlength' => true])->label(false) ?>

        <?= $form->field($model, 'clave')->passwordInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'nclave')->passwordInput(['maxlength' => true]) ?>


    </div>


    <div class="modal-footer">
        <?= Html::submitButton( 'Actualizar', ['class' => 'btn btn-primary']) ?>
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
                //$.pjax.reload({container:"#clientes-grid-pjax"});
            });
            return false;
        }).on("submit", function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
    ');
    ?>

</div>