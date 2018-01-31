<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
?>

<div class="box box-solid box-success">
    <div class="box-header">
        <h3 class="box-title">AGREGAR UNIDADES AL INVENTARIO</h3>
    </div>
    <?php $form = ActiveForm::begin([
        'id'=>'form-unidad',
        'type'=>ActiveForm::TYPE_VERTICAL
    ]); ?>
    <div class="box-body">
        <div class="form-group">
            <label class="control-label col-sm-2">Producto</label>
            <div class="col-sm-10">
                <p class="form-control-static">
                    <?= $producto->descripcion;?>
                </p>
            </div>
        </div>
        <?php foreach ($producto->unidades as $i=>$unidad):?>
            
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <input name="ingreso[<?= $i;?>][equivalencia]" type="hidden" value="<?= $unidad->equivalencia;?>"/>
                    <div class="input-group">
                        <input type="text" class="form-control" name="ingreso[<?= $i;?>][cantidad]">
                        <span class="input-group-addon"><?= $unidad->descripcion ?></span>
                    </div>
                </div>
            </div>

        <?php endforeach;?>
    </div>
    <div class="box-footer">
        <?= Html::submitButton('Registrar Inventario', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancela', '#', ['class'=>'btn btn-danger', 'id'=>'cancela-reg-unidad'])?>
    </div>

    <?php ActiveForm::end(); ?>
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
                        $("#registro-unidades").html(result.vista).slideUp("slow");
                        $("#lista-unidades").html(result.listas).slideDown("slow");
                        $("#inventario-unidades-index").remove();
                        actualizastock();                        
                    }
                }
            });
            return false;
        }).on("submit", function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
        function actualizastock(){
            $.ajax({
                url:"'.Url::to(['productos/actualizastock','id'=>$producto->idproducto]).'",
                type:"get",
                success:function(html){
                    $("#producto-stock").html(html);
                }
            });
        }
        
    ');
?>