<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de beneficios</h1>
        </div>
    </div>
</div>
<div class="block">
    <div class="container container--max--xl">
        <div class="row mb-4">
            <div class="col-3">
                <a class="btn btn-success" href="<?php echo site_url('marketing/beneficios/crear'); ?>">Crear beneficio</a>
            </div>
        </div>
        
        <div id="contenedor_beneficios"></div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>
<script>
    listarBeneficios = () => {
        // Si no hay valor en la búsqueda, pero si en local storage, lo pone
        if($("#buscar_beneficio").val() == "" && localStorage.simonBolivar_busquedaBeneficio) $("#buscar_beneficio").val(localStorage.simonBolivar_busquedaBeneficio)
        localStorage.simonBolivar_contador = 0
        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar_beneficio").val()
        }
        cargarInterfaz('marketing/beneficios/lista', 'contenedor_beneficios', datos)
    }
    $().ready(() => {
        listarBeneficios()
        $("#buscar_beneficio").keyup(() => {
            // Se almacena el valor de búsqueda en local storage
            localStorage.simonBolivar_busquedaBeneficio = $("#buscar_beneficio").val()
            listarBeneficios()
        })
    })
</script>