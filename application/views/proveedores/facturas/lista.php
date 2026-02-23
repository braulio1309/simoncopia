<style>
    #tabla_cuentas_por_pagar tbody td {
        font-size: 0.7em;
        padding: 5px;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_cuentas_por_pagar"></table>

<script>
    $().ready(async () => {
        let tablaCuentasPorPagar = $("#tabla_cuentas_por_pagar").DataTable({
            ajax: {
                url: `${$("#site_url").val()}proveedores/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'erp_cuentas_por_pagar'
                    datos.numero_documento = <?php echo $datos['numero_documento']; ?>

                    // Filtros personalizados
                    datos.filtros_personalizados = {
                        fecha_documento: $('#filtro_fecha_documento').val(),
                        id: $('#filtro_id').val(),
                        sede: $('#filtro_sede').val(),
                        numero_documento_cruce: $('#filtro_numero_documento_cruce').val(),
                        valor_documento: $('#filtro_valor_documento').val(),
                        valor_abonos: $('#filtro_valor_abonos').val(),
                        valor_saldo: $('#filtro_valor_saldo').val(),
                        notas: $('#filtro_notas').val(),
                    }
                },
            },
            columns: [
                {
                    title: `
                        Id
                        <input type="text" id="filtro_id" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'row_id',
                    className: 'text-right',
                },
                {
                    title: `
                        Sede
                        <input type="text" id="filtro_sede" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'sede',
                },
                {
                    title: `
                        Documento
                        <input type="text" id="filtro_numero_documento_cruce" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'documento_cruce',
                    className: 'text-right',
                },
                { 
                    title: `
                        Fecha
                        <input type="date" id="filtro_fecha_documento" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'fecha' 
                },
                { 
                    title: `
                        Valor
                        <input type="number" id="filtro_valor_documento" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'valor_documento',
                    data: null,
                    className: 'text-right',
                    render: (cuenta, type, row) => {
                        return `$${parseFloat(cuenta.valor_documento).toLocaleString('es-CO')}`
                    }
                },
                { 
                    title: `
                        Abonos
                        <input type="number" id="filtro_valor_abonos" class="form-control form-control-sm border-secondary">
                    `,
                    data: null,
                    className: 'text-right',
                    render: (cuenta, type, row) => {
                        return `$${parseFloat(cuenta.valor_abonos).toLocaleString('es-CO')}`
                    }
                },
                { 
                    title: `
                        Saldo
                        <input type="number" id="filtro_valor_saldo" class="form-control form-control-sm border-secondary">
                    `,
                    data: null,
                    className: 'text-right',
                    render: (cuenta, type, row) => {
                        return `$${parseFloat(cuenta.valor_saldo).toLocaleString('es-CO')}`
                    }
                },
                { 
                    title: `
                        Notas
                        <input type="text" id="filtro_notas" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'notas' 
                },
                {
                    title: 'Opciones',
                    data: null, 
                    render: (cuenta, type, row) => {
                        return (cuenta.valor_saldo == 0)
                        ? `
                            <div class="p-1">
                                <a type="button" class="btn btn-sm btn-danger" onClick="javascript:generarReporte('pdf/proveedores_comprobante_egreso', {id: ${cuenta.id}})" title="Descargar comprobante de egreso" style="color: white;">
                                    <i class="fa fa-file-download"></i>
                                </a>
                            </div>
                        `
                        : ''
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
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaCuentasPorPagar.draw())
            },
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
            searching: false,
            serverSide: true,
            stateSave: false,
        })
    })
</script>