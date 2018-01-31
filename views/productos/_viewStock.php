<?php

use app\models\Stock;



$stocks=Stock::find()->where(['idunidad'=>$model->undpri->idunidad])->all();
?>

<div class="widget-box widget-transparent" >
    <div class="widget-header">
        <h3 class="widget-title">
            STOCKS DEL PRODUCTO
        </h3>
    </div>
    <div class="widget-body">
        <div class="widget-main">
            <?php if(count($stocks)){?>
                <table class="table">
                    <tr>
                        <th>BOTICA</th>
                        <th>DISPONIBLE</th>
                    </tr>
                    <?php foreach($stocks as $stock){?>
                        <tr>
                            <td><?= $stock->botica->nomrazon;?></td>
                            <td class="text-right"><?= number_format($stock->fisico-$stock->bloqueado,2);?></td>
                        </tr>
                    <?php }?>
                </table>
            <?php }else{
                echo "No hay stock de este producto actualmente";
            }?>
        </div>

    </div>
</div>
