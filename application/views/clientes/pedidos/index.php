<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de pedidos de los clientes</h1>
        </div>
    </div>
</div>

<div class="pl-5 pr-5">
    <div id="contenedor_pedidos"></div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    listarPedidos = async () => {
        Swal.fire({
            title: 'Estamos sincronizando los pedidos con el WMS...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        // Ejecución del webhook que extrae los datos del WMS
        fetch(`${$("#site_url").val()}webhooks/importar_datos_wms/pedidos`)
            .then(respuesta => respuesta.json())
            .catch(error => console.error(error))
        
        // Ejecución del webhook que extrae los datos del WMS
        fetch(`${$("#site_url").val()}webhooks/importar_datos_wms/pedidos_tracking`)
            .then(respuesta => respuesta.json())
            .then(datos => Swal.close())
            .catch(error => console.error(error))

        cargarInterfaz('clientes/pedidos/lista', 'contenedor_pedidos')
    }

    $().ready(() => {
        listarPedidos()
    })
</script>