<style>
    #tabla_importaciones_bitacora tbody td {
        font-size: 0.9em;
        padding: 5px;
    }
</style>

<table class="table-striped table-bordered" id="tabla_importaciones_bitacora"></table>

<script>
    $().ready(async () => {
        let tablaImportacionesBitacora = $("#tabla_importaciones_bitacora").DataTable({
            ajax: {
                url: `${$("#site_url").val()}importaciones/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'importaciones_bitacora'
                    datos.importacion_id = $('#importacion_id').val()
                },
            },
            columns: [
                {
                    title: `Id`,
                    data: 'id',
                    className: 'text-right',
                    width: '70px'
                },
                {
                    title: `Importacion ID`,
                    data: 'importacion_id',
                    className: 'text-right',
                    width: '70px'
                },
                 {
                    title: `Proveedor`,
                    data: 'proveedor',
                    className: 'text-right',
                    width: '70px'
                },
                { 
                    title: `Creación`,
                    data: null, 
                    width: '100px',
                    render: (id, type, bitacora) => {
                        return `
                            <a href="javascript:cargarBitacoraDetalle(${bitacora.id})">
                                ${bitacora.fecha}
                            </a>
                        `
                    }
                },
                { title: 'Hora', data: 'hora', width: '100px' },
                { title: `Comentarios`, data: 'observaciones' },
                { title: 'Usuario', data: 'nombre_usuario' },
            ],
            columnDefs: [
                { targets: '_all', className: 'dt-head-center p-1' }
            ],
            deferRender: true,
            fixedHeader: true,
            info: true,
            language: {
                decimal: ',',
                thousands: '.',
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            ordering: false,
            orderCellsTop: true,
            pageLength: 100,
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