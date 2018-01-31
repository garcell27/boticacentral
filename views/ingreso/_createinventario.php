<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Ingreso */

    $this->title = 'REGISTRO INVENTARIO';

?>

<div class="modal-header">
    <?= Html::button('<span aria-hidden="true">×</span>',[
        'class' =>'close',
        'data-dismiss'=>'modal',
        'aria-label'=>'Close'
    ])?>
    <h2 class="modal-title"><?= Html::encode($this->title) ?></h2>
</div>
<?php $form = ActiveForm::begin([
    'id'=>'form-inventario',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
]); ?>
<div class="modal-body">
    <?= $form->field($model, 'idbotica')->dropDownList($model->getCboInvDisponible(),[
        'prompt'=>'SELECCIONE'
    ]) ?>
</div>
<div class="modal-footer">
    <?= Html::submitButton('REGISTRAR', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs('
    // obtener la id del formulario y establecer el manejador de eventos
        $("form#form-inventario").on("beforeSubmit", function(e) {
            var form = $(this);
            $.post(
                form.attr("action")+"?submit=true",
                form.serialize()
            )
            .done(function(result) {
                if(result.message=="ok"){
                     $.notify(result.growl.message,result.growl.options);
                     $("#modal2").modal("hide");
                     $.pjax.reload({container:"#inventario-grid-pjax"});
                }
                
            });
            return false;
        }).on("submit", function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
    ');
?>

