<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\select2\Select2;

$this->title = 'Registro de Laboratorio';

?>
<div class="modal-header">
    <?= Html::button('<span aria-hidden="true">×</span>',[
        'class' =>'close',
        'data-dismiss'=>'modal',
        'aria-label'=>'Close'
    ])?>
    <h2 class="modal-title"><?= Html::encode($this->title) ?></h2>
</div>
<div class="proveedores-form">
    
    <?php $form = ActiveForm::begin([
        'id'=>'form-laboratorio',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
    ]); ?>
    
    <div class="modal-body">
        <?= $form->field($model, 'idproveedor')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'idlaboratorio')
            ->widget(Select2::className(),[
                'data' => $model->getCboLaboratorios($model->idproveedor),
                'language'=>'es',
                'options' => ['placeholder' => 'Elija Laboratorio'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]) ?>

    </div>


    <div class="modal-footer">
        <?= Html::submitButton('Asignar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
    $this->registerJs('
    // obtener la id del formulario y establecer el manejador de eventos
        $("form#form-laboratorio").on("beforeSubmit", function(e) {
            var form = $(this);
            $.post(
                form.attr("action")+"?submit=true",
                form.serialize()
            )
            .done(function(result) {
                if(result.message=="ok"){
                     $.notify(result.growl.message,result.growl.options);
                     $("#modal").modal("hide");
                     reload('.$model->idproveedor.');
                }
                
                //$.pjax.reload({container:"#productos-grid-pjax"});
            });
            return false;
        }).on("submit", function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
    ');
    ?>