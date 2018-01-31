<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
$mistock=$producto->verStock($inventario->idbotica);
?>

<div class="modal-header">
    <?= Html::button('<span aria-hidden="true">×</span>',[
        'class' =>'close',
        'data-dismiss'=>'modal',
        'aria-label'=>'Close'
    ])?>
    <h3 class="modal-title">PRODUCTO: <?= $producto->descripcion;?></h3>
</div>
<?php $form = ActiveForm::begin([
    'id'   => 'form-detalle',
    'type' => ActiveForm::TYPE_VERTICAL
]);?>
<div class="modal-body">
    <?= $form->field($model,'cantestimada')->hiddenInput(['value'=>$mistock['disponible']])->label(false);?>
    <fieldset>
        <legend>STOCK ACTUAL</legend>
        <p><?= $mistock['infotext'];?></p>
    </fieldset>
    <fieldset>
        <legend>CANTIDAD INVENTARIADA</legend>
        <?php foreach ($producto->unidades as $i => $unidad):?>
            <div class="col-lg-6">
                <div class="form-group">
                    <input name="cantinventariada[<?=$i;?>][equivalencia]" type="hidden" value="<?=$unidad->equivalencia;?>"/>
                    <div class="input-group">
                        <input type="text" class="form-control" name="cantinventariada[<?=$i;?>][cantidad]" autocomplete="off">
                        <span class="input-group-addon"><?=$unidad->descripcion?></span>
                    </div>
                </div>
            </div>
        <?php endforeach;?>
    </fieldset>
    <fieldset>
        <legend>CANTIDAD VENDIDA</legend>
        <?php foreach ($producto->unidades as $i => $unidad):?>
            <div class="col-lg-6">
                <div class="form-group">
                    <input name="cantvendida[<?=$i;?>][equivalencia]" type="hidden" value="<?=$unidad->equivalencia;?>"/>
                    <div class="input-group">
                        <input type="text" class="form-control" name="cantvendida[<?=$i;?>][cantidad]" autocomplete="off">
                        <span class="input-group-addon"><?=$unidad->descripcion?></span>
                    </div>
                </div>
            </div>
        <?php endforeach;?>
    </fieldset>
    <fieldset>
        <legend>OBSERVACIONES</legend>
        <?= $form->field($model,'observaciones')->textarea()->label(false);?>
    </fieldset>
</div>
<div class="modal-footer">
    <?=Html::submitButton('Agregar', ['class'  => 'btn btn-success'])?>
    <?=Html::a('Cancela', '#', ['class' => 'btn btn-danger', 'id' => 'cancela-det', 'data-dismiss'=>'modal',
        'aria-label'=>'Close'])?>
</div>
<?php ActiveForm::end();?>

<?php
$script= <<<JS
    $("form#form-detalle").on("beforeSubmit", function(e) {
            var form = $(this);
            $.ajax({
                url:form.attr("action")+"&submit=true",
                type:"post",
                data:form.serialize(),
                success:function(result){
                    if(result.message=="ok"){
                        location.reload();
                    }
                }
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
