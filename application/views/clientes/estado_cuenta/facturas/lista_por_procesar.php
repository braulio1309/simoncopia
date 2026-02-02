<?php $recibos_pendientes_por_aplicar = $this->configuracion_model->obtener('recibos', ['documento_numero' => $datos['numero_documento'], 'recibo_estado_id' => 3]); ?>

<div class="modal fade" id="modal_por_procesar" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo "Facturas pendientes por procesar en el ERP"; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="block">
                    <div class="container container--max--xl">
                        <?php if(!empty($recibos_pendientes_por_aplicar)) { ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">#</th>
                                        <th scope="col" class="text-center">Id</th>
                                        <th scope="col" class="text-center">Fecha</th>
                                        <th scope="col" class="text-center">Creador</th>
                                        <th scope="col" class="text-center">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 1;

                                    foreach ($recibos_pendientes_por_aplicar as $recibo) {
                                    ?>
                                        <tr>
                                            <th scope="row" class="text-right"><?php echo $contador++; ?></th>
                                            <td class="text-right"><?php echo $recibo->id; ?></td>
                                            <td><?php echo $recibo->fecha; ?></td>
                                            <td><?php echo $recibo->usuario_creacion; ?></td>
                                            <td class="text-right"><?php echo formato_precio($recibo->valor); ?></td>
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
        $('#modal_por_procesar').modal({
            backdrop: 'static',
            keyboard: true
        })
    })
</script>