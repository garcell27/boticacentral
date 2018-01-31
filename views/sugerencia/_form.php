<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Sugerencia */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sugerencia-form  widget-body">

    <?php $form = ActiveForm::begin([
        'id'=>'form-sugerencia',
        'type'=>ActiveForm::TYPE_HORIZONTAL
    ]); ?>
    <div class="widget-main">
        <?= $form->field($model,'idproducto')->hiddenInput()->label(false)?>

        <?= $form->field($model, 'sugerido')->widget(Select2::className(),[
            'data' => $model->getCboProductos(),
            'language'=>'es',
            'options' => ['placeholder' => 'Elija Producto'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]) ?>

        <div class="widget-toolbox">
            <?= Html::submitButton($model->isNewRecord ? 'REGISTRAR' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a('Cancela', '#', [
                'class'=>'btn btn-danger cancela-widget-vinculado',
                'data-wmain'=>'#registro-sugerencias',
                'data-wsecondary'=>'#sugerencia-index'
            ])?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs('
    $("form#form-sugerencia").on("beforeSubmit", function(e) {
            var form = $(this);
            $.ajax({
                url:form.attr("action")+"'.($model->isNewRecord?'&':'?').'submit=true",
                type:"post",
                data:form.serialize(),
                success:function(result){
                    if(result.message=="ok"){
                        $("#registro-sugerencias").slideUp("slow");
                        $("#sugerencia-index").html(result.listas).slideDown("slow");
                         $.pjax.reload({container:"#productos-grid-pjax"});
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