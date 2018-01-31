<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Transferencia */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transferencia-form">

    <?php $form = ActiveForm::begin([
        'id'=>'form-transferencia',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
    ]); ?>
    <div class="modal-body">

    <?= $form->field($model, 'idbotorigen')->dropDownList($model->getCboOrigen(),[
        'prompt'=>'- Seleccione -',
        'id'=>'cbo-idbotorigen'
    ]) ?>

    <?= $form->field($model, 'idbotdestino')->widget(DepDrop::className(),[
        'pluginOptions'=>[
            'depends'=>['cbo-idbotorigen'],
            'placeholder' => '- Seleccione -',
            'url' => Url::to(['/transferencia/cb-origen'])
        ]
    ]) ?>


    <div class="modal-footer">
        <?= Html::submitButton('Grabar' , ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs('
    // obtener la id del formulario y establecer el manejador de eventos
    
        $("form#form-transferencia").on("beforeSubmit", function(e) {
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
                $.pjax.reload({container:"#transferencias-grid-pjax"});
            });
            return false;
        }).on("submit", function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
    ');
?>