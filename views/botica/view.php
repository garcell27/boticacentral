<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Botica */

$this->title = $model->idbotica;
$this->params['breadcrumbs'][] = ['label' => 'Boticas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="botica-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->idbotica], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->idbotica], [
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
            'idbotica',
            'nomrazon',
            'ruc',
            'direccion',
            'idclientecaja',
            'idinventario',
            'idcompped',
            'tipo_almacen',
        ],
    ]) ?>

</div>
