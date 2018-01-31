<?php
use kartik\widgets\ActiveForm;
?>
<div class="modal-body">
    <?php
        $form = ActiveForm::begin([
            'id'=>'form-ingreso',
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
        ]);
    ?>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>PRODUCTO</th>
            <th>CANTIDAD</th>
            <th>UNIDAD</th>
        </tr>

        </thead>
        <tbody>
        <?php foreach ($model->detalleIngreso as $detalle):?>
            <tr>
                <td><?= $detalle->unidad->producto->descripcion ?></td>
                <td><?= $detalle->cantidad ?></td>
                <td><?= $detalle->unidad->descripcion ?></td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
</div>