<?php if (isset($id)) {
    $beneficio = $this->marketing_model->obtener('marketing_beneficios', ['id' => $id]);
    echo "<input type='hidden' id='beneficio_id' value='$beneficio->id' />";
}
?>
<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="form-row">
                    <div class="form-group col-lg-6">
                        <label for="beneficio_nombre">Nombre *</label>
                        <input type="text" class="form-control" id="beneficio_nombre" value="<?php echo (isset($beneficio) ? $beneficio->nombre : ''); ?>">
                    </div>
                    <div class="form-group col-lg-6">
                        <label for="beneficio_tipo">Tipo *</label>
                        <select id="beneficio_tipo" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="promoción" <?php echo (isset($beneficio) && $beneficio->beneficio_tipo == 'promoción' ? 'selected' : ''); ?>>Promoción</option>
                            <option value="código descuento" <?php echo (isset($beneficio) && $beneficio->beneficio_tipo == 'código descuento' ? 'selected' : ''); ?>>Código descuento</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-6" id="grupo_codigo_descuento" style="display: none;">
                        <label for="codigo_descuento">Código de descuento</label>
                        <input type="text" class="form-control" id="codigo_descuento" value="<?php echo (isset($beneficio) ? $beneficio->codigo_descuento : ''); ?>">
                    </div>
                    <div class="form-group col-lg-6">
                        <label for="tipo_venta">Tipo de venta *</label>
                        <select id="tipo_venta" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="contado" <?php echo (isset($beneficio) && $beneficio->tipo_venta == 'contado' ? 'selected' : ''); ?>>Contado</option>
                            <option value="crédito" <?php echo (isset($beneficio) && $beneficio->tipo_venta == 'crédito' ? 'selected' : ''); ?>>Crédito</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-3">
                        <label for="fecha_inicio">Fecha inicial *</label>
                        <input type="date" class="form-control" id="fecha_inicio" value="<?php echo (isset($beneficio)) ? $beneficio->fecha_inicio : date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group col-lg-3">
                        <label for="fecha_final">Fecha final *</label>
                        <input type="date" class="form-control" id="fecha_final" value="<?php echo (isset($beneficio)) ? $beneficio->fecha_final : date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group col-lg-3">
                        <label for="presupuesto">Presupuesto (valor máximo) *</label>
                        <input type="number" class="form-control" id="presupuesto" step="0.01" min="0" value="<?php echo (isset($beneficio) ? $beneficio->presupuesto : ''); ?>">
                    </div>
                    <div class="form-group col-lg-3">
                        <label for="limite_uso">Límite de uso *</label>
                        <input type="number" class="form-control" id="limite_uso" min="0" value="<?php echo (isset($beneficio) ? $beneficio->limite_uso : ''); ?>" placeholder="Cantidad de items">
                    </div>
                </div>
                <button class="btn btn-info" onClick="javascript:history.back()">Volver</button>
                <button class="btn btn-success" onclick="javascript:guardarBeneficio()">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>
<script>
    guardarBeneficio = async () => {
        let id = $("#beneficio_id").val()
        let camposObligatorios = [
            $("#beneficio_nombre"),
            $("#beneficio_tipo"),
            $("#fecha_inicio"),
            $("#fecha_final"),
            $("#presupuesto"),
            $("#limite_uso"),
            $("#tipo_venta")
        ]
        if (!validarCamposObligatorios(camposObligatorios)) return false
        // Si el tipo es "código descuento", validar que el código esté presente
        if ($("#beneficio_tipo").val() === "código descuento" && !$("#codigo_descuento").val()) {
            mostrarAviso('alerta', 'Debe ingresar un código de descuento')
            return false
        }
        let datos = {
            tipo: 'marketing_beneficios',
            nombre: $("#beneficio_nombre").val(),
            beneficio_tipo: $("#beneficio_tipo").val(),
            codigo_descuento: $("#codigo_descuento").val(),
            fecha_inicio: $("#fecha_inicio").val(),
            fecha_final: $("#fecha_final").val(),
            presupuesto: $("#presupuesto").val(),
            limite_uso: $("#limite_uso").val(),
            tipo_venta: $("#tipo_venta").val(),
        }
        // Crear o actualizar beneficio
        if (id) {
            datos.id = id
            await consulta('actualizar', datos, false)
        } else {
            await consulta('crear', datos, false)
        }
        mostrarAviso('exito', 'Beneficio guardado correctamente')
        setTimeout(() => history.back(), 1500)
    }
    $().ready(() => {
        // Mostrar/ocultar campo de código de descuento según el tipo
        $("#beneficio_tipo").change(function() {
            if ($(this).val() === "código descuento") {
                $("#grupo_codigo_descuento").show()
            } else {
                $("#grupo_codigo_descuento").hide()
                $("#codigo_descuento").val('')
            }
        })
        // Disparar el evento change al cargar la página si ya hay un tipo seleccionado
        if ($("#beneficio_tipo").val() === "código descuento") {
            $("#grupo_codigo_descuento").show()
        }
    })
</script>