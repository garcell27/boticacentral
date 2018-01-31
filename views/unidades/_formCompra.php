<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;

?>

    <div class="modal-header">
        <h3 class="widget-title">PRODUCTO: <?= $producto->descripcion;?></h3>
    </div>
<?php $form = ActiveForm::begin([
		'id'   => 'form-unidad',
		'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
	]);?>
<div class="modal-body">          
            <?= $form->field($model, 'idunidad')->dropDownList(
                    $model->getCboUnidades($producto->idproducto)) ?>

            <?= $form->field($model, 'cantidad')->textInput() ?>
            <?= $form->field($model, 'costound')->textInput() ?>
    <p>
    <?=Html::submitButton('Agregar', ['class'  => 'btn btn-success'])?>
    <?=Html::a('Cancela', '#', ['class' => 'btn btn-danger', 'id' => 'cancela-det'])?>
    </p>
</div>

<?php ActiveForm::end();?>
<?php
$this->registerJs('
    // obtener la id del formulario y establecer el manejador de eventos
        $("form#form-unidad").on("beforeSubmit", function(e) {
            var form = $(this);
            $.ajax({
                url:form.attr("action")+"&submit=true",
                type:"post",
                data:form.serialize(),
                success:function(result){
                    if(result.message=="ok"){
                        $("#modal").modal("hide");
                        $("#search-prod").val("");    
                        actualizaTabla();                        
                    }
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