<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de pagos de importaciones</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        
        <div id="contenedor_importaciones_pagos"></div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    listarImportacionesPagos = () => {
        // Si no hay valor en la búsqueda, pero si en loca storage, lo pone
        if($("#buscar_importaciones_pagos").val() == "" && localStorage.simonBolivar_busquedaImportacionPago) $("#buscar_importaciones_pagos").val(localStorage.simonBolivar_busquedaImportacionPago)

        localStorage.simonBolivar_contador = 0

        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar_importaciones_pagos").val()
        }

        cargarInterfaz('importaciones_pagos/lista', 'contenedor_importaciones_pagos', datos)
    }

    $().ready(() => {
        listarImportacionesPagos()

        $("#buscar_importaciones_pagos").keyup(() => {
            // Se almacena el valor de búsqueda en local storage
            localStorage.simonBolivar_busquedaImportacionPago = $("#buscar_importaciones_pagos").val()

            listarImportacionesPagos()
        })
    })
</script>