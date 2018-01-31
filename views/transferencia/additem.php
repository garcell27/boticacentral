<?php
use yii\helpers\Html;

$this->title = 'Agregar a Carrito';
?>
<div class="modal-header">
    <?= Html::button('<span aria-hidden="true">×</span>',[
        'class' =>'close',
        'data-dismiss'=>'modal',
        'aria-label'=>'Close'
    ])?>
    <h2 class="modal-title"><?= Html::encode($this->title) ?></h2>
</div>
<?= $this->render('_formitem', [
    'producto' => $producto,
    'model'=>$model,
    'botica'=>$botica
]) ?>


