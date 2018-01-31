<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Talonarios */

$this->title = $model->idtalonario;
$this->params['breadcrumbs'][] = ['label' => 'Talonarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="talonarios-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->idtalonario], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->idtalonario], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'idtalonario',
            'idcomprobante',
            'serie',
            'numero',
            'maxitem',
            'idbotica',
        ],
    ]) ?>

</div>
