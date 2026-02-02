<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Usuarios del sistema</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div>
            <a class="btn btn-success" href="<?php echo site_url('usuarios/registro'); ?>">Crear</a>
        </div>

        <div class="form-row mt-2">
            <div class="form-group col-md-12">
                <input type="text" class="form-control" id="buscar_usuario" placeholder="Buscar por alguna palabra clave" autofocus>
            </div>
        </div>

        <div id="contenedor_usuarios"></div>
    </div>

    <div class="block-space block-space--layout--before-footer"></div>
</div>

<script>
    listarUsuarios = () => {
        // Si no hay valor en la búsqueda, pero si en loca storage, lo pone
        if($("#buscar_usuario").val() == "" && localStorage.simonBolivar_busquedaTercero) $("#buscar_usuario").val(localStorage.simonBolivar_busquedaTercero)

        localStorage.simonBolivar_contador = 0

        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar_usuario").val(),
        }

        cargarInterfaz('configuracion/usuarios/lista', 'contenedor_usuarios', datos)
    }

    $().ready(() => {
        listarUsuarios()

        $("#buscar_usuario").keyup(() => {
            // Se almacena el valor de búsqueda en local storage
            localStorage.simonBolivar_busquedaTercero = $("#buscar_usuario").val()

            // Recarga de la vista
            listarUsuarios()
        })
    })
</script>