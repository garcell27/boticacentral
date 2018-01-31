<?php
use Yii;
use yii\helpers\Json;
?>


<?php foreach($datos as $indice=>$fila){?>
    <?php
        $concentracion='';
        $presentacion='';
        $nomcliente='';
        if($fila['detalle']!=null &&$fila['detalle']!==''){
            $detalle=Json::decode($fila['detalle'],true);
            if(isset($detalle['concentracion'])){
                $concentracion=$detalle['concentracion'];
            }
            if(isset($detalle['presentacion'])){
                $presentacion=$detalle['presentacion'];
            }
        }
        if($fila['cliente']!=null){
            $cliente= Json::decode($fila['cliente'],true);
            $nomcliente=$cliente['nomcliente'];
        }
    ?>
    <?php if($indice%10==0){?>
    <table cellspacing="0" border="1">
        <tr>
            <th rowspan="3">Fecha</th>
            <th colspan="6">Alcohol Etílico</th>
            <th colspan="6">Tipo de Salida de Alcohol Etílico</th>
            <th rowspan="3">Establecimiento de Salida</th>
            <th colspan="3">Datos del Adquiriente</th>
            <th rowspan="3">Observaciones</th>
        </tr>
        <tr>
            <th rowspan="2">Denominación</th>
            <th colspan="2">Documento</th>
            <th rowspan="2">Conc %</th>
            <th rowspan="2">Unidad Kg o L</th>
            <th rowspan="2">Presentación Número de envases</th>
            <th colspan="2">Venta</th>
            <th rowspan="2">Salida del País</th>
            <th rowspan="2">Merma (*)</th>
            <th rowspan="2">Pérdida (*)</th>
            <th rowspan="2">Otros</th>
            <th rowspan="2">Apellidos y Nombres o Razón Social</th>
            <th rowspan="2">Registro Único</th>
            <th rowspan="2">Lugar de Entrega</th>
        </tr>
        <tr>
            <th>Tipo</th>
            <th>Número</th>
            <th>Envasado</th>
            <th>A Granel</th>
        </tr>
    <?php }?>
        <tr >
            <td><?= Yii::$app->formatter->asDate($fila['fecha'],'dd-MM-yy')?></td>
            <td class="text-center">ALCOHOL ETILICO</td>
            <td class="text-center"><?=$fila['comprobante']?></td>
            <td class="text-center"><?=$fila['ndocumento']?></td>
            <td class="text-center"><?= $concentracion?></td>
            <td class="text-center">L</td>
            <td class="text-center"><?= ((int)$fila['cantidad']).'<br>'.$presentacion?></td>
            <td class="text-center">X</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-center">MISMO LOCAL BOTICA "<?= $botica->nomrazon?>"</td>
            <td class="text-center"><?=$nomcliente?></td>
            <td></td>
            <td class="text-center">BOTICA</td>
            <td></td>
        </tr>
    <?php if((($indice+1)%10==0) && (count($datos)!=($indice+1))){?>
        </table>
        <pagebreak></pagebreak>
    <?php }?>
    <?php if(count($datos)==($indice+1)){?>
        </table>
    <?php }?>
<?php }?>



