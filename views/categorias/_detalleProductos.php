<?php
use yii\helpers\Html;
?><div class="widget-box" id="det-prod-<?= $model->idcategoria?>">
    <div class="widget-header">
        <div class="widget-toolbar">
            <?= count($model->productos)?> PRODUCTOS
        </div>
        <h5 class="widget-title">DETALLES DE LA CATEGORIA</h5>
    </div>
    <div class="widget-body">
        <div class="main">
            <?php if(count($model->productos)){?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr><th>ID</th><th>DESCRIPCION</th><th>LABORATORIO</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($model->ultproductos as $producto):?>
                            <tr>
                                <td><?=$producto->idproducto?></td>
                                <td><?=$producto->descripcion?></td>
                                <td><?=$producto->laboratorio->nombre?></td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                    <?php if(count($model->productos)>10){?>
                    <tfoot>
                        <tr>
                            <td class="center" colspan="3">
                                <?= Html::a('Ver mas', ['/productos'])?>
                            </td>
                        </tr>
                    </tfoot>
                    <?php }?>
                </table>
            <?php }?>
        </div>
    </div>
</div>
