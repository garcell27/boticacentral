<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Stock */

$this->title = 'STOCK MINIMO EN '. $model->botica->nomrazon;;

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
    'id'=>'form-stock',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
]); ?>
<div class="modal-body">

    <?= $form->field($model, 'minimo')->textInput(['maxlength' => true]) ?>

</div>
<div class="modal-footer">
    <?= Html::submitButton( 'Grabar', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>

