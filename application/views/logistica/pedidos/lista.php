<style>
    #tabla_pedidos tbody td {
        font-size: 0.9em;
        padding: 8px;
        vertical-align: middle;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_pedidos"></table>

<script>
    $().ready(async () => {
        tablaPedidos = $("#tabla_pedidos").DataTable({
            ajax: {
                url: `${$("#site_url").val()}logistica/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'pedidos'

                    datos.filtros_personalizados = {
                        fecha: $('#filtro_fecha').val(),
                        numero: $('#filtro_numero').val(),
                        nit: $('#filtro_nit').val(),
                        razon_social: $('#filtro_razon_social').val(),
                        orden_compra: $('#filtro_orden_compra').val(),
                        creador: $('#filtro_creador').val(),
                        items: $('#filtro_items').val(),
                        valor: $('#filtro_valor').val()
                    }
                }
            },
            columns: [
                { 
                    title: `
                        Fecha
                        <input type="date" id="filtro_fecha" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'fecha',
                },
                { 
                    title: `
                        Numero
                        <input type="number" id="filtro_numero" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'numero'
                },
                { 
                    title: `
                        Nit
                        <input type="number" id="filtro_nit" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'nit'
                },
                { 
                    title: `
                        Razón Social
                        <input type="text" id="filtro_razon_social" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'razon_social'
                },
                { 
                    title: `
                        Orden de compra
                        <input type="text" id="filtro_orden_compra" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'orden_compra'
                },
                { 
                    title: `
                        Creador
                        <input type="text" id="filtro_creador" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'creador'
                },
                { 
                    title: `
                        Ítems
                        <input type="number" id="filtro_items" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'items'
                },
                {
                    title: `
                        Valor
                        <input type="number" id="filtro_valor" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'valor',
                    className: 'text-right',
                    render: function(data, type, row) {

                        if (type === 'display' || type === 'filter') {
                            return parseFloat(data || 0).toLocaleString('es-CO');
                        }

                        return data; // para ordenar correctamente
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
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaPedidos.draw())
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
            searching: false,
            serverSide: true,
            stateSave: false,
        })
    })
</script>