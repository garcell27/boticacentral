<?php
use app\assets\AppAssetReport;
AppAssetReport::register($this);
?>

<?php foreach ($laboratorios as $laboratorio):?>

    <h3><?= $laboratorio->nombre?></h3>
    <table class="table" border="1">
        <thead>
            <tr><th>PRODUCTO</th><th>ALMACEN</th><th>YANET</th><th>SALUD</th><th>TOTAL</th></tr>
        </thead>
        <tbody>
        <?php foreach ($laboratorio->productos as $producto):?>
            <?php
            $infoalmacen=$producto->verStock(1);
            $infoyanet=$producto->verStock(2);
            $infosalud=$producto->verStock(3);
            if($infoalmacen['existe']){
                $stockalmacen= $infoalmacen['disponible'];
            }else{
                $stockalmacen= 0;
            }
            if($infoyanet['existe']){
                $stockyanet = $infoyanet['disponible'];
            }else{
                $stockyanet = 0;
            }
            if($infosalud['existe']){
                $stocksalud = $infosalud['disponible'];
            }else{
                $stocksalud = 0;
            }
            $stocktotal=$stockalmacen+$stockyanet+$stocksalud;
            ?>
            <tr>
                <td><?= $producto->descripcion;?> (<?= $producto->undpri->descripcion;?>)</td>
                <td class="right"><?= number_format($stockalmacen,2,'.','');?></td>
                <td class="right"><?= number_format($stockyanet,2,'.','');?></td>
                <td class="right"><?= number_format($stocksalud,2,'.','')?></td>
                <td class="right"><?= number_format($stocktotal,2,'.','')?></td>

            </tr>
        <?php endforeach;?>
        </tbody>

    </table>
<?php endforeach;?>