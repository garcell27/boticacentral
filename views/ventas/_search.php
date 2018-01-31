<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model app\models\PedidosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ventas-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'type'=>ActiveForm::TYPE_INLINE
    ]); ?>

    <?= $form->field($model, 'fecha_registro')->widget(DateControl::className(),[
        'type'=>DateControl::FORMAT_DATE,
        'ajaxConversion' => false,
        'options' => [
            'removeButton' => false,
            'pluginOptions' => [
                'maxDate' => date('Y-m-d'),
                
            ]
        ]
    ]) ?>
    <div class="form-group">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-xs btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
