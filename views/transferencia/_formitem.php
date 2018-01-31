<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Json;
use app\models\Stock;
use yii\helpers\Url;
$listund=[];
$stock=Stock::find()->where([
    'idunidad'=>$producto->undpri->idunidad,
    'idbotica'=>$botica->idbotica,
])->one();
foreach ($producto->undventas as $i=>$und){
    $listund[$i]=$und->attributes;
    $listund[$i]['stock']=$stock->getDispVenta($und->idunidad);
    if($model->isNewRecord){
        if($i==0){
            $undini=$listund[$i];
        }
    }else{
        if($und->idunidad==$model->idunidad){
            $undini=$listund[$i];
        }
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
    <?= $form->field($model, 'idunidad')->dropDownList(
        $producto->getCboUndVentas(),
        [
            'disabled'=>$model->isNewRecord?false:true,
            'onchange'=>'cambiarund()'
        ]
    ) ?>
    <?= $form->field($model, 'cantidad')->input('number',[
        'min'=>0.5,'max'=>$undini['stock'],'step'=>0.5
    ]) ?>
    <?= $form->field($model, 'preciounit')->input('number',[
        'min'=>$undini['preciomin'],
        'step'=>'0.1',
        'max'=>$undini['preciomax'],
        'value'=>$model->isNewRecord?$undini['preciosug']:$model->preciounit,
        'format'=>'number'
    ]) ?>
</div>
<div class="modal-footer">
    <?= Html::submitButton($model->isNewRecord ? 'Registrar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
                $("#'.Html::getInputId($model,'cantidad').'").val(listund[i].stock);                }
                $("#'.Html::getInputId($model,'preciounit').'").attr("value",listund[i].preciosug);
                $("#'.Html::getInputId($model,'preciounit').'").attr("min",listund[i].preciomin);
                $("#'.Html::getInputId($model,'preciounit').'").attr("max",listund[i].preciomax);
                $("#'.Html::getInputId($model,'cantidad').'").attr("max",listund[i].stock);
                
            }
        }
    }
    $("form#form-item").on("beforeSubmit", function(e) {
            var form = $(this);
            $.ajax({
                url:form.attr("action")+"&submit=true",
                type:"post",
                data:form.serialize(),
                success:function(result){
                    if(result.message=="ok"){
                        $.notify(result.growl.message,result.growl.options);                        
                        $("#modal").modal("hide");
                        $.pjax.reload({container:"#lista-detalle"});                         
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