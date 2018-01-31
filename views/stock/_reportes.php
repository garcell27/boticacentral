<?php
use yii\helpers\Html;
$numlab=count($laboratorios);
$pag= ceil($numlab/50);
?>
<div class="row">
<?php for($i=0; $i<$pag;$i++){
    $ini=50*$i+1;
    $fin=$ini+49;
    if($fin>$numlab){
        $fin=$numlab;
    }
?>

    <div class="col-md-3">
        <div class="well">
            <h3 class="text-center">Del <?= $ini?> al <?= $fin?></h3>
            <div class="clearfix">
                <div class="grid2">
                    <?=Html::a('<i class="fa fa-5x fa-file-pdf-o"></i>',['generar-pdf','id'=>$i+1],[
                        'target'=>'_blank',
                        'class'=>'red'
                    ])?>
                </div>
                <div class="grid2">
                    <?=Html::a('<i class="fa fa-5x fa-file-excel-o"></i>',['generar-excel','id'=>$i+1],[
                        'target'=>'_blank',
                        'class'=>'green'
                    ])?>
                </div>
            </div>

        </div>

    </div>

<?php }?>
</div>
<?php

