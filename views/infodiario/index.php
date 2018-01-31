<?php

use kartik\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use yii\helpers\Html;
$this->title='INFORME DIARIO';

print_r($model->getEndDate());
?>
<div class="infodiario row">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
    ]); ?>
    <div class="col-md-4"><?= $form->field($model, 'fecha')->widget(DateControl::className(),[
            'type'=>DateControl::FORMAT_DATE,
            'ajaxConversion' => false,
            'widgetOptions'=>[
                'pluginOptions'=>[
                    'autoclose'=>true,
                    'startDate'=>$model->getStartDate(),
                    'endDate'=>$model->getEndDate(),
                ]
            ]
        ]) ?></div>
    <div class="col-md-6"><?= $form->field($model, 'botica')->dropDownList($model->getCboBotica(),['prompt'=>'SELECCIONE']) ?></div>
    <div class="col-md-2">
        <?= Html::submitButton('CONSULTAR', ['class' => 'btn btn-success btn-sm']) ?>
    </div>


    <?php ActiveForm::end(); ?>
</div>
<hr>
<?php if(count($data)){
    echo $this->render('resultado',[
        'data'=>$data
    ]);
}?>

