
<div class="w-100 p-5">
    <div class="mb-4">
        <a class="btn btn-success" href="javascript:cargarBitacoraDetalle()">Crear</a>
    </div>

    <div id="contenedor_importaciones_bitacora"></div>
    <div id="contenedor_importaciones_bitacora_detalle"></div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    cargarBitacoraDetalle = (id = null) => {
        cargarInterfaz('importaciones/bitacora/detalle', 'contenedor_importaciones_bitacora_detalle', {id: id})
    }

    listarImportacionesBitacora = () => {
        cargarInterfaz('importaciones/bitacora/lista', 'contenedor_importaciones_bitacora')
    }

    $().ready(() => {
        listarImportacionesBitacora()
    })
</script>