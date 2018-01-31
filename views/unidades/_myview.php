<?php
use kartik\helpers\Html;
use yii\helpers\Url;
$error=false;
if($model->tipo=='S' && ($model->equivalencia==0 || $model->equivalencia==1)){
    $error=true;
    $motivo="Dato equivalente erroneo";
}
if($model->paraventa==1 && ($model->preciosug==0 || $model->preciosug==null)){
    $error=true;
    $motivo="Error de Venta";
}
?>
<div class="thumbnail search-thumbnail <?= $error?'box-warning':'box-primary'?>">
    <div class="caption">
        <div class="pull-right">
            <?= Html::a('<i class="fa fa-pencil"></i>','#',[
                'class' => 'btn btn-xs btn-info widget-vinculado',
                'data-url'=>Url::to(['unidades/update','id'=>$model->idunidad]),
                'data-wmain'=>'#lista-unidades',
                'data-remoto'=>'#registro-unidades'
            ])?>
        </div>
        <h4 class="search-title"><?=$model->descripcion;?></h4>
        <p>
            <?=$model->paraventa?"S/ ".$model->preciosug:"-Sin venta-"?><br>
            <?=$model->tipo=='P'?"UND PRIMARIA":"EQUIVALE A ".$model->equivalencia." ".$model->undprimaria->descripcion ?><br>
            <?= $error?'<span class="label label-warning">'.$motivo.'</span>':'<span class="label label-success">No tiene error</span>';?>
        </p>
        
            
        
    </div>
</div>

