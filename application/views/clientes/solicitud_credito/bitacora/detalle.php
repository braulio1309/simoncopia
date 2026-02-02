<?php if($datos['id']) $bitacora = $this->clientes_model->obtener('clientes_solicitudes_credito_bitacora', ['id' => $datos['id']]); ?>

<div class="modal fade" id="modal_bitacora" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Gestión de la bitácora</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="block">
                    <div class="w-100">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12">
                                <label for="comentarios">Comentarios *</label>
                                <textarea class="form-control" id="comentarios" rows="4"><?php if(isset($bitacora)) echo $bitacora->observaciones; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php if(!isset($bitacora)) { ?>
                    <button class="btn btn-success" onClick="javascript:guardarBitacora(<?php if(isset($bitacora)) echo $bitacora->id; ?>)">Guardar</button>
                <?php } ?>
    
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    guardarBitacora = async (id = null) => {
        let camposObligatorios = [
            $('#comentarios')
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datos = {
            tipo: 'clientes_solicitudes_credito_bitacora',
            observaciones: $('#comentarios').val(),
            solicitud_id: $('#id_solicitud_credito').val(),
            usuario_id: $('#sesion_usuario_id').val()
        }

        if (id) {
            datos.id = id
            await consulta('actualizar', datos)
        } else {
            console.log(datos)
            await consulta('crear', datos)
        }

        listarSolicitudesCreditoBitacora()
        $('#modal_bitacora').modal('hide')
    }

    $().ready(function() {
        $('#modal_bitacora').modal({
            backdrop: 'static',
            keyboard: true
        })
    })
</script>