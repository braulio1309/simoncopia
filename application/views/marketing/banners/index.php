<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de banners</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div class="row mb-4">
            <div class="col-3">
                <a class="btn btn-success" href="<?php echo site_url('marketing/banners/crear'); ?>">Subir imagen</a>
            </div>
        </div>
        
        <div id="contenedor_banners"></div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    listarBanners = () => {
        // Si no hay valor en la búsqueda, pero si en loca storage, lo pone
        if($("#buscar_banner").val() == "" && localStorage.simonBolivar_busquedaBanner) $("#buscar_banner").val(localStorage.simonBolivar_busquedaBanner)

        localStorage.simonBolivar_contador = 0

        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar_banner").val()
        }

        cargarInterfaz('marketing/banners/lista', 'contenedor_banners', datos)
    }

    $().ready(() => {
        listarBanners()

        $("#buscar_banner").keyup(() => {
            // Se almacena el valor de búsqueda en local storage
            localStorage.simonBolivar_busquedaBanner = $("#buscar_banner").val()

            listarBanners()
        })
    })
</script>