<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Talonarios */

$this->title = 'Create Talonarios';
$this->params['breadcrumbs'][] = ['label' => 'Talonarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="talonarios-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
