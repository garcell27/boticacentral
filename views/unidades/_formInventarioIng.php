<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>

    <div class="modal-header">
        <h3 class="modal-title">PRODUCTO: <?= $producto->descripcion;?></h3>
    </div>
<?php $form = ActiveForm::begin([
		'id'   => 'form-unidad',
		'type' => ActiveForm::TYPE_VERTICAL
	]);?>
<div class="modal-body">          
            <?php foreach ($producto->unidades as $i => $unidad):?>            
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <input name="ingreso[<?=$i;?>][equivalencia]" type="hidden" value="<?=$unidad->equivalencia;?>"/>
                        <div class="input-group">
                            <input type="text" class="form-control" name="ingreso[<?=$i;?>][cantidad]">
                            <span class="input-group-addon"><?=$unidad->descripcion?></span>
                        </div>
                    </div>
                </div>

            <?php endforeach;?>
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