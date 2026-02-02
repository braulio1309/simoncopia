<div class="modal fade" id="modal_rechazo" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rechazo del pago</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="block">
                    <div class="form-row">
                        <label for="motivo_rechazo_id">Motivo del rechazo *</label>
                        <select id="motivo_rechazo_id" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach($this->configuracion_model->obtener('motivos_rechazo', ['interfaz_id' => 1]) as $motivo_rechazo) echo "<option value='$motivo_rechazo->id'>$motivo_rechazo->nombre</option>"; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="rechazo_comentarios">Notas adicionales <span class="text-muted">(Opcional)</span></label>
                        <textarea id="rechazo_comentarios" class="form-control" rows="4"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" onClick="javascript:rechazarPago(<?php echo $datos['id_recibo']; ?>, true)">Confirmar rechazo</button>
            </div>
        </div>
    </div>
</div>

<script>
     $().ready(function() {
        $('#modal_rechazo').modal({
            backdrop: 'static',
            keyboard: true
        })
    })
</script>