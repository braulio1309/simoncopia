<style>
    #tabla_solicitudes_credito tbody td {
        font-size: 0.7em;
        padding: 5px;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_solicitudes_credito"></table>

<script>
    $().ready(async () => {
        let tablaSolicitudesCredito = $("#tabla_solicitudes_credito").DataTable({
            ajax: {
                url: `${$("#site_url").val()}clientes/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'solicitudes_credito'

                    // Filtros personalizados
                    datos.filtro_fecha_creacion = $('#filtro_fecha_creacion').val()
                    datos.filtro_numero_documento = $('#filtro_numero_documento').val()
                    datos.filtro_nombre = $('#filtro_nombre').val()
                    datos.filtro_id = $('#filtro_id').val()
                    datos.filtro_estado = $('#filtro_estado').val()
                    datos.filtro_usuario_asignado = $('#filtro_usuario_asignado').val()
                    datos.filtro_vendedor = $('#filtro_vendedor').val()
                    datos.filtro_fecha_cierre = $('#filtro_fecha_cierre').val()
                    datos.filtro_motivo_rechazo = $('#filtro_motivo_rechazo').val()
                    datos.filtro_cupo = $('#filtro_cupo').val()
                    datos.filtro_ultimo_comentario = $('#filtro_ultimo_comentario').val(),
                    datos.filtro_tipo = $('#filtro_tipo').val()
                },
            },
            columns: [
                {
                    title: `
                        Id
                        <input type="text" id="filtro_id" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'id',
                    className: 'text-right',
                    width: '70px',
                },
                {
                    title: `
                        Creación
                        <input type="date" id="filtro_fecha_creacion" class="form-control form-control-sm border-secondary" style='width: 100px;'>
                    `,
                    data: 'fecha'
                },
                { title: 'Hora', data: 'hora' },
                {
                    title: `
                        Cierre
                        <input type="date" id="filtro_fecha_cierre" class="form-control form-control-sm border-secondary" style='width: 100px;'>
                    `,
                    data: 'fecha_cierre'
                },
                { title: 'Hora', data: 'hora_cierre' },
                { 
                    title: `
                        NIT
                        <input type="text" id="filtro_numero_documento" class="form-control form-control-sm border-secondary">
                    `,
                    data: null,
                    render: (solicitud, type, row) => {
                        return `
                            <a href="${$('#site_url').val()}/clientes/credito/ver/${solicitud.id}" target="_blank">
                                ${solicitud.documento_numero}
                            </a>
                        `
                    }
                },
                { 
                    title: `
                        Nombre
                        <input type="text" id="filtro_nombre" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'nombre_solicitante' 
                },
                { 
                    title: `
                        Estado
                        <input type="text" id="filtro_estado" class="form-control form-control-sm border-secondary">
                    `, 
                    data: null,
                    render: (solicitud, type, row) => {
                        return `
                            <div class="status-badge status-badge--style--${solicitud.estado_clase} status-badge--has-text">
                                <div class="status-badge__body">
                                    <div class="status-badge__text">
                                        ${solicitud.estado}
                                    </div>
                                </div>
                            </div>
                        `
                    }
                },
                { 
                    title: `
                        Tipo
                        <input type="text" id="filtro_tipo" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'tipo' 
                },
                { 
                    title: `
                        Vendedor
                        <input type="text" id="filtro_vendedor" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'vendedor_nombre',
                    width: '150px'
                },
                { 
                    title: `
                        Gestionador
                        <input type="text" id="filtro_usuario_asignado" class="form-control form-control-sm border-secondary">
                    `, 
                    data: null,
                    render: (solicitud, type, row) => {
                        let automatico = (solicitud.usuario_asignado_automaticamente == 1) ? '🕞' : ''

                        return (solicitud.nombre_usuario_asignado != '-') 
                        ? `${automatico}${solicitud.nombre_usuario_asignado}`
                        : `
                            <a type="button" class="btn btn-sm btn-primary" href="javascript:cargarAsignarUsuario(${solicitud.id})" title="Asignar usuario">
                                Asignar
                            </a>
                        `
                    }
                },
                {
                    title: `
                        Cupo
                        <input type="number" id="filtro_cupo" class="form-control form-control-sm border-secondary">
                    `, 
                    data: null,
                    className: 'text-right',
                    render: (solicitud, type, row) => {
                        return parseFloat(solicitud.cupo_asignado).toLocaleString('es-CO')
                    }
                },
                { 
                    title: `
                        Motivo rechazo
                        <input type="text" id="filtro_motivo_rechazo" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'motivo_rechazo' 
                },
                { 
                    title: `
                        Último comentario
                        <input type="text" id="filtro_ultimo_comentario" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'ultimo_comentario' 
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
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaSolicitudesCredito.draw())
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
            searching: true,
            serverSide: true,
            stateSave: false,
        })
    })
</script>