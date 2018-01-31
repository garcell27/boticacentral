<?php
use app\models\DetalleTransferencia;

?>
<h4>DETALLE DEL STOCK DEL PRODUCTO (<?= $stock->unidad->descripcion;?>)</h4>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>ALMACEN/BOTICA</th>
            <th>FISICO</th>
            <th>SEPARADO</th>
            <th>BLOQUEADO</th>
            <th>DISPONIBLE</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($stocks as $st):?>
        <tr>
            <td><?= $st->botica->nomrazon;?></td>
            <td class="text-right"><?= $st->fisico;?></td>
            <td class="text-right"><?= $st->separado;?></td>
            <td class="text-right"><?= $st->bloqueado;?></td>
            <td class="text-right"><?= $st->getDisponible();?></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>
<h4>TRANSFERENCIAS PENDIENTES O ENVIADAS</h4>
<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>ALMACEN/BOTICA</th>
        <th>UNIDAD</th>
        <th>CANTIDAD</th>
        <th>STOCK</th>
        <th>ESTADO</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach($consulta as $fila):?>
            <?php
            $filastock =\app\models\Stock::find()->where([
                'idunidad'=>$fila->unidad->producto->undpri->idunidad,
                'idbotica'=>$fila->transferencia->botdestino->idbotica,
            ])->one();
            if($filastock){
                $mistock=$filastock->verStock();
            }else{
                $mistock='<label class="label label-sm label-danger">Vacio</label>';
            }
            ?>
        <tr>

            <td><?= $fila->transferencia->botdestino->nomrazon;?></td>
            <td><?= $fila->unidad->descripcion;?></td>
            <td class="text-right"><?= $fila->cantidad;?></td>
            <td><?= $mistock;?></td>
            <td ><?= $fila->transferencia->getLblEstado();?></td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>
