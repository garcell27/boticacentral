<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
if(!$model->isNewRecord){
    $model->numequi=$model->equivalencia;
    $model->undequi="1.00";
}
/* @var $this yii\web\View */
/* @var $model app\models\Unidades */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="unidades-form widget-body">

    <?php $form = ActiveForm::begin([
        'id'=>'form-unidad',
        'type'=>ActiveForm::TYPE_VERTICAL
    ]); ?>

    <div class="widget-main">
        <?= $form->field($model,'idproducto')->hiddenInput()->label(false)?>
        <?= $form->field($model, 'idundprimaria')->hiddenInput()->label(false) ?>
        <?= Form::widget([
            'model'=>$model,
            'form'=>$form,
            'columns'=>3,
            'attributes'=>[       // 2 column layout
                'descripcion'=>['type'=>Form::INPUT_TEXT,'options'=>['class'=>'mayus']],
                'equivalencia'=>[
                    'label'=>'Equivale:',
                    'columns'=>4,
                    'attributes'=>[
                        'numequi'=>[
                            'type'=>Form::INPUT_TEXT,
                            'options'=>['placeholder'=>'N°'],
                            'columnOptions'=>['colspan'=>1],
                        ],
                        'undequi'=>[
                            'type'=>Form::INPUT_DROPDOWN_LIST,
                            'items'=>$model->getCboUndEquiv(),
                            'options'=>['placeholder'=>'N°'],
                            'columnOptions'=>['colspan'=>3],
                        ],
                    ]
                ],
                'paraventa'=>[
                    'type'=>Form::INPUT_RADIO_LIST,  'items'=>[1=>'Si', 0=>'No'],
                    'options'=>[
                        'inline'=>true,
                        'onchange'=>"changeTipoVenta()"
                    ]
                ]

            ]
        ]) ?>
        <?= Form::widget([
            'model'=>$model,
            'form'=>$form,
            'columns'=>3,
            'attributes'=>[
                'preciomin'=>['type'=>Form::INPUT_TEXT,'options'=>['disabled'=>$model->paraventa?false:true]],
                'preciomax'=>['type'=>Form::INPUT_TEXT,'options'=>['disabled'=>$model->paraventa?false:true]],
                'preciosug'=>['type'=>Form::INPUT_TEXT,'options'=>['disabled'=>$model->paraventa?false:true]],
            ]
        ]) ?>

        <div class="widget-toolbox">
            <?= Html::submitButton($model->isNewRecord ? 'Registrar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a('Cancela', '#', [
                'class'=>'btn btn-danger cancela-widget-vinculado',
                'data-wmain'=>'#registro-unidades',
                'data-wsecondary'=>'#lista-unidades'
            ])?>
        </div>


    </div>


    <?php ActiveForm::end(); ?>

</div>
<?php
$enlace=$model->isNewRecord?'&':'?';
$idformpmin='#'.Html::getInputId($model,'preciomin');
$idformpmax='#'.Html::getInputId($model,'preciomax');
$idformpsug='#'.Html::getInputId($model,'preciosug');
$nametipo=Html::getInputName($model,'paraventa');
$script=<<<JS
    // obtener la id del formulario y establecer el manejador de eventos
        $("form#form-unidad").on("beforeSubmit", function(e) {
            var form = $(this);
            $.ajax({
                url:form.attr("action")+"$enlace"+"submit=true",
                type:"post",
                data:form.serialize(),
                beforeSend:function(){
                    $('#registro-unidades .widget-box').append('<div class="widget-box-overlay">'+
                        '<i class="ace-icon loading-icon fa fa-spin fa-spinner fa-2x white"></i>'+
                    '</div>');
                    $('#registro-unidades .widget-box').addClass('position-relative');
                },
                success:function(result){
                    if(result.message=="ok"){
                        //$("#registro-unidades").html(result.vista).delay(5000).slideUp("slow");
                        $("#registro-unidades").slideUp("slow");
                        $("#lista-unidades").html(result.listas).slideDown("slow");
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

        function changeTipoVenta(){
            var valor= parseInt($("input[name='$nametipo']:checked").val());
            if(valor==1){
                $("$idformpmin").removeAttr("disabled");
                $("$idformpmax").removeAttr("disabled");
                $("$idformpsug").removeAttr("disabled");
            }else{
                $("$idformpmin").attr("disabled","disabled");
                $("$idformpmax").attr("disabled","disabled");
                $("$idformpsug").attr("disabled","disabled");
            }

        }
JS;

$this->registerJs($script);
?>
