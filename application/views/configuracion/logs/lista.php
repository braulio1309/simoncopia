<style>
    #tabla_logs tbody td {
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

<div class="table-responsive">
    <table class="table-striped table-bordered w-100" id="tabla_logs"></table>
</div>

<script>
    function verDetalleLog(btn) {
        const observacion = btn.dataset.observacion || ''
        let contenido = ''

        // Intentar parsear como JSON
        try {
            const json = JSON.parse(observacion)

            contenido = `
                <pre style="
                    text-align:left;
                    max-height:400px;
                    overflow:auto;
                    background:#1e1e1e;
                    color:#dcdcdc;
                    padding:12px;
                    border-radius:6px;
                    font-size:12px;
                ">${JSON.stringify(json, null, 2)}</pre>
            `
        } catch (e) {
            // No es JSON entonces es texto plano
            contenido = `
                <div style="
                    text-align:left;
                    white-space:pre-wrap;
                    max-height:400px;
                    overflow:auto;
                ">
                    ${observacion || 'Sin observación'}
                </div>
            `
        }

        Swal.fire({
            title: 'Detalle del log',
            html: contenido,
            width: '800px',
            showCloseButton: true,
            showConfirmButton: false
        })
    }

    $().ready(async () => {
        tablaLogs = $("#tabla_logs").DataTable({
            ajax: {
                url: `${$("#site_url").val()}configuracion/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'logs'
                    datos.filtros_personalizados = {
                        fecha: $('#filtro_fecha').val(),
                        modulo: $('#filtro_modulo').val(),
                        log_tipo: $('#filtro_log_tipo').val(),
                        observacion: $('#filtro_observacion').val(),
                        usuario: $('#filtro_usuario').val(),
                    }
                    // filtro según el rango de fecha
                    datos.fecha_inicial = $("#fecha_inicial").val()
                    datos.fecha_final   = $("#fecha_final").val()
                }
            },
            columns: [
                { 
                    title: `Fecha <br><input type="date" id="filtro_fecha" class="form-control form-control-sm border-secondary mt-1">`,
                    data: 'fecha_creacion'
                },
                { 
                    title: `
                        Módulo
                        <select id="filtro_modulo" class="form-control form-control-sm border-secondary mt-1">
                            <option value="">Seleccione un módulo</option>
                            <?php foreach($this->configuracion_model->obtener('modulos') as $modulo) echo "<option value='$modulo->nombre'>$modulo->nombre</option>"; ?>
                        </select>
                    `,
                    data: 'modulo',
                },
                { 
                    title: `
                        Tipo
                        <select id="filtro_log_tipo" class="form-control form-control-sm border-secondary mt-1">
                            <option value="">Seleccione un tipo de log</option>
                            <?php foreach($this->configuracion_model->obtener('logs_tipos') as $log_tipo) echo "<option value='$log_tipo->nombre'>$log_tipo->nombre</option>"; ?>
                        </select>
                    `,
                    data: 'log_tipo',
                },
                { 
                    title: `
                        Observación
                        <input type="text" id="filtro_observacion" class="form-control form-control-sm border-secondary mt-1">
                    `,
                    data: 'observacion',
                    render: function (data, type) {
                        if (!data) return ''

                        // Para búsqueda y ordenamiento se devuelve completo
                        if (type !== 'display') return data

                        // Muestra solo los primeros 100 caracteres
                        if (data.length > 100) return `${data.substring(0, 100)}...`
                        return data
                    }
                },
                { 
                    title: `
                        Usuario
                        <input type="text" id="filtro_usuario" class="form-control form-control-sm border-secondary mt-1">
                    `,
                    data: 'usuario',
                },
                {
                    title: 'Acciones',
                    data: null, 
                    orderable: false,
                    render: (data, type, row) => {
                        return `
                            <button class="btn btn-sm btn-info" title="Ver detalle" onclick="verDetalleLog(this)" data-observacion='${(data.observacion ?? '').replace(/'/g, "&apos;")}'>
                                <i class="fa fa-eye"></i>
                            </button>
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
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaLogs.draw())
            },
            language: {
                decimal: ',',
                thousands: '.',
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            ordering: false,
            orderCellsTop: true,
            pageLength: 50,
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