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
                <th>UNIDAD</th>
                <th>CANTIDAD</th>
                <th>COSTO</th>
                <th>SUBTOTAL</th>
                <th></th>
            </tr>

        </thead>
        <tbody>
            <?php foreach ($model->detalleIngreso as $detalle):?>
                <tr>
                    <td><?= $detalle->unidad->producto->descripcion ?></td>
                    <td><?= $detalle->unidad->producto->laboratorio->nombre ?></td>
                    <td><?= $detalle->unidad->descripcion ?></td>
                    <td class="text-right"><?= $detalle->cantidad ?></td>
                    <td class="text-right"><?= $detalle->costound ?></td>
                    <td class="text-right"><?= $detalle->subtotal ?></td>

                    <td>
                        <?php if($model->estado=='P'){?>
                            <?=Html::a('<i class="btn-xs fa fa-trash"></i>','#',[
                                 'id'=>'delete-detalle-item',
                                 'data-url'=>  Url::to([
                                     'ingreso/eliminadetalle',
                                     'id'=>$detalle->iddetalle,
                                 ]),
                                 'class'=>'btn btn-danger btn-xs',
                                 'data-tabla'=>'detalle-table-'.$model->idingreso,
                                 'data-idingreso'=>$model->idingreso,
                             ]);?>
                        <?php }elseif($model->botica->tipo_almacen==1){?>
                            <?= Html::a('<i class="btn-xs fa fa-pencil"></i>','#',[
                                'id'=>'update-detalle-item',
                                'data-url'=>  Url::to([
                                    'ingreso/updatedetalle',
                                    'id'=>$detalle->iddetalle,
                                ]),
                                'class'=>'btn btn-info btn-xs',
                                'data-tabla'=>'detalle-table-'.$model->idingreso,
                                'data-idingreso'=>$model->idingreso,
                            ])?>
                        <?php }?>
                    </td>

                </tr>
            <?php endforeach;?>
        
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5">SUBTOTAL</th>
                <th class="text-right"><?= $model->total-$model->total_igv;?></th>
                <th></th>
            </tr>
            <tr>
                <th colspan="5">IGV (<?= $model->porcentaje*100?> %)</th>
                <th class="text-right"><?=$model->total_igv;?></th>
               <th></th>
            </tr>
            <tr>
                <th colspan="5">TOTAL</th>
                <th class="text-right"><?=$model->total?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
<?php endif;?>
