<?php $consecutivos = $this->contabilidad_model->obtener('comprobantes_contables_tareas_detalle', $datos); ?>

<div class="modal fade" id="modal_consecutivos" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalle de la validación</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="block">
                    <div class="container">
                        <div class="table-responsive">
                            <table class="table-striped table-bordered" id="tabla_comprobantes_detalle" width="100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Consecutivo</th>
                                        <th class="text-center">¿Existe?</th>
                                        <th class="text-center">¿Tiene comprobante?</th>
                                        <th class="text-center">¿Comprobante correcto?</th>
                                        <th class="text-center">Soportes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($consecutivos as $registro) { ?>
                                        <tr>
                                            <td class="text-center"><?php echo $registro->consecutivo_numero; ?></td>
                                            <td class="text-center"><?php echo ($registro->consecutivo_existe) ? 'Sí' : 'No' ; ?></td>
                                            <td class="text-center"><?php echo ($registro->comprobante_existe) ? 'Sí' : 'No' ; ?></td>
                                            <td class="text-center"><?php echo ($registro->comprobante_coincide) ? 'Sí' : 'No' ; ?></td>
                                            <td class="text-center"><?php echo $registro->cantidad_soportes; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
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
        $('#modal_consecutivos').modal({
            backdrop: 'static',
            keyboard: true
        })

        var tablaRecibos = $("#tabla_comprobantes_detalle").DataTable({
            deferRender: true,
            fixedHeader: false,
            info: true,
            language: {
                decimal: ',',
                thousands: '.',
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            ordering: false,
            pageLength: 100,
            paging: true,
            processing: true,
            scrollCollapse: true,
            scroller: true,
            // scrollX: false,
            // scrollY: false,
            searching: true,
            serverSide: false,
            stateSave: false,
        })
    })
</script>