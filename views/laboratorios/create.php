<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Laboratorio */

$this->title = 'Registro de Laboratorio';
$this->params['breadcrumbs'][] = ['label' => 'Laboratorios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modal-header">
    <?= Html::button('<span aria-hidden="true">×</span>',[
        'class' =>'close',
        'data-dismiss'=>'modal',
        'aria-label'=>'Close'
    ])?>
    <h2 class="modal-title"><?= Html::encode($this->title) ?></h2>
</div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>


