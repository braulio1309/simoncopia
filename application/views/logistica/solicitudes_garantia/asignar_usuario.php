<?php $solicitud = $this->logistica_model->obtener('productos_solicitudes_garantia', ['id' => $datos['id']]); ?>

<div class="modal fade" id="modal_asignar_usuario" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Asignar usuario</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="block">
                    <div class="w-100">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12">
                                <label for="usuario">Usuarios *</label>
                                <select id="usuario" class="form-control">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($this->configuracion_model->obtener("usuarios") as $usuario) echo "<option value='$usuario->id'>$usuario->nombre_completo</option>"; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onClick="javascript:guardarUsuarioAsignado(<?php echo $datos['id']; ?>)">Guardar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php if ($solicitud->usuario_asignado_id) { ?>
    <script>
        $().ready(() => {
            $('#usuario').val('<?php echo $solicitud->usuario_asignado_id; ?>')
        })
    </script>
<?php } ?>

<script>
    guardarUsuarioAsignado = async (id) => {
        let camposObligatorios = [
            $('#usuario')
        ]
        let usuarioSeleccionado = $('#usuario option:selected').text()

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datos = {
            tipo: 'productos_solicitudes_garantia',
            id: id,
            usuario_asignado_id: $('#usuario').val()
        }

        await consulta('actualizar', datos)

        // Creación del registro en bitácora
        await consulta('crear', {
            tipo: 'productos_solicitudes_garantia_bitacora',
            solicitud_id: id,
            usuario_id: $('#sesion_usuario_id').val(),
            observaciones: `Solicitud asignada a ${usuarioSeleccionado}`,
        })

        listarSolicitudesGarantia()
        $('#modal_asignar_usuario').modal('hide')

        mostrarAviso('exito', `Se ha asignado el usuario correctamente`, 5000)
    }

    $().ready(function() {
        $('#modal_asignar_usuario').modal({
            backdrop: 'static',
            keyboard: true
        })

        $('#usuario').select2({
            dropdownParent: $('#modal_asignar_usuario .block'),
            width: '100%'
        })
    })
</script>