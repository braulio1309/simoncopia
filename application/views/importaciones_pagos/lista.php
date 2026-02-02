<style>
    #tabla_importaciones_pagos tbody td {
        font-size: 0.7em;
        padding: 5px;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_importaciones_pagos"></table>

<script>
    $().ready(async () => {
        tablaImportacionesPagos = $("#tabla_importaciones_pagos").DataTable({
            ajax: {
                url: `${$("#site_url").val()}importaciones_pagos/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'importaciones_pagos'

                    datos.filtros_personalizados = {
                        importacion: $('#filtro_importacion').val(),
                        estado: $('#filtro_estado').val(),
                        fecha: $('#filtro_fecha').val(),
                        observaciones: $('#filtro_observaciones').val(),
                        factura_numero: $('#filtro_factura_numero').val(),
                        valor_moneda_extranjera: $('#filtro_valor_moneda_extranjera').val(),
                        valor_trm: $('#filtro_valor_trm').val(),
                        valor_cop: $('#filtro_valor_cop').val(),
                        origen_recursos: $('#filtro_origen_recursos').val(),
                        cuenta_bancaria: $('#filtro_cuenta_bancaria').val(),
                        fecha_creacion: $('#filtro_fecha_creacion').val()
                    }
                }
            },
            columns: [
                { 
                    title: `
                        Importación
                        <input type="text" id="filtro_importacion" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'importacion',
                },
                { 
                    title: `
                        Estado
                        <input type="text" id="filtro_estado" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'estado_texto'
                },
                { 
                    title: `
                        fecha
                        <input type="text" id="filtro_fecha" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'fecha'
                },
                { 
                    title: `
                        Observaciones
                        <input type="text" id="filtro_observaciones" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'observaciones'
                },
                { 
                    title: `
                        Factura número
                        <input type="number" id="filtro_factura_numero" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'factura_numero'
                },
                { 
                    title: `
                        Valor moneda extranjera
                        <input type="number" id="filtro_valor_moneda_extranjera" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'valor_moneda_extranjera',
                    className: 'dt-body-right',
                    render: (data, type) => {
                        if (type === 'display' || type === 'filter') {
                            return parseFloat(data || 0).toLocaleString('es-CO')
                        }
                        return data
                    }
                },
                { 
                    title: `
                        Valor trm
                        <input type="number" id="filtro_valor_trm" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'valor_trm',
                    className: 'dt-body-right',
                    render: (data, type) => {
                        if (type === 'display' || type === 'filter') {
                            return parseFloat(data || 0).toLocaleString('es-CO')
                        }
                        return data
                    }
                },
                { 
                    title: `
                        Valor cop
                        <input type="number" id="filtro_valor_cop" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'valor_cop',
                    className: 'dt-body-right',
                    render: (data, type) => {
                        if (type === 'display' || type === 'filter') {
                            return parseFloat(data || 0).toLocaleString('es-CO')
                        }
                        return data
                    }
                },
                { 
                    title: `
                        Origen de recursos
                        <input type="text" id="filtro_origen_recursos" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'origen_recursos'
                },
                { 
                    title: `
                        Cuenta bancaria
                        <input type="text" id="filtro_cuenta_bancaria" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'cuenta_bancaria'
                },
                { 
                    title: `
                        Fecha de creación
                        <input type="date" id="filtro_fecha_creacion" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'fecha_creacion'
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
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaImportacionesPagos.draw())
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