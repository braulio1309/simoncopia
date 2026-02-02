<div class="modal fade" id="modal_aprobacion_solicitud_credito" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Aprobación de la solicitud de crédito</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="block">
                    <div class="form-row">
                        <div class="form-group mb-2 alert alert-success col-lg-12" role="alert">
                            Una vez apruebe la solicitud, el cliente se va a crear/actualizar en el ERP Siesa
                        </div>

                        <div class="form-group col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="aprobacion_responsable_iva">¿Responsable de IVA? *</label>
                                <select id="aprobacion_responsable_iva" class="form-control">
                                    <option value="">Selecciona...</option>
                                    <option value="0" data-responsable_iva="49" data-causante_iva="ZY">No</option>
                                    <option value="1" data-responsable_iva="48" data-causante_iva="01">Sí</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-lg-6 col-sm-12">
                            <label for="aprobacion_cupo">Indique el cupo aprobado</label>
                            <input id="aprobacion_cupo" class="form-control" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onClick="javascript:aprobarSolicitudCredito(<?php echo $datos['id']; ?>, true)">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
     $().ready(function() {
        $('#modal_aprobacion_solicitud_credito').modal({
            backdrop: 'static',
            keyboard: true
        })

        $(`#aprobacion_cupo`).on('keyup', function() {
            // Se formatea el campo
            $(this).val(formatearNumero($(this).val()))
        })
    })
</script>