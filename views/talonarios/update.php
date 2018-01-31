<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Talonarios */

$this->title = 'Update Talonarios: ' . $model->idtalonario;
$this->params['breadcrumbs'][] = ['label' => 'Talonarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idtalonario, 'url' => ['view', 'id' => $model->idtalonario]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="talonarios-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
