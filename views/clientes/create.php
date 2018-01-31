<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Clientes */

$this->title = 'Registro de Cliente';

?>

<div class="modal-header">
    <?= Html::button('<span aria-hidden="true">×</span>',[
        'class' =>'close',
        'data-dismiss'=>'modal',
        'aria-label'=>'Close'
    ])?>
    <h2 class="modal-title"><?=Html::encode($this->title) ?></h2>
</div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
