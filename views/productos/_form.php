<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Productos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="productos-form">

    <?php $form = ActiveForm::begin([
        'id'=>'form-producto',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
    ]); ?>
    <div class="modal-body">
        <?= $form->field($model, 'idcategoria')->widget(Select2::className(),[
            'data' => $model->getCboCategorias(),
            'language'=>'es',
            'options' => ['placeholder' => 'Elija Categoria'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]) ?>

        <?= $form->field($model, 'idlaboratorio')->widget(Select2::className(),[
            'data' => $model->getCboLaboratorios(),
            'language'=>'es',
            'options' => ['placeholder' => 'Elija Laboratorio'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]) ?>

        <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'caducidad')->radioList([
            'No','Si'
        ],['inline'=>true]) ?>

    </div>


    <div class="modal-footer">
        <?= Html::submitButton($model->isNewRecord ? 'REGISTRAR' : 'ACTUALIZAR', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$script= <<<JS

    // obtener la id del formulario y establecer el manejador de eventos
        $("form#form-producto").on("beforeSubmit", function(e) {
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
                $.pjax.reload({container:"#productos-grid-pjax"});
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