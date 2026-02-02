<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Contactos telefónicos</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div>
            <a class="btn btn-success" href="<?php echo site_url('configuracion/contactos/crear'); ?>">Importar contactos</a>
        </div>

        <div class="form-row mt-2">
            <div class="form-group col-md-12">
                <input type="text" class="form-control" id="buscar_contacto" placeholder="Buscar por alguna palabra clave" autofocus>
            </div>
        </div>

        <div id="contenedor_contactos"></div>
    </div>

    <div class="block-space block-space--layout--before-footer"></div>
</div>

<script>
    eliminarContacto = async(id) => {
        let confirmacion = await confirmar('Eliminar', `¿Está seguro de eliminar el contacto?`)
        
        if (confirmacion) {
            let eliminar = await consulta('eliminar', {tipo: 'terceros_contactos', id: id})

            if(eliminar) {
                agregarLog(39, `Registro con id ${id}`)

                listarContactos()
            }
        }
    }

    listarContactos = () => {
        // Si no hay valor en la búsqueda, pero si en loca storage, lo pone
        if($("#buscar_contacto").val() == "" && localStorage.simonBolivar_busquedaContacto) $("#buscar_contacto").val(localStorage.simonBolivar_busquedaContacto)

        localStorage.simonBolivar_contador = 0

        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar_contacto").val(),
        }

        cargarInterfaz('configuracion/contactos/lista', 'contenedor_contactos', datos)
    }

    $().ready(() => {
        listarContactos()

        $("#buscar_contacto").keyup(() => {
            // Se almacena el valor de búsqueda en local storage
            localStorage.simonBolivar_busquedaContacto = $("#buscar_contacto").val()

            // Recarga de la vista
            listarContactos()
        })
    })
</script>