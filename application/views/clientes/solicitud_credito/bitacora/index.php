<input type="hidden" id="id_solicitud_credito" value="<?php echo $datos['id']; ?>">

<div class="w-100 p-5">
    <div class="mb-4">
        <a class="btn btn-success" href="javascript:cargarBitacoraDetalle()">Crear</a>
    </div>

    <div id="contenedor_solicitudes_credito_bitacora"></div>
    <div id="contenedor_solicitudes_credito_bitacora_detalle"></div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    cargarBitacoraDetalle = (id = null) => {
        cargarInterfaz('clientes/solicitud_credito/bitacora/detalle', 'contenedor_solicitudes_credito_bitacora_detalle', {id: id})
    }

    listarSolicitudesCreditoBitacora = () => {
        cargarInterfaz('clientes/solicitud_credito/bitacora/lista', 'contenedor_solicitudes_credito_bitacora')
    }

    $().ready(() => {
        listarSolicitudesCreditoBitacora()
    })
</script>