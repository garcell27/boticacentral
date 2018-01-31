<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Sugerencia */


$this->params['breadcrumbs'][] = ['label' => 'Sugerencias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="widget-box widget-color-green">
    <div class="widget-header">
        <h3 class="widget-title">REGISTRO DE SUGERENCIA</h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
