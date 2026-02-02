<div class="table-responsive">
    <table class="table-striped table-bordered" id="tabla_facturas" width="100%">
        <thead>
            <tr>
                <th class="text-center">NIT</th>
                <th class="text-center">Razon social</th>
                <th class="text-center">Seleccionar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($datos['resultado'] as $cliente) { ?>
                <tr>
                    <td class="text-right"><?php echo $cliente['f200_nit']; ?></td>
                    <td><?php echo $cliente['f200_razon_social']; ?></td>
                    <td class="text-center">
                        <button type="button" onClick="javascript:seleccionarCliente('<?php echo $cliente['f200_nit']; ?>')" class="btn btn-primary">+</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>