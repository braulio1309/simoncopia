<style>
    #tabla_pedidos tbody td {
        font-size: 0.7em;
        padding: 5px;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_pedidos"></table>

<script>
    $().ready(async () => {
        var tablaPedidos = $("#tabla_pedidos").DataTable({
            ajax: {
                url: `${$("#site_url").val()}clientes/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'pedidos'

                    // Filtros personalizados
                    datos.filtro_numero_pedido = $('#filtro_numero_pedido').val()
                    datos.filtro_nombre_consecutivo = $('#filtro_nombre_consecutivo').val()
                    datos.filtro_fecha_creacion = $('#filtro_fecha_creacion').val()
                    datos.filtro_numero_documento = $('#filtro_numero_documento').val()
                    datos.filtro_nombre = $('#filtro_nombre').val()
                    datos.filtro_estado = $('#filtro_estado').val()
                },
            },
            columns: [
                { 
                    title: `
                        Pedido
                        <input type="text" id="filtro_numero_pedido" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'numero_documento' 
                },
                { 
                    title: `
                        Nombre
                        <input type="text" id="filtro_nombre_consecutivo" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'consecutivo_nombre' 
                },
                { 
                    title: `
                        Fecha
                        <input type="date" id="filtro_fecha_creacion" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'fecha_documento' 
                },
                { 
                    title: `
                        NIT
                        <input type="text" id="filtro_numero_documento" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'nit' 
                },
                { 
                    title: `
                        Nombre
                        <input type="text" id="filtro_nombre" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'rzon_social' 
                },
                { title: 'Productos', data: 'cantidad_productos', className: 'text-right' },
                { 
                    title: `
                        Estado
                        <input type="text" id="filtro_estado" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'ultimo_estado' 
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