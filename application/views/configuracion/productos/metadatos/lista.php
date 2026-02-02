<style>
    #tabla_productos_metadatos {
        font-size: 0.8em;
        font-family: Futura;
    }

    #tabla_productos_metadatos th {
        background-color: #19287F;
        color: white;
    }
</style>

<div class="table-responsive">
    <table class="table-striped table-bordered" id="tabla_productos_metadatos">
        <thead>
            <tr>
                <th class="text-center">Referencia</th>
                <th class="text-center">Nombre</th>
                <th class="text-center">Palabras clave</th>
                <th class="text-center">Detalles técnicos</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Slug</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    $().ready(() => {
        $("#tabla_productos_metadatos").DataTable({
            ajax: {
                url: `${$("#site_url").val()}configuracion/obtener_datos_tabla`,
                data: {
                    tipo: "productos_metadatos",
                },
            },
            columns: [
                {
                    data: 'referencia',
                },
                {
                    data: 'notas',
                    render: (data, type, row) => {
                        let notas = recortarTexto(data)

                        return `
                            <div class="copiar" data-valor="${data}" title="${data}">${notas}</div>
                        `
                    }
                },
                { data: 'palabras_clave' },
                { 
                    data: null,
                    render: (producto, type, row) => {
                        return (producto.detalles_tecnicos && producto.detalles_tecnicos != '') ? `✅` : `➖`
                    },
                    className: 'text-center'
                },
                { 
                    data: null,
                    render: (data, type, row) => {
                        let estadoClase = (data.disponible > 0) ? 'success' : 'failure'
                        let estadoNombre = (data.disponible > 0) ? 'Disponible' : 'Agotado'

                        return `
                            <div class="status-badge status-badge--style--${estadoClase} product__stock status-badge--has-text">
                                <div class="status-badge__body">
                                    <div class="status-badge__text">${estadoNombre}</div>
                                    <div class="status-badge__tooltip" tabindex="0" data-toggle="tooltip"></div>
                                </div>
                            </div>
                        `
                    }
                },
                { data: 'slug' },
                {
                    data: null, 
                    render: (data, type, row) => {
                        return `
                            <div class="p-1">
                                <a type="button" class="btn btn-sm btn-primary" href="${$('#site_url').val()}productos/ver/${data.slug}" target="_blank">
                                    Ver
                                </a>

                                <a type="button" class="btn btn-sm btn-primary" href="${$("#site_url").val()}configuracion/productos/editar/${data.id}">
                                    Editar
                                </a>

                                <a type="button" class="btn btn-sm btn-danger" href="javascript:eliminarProductoMetaDatos(${data.id})">
                                    Eliminar
                                </a>
                            </div>
                        `
                    }
                }
            ],
            serverSide: true,
            stateSave: true,
            scrollY: '320px',
            searching: true,
            language: {
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            scrollX: false,
            scrollCollapse: true,
            drawCallback: function() {
                $('.copiar').on('copy', function(evento) {
                    let textoIncompleto = window.getSelection().toString()
                    let elemento = $(this)

                    if (textoIncompleto) {
                        let textoCompleto = elemento.data('valor')

                        evento.preventDefault()
                        evento.originalEvent.clipboardData.setData('text/plain', textoCompleto)
                    }
                })
            }
        })

        $('#contenedor_mensaje_carga').html('')
    })
</script>