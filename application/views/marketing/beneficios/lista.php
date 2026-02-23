<style>
    #tabla_beneficios tbody td {
        font-size: 0.9em;
        padding: 8px;
        vertical-align: middle;
    }
</style>
<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_beneficios"></table>
<script>
    let tablaBeneficios = null
    $().ready(async () => {
        tablaBeneficios = $("#tabla_beneficios").DataTable({
            ajax: {
                url: `${$("#site_url").val()}marketing/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'beneficios'
                    datos.filtros_personalizados = {
                        nombre: $('#filtro_nombre').val(),
                        beneficio_tipo: $('#filtro_tipo').val(),
                        tipo_venta: $('#filtro_tipo_venta').val(),
                        fecha_inicio: $('#filtro_fecha_inicio').val(),
                        fecha_final: $('#filtro_fecha_final').val()
                    }
                }
            },
            columns: [
                { 
                    title: `
                        Nombre
                        <input type="text" id="filtro_nombre" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'nombre',
                },
                { 
                    title: `
                        Tipo
                        <select id="filtro_tipo" class="form-control form-control-sm border-secondary">
                            <option value="">Todos</option>
                            <option value="promoción">Promoción</option>
                            <option value="código descuento">Código descuento</option>
                        </select>
                    `,
                    data: 'beneficio_tipo',
                },
                {
                    title: 'Código de descuento',
                    data: 'codigo_descuento',
                    render: (data) => data ? data : '-'
                },
                {
                    title: 'Valor presupuesto',
                    data: 'presupuesto',
                    className: 'dt-body-right',
                    render: (data) => data ? new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(data) : '$0'
                },
                {
                    title: 'Valor usado',
                    data: 'valor_usado',
                    className: 'dt-body-right',
                    render: (data) => data ? new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(data) : '$0'
                },
                { 
                    title: `Fecha inicio <input type="date" id="filtro_fecha_inicio" class="form-control form-control-sm border-secondary">`,
                    data: 'fecha_inicio'
                },
                { 
                    title: `Fecha final <input type="date" id="filtro_fecha_final" class="form-control form-control-sm border-secondary">`,
                    data: 'fecha_final'
                },
                { 
                    title: `
                        Tipo de venta
                        <select id="filtro_tipo_venta" class="form-control form-control-sm border-secondary">
                            <option value="">Todos</option>
                            <option value="contado">Contado</option>
                            <option value="crédito">Crédito</option>
                        </select>
                    `,
                    data: 'tipo_venta'
                },
                {
                    title: 'Acciones',
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: (data) => {
                        return `
                            <a class="btn btn-sm btn-primary"
                               href="${$("#site_url").val()}marketing/beneficios/editar/${data.id}" 
                               title="Editar beneficio">
                                <i class="fa fa-pencil"></i>
                            </a>
                        `
                    }
                }
            ],
            columnDefs: [
                { targets: '_all', className: 'dt-head-center p-1' }
            ],
            deferRender: true,
            fixedHeader: true,
            info: true,
            initComplete: function () {
                // Cuando un campo de filtro personalizado cambie, se redibuja la tabla
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaBeneficios.draw())
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
            scrollX: true,
            scrollY: false,
            searching: true,
            serverSide: true,
            stateSave: false,
        })
    })
</script>