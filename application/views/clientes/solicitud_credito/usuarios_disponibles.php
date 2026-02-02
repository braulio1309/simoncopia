<div class="modal fade" id="modal_usuarios_disponibles" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Usuarios en línea disponibles para asignación de solicitudes de crédito</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="block">
                    <?php
                    $usuarios_disponibles = $this->configuracion_model->obtener('usuarios_disponibles');
                    
                    foreach ($usuarios_disponibles as $usuario) {
                        echo "$usuario->razon_social<hr>";
                    }
                    ?>
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
        $('#modal_usuarios_disponibles').modal({
            backdrop: 'static',
            keyboard: true
        })
    })
</script>