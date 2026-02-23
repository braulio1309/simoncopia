<style>
    #tabla_reglas_facturacion tbody td {
        font-size: 0.9em;
        padding: 8px;
        vertical-align: middle;
    }
    .dt-buttons-gap {
        display: flex;
        gap: 5px;
        justify-content: center;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered w-100" id="tabla_reglas_facturacion"></table>

<script>
    let tablaReglasFacturacion = null

    const eliminarReglaFacturacion = (id) => {
        Swal.fire({
            title: '¿Eliminar regla?',
            text: 'Esta acción eliminará la regla de facturación. No se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-trash"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (!result.isConfirmed) return

            consulta('eliminar', { tipo: 'facturacion_reglas', id: id }, false).then(() => {
                mostrarAviso('exito', 'Regla eliminada correctamente')
                tablaReglasFacturacion.ajax.reload(null, false)
            }).catch(() => {
                mostrarAviso('error', 'No se pudo eliminar la regla de facturación')
            })
        })
    }

    $().ready(async () => {
        tablaReglasFacturacion = $("#tabla_reglas_facturacion").DataTable({
            ajax: {
                url: `${$("#site_url").val()}logistica/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'facturacion_reglas'

                    datos.filtros_personalizados = {
                        cliente_nit: $('#filtro_cliente_nit').val(),
                        nombre: $('#filtro_nombre').val(),
                        tipo_frecuencia: $('#filtro_tipo_frecuencia').val(),
                        activa: $('#filtro_activa').val()
                    }
                }
            },
            columns: [
                {
                    title: `
                        Cliente NIT
                        <input type="number" id="filtro_cliente_nit" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'cliente_nit'
                },
                {
                    title: `
                        Nombre
                        <input type="text" id="filtro_nombre" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'nombre'
                },
                {
                    title: `
                        Frecuencia
                        <select id="filtro_tipo_frecuencia" class="form-control form-control-sm border-secondary">
                            <option value="">Todos</option>
                            <option value="diaria">Diaria</option>
                            <option value="semanal">Semanal</option>
                            <option value="mensual">Mensual</option>
                            <option value="personalizada">Personalizada</option>
                        </select>
                    `,
                    data: 'tipo_frecuencia',
                    render: data => {
                        return data.charAt(0).toUpperCase() + data.slice(1)
                    }
                },
                {
                    title: 'Día semana',
                    data: 'dia_semana',
                    render: data => data ? data : '-'
                },
                {
                    title: 'Día mes',
                    data: 'dia_mes',
                    render: data => data ? data : '-'
                },
                {
                    title: 'Hora programada',
                    data: 'hora_programada'
                },
                {
                    title: `
                        Activa
                        <select id="filtro_activa" class="form-control form-control-sm border-secondary">
                            <option value="">Todos</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    `,
                    data: 'activa',
                    render: data => data == 1
                        ? '<span class="badge badge-success">Sí</span>'
                        : '<span class="badge badge-danger">No</span>'
                },
                {
                    title: 'Orden de compra',
                    data: 'requiere_orden_compra',
                    render: data => data == 1
                        ? '<span class="badge badge-info">Sí</span>'
                        : '<span class="badge badge-secondary">No</span>'
                },
                {
                    title: 'Acciones',
                    data: null,
                    orderable: false,
                    render: (data) => {
                        return `
                            <div class="dt-buttons-gap">
                                <a class="btn btn-sm btn-primary"
                                   href="${$("#site_url").val()}logistica/pedidos/reglas_facturacion/editar/${data.id}"
                                   title="Editar regla">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-danger"
                                        title="Eliminar regla"
                                        onclick="eliminarReglaFacturacion(${data.id})">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
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
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaReglasFacturacion.draw())
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
            searching: false,
            serverSide: true,
            stateSave: false,
        })
    })
</script>
