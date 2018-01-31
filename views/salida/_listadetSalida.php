<?php 
use kartik\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
if(!count($model->detalleSalida)):
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
                <th></th>
            </tr>

        </thead>
        <tbody>
            <?php foreach ($model->detalleSalida as $detalle):?>
                <tr>
                    <td><?= $detalle->unidad->producto->descripcion ?></td>
                    <td><?= $detalle->unidad->producto->laboratorio->nombre ?></td>
                    <td><?= $detalle->cantidad ?></td>
                    <td><?= $detalle->unidad->descripcion ?></td>                    
                    <td><?=Html::a('<i class="btn-xs fa fa-trash"></i>','#',[
                         'id'=>'delete-detalle-item',
                         'data-url'=>  Url::to([
                             'salida/eliminadetalle',
                             'id'=>$detalle->iddetallesalida,
                         ]),
                         'class'=>'btn btn-danger btn-xs',
                         'data-tabla'=>'detalle-table-'.$model->idsalida,
                         'data-idsalida'=>$model->idsalida,
                     ]);?></td>
                        
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
<?php endif;?>
