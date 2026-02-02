<input type="hidden" id="id_solicitud_garantia" value="<?php echo $datos['solicitud_garantia']['id']; ?>">

<div class="w-100 p-5">
    <div class="mb-4">
        <a class="btn btn-success" href="javascript:cargarBitacoraDetalle()">Crear</a>
    </div>

    <div id="contenedor_solicitudes_garantia_bitacora"></div>
    <div id="contenedor_solicitudes_garantia_bitacora_detalle"></div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    cargarBitacoraDetalle = (id = null) => {
        cargarInterfaz('logistica/solicitudes_garantia/bitacora/detalle', 'contenedor_solicitudes_garantia_bitacora_detalle', {id: id})
    }

    listarSolicitudesGarantiBitacora = () => {
        cargarInterfaz('logistica/solicitudes_garantia/bitacora/lista', 'contenedor_solicitudes_garantia_bitacora')
    }

    $().ready(() => {
        listarSolicitudesGarantiBitacora()
    })
</script>