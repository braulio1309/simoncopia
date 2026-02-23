<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de pedidos</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        
        <div id="contenedor_pedidos"></div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    listarPedidos = () => {
        // Si no hay valor en la búsqueda, pero si en loca storage, lo pone
        if($("#buscar_pedido").val() == "" && localStorage.simonBolivar_busquedaPedido) $("#buscar_pedido").val(localStorage.simonBolivar_busquedaPedido)

        localStorage.simonBolivar_contador = 0

        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar_pedido").val()
        }

        cargarInterfaz('logistica/pedidos/lista', 'contenedor_pedidos', datos)
    }

    $().ready(() => {
        listarPedidos()

        $("#buscar_pedido").keyup(() => {
            // Se almacena el valor de búsqueda en local storage
            localStorage.simonBolivar_busquedaPedido = $("#buscar_pedido").val()

            listarPedidos()
        })
    })
</script>