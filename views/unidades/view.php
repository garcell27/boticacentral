<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Unidades */

$this->title = $model->idunidad;

?>
<div class="unidades-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->idunidad], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->idunidad], [
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
            'idunidad',
            'descripcion',
            'idproducto',
            'tipo',
            'paraventa',
            'equivalencia',
            'idundprimaria',
            'preciomin',
            'preciomax',
            'preciosug',
        ],
    ]) ?>

</div>
