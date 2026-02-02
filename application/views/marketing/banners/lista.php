<style>
    #tabla_banners tbody td {
        font-size: 0.9em;
        padding: 8px;
        vertical-align: middle;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_banners"></table>

<script>
    function verImagen(id, nombreArchivo) {
        let url = `${$("#site_url").val()}archivos/banners/${id}/${nombreArchivo}`

        let imagen = new Image()
        imagen.onload = () => {
            Swal.fire({
                title: 'Vista previa',
                imageUrl: url,
                showCloseButton: true,
                showConfirmButton: false
            })
        }
        imagen.onerror = () => {
            mostrarAviso('alerta', 'La imagen no se encontró')
        }
        imagen.src = url
    }

    $().ready(async () => {
        tablaBanners = $("#tabla_banners").DataTable({
            ajax: {
                url: `${$("#site_url").val()}marketing/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'banners'

                    datos.filtros_personalizados = {
                        nombre: $('#filtro_nombre').val(),
                        fecha_creacion: $('#filtro_fecha_creacion').val()
                    }
                }
            },
            columns: [
                { 
                    title: `
                        Tipo de banner
                        <input type="text" id="filtro_nombre" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'banner_tipo_nombre',
                },
                { 
                    title: `
                        Fecha de creación
                        <input type="date" id="filtro_fecha_creacion" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'fecha_creacion'
                },
                {
                    title: 'Acciones',
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: (data) => {
                        return `
                            <button class="btn btn-sm btn-primary"
                                title="Visualizar imagen"
                                onclick="verImagen(${data.id}, '${data.nombre_archivo}')">
                                <i class="fa fa-eye"></i>
                            </button>
                        `
                    }
                }
            ],
            columnDefs: [
                { targets: '_all', className: 'dt-head-center p-1' } // Todo el encabezado alineado al centro
            ],
            deferRender: true,
            fixedHeader: true,
            info: true,
            initComplete: function () {
                // Cuando un campo de filtro personalizado cambie, se redibuja la tabla
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaBanners.draw())
            },
            language: {
                decimal: ',',
                thousands: '.',
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            ordering: false,
            orderCellsTop: true,
            pageLength: 25,
            paging: true,
            processing: true,
            scrollCollapse: true,
            scroller: true,
            scrollX: false,
            scrollY: false,
            searching: true,
            serverSide: true,
            stateSave: false,
        })
    })
</script>