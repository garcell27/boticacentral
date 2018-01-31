<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="widget-box widget-color-dark">
    <div class="widget-header">
        <h3 class="widget-title">AGREGAR UNIDADES DE SALIDA</h3>
    </div>
<?php $form = ActiveForm::begin([
		'id'   => 'form-unidad',
		'type' => ActiveForm::TYPE_VERTICAL
	]);?>
<div class="widget-body">
        <div class="widget-main">
<?php foreach ($producto->unidades as $i => $unidad):?>
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <input name="salida[<?=$i;?>][equivalencia]" type="hidden" value="<?=$unidad->equivalencia;?>"/>
                        <div class="input-group">
                            <input type="text" class="form-control" name="salida[<?=$i;?>][cantidad]">
                            <span class="input-group-addon"><?=$unidad->descripcion?></span>
                        </div>
                    </div>
                </div>

<?php endforeach;?>
<p>
<?=Html::submitButton('Agregar', ['class'  => 'btn btn-success'])?>
                <?=Html::a('Cancela', '#', ['class' => 'btn btn-danger', 'id' => 'cancela-det-'.$idsalida])?>
</p>
        </div>
    </div>

<?php ActiveForm::end();?>
</div>
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
                        $("#form-add-detalle-'.$idsalida.'").html(result.vista).slideUp("slow");
                        //$("#lista-unidades").html(result.listas).slideDown("slow");
                        actualizaTabla("detalle-table-'.$idsalida.'",'.$idsalida.');                        
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