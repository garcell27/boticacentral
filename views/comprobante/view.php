<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Comprobante */

$this->title = $model->idcomprobante;
$this->params['breadcrumbs'][] = ['label' => 'Comprobantes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comprobante-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->idcomprobante], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->idcomprobante], [
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
            'idcomprobante',
            'descripcion',
            'abreviatura',
            'tipocompra',
            'tipoventa',
        ],
    ]) ?>

</div>
