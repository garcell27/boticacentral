<?php
use yii\helpers\Url;
use yii\helpers\Json;
$this->title = 'Auditar';
$this->params['breadcrumbs'][] = $this->title;
$url= Url::to(['comprimir-pedido','id'=>'']);
$convertido=Json::encode($datos);
$porcentaje=number_format(($datos['npedidos']-$datos['npedidosnull'])*100/$datos['npedidos'],2);

function  progresocolor($porcentaje){
    if($porcentaje<25){
        $progcolor='progress-bar-danger';
    }elseif($porcentaje<50){
        $progcolor='progress-bar-warning';
    }else if($porcentaje<100){
        $progcolor='progress-bar-info';
    }else{
        $progcolor='progress-bar-success';
    }
    return $progcolor;
}

?>
<h2>PEDIDOS COMPRIMIDOS </h2>

<div class="progress pos-rel" data-percent="<?=$porcentaje?>%">
    <div class="progress-bar <?= progresocolor($porcentaje)?>"
         style="width:<?=$porcentaje?>%;"></div>
</div>
<h4 ><?= $datos['npedidos'] - $datos['npedidosnull'];?>
    Pedidos comprimidos de <?= $datos['npedidos'];?> Pedidos</h4>
<div class="row">
<?php foreach($datos['detalles'] as $detalle):?>
    <?php if($detalle['pedidos']>0){?>
    <div class="col-md-6">
        <div class="well">
            <h3>Botica <?= $detalle['info']->nomrazon?></h3>
            <?php $porcentaje=number_format(($detalle['pedidos']-$detalle['nulos'])*100/$detalle['pedidos'],2)?>
            <div class="progress pos-rel" data-percent="<?=$porcentaje?>%">
                <div class="progress-bar <?= progresocolor($porcentaje)?>"
                     style="width:<?=$porcentaje?>%;"></div>
            </div>

        </div>
    </div>
    <?php }?>
<?php endforeach;?>
</div>
<?php
$script=<<<JS
    var datos=$convertido;
JS;

$this->registerJs($script,  $this::POS_END);
?>