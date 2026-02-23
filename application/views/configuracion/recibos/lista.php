<?php $permisos = $this->configuracion_model->obtener('permisos'); ?>

<!-- Inputs ocultos para pasar como filtros -->
<input type="hidden" id="recibo_id_tipo" value="<?php echo $datos['id_tipo_recibo']; ?>">
<input type="hidden" id="configuracion_comprobantes_eliminar" value="<?php echo (in_array(['configuracion' => 'configuracion_comprobantes_eliminar'], $permisos)); ?>">

<!-- Si es comprobante -->
<?php if($datos['id_tipo_recibo'] == 3) { ?>
    <a class="btn btn-success mb-2" href="<?php echo site_url('configuracion/comprobantes/crear'); ?>">Subir comprobante</a>
<?php } ?>

<style>
    #tabla_recibos tbody td {
        font-size: 0.7em;
        padding: 5px;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_recibos"></table>

<script>
    $().ready(async () => {
        var tablaRecibos = $("#tabla_recibos").DataTable({
            ajax: {
                url: `${$("#site_url").val()}configuracion/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'recibos'
                    datos.id_tipo_recibo = $('#recibo_id_tipo').val()

                    // Filtros personalizados
                    datos.filtro_fecha_creacion = $('#filtro_fecha_creacion').val()
                    datos.filtro_numero_documento = $('#filtro_numero_documento').val()
                    datos.filtro_nombre = $('#filtro_nombre').val()
                    datos.filtro_forma_pago = $('#filtro_forma_pago').val()
                    datos.filtro_recibo_siesa = $('#filtro_recibo_siesa').val()
                    datos.filtro_estado = $('#filtro_estado').val()
                    datos.filtro_usuario_creador = $('#filtro_usuario_creador').val()
                    datos.filtro_comentarios = $('#filtro_comentarios').val()
                    datos.filtro_observaciones = $('#filtro_observaciones').val()
                    datos.filtro_valor = $('#filtro_valor').val()
                    datos.filtro_telefono = $('#filtro_telefono').val()
                    datos.filtro_token = $('#filtro_token').val()
                },
            },
            columns: [
                {
                    title: `
                        Fecha ingreso
                        <input type="date" id="filtro_fecha_creacion" class="form-control form-control-sm border-secondary">
                    `,
                    data: null,
                    render: (recibo, type, row) => {
                        return `
                            <a href="${$('#site_url').val()}/configuracion/recibos/id/${recibo.token}" target="_blank">
                                ${recibo.fecha}
                            </a>
                        `
                    }
                },
                { title: 'Hora ingreso', data: 'hora' },
                {
                    title: 'Fecha pago', 
                    data: null,
                    visible: ($('#recibo_id_tipo').val() == 3), // Visible si es comprobantes
                    render: (recibo, type, row) => {
                        return `${recibo.fecha_consignacion}`
                    }
                },
                { 
                    title: `
                        NIT
                        <input type="text" id="filtro_numero_documento" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'documento_numero' },
                { 
                    title: `
                        Nombre
                        <input type="text" id="filtro_nombre" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'razon_social' },
                {
                    title: `
                        Forma pago
                        <input type="text" id="filtro_forma_pago" class="form-control form-control-sm border-secondary">
                    `, 
                    data: null,
                    visible: ($('#recibo_id_tipo').val() != 3), // Visible si no es comprobantes
                    render: (recibo, type, row) => {
                        let wompi = JSON.parse(recibo.wompi_datos)

                        if(!wompi) return ``

                        // Si es tarjeta de crédito, se obtiene la información de la tarjeta
                        let metodoPago = (wompi.payment_method_type == 'CARD') ? wompi.payment_method.extra.name : ''

                        return `
                            <div class="wishlist__product-name">
                                ${wompi.payment_method_type}
                            </div>

                            <div class="wishlist__product-rating">
                                <div class="wishlist__product-rating-title">
                                    ${metodoPago}
                                </div>
                            </div>
                        `
                    }
                },
                { 
                    title: `
                        Recibo Siesa
                        <input type="text" id="filtro_recibo_siesa" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'numero_siesa' },
                {
                    title: `
                        Estado
                        <input type="text" id="filtro_estado" class="form-control form-control-sm border-secondary">
                    `,
                    data: null,
                    render: (recibo, type, row) => {
                        // El nombre de estado aprobado se cambia si es para comprobantes
                        let estado = ($('#recibo_id_tipo').val() == 3 && recibo.recibo_estado_id == 1) ? 'Cargado en Siesa' : recibo.estado

                        return `
                            <div class="status-badge status-badge--style--${recibo.estado_clase} status-badge--has-text">
                                <div class="status-badge__body">
                                    <div class="status-badge__text">
                                        ${estado}
                                    </div>
                                </div>
                            </div>
                        `
                    }
                },
                {
                    title: `
                        Usuario creador
                        <input type="text" id="filtro_usuario_creador" class="form-control form-control-sm border-secondary">
                    `, 
                    data: null,
                    visible: ($('#recibo_id_tipo').val() == 3), // Visible si es comprobantes
                    render: (recibo, type, row) => {
                        return `${recibo.usuario_creacion}`
                    }
                },
                {
                    title: `
                        Comentarios
                        <input type="text" id="filtro_comentarios" class="form-control form-control-sm border-secondary">
                    `, 
                    data: null,
                    visible: ($('#recibo_id_tipo').val() == 3), // Visible si es comprobantes
                    render: (recibo, type, row) => {
                        let comentarios = recibo.comentarios || ''
                        return `${comentarios}`
                    }
                },
                {
                    title: `
                        Observaciones
                        <input type="text" id="filtro_observaciones" class="form-control form-control-sm border-secondary">
                    `, 
                    data: null,
                    visible: ($('#recibo_id_tipo').val() == 3), // Visible si es comprobantes
                    render: (recibo, type, row) => {
                        let observaciones = recibo.observaciones || ''
                        return `${observaciones}`
                    }
                },
                {
                    title: `
                        Valor
                        <input type="number" id="filtro_valor" class="form-control form-control-sm border-secondary">
                    `, 
                    data: null,
                    className: 'text-right',
                    render: (recibo, type, row) => {
                        return parseFloat(recibo.valor).toLocaleString('es-CO')
                    }
                },
                { 
                    title: `
                        Teléfono
                        <input type="text" id="filtro_telefono" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'telefono',
                    visible: ($('#recibo_id_tipo').val() == 1)},
                { 
                    title: `
                        Número de recibo
                        <input type="text" id="filtro_token" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'token',
                    visible: ($('#recibo_id_tipo').val() == 1)},
                {
                    title: 'Opciones',
                    data: null,
                    width: '170px',
                    render: (recibo, type, row) => {
                        let botonEliminarComprobante = ($('#recibo_id_tipo').val() == 3 && $('#configuracion_comprobantes_eliminar').val() == 1 && !recibo.fecha_actualizacion_bot)
                        ? `
                            <a type="button" class="btn btn-sm btn-danger" title="Eliminar" href="javascript:eliminarComprobante(${recibo.id})">
                                <i class="fas fa-trash"></i>
                            </a>
                        `
                        : ``

                        let botonReciboCaja = (($('#recibo_id_tipo').val() == 2 && recibo.wompi_status == 'APPROVED') || $('#recibo_id_tipo').val() == 3)
                        ? `
                            <a type="button" class="btn btn-sm btn-danger" href="${$('#site_url').val()}/reportes/pdf/recibo/${recibo.token}" target="_blank">
                                <i class="fas fa-file-pdf"></i>
                        </a>
                        ` 
                        : ``

                        return `
                            <td class="p-1">
                                ${botonReciboCaja}
                                
                                ${botonEliminarComprobante}
                            </td>
                        `
                    }
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
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaRecibos.draw())
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