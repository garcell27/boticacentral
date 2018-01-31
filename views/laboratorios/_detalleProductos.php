<p>
    Este laboratorio tiene <?= count($productos);?> productos asignados
</p>

<?php if(count($productos)){?>
    <table class="table table-bordered table-striped">
        <thead>
            <tr><th>DESCRIPCION</th><th>LABORATORIO</th><th>CATEGORIA</th></tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $producto):?>
                <tr>
                    <td><?=$producto->descripcion?></td>
                    <td><?=$producto->laboratorio->nombre?></td>
                    <td><?=$producto->categoria->descripcion?></td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
<?php }?>