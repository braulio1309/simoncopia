<?php if($datos['id']) $bitacora = $this->importaciones_model->obtener('importaciones_bitacora', ['id' => $datos['id']]); ?>

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
                <button class="btn btn-success" onClick="javascript:guardarBitacora(<?php if(isset($bitacora)) echo $bitacora->id; ?>)">Guardar</button>
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
            tipo: 'importaciones_bitacora',
            observaciones: $('#comentarios').val(),
            importacion_id: $('#importacion_id').val(),
            usuario_id: $('#sesion_usuario_id').val()
        }

        if (id) {
            datos.id = id
            await consulta('actualizar', datos)
        } else {
            await consulta('crear', datos)
        }

        listarImportacionesBitacora()
        $('#modal_bitacora').modal('hide')
    }

    $().ready(function() {
        $('#modal_bitacora').modal({
            backdrop: 'static',
            keyboard: true
        })
    })
</script>