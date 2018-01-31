<?php 
use kartik\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
if(!count($model->detalleIngreso)):
    echo Alert::widget([
        'type' => Alert::TYPE_INFO,
        'body' => 'No contiene ningun detalle agregado',
        'closeButton'=>false,
    ]);
else:?>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>PRODUCTO</th>
                <th>LABORATORIO</th>
                <th>CANTIDAD</th>
                <th>UNIDAD</th>
                <?php if($model->estado=='P'){?><th></th><?php }?>
            </tr>

        </thead>
        <tbody>
            <?php foreach ($model->detalleIngreso as $detalle):?>
                <tr>
                    <td><?= $detalle->unidad->producto->descripcion ?></td>
                    <td><?= $detalle->unidad->producto->laboratorio->nombre ?></td>
                    <td class="text-right"><?= $detalle->cantidad ?></td>
                    <td><?= $detalle->unidad->descripcion ?></td>
                     <?php if($model->estado=='P'){?>
                    <td><?=Html::a('<i class="btn-xs fa fa-trash"></i>','#',[
                         'id'=>'delete-detalle-item',
                         'data-url'=>  Url::to([
                             'ingreso/eliminadetalle',
                             'id'=>$detalle->iddetalle,
                         ]),
                         'class'=>'btn btn-danger btn-xs',
                         'data-tabla'=>'detalle-table-'.$model->idingreso,
                         'data-idingreso'=>$model->idingreso,
                     ]);?></td>
                         <?php }?>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
<?php endif;?>
