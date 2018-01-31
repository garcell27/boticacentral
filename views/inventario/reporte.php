<?php
$suma=0;
$suma2=0;
?>
<h3 class="text-center"><?=$titulo;?></h3>
<table border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>CODIGO</th>
            <th>PRODUCTO</th>
            <th>LABORATORIO</th>
            <th>INVENTARIADO</th>
            <th>VENDIDO</th>
            <th>VALOR INV.</th>
            <?php if($tipo!='conforme'):?>
                <th>DIFERENCIA</th>
                <th>VALOR DIF.</th>
            <?php endif?>

        </tr>
    </thead>
    <tbody>
        <?php foreach($detalles as $item):?>
            <?php
                $valordetalle=($item->cantinventariada+$item->cantvendida)*$item->unidad->preciosug;
                switch($tipo){
                    case 'excedente':
                        $diferencia=$item->cantinventariada+$item->cantvendida-$item->cantestimada;
                        $valordif=$diferencia*$item->unidad->preciosug;
                        $suma2+=$valordif;
                        break;
                    case 'faltante':
                        $diferencia=$item->cantestimada-$item->cantinventariada-$item->cantvendida;
                        $valordif=$diferencia*$item->unidad->preciosug;
                        $suma2+=$valordif;
                        break;
                    default:
                        break;
                }
                $suma+=$valordetalle;
            ?>
            <tr>
                <td class="text-right"><?=$item->unidad->producto->idproducto;?></td>
                <td>
                    <?=$item->unidad->producto->descripcion;?> (<?=$item->unidad->descripcion;?>)
                </td>
                <td>
                    <?=$item->unidad->producto->laboratorio->nombre;?>
                </td>
                <td class="text-right"><?= $item->cantinventariada;?></td>
                <td class="text-right"><?= $item->cantvendida;?></td>
                <td class="text-right">
                    <?= number_format($valordetalle,2)?>
                </td>
            <?php if($tipo!='conforme'):?>
                <td class="text-right"><?= number_format($diferencia,2)?></td>
                <td class="text-right"><?= number_format($valordif,2)?></td>
            <?php endif?>
            </tr>
        <?php endforeach;?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5">TOTAL (S/)</td>
            <td class="text-right"><?=number_format($suma,2);?></td>
        <?php if($tipo!='conforme'):?>
            <td></td>
            <td class="text-right"><?=number_format($suma2,2)?></td>
        <?php endif?>
        </tr>
    </tfoot>
</table>