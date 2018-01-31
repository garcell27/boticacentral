<?php
use yii\helpers\Html;
function gruporeporte($mes,$anio){
    $reportanio=$anio;
    switch($mes){
        case 1:
            $grupo=1;
            break;
        case 2:
            $grupo=1;
            break;
        case 3:
            $grupo=1;
            break;
        case 4:
            $grupo=2;
            break;
        case 5:
            $grupo=2;
            break;
        case 6:
            $grupo=2;
            break;
        case 7:
            $grupo=3;
            break;
        case 8:
            $grupo=3;
            break;
        case 9:
            $grupo=3;
            break;
        case 10:
            $grupo=4;
            break;
        case 11:
            $grupo=4;
            break;
        case 12:
            $grupo=4;
            break;
    }
    return [
        'grupo'=>$grupo,
        'anio'=>$reportanio
    ];
}

$this->title='INFORME DEL ALCOHOL';
$defaultgrupo=0;
$defaultanio=2017;
?>
<div class="infoalcohol row">
    <?php foreach($alldata as $reporte):?>
        <?php $tdata=count($reporte['datos'])?>
    <div class="col-md-6">
        <div class="widget-box">
            <div class="widget-header">
                <h4 class="widget-title"><?= $reporte['botica']['nomrazon']?></h4>
            </div>
            <div class="widget-body">
                <div class="widget-main" >
                    <div class="dd">
                    <?php foreach($reporte['datos'] as $indice=>$periodo){
                        $mireporte=gruporeporte($periodo['mes'],$periodo['anio']);
                        if ($defaultgrupo!=$mireporte['grupo'] && $defaultgrupo!=$mireporte['anio']) {
                            $defaultgrupo=$mireporte['grupo'];
                            $defaultanio=$mireporte['anio'];
                            if($indice!=0){echo '</ol>'; }
                                echo Html::a('Reporte Grupal N° '.$defaultgrupo.', año'.$defaultanio,
                                    ['reportegrupal','grupo'=>$defaultgrupo,'anio'=>$defaultanio,'idbotica'=>$reporte['botica']['idbotica']],[
                                        'target'=>'_blank',
                                        'class'=>'btn btn-block btn-info'
                                    ]);
                            ?>

                                     <ol class="dd-list">
                                <?php }

                            ?>
                            <li class="dd-item" data-id="<?=$periodo['mes']?>">
                                <div class="dd-handle">
                                    <?=$periodo['periodo'] ?>
                                    <span class="badge badge-info"><?=$periodo['numdocumentos']?></span>
                                    <span class="label label-inverse arrowed-in-right"><?= $periodo['ntickets']?></span>
                                    <span class="label label-inverse arrowed"><?= $periodo['nboletas']?></span>
                                    <span class="label label-success pull-right">S/ <?=$periodo['ptotal']?></span>
                                </div>

                                <ol class="dd-list">
                                    <?php foreach($periodo['datos'] as $dt){?>
                                        <li class="dd-item">
                                            <div class="dd-handle btn-yellow no-hover">
                                                <?=$dt['producto'] ?>
                                                <span class="badge badge-inverse"><?=$dt['numdocumentos']?></span>
                                                <span class="label label-warning arrowed-in-right"><?= $dt['ntickets']?></span>
                                                <span class="label label-warning arrowed"><?= $dt['nboletas']?></span>
                                                <span class="label label-danger pull-right">S/ <?=$dt['ptotal']?></span>
                                            </div>
                                            <ol class="dd-list">
                                                <?php foreach($dt['detalles'] as $dtll){?>
                                                    <li class="dd-item">
                                                        <div class="dd-handle">
                                                            <?=$dtll['comprobante'] ?>
                                                            <span class="badge badge-warning"><?=$dtll['numdocumentos']?></span>
                                                            <span class="label label-info pull-right">S/ <?=$dtll['ptotal']?></span>
                                                        </div>
                                                        <ol class="dd-list">
                                                            <?php foreach($dtll['detalles'] as $referencias){?>
                                                                <li class="dd-item">
                                                                    <div class="dd-handle">
                                                                        <?=$referencias['usuario'] ?>
                                                                        <span class="badge badge-danger"><?=$referencias['numdocumentos']?></span>
                                                                        <span class="label label-inverse pull-right">S/ <?=$referencias['ptotal']?></span>
                                                                    </div>
                                                                </li>
                                                            <?php }?>
                                                        </ol>
                                                    </li>

                                                <?php }?>
                                            </ol>
                                        </li>

                                    <?php }?>
                                </ol>

                            </li>
                            <?php if($indice==$tdata-1){?></ol><?php }?>
                        <?php }?>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <?php endforeach?>
</div>


<?php

$this->registerJsFile(
    '@web/js/jquery.nestable.min.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);

$script= <<<JS
   jQuery(function($){

				$('.dd').nestable({
                    maxDepth: 0,
                    group: $(this).attr('id')
                });
                $('.dd').nestable('collapseAll');
				$('.dd-handle a').on('mousedown', function(e){
					e.stopPropagation();
				});



	});

JS;
$this->registerJs($script,\yii\web\View::POS_END);
?>