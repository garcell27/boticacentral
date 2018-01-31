<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'USUARIO: '.$model->idusuario;
?>
<div class="modal-header">
    <?= Html::button('<span aria-hidden="true">×</span>',[
        'class' =>'close',
        'data-dismiss'=>'modal',
        'aria-label'=>'Close'
    ])?>
    <h3 class="modal-title"><?= Html::encode($this->title) ?></h3>

</div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'idusuario',
            'username',
            'email:email',
            'namefull',
            [
                'label'=>'ROL',
                'value'=>$model->role->nombre
            ],
            'status',
        ],
    ]) ?>

</div>
    