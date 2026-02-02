<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de campañas</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <?php if(isset($permisos) && in_array(['marketing' => 'marketing_campanias_gestionar'], $permisos)) { ?>
            <div class="row mb-4">
                <div class="col-3">
                    <a class="btn btn-success" href="<?php echo site_url('marketing/campanias/crear'); ?>">Crear campaña</a>
                </div>

                <div class="col-9 text-right">
                    <a type="button" class="btn btn-info" href="<?php echo base_url().'archivos/plantillas/marketing_importacion_campanias_contactos.xlsx'; ?>" download>Descargar archivo plano</a>
                </div>
            </div>
        <?php } ?>
        
        <div id="contenedor_campanias"></div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    listarCampanias = () => {
        // Si no hay valor en la búsqueda, pero si en loca storage, lo pone
        if($("#buscar_campania").val() == "" && localStorage.simonBolivar_busquedaCampania) $("#buscar_campania").val(localStorage.simonBolivar_busquedaCampania)

        localStorage.simonBolivar_contador = 0

        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar_campania").val()
        }

        cargarInterfaz('marketing/lista', 'contenedor_campanias', datos)
    }

    $().ready(() => {
        listarCampanias()

        $("#buscar_campania").keyup(() => {
            // Se almacena el valor de búsqueda en local storage
            localStorage.simonBolivar_busquedaCampania = $("#buscar_campania").val()

            listarCampanias()
        })
    })
</script>