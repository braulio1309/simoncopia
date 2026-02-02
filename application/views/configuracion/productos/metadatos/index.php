<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Metadatos de productos</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xxl">
        <div class="row mb-4">
            <div class="col-3">
                <a class="btn btn-success" href="<?php echo site_url('configuracion/productos/crear'); ?>">Agregar metadatos</a>
            </div>

            <div class="col-9">
                <button type="button" class="btn btn-success importar">Importar desde archivo plano</button>
                <input type="file" class="d-none" id="importar_archivo" onchange="javascript:importarProductosMetaDatos()" accept=".xlsx,.xls,.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                <a type="button" class="btn btn-info" href="<?php echo base_url().'archivos/plantillas/productos_metadatos.xlsx'; ?>" download>Descargar archivo plano</a>
            </div>
        </div>

        <div id="contenedor_productos_metadatos"></div>
    </div>

    <div class="block-space block-space--layout--before-footer"></div>
</div>

<script>
    recortarTexto = (texto, textoLimite = 50) => {
        let textoRecortado = texto.slice(0, textoLimite)
        textoRecortado += (texto.length > textoLimite ? "..." : "")

        return textoRecortado
    }

    eliminarProductoMetaDatos = async (id) => {
        let confirmacion = await confirmar('Eliminar', `¿Está seguro de eliminar el metadato del producto?`)
        
        if (confirmacion) {
            let eliminar = await consulta('eliminar', {tipo: 'productos_metadatos', id: id})

            if (eliminar) {
                listarProductosMetaDatos()
            }
        }
    }

    importarProductosMetaDatos = () => {
        Swal.fire({
            title: 'Estamos subiendo el archivo y importando los metadatos de productos en nuestros sistemas...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        let archivo = $('#importar_archivo').prop('files')[0]
        let documento = new FormData()
        documento.append("archivo", archivo)

        let subida = new XMLHttpRequest()
        subida.open('POST', `${$("#site_url").val()}configuracion/subir`)
        subida.send(documento)
        subida.onload = evento => {
            let respuesta = JSON.parse(evento.target.responseText)

            Swal.close()

            if (respuesta.exito) {
                listarProductosMetaDatos()
                mostrarAviso('exito', `¡${respuesta.mensaje}!`, 20000)
                return false
            } 

            mostrarAviso('error', `¡${respuesta.mensaje}!`, 20000)
        }
    }

    listarProductosMetaDatos = () => {
        cargarInterfaz('configuracion/productos/metadatos/lista', 'contenedor_productos_metadatos')
    }

    $().ready(() => {
        listarProductosMetaDatos()

        $(".importar").click(() => $("#importar_archivo").trigger('click'))
    })
</script>