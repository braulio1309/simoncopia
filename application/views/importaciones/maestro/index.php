<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Maestro de Anticipos Proveedores</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div class="mb-4">
            <a class="btn btn-success" href="<?php echo site_url('importaciones/maestro/crear'); ?>">Crear</a>

            
        </div>

        <div id="contenedor_anticipos"></div>
    </div>

    <div class="block-space block-space--layout--before-footer"></div>
</div>

<script>
    eliminarAnticipo = async (id) => {
        let confirmacion = await confirmar('Eliminar', `¿Está seguro de eliminar este registro de anticipo?`)
        if (confirmacion) {
            // AQUÍ se conecta con la tabla que creamos en SQL
            let eliminar = await consulta('eliminar', {tipo: 'importaciones_maestro_anticipos', id: id})

            if (eliminar) {
                listarAnticipos()
            }
        }
    }

    listarAnticipos = () => {
        // Carga la lista en el nuevo contenedor
        // Asegúrate de crear la vista 'lista.php' dentro de la carpeta 'importaciones/maestro_anticipos'
        cargarInterfaz('importaciones/maestro/lista', 'contenedor_anticipos')
    }

    $().ready(() => {
        listarAnticipos()
    })
</script>