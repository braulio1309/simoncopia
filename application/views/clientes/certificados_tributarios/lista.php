<style>
    #tabla_certificados_tributarios tbody td {
        font-size: 0.7em;
        padding: 5px;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_certificados_tributarios"></table>

<script>
    $().ready(async () => {
        tablaCertificadosTributarios = $("#tabla_certificados_tributarios").DataTable({
            ajax: {
                url: `${$("#site_url").val()}clientes/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'clientes_retenciones_informe'

                    datos.filtros_personalizados = {
                        nit: $('#filtro_nit').val(),
                        razon_social: $('#filtro_razon_social').val(),
                        vendedor: $('#filtro_vendedor').val(),
                        fuente: $('#filtro_fuente').val(),
                        iva: $('#filtro_iva').val(),
                        ica: $('#filtro_ica').val(),
                        fecha_actualizacion: $('#filtro_fecha_actualizacion').val()
                    }
                }
            },
            columns: [
                { 
                    title: `
                        NIT
                        <input type="number" id="filtro_nit" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'nit',
                },
                { 
                    title: `
                        Razón social
                        <input type="text" id="filtro_razon_social" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'razon_social'
                },
                { 
                    title: `
                        Vendedor
                        <input type="text" id="filtro_vendedor" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'vendedor'
                },
                { 
                    title: `
                        Fuente
                        <input type="number" id="filtro_fuente" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'valor_retencion_fuente',
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
                        IVA
                        <input type="number" id="filtro_iva" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'valor_retencion_iva',
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
                        ICA
                        <input type="number" id="filtro_ica" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'valor_retencion_ica',
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
                        Fecha de actualización
                        <input type="date" id="filtro_fecha_actualizacion" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'fecha_actualizacion'
                },
                {
                    title: 'Acciones',
                    data: null, 
                    orderable: false,
                    render: (data, type, row) => {
                        return `
                            <div class="dt-buttons-gap">
                                <a class="btn btn-sm btn-success" href="${$("#site_url").val()}clientes/certificados_tributarios/crear/${data.nit}" title="Subir certificado de retención" target="_blank">
                                    <i class="fa fa-upload"></i>
                                </a>
                            </div>
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
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaCertificadosTributarios.draw())
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