<?php
$id_pago = $this->uri->segment(4);
$pago = null;
if ($id_pago) {
    $pago = $this->importaciones_pagos_model->obtener(['id' => $id_pago]);
    $pago = $pago ? $pago[0] : null;

}

$tipos_pago = $this->importaciones_model->obtener('importaciones_pagos_tipos');
$importaciones = $this->importaciones_model->obtener('importaciones');
$usuarios = $this->configuracion_model->obtener('usuarios', ['activo' => 1]);
?>

<style>
    .form-container-pdf {
        background: #fff;
        padding: 30px;
        border-radius: 8px;
    }

    .pdf-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #333;
        margin-top: 25px;
        margin-bottom: 15px;
        border-bottom: 1px solid #eee;
        padding-bottom: 8px;
    }

    .form-group label {
        font-weight: 500;
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .form-control {
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 10px;
        height: auto;
    }

    .btn-save-pdf {
        background-color: #28a745;
        border: none;
        padding: 12px 30px;
        font-weight: 600;
        border-radius: 6px;
    }

    .btn-cancel-pdf {
        background: transparent;
        color: #666;
        border: none;
        padding: 12px 30px;
        font-weight: 600;
    }

    .header-pdf {
        margin-bottom: 30px;
    }

    .header-pdf h1 {
        font-size: 1.8rem;
        font-weight: 700;
    }
</style>

<div class="container mt-4 mb-5">
    <div class="header-pdf">
        <a href="<?php echo site_url('importaciones/pagos'); ?>" class="text-muted"><i class="fas fa-arrow-left"></i> Volver</a>
        <h1 class="mt-2"><?php echo $pago ? 'Editar Pago' : 'Nuevo Pago'; ?></h1>
        <p class="text-muted">Registra un nuevo pago de importación</p>
    </div>

    <form id="form_pago" class="form-container-pdf border shadow-sm" enctype="multipart/form-data">
        <input type="hidden" id="id" name="id" value="<?php echo $pago ? $pago->id : ''; ?>">

        <div class="pdf-section-title">Información Básica</div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Fecha de Pago *</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $pago ? $pago->fecha : date('Y-m-d'); ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tipo de Pago *</label>
                    <select class="form-control" id="tipo_pago_id" name="tipo_pago_id" required>
                        <option value="">Seleccionar tipo</option>
                        <?php foreach ($tipos_pago as $tipo): ?>
                            <option value="<?php echo $tipo->id; ?>" <?php echo ($pago && $pago->tipo_pago_id == $tipo->id) ? 'selected' : ''; ?>>
                                <?php echo $tipo->nombre; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Estado *</label>
                    <select class="form-control" id="estado_id" name="estado_id" required>
                        <option value="1" <?php echo ($pago && $pago->estado_id == 1) ? 'selected' : 'selected'; ?>>Pendiente por pagar</option>
                        <option value="0" <?php echo ($pago && $pago->estado_id == 0) ? 'selected' : ''; ?>>Pagado</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="pdf-section-title">Montos</div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Moneda del Pago *</label>
                    <select class="form-control" id="tipo_moneda_id" name="tipo_moneda_id" required onchange="calcularValorCOP()">
                        <option value="2" <?php echo ($pago && $pago->tipo_moneda_id == 2) ? 'selected' : 'selected'; ?>>USD</option>
                        <option value="1" <?php echo ($pago && $pago->tipo_moneda_id == 1) ? 'selected' : ''; ?>>COP</option>
                        <option value="3" <?php echo ($pago && $pago->tipo_moneda_id == 3) ? 'selected' : ''; ?>>EUR</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Valor a Pagar (Moneda Ext.) *</label>
                    <input type="number" step="0.01" class="form-control" id="valor_moneda_extranjera" name="valor_moneda_extranjera" value="<?php echo $pago ? $pago->valor_moneda_extranjera : ''; ?>" required onkeyup="calcularValorCOP()">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Tasa (TRM)</label>
                    <input type="number" step="0.01" class="form-control" id="valor_trm" name="valor_trm" value="<?php echo $pago ? $pago->valor_trm : ''; ?>" onkeyup="calcularValorCOP()">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Valor a Pagar (COP)</label>
                    <input type="number" class="form-control" id="valor_cop" name="valor_cop" value="<?php echo $pago ? $pago->valor_cop : ''; ?>" readonly style="background-color: #f9fafb;">
                </div>
            </div>
        </div>

        <div class="pdf-section-title">Relaciones</div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Proveedor/Entidad (Importación) *</label>
                    <select class="form-control" id="importacion_id" name="importacion_id" required>
                        <option value="">Seleccionar importación</option>
                        <?php foreach ($importaciones as $imp): ?>
                            <option value="<?php echo $imp->id; ?>" <?php echo ($pago && $pago->importacion_id == $imp->id) ? 'selected' : ''; ?>>
                                <?php echo $imp->numero_orden_compra; ?> - <?php echo $imp->razon_social; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Número de OC</label>
                    <input type="text" class="form-control" id="orden_compra" name="orden_compra" value="<?php echo $pago ? $pago->orden_compra : ''; ?>">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Factura Comercial</label>
                    <input type="text" class="form-control" id="factura_numero" name="factura_numero" value="<?php echo $pago ? $pago->factura_numero : ''; ?>">
                </div>
            </div>
        </div>

        <div class="pdf-section-title">Información Bancaria</div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Referencia Bancaria (SWIFT/ID)</label>
                    <input type="text" class="form-control" id="referencia_bancaria" name="referencia_bancaria" value="<?php echo $pago ? $pago->referencia_bancaria : ''; ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Soporte Bancario (Archivo)</label>
                    <input type="file" class="form-control-file" id="comprobante" name="comprobante" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Origen de Recursos *</label>
                    <select class="form-control" id="origen_recursos" name="origen_recursos" required>
                        <option value="1" <?php echo ($pago && $pago->origen_recursos == 1) ? 'selected' : ''; ?>>Recursos propios</option>
                        <option value="2" <?php echo ($pago && $pago->origen_recursos == 2) ? 'selected' : ''; ?>>Financiación</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="pdf-section-title">Legalización</div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Requiere Legalización *</label>
                    <select class="form-control" id="requiere_legalizacion" name="requiere_legalizacion" onchange="toggleFechaLegalizacion()" required>
                        <option value="0" <?php echo ($pago && $pago->requiere_legalizacion == 0) ? 'selected' : 'selected'; ?>>No</option>
                        <option value="1" <?php echo ($pago && $pago->requiere_legalizacion == 1) ? 'selected' : ''; ?>>Sí</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4" id="grupo_fecha_legalizacion" style="display: none;">
                <div class="form-group">
                    <label>Fecha Estimada Legalización</label>
                    <input type="date" class="form-control" id="fecha_legalizacion" name="fecha_legalizacion" value="<?php echo $pago ? $pago->fecha_legalizacion : ''; ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Usuario que Solicita *</label>
                    <select class="form-control" id="usuario_solicita_id" name="usuario_solicita_id" required>
                        <option value="">Seleccionar usuario</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?php echo $usuario->id; ?>" <?php echo ($pago && $pago->usuario_solicita_id == $usuario->id) ? 'selected' : ''; ?>>
                                <?php echo $usuario->nombres; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="pdf-section-title">Observaciones</div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Notas adicionales sobre el pago..."><?php echo $pago ? $pago->observaciones : ''; ?></textarea>
                </div>
            </div>
        </div>

        <div class="text-right mt-5">
            <a href="<?php echo site_url('importaciones/pagos'); ?>" class="btn btn-cancel-pdf">Cancelar</a>
            <button type="button" class="btn btn-primary btn-save-pdf" onclick="guardarPago(this)">
                <i class="fas fa-save"></i> Guardar Pago
            </button>
        </div>
    </form>
</div>

<script>
    function calcularValorCOP() {
        const tipoMoneda = $('#tipo_moneda_id').val();
        const valorMoneda = parseFloat($('#valor_moneda_extranjera').val()) || 0;
        const valorTRM = parseFloat($('#valor_trm').val()) || 1;
        let valorCOP = (tipoMoneda == '1') ? valorMoneda : valorMoneda * valorTRM;
        $('#valor_cop').val(valorCOP.toFixed(2));
    }

    function toggleFechaLegalizacion() {
        if ($('#requiere_legalizacion').val() == '1') {
            $('#grupo_fecha_legalizacion').fadeIn();
        } else {
            $('#grupo_fecha_legalizacion').fadeOut();
        }
    }

    async function guardarPago(btn) {
        // Si el botón ya está deshabilitado, salimos para evitar la doble ejecución
        if ($(btn).is(':disabled')) return;

        // 1. Validaciones
        const campos = [
            $('#importacion_id'), $('#fecha'), $('#tipo_pago_id'),
            $('#tipo_moneda_id'), $('#valor_moneda_extranjera'),
            $('#origen_recursos'), $('#usuario_solicita_id')
        ];

        if (!validarCamposObligatorios(campos)) return false;

        // Deshabilitamos el botón y cambiamos el texto visualmente
        $(btn).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

        try {
            const idExistente = $('#id').val();
            const accion = idExistente ? 'actualizar' : 'crear';

            // 2. Preparar datos para 'consulta'
            const datos = {
                tipo: 'importaciones_pagos',
                id: idExistente,
                importacion_id: $('#importacion_id').val(),
                fecha: $('#fecha').val(),
                tipo_pago_id: $('#tipo_pago_id').val(),
                estado_id: $('#estado_id').val(),
                tipo_moneda_id: $('#tipo_moneda_id').val(),
                valor_moneda_extranjera: $('#valor_moneda_extranjera').val(),
                valor_trm: $('#valor_trm').val(),
                valor_cop: $('#valor_cop').val(),
                orden_compra: $('#orden_compra').val(),
                factura_numero: $('#factura_numero').val(),
                referencia_bancaria: $('#referencia_bancaria').val(),
                origen_recursos: $('#origen_recursos').val(),
                requiere_legalizacion: $('#requiere_legalizacion').val(),
                fecha_legalizacion: $('#fecha_legalizacion').val(),
                usuario_solicita_id: $('#usuario_solicita_id').val(),
                observaciones: $('#observaciones').val()
            };

            // 3. Guardar datos vía consulta
            const respuesta = await consulta(accion, datos);

            if (respuesta) {
                const idRegistro = respuesta.resultado;
                const inputArchivo = $('#comprobante')[0];

                // 4. Subida de archivo (solo si hay uno seleccionado)
                if (inputArchivo.files.length > 0) {
                    const formData = new FormData();
                    formData.append('comprobante', inputArchivo.files[0]);
                    formData.append('id', idRegistro);

                    await $.ajax({
                        url: '<?php echo site_url("importaciones/pagos/guardar?solo_archivo=1"); ?>',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false
                    });
                }

                mostrarAviso('exito', 'Pago procesado correctamente');
                setTimeout(() => window.location.href = '<?php echo site_url("importaciones/pagos"); ?>', 1000);

            } else {
                throw new Error('Error en el servidor');
            }

        } catch (error) {
            console.error(error);
            mostrarAviso('error', 'Hubo un error al procesar los datos');

            // Si hay error, volvemos a habilitar el botón para permitir reintento
            $(btn).prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Pago');
        }
    }

    $(document).ready(function() {
        calcularValorCOP();
        toggleFechaLegalizacion();
    });
</script>