<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Solicitudes de cotización</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div class="row mb-4">
            <div class="col-3">
                <a class="btn btn-success" href="<?php echo site_url('proveedores/solicitudes/crear'); ?>">Crear solicitud</a>
            </div>

            <div class="col-9">
                <button type="button" class="btn btn-success importar">Importar desde archivo plano</button>

                <a type="button" class="btn btn-info" href="<?php echo base_url().'archivos/plantillas/proveedores_importacion_solicitud_cotizacion.xlsx'; ?>" download>Descargar archivo plano</a>

                <a type="button" class="btn btn-primary" href="#" onClick="javascript:copiar_enlace('<?php echo site_url('proveedores'); ?>')"><i class="fa fa-paste"></i> Copiar enlace</a>

                <input type="file" class="d-none" id="importar_archivo" onchange="javascript:importarProductosMetaDatos()" accept=".xlsx,.xls,.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
            </div>
        </div>

        <div id="contenedor_solicitudes"></div>
    </div>

    <div class="block-space block-space--layout--before-footer"></div>
</div>

<script>
    eliminarSolicitudes = async (id) => {
        let confirmacion = await confirmar('Eliminar', `¿Está seguro de eliminar la solicitud de cotización?`)
        
        if (confirmacion) {
            let eliminar = await consulta('eliminar', {tipo: 'proveedores_cotizaciones_solicitudes', id: id})

            if (eliminar) {
                listarSolicitudes()
            }
        }
    }

    listarSolicitudes = () => {
        cargarInterfaz('proveedores/solicitudes/lista', 'contenedor_solicitudes')
    }

    importarProductosMetaDatos = () => {
        Swal.fire({
            title: 'Estamos subiendo el archivo y importando los productos de la solicitud de cotización en nuestros sistemas...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        let archivo = $('#importar_archivo').prop('files')[0]
        let documento = new FormData()
        documento.append("archivo", archivo)

        let subida = new XMLHttpRequest()
        subida.open('POST', `${$("#site_url").val()}proveedores/importar_solicitud_cotizacion`)
        subida.send(documento)
        subida.onload = evento => {
            let respuesta = JSON.parse(evento.target.responseText)

            Swal.close()

            if (respuesta.exito) {
                listarSolicitudes()
                mostrarAviso('exito', `¡${respuesta.mensaje}!`, 20000)
                return false
            } 

            mostrarAviso('error', `¡${respuesta.mensaje}!`, 20000)
        }
    }

    listarProveedoresDisponibles = async solicitudId => {
        let listado = await consulta('obtener', {
            tipo: 'proveedores_disponibles_por_cotizacion',
            id: solicitudId,
        })

        let html = `
        <table style="width:100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th class='text-center' style="border-bottom: 1px solid #ccc;">Proveedor</th>
                    <th class='text-center' style="border-bottom: 1px solid #ccc;">Productos</th>
                </tr>
            </thead>
            <tbody>
                ${listado.resultado.map(item => `
                    <tr>
                        <td>${item.proveedor}</td>
                        <td>${item.cantidad_productos}</td>
                    </tr>
                `).join("")}
            </tbody>
        </table>
        `

        mostrarAviso('info', html, 60000)
    }

    $().ready(() => {
        listarSolicitudes()

        $(".importar").click(() => $("#importar_archivo").trigger('click'))
    })
</script>