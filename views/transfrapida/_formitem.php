<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

$listund=[];
$producto=$stock->unidad->producto;
foreach ($producto->undventas as $i=>$und){
    $listund[$i]=$und->attributes;
    $listund[$i]['stock']=$stock->getDispVenta($und->idunidad);
    if($i==0){
        $undini=$listund[$i];
    }
}
?>

<?php $form = ActiveForm::begin([
    'id'=>'form-item',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
]); ?>
    <div class="modal-body">
        <?= $form->field($producto, 'descripcion')->staticInput() ?>
        <?= $form->field($model, 'idbotica')->dropDownList(
            ArrayHelper::map($boticas,'idbotica','nomrazon'),
            ['onchange'=>'cambiarund()']
        ) ?>
        <?= $form->field($model, 'idunidad')->dropDownList(
            $producto->getCboUndVentas(),
            ['onchange'=>'cambiarund()']
        ) ?>
        <?= $form->field($model, 'cantidad')->input('number',[
            'min'=>0.5,'max'=>$undini['stock'],'step'=>0.5
        ]) ?>
    </div>
    <div class="modal-footer">
        <?= Html::submitButton('Registrar', ['class' => 'btn btn-success']) ?>
    </div>
<?php ActiveForm::end(); ?>

<?php
$this->registerJs('
    var listund='.Json::encode($listund).';
    function cambiarund(){
        var idundsel=$("#'.Html::getInputId($model,'idunidad').'").val();
        for(var i=0; i<listund.length; i++){
            if(listund[i].idunidad==idundsel){
                var valor=$("#'.Html::getInputId($model,'cantidad').'").val();
                if(valor!="" && valor>listund[i].stock){
                    $("#'.Html::getInputId($model,'cantidad').'").val(listund[i].stock);
                }
                $("#'.Html::getInputId($model,'cantidad').'").attr("max",listund[i].stock);

            }
        }
    }
     $("form#form-item").on("beforeSubmit", function(e) {
            var form = $(this);
            $.ajax({
                url:form.attr("action")+"?submit=true",
                type:"post",
                data:form.serialize(),
                success:function(result){
                    if(result.message=="ok"){
                        $.notify(result.growl.message,result.growl.options);
                        $("#modal").modal("hide");
                        $.pjax.reload({container:"#stock-grid"});
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