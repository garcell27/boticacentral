<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Ingreso */

$this->title = 'Actualizar Ingreso: ' . $model->idingreso;

?>
<div class="modal-header">
    <?= Html::button('<span aria-hidden="true">×</span>',[
        'class' =>'close',
        'data-dismiss'=>'modal',
        'aria-label'=>'Close'
    ])?>
    <h2 class="modal-title"><?= Html::encode($this->title) ?></h2>
</div>
<div class="modal-body">
    <?php
    if($model->tipo=='I'){
        echo $this->render('_formdetIngreso', [
            'model' => $model,
        ]);
    }else{

    }
    ?>
</div>
