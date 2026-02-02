<?php
$movimientos = $this->clientes_model->obtener('clientes_facturas_movimientos', [
    'f350_consec_docto' => $datos['documento_cruce'],
    'f200_nit' => $datos['numero_documento'],
    // 'f351_id_sucursal' => str_pad($datos['id_sucursal'], 3, '0', STR_PAD_LEFT),
]);
?>

<div class="modal fade" id="modal_movimientos" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo "Detalle de las retenciones aplicadas en la factura {$datos['documento_cruce']}"; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="block">
                    <div class="container container--max--xl">
                        <?php if(empty($movimientos)) echo 'La factura no tiene retenciones'; ?>

                        <?php if(!empty($movimientos)) { ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">#</th>
                                        <th scope="col" class="text-center">Código</th>
                                        <th scope="col" class="text-center">Nombre</th>
                                        <th scope="col" class="text-center">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador_movimientos = 1;

                                    foreach ($movimientos as $movimiento) {
                                    ?>
                                        <tr>
                                            <th scope="row" class="text-right"><?php echo $contador_movimientos++; ?></th>
                                            <td><?php echo $movimiento->f253_id; ?></td>
                                            <td><?php echo $movimiento->nombre_homologado; ?></td>
                                            <td><?php echo formato_precio($movimiento->f351_valor_db); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
     $().ready(function() {
        $('#modal_movimientos').modal({
            backdrop: 'static',
            keyboard: true
        })
    })
</script>