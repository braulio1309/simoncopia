<style>
    #tabla_cotizaciones tbody td {
        font-size: 0.9em;
        padding: 8px;
    }
</style>

<div class="table-responsive">
    <table class="table table-striped table-bordered w-100" id="tabla_cotizaciones"></table>
</div>

<script>
$().ready(() => {
    const tabla = $("#tabla_cotizaciones").DataTable({
        ajax: {
            url: `${$("#site_url").val()}proveedores/obtener_datos_tabla`,
            data: function (datos) {

                datos.tipo = "proveedores_cotizaciones_detalle"
                datos.cotizacion_id = <?php echo $datos['cotizacion_id'] ?>

                datos.search = datos.search || {}
                datos.search.value = datos.search.value || ''

                datos.filtros_personalizados = {
                    proveedor: $('#filtro_proveedor').val(),
                    productos_disponibles: $('#filtro_productos_disponibles').val(),
                    productos_cotizados: $('#filtro_productos_cotizados').val()
                }
            }
        },

        columns: [
            {
                title: `
                    Proveedor
                    <input type="text" id="filtro_proveedor" class="form-control form-control-sm border-secondary">
                `,
                data: 'proveedor'
            },
            {
                title: `
                    Productos disponibles
                    <input type="number" id="filtro_productos_disponibles" class="form-control form-control-sm border-secondary">
                `,
                data: 'cantidad_productos',
                className: 'dt-body-right'
            },
            {
                title: `
                    Productos cotizados
                    <input type="number" id="filtro_productos_cotizados" class="form-control form-control-sm border-secondary">
                `,
                data: 'cantidad_productos_cotizados',
                className: 'dt-body-right'
            }
        ],
        columnDefs: [
            { targets: '_all', className: 'dt-head-center p-1' }
        ],
        autoWidth: false,
        serverSide: false,
        stateSave: true,
        scrollY: '320px',
        scrollCollapse: true,
        searching: true,
        ordering: false,
        orderCellsTop: true,

        initComplete: function () {
            $(`input[id^='filtro_']`).on('keyup change', () => tabla.ajax.reload())
        },

        language: {
            url: '<?php echo base_url() ?>js/dataTables_espanol.json'
        }
    })

    $('#contenedor_mensaje_carga').html('')
})
</script>
