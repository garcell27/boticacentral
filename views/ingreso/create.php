<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model app\models\Ingreso */

$this->title = 'REGISTRO COMPRA';

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
    'id'=>'form-ingreso',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
]); ?>
<div class="modal-body">
    <?= Form::widget([
        'model'=>$model,
        'form'=>$form,
        'columns'=>2,
        'attributes'=>[
            'idproveedor'=>[
                'type'=>Form::INPUT_WIDGET,
                'widgetClass'=>'\kartik\widgets\Select2', 
                'options'=>[
                    'data'=>$model->getCboProveedores(),
                    'options' => ['placeholder' => 'Elija Proveedor'],
                    'pluginOptions' => ['allowClear' => true],
                ], 
            ],            
            'f_emision'=>[
                'type'=>Form::INPUT_WIDGET, 
                'widgetClass'=>'kartik\datecontrol\DateControl',
                'options'=>[
                    'type'=>DateControl::FORMAT_DATE,
                    'ajaxConversion' => false,
                    'options' => [
                        'removeButton' => false,
                        'pluginOptions' => [
                            'maxDate' => date('Y-m-d'),
                        ]
                    ]
                ]
            ],
            'conigv'=>[
                'type'=>Form::INPUT_RADIO_LIST, 
                'items'=>[1=>'Si', 0=>'No'], 
                'options'=>['inline'=>true]
            ],
            'idcomprobante'=>[
                'type'=>Form::INPUT_WIDGET,
                'widgetClass'=>'\kartik\widgets\Select2', 
                'options'=>[
                    'data'=>$model->getCboComprobante(),
                    'options' => ['placeholder' => 'Elija Comprobante'],
                    'pluginOptions' => ['allowClear' => true],
                ], 
            ],
            'n_comprobante'=>[
                'type'=>Form::INPUT_TEXT,
                 
            ],
            'idbotica'=>[
                'type'=>Form::INPUT_WIDGET,
                'widgetClass'=>'\kartik\widgets\Select2', 
                'options'=>[
                    'data'=>$model->getCboBoticaAlmacen(),
                    'options' => ['placeholder' => 'Elija Botica'],
                    'pluginOptions' => ['allowClear' => true],
                ], 
                 
            ],
        ]
    ])?>
</div>
<div class="modal-footer">
    <?= Html::submitButton('REGISTRAR', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs('
    // obtener la id del formulario y establecer el manejador de eventos
        $("form#form-ingreso").on("beforeSubmit", function(e) {
            var form = $(this);
            $.post(
                form.attr("action")+"?submit=true",
                form.serialize()
            )
            .done(function(result) {
                if(result.message=="ok"){
                     $.notify(result.growl.message,result.growl.options);
                     $("#modal").modal("hide");
                     $.pjax.reload({container:"#compra-grid-pjax"});
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

