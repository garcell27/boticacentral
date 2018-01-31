<?php
use yii\helpers\Html;

$this->title = 'REGISTRO DE TRANSFERENCIA';
?>
<div class="modal-header">
    <?= Html::button('<span aria-hidden="true">×</span>',[
        'class' =>'close',
        'data-dismiss'=>'modal',
        'aria-label'=>'Close'
    ])?>
    <h2 class="modal-title"><?= Html::encode($this->title) ?></h2>
</div>

<?php
if(count($boticas)){
    echo $this->render('_formitem', [
        'stock' => $stock,
        'model'=>$model,
        'boticas'=>$boticas
    ]);
}else{?>
    <div class="modal-body">
        <div class="alert alert-info">
            Se han realizado las transferencias a todas sus sucursales
        </div>
    </div>
    <div class="modal-footer">
        <?= Html::button('Cerrar', [
            'class' => 'btn btn-danger',
            'data-dismiss'=>'modal',
            'aria-label'=>'Close'
        ]) ?>
    </div>
<?php }?>


