<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Transferencia */

$this->title = 'Update Transferencia: ' . $model->idtransferencia;
$this->params['breadcrumbs'][] = ['label' => 'Transferencias', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idtransferencia, 'url' => ['view', 'id' => $model->idtransferencia]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="transferencia-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
