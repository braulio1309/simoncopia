<table class="table-striped table-bordered" id="tabla_comprobantes_contables"></table>

<script>
    $().ready(() => {
        var tablaComprobantesContables = $("#tabla_comprobantes_contables").DataTable({
            ajax: {
                url: `${$("#site_url").val()}contabilidad/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'comprobantes_contables_tareas'
                },
            },
            columns: [
                {
                    title: `Id`,
                    data: 'id',
                    className: 'dt-right',
                },
                { 
                    title: `Rango`,
                    width: '150px',
                    data: null,
                    render: (tarea, type, row) => {
                        return `${tarea.consecutivo_inicial} - ${tarea.consecutivo_final}`
                    },
                },
                {
                    title: `Tipo`,
                    data: 'tipo_comprobante',
                    width: '320px',
                },
                {
                    title: `Sede`,
                    data: 'centro_operativo',
                },
                {
                    title: `Año`,
                    data: 'anio',
                    width: '70px',
                },
                {
                    title: `Mes`,
                    data: 'mes_nombre',
                },
                {
                    title: `Total`,
                    className: 'dt-right',
                    data: null,
                    render: (tarea, type, row) => {
                        return `<a href="javascript:;" onClick="javascript:cargarConsecutivos({ comprobante_contable_tarea_id: ${tarea.id} }); ">${tarea.cantidad_comprobantes}</a>`
                    },
                },
                {
                    title: `Existentes`,
                    data: null,
                    render: (tarea, type, row) => {
                        return `<a href="javascript:;" onClick="javascript:cargarConsecutivos({ consecutivo_existe: 1 }); ">${tarea.consecutivo_existe}</a>`
                    },
                    className: 'dt-right',
                    width: '70px',
                },
                {
                    title: `Faltantes`,
                    data: null,
                    render: (tarea, type, row) => {
                        return `<a href="javascript:;" onClick="javascript:cargarConsecutivos({ comprobante_contable_tarea_id: ${tarea.id}, consecutivo_existe: 0 }); ">${tarea.consecutivo_no_existe}</a>`
                    },
                    className: 'dt-right',
                    width: '70px',
                },
                {
                    title: `Comprobantes encontrados`,
                    data: null,
                    render: (tarea, type, row) => {
                        return `<a href="javascript:;" onClick="javascript:cargarConsecutivos({ comprobante_contable_tarea_id: ${tarea.id}, comprobante_coincide: 1 }); ">${tarea.comprobante_coincide}</a>`
                    },
                    className: 'dt-right',
                    width: '70px',
                },
                {
                    title: `Comprobantes faltantes`,
                    data: null,
                    render: (tarea, type, row) => {
                        return `<a href="javascript:;" onClick="javascript:cargarConsecutivos({ comprobante_contable_tarea_id: ${tarea.id}, comprobante_coincide: 0 }); ">${tarea.comprobante_no_coincide}</a>`
                    },
                    className: 'dt-right',
                    width: '70px',
                },
                {
                    title: `Soportes`,
                    data: null,
                    render: (tarea, type, row) => {
                        return `<a href="javascript:;" onClick="javascript:cargarConsecutivos({ comprobante_contable_tarea_id: ${tarea.id} }); ">${tarea.cantidad_soportes}</a>`
                    },
                    className: 'dt-right',
                    width: '70px',
                },
                {
                    title: `Minutos`,
                    data: 'tiempo_ejecucion_minutos',
                    className: 'dt-right',
                    width: '70px',
                },
            ],
            columnDefs: [
                { targets: '_all', className: 'dt-head-center p-1' } // Todo el encabezado alineado al centro
            ],
            deferRender: true,
            fixedHeader: true,
            info: true,
            initComplete: function () {
                // Cuando un campo de filtro personalizado cambie, se redibuja la tabla
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaComprobantesContables.draw())
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