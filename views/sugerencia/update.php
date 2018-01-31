<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sugerencia */

$this->title = 'Update Sugerencia: ' . $model->idsugerencia;
$this->params['breadcrumbs'][] = ['label' => 'Sugerencias', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idsugerencia, 'url' => ['view', 'id' => $model->idsugerencia]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sugerencia-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
