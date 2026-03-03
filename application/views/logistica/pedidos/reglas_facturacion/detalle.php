<?php
if (isset($id)) {
    $regla = $this->logistica_model->obtener('facturacion_reglas', ['id' => $id]);
    echo "<input type='hidden' id='regla_id' value='$regla->id' />";
}
$titulo_pagina = isset($id) ? 'Editar regla de facturación' : 'Crear regla de facturación';
?>

<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title"><?php echo $titulo_pagina; ?></h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="form-row">
                    <div class="form-group col-lg-4">
                        <label for="regla_cliente_nit">NIT del cliente *</label>
                        <input type="number" class="form-control" id="regla_cliente_nit" value="<?php echo (isset($regla) ? $regla->cliente_nit : ''); ?>">
                    </div>

                    <div class="form-group col-lg-8">
                        <label for="regla_nombre">Nombre *</label>
                        <input type="text" class="form-control" id="regla_nombre" value="<?php echo (isset($regla) ? $regla->nombre : ''); ?>">
                    </div>

                    <div class="form-group col-lg-4">
                        <label for="regla_tipo_frecuencia">Tipo de frecuencia *</label>
                        <select id="regla_tipo_frecuencia" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="diaria" <?php echo (isset($regla) && $regla->tipo_frecuencia == 'diaria' ? 'selected' : ''); ?>>Diaria</option>
                            <option value="semanal" <?php echo (isset($regla) && $regla->tipo_frecuencia == 'semanal' ? 'selected' : ''); ?>>Semanal</option>
                            <option value="mensual" <?php echo (isset($regla) && $regla->tipo_frecuencia == 'mensual' ? 'selected' : ''); ?>>Mensual</option>
                            <option value="personalizada" <?php echo (isset($regla) && $regla->tipo_frecuencia == 'personalizada' ? 'selected' : ''); ?>>Personalizada</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-4" id="grupo_dia_semana" style="display: none;">
                        <label for="regla_dia_semana">Día de la semana (1=Lunes, 7=Domingo) *</label>
                        <input type="number" class="form-control" id="regla_dia_semana" min="1" max="7" value="<?php echo (isset($regla) ? $regla->dia_semana : ''); ?>">
                    </div>

                    <div class="form-group col-lg-4" id="grupo_dia_mes" style="display: none;">
                        <label for="regla_dia_mes">Día del mes (1-31) *</label>
                        <input type="number" class="form-control" id="regla_dia_mes" min="1" max="31" value="<?php echo (isset($regla) ? $regla->dia_mes : ''); ?>">
                    </div>

                    <div class="form-group col-lg-4">
                        <label for="regla_hora_programada">Hora programada</label>
                        <input type="time" class="form-control" id="regla_hora_programada" value="<?php echo (isset($regla) ? $regla->hora_programada : '00:00'); ?>">
                    </div>

                    <div class="form-group col-lg-4">
                        <label for="regla_activa">Activa *</label>
                        <select id="regla_activa" class="form-control">
                            <option value="1" <?php echo (isset($regla) && $regla->activa == 1 ? 'selected' : (!isset($regla) ? 'selected' : '')); ?>>Sí</option>
                            <option value="0" <?php echo (isset($regla) && $regla->activa == 0 ? 'selected' : ''); ?>>No</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-4">
                        <label for="regla_requiere_orden_compra">Requiere orden de compra *</label>
                        <select id="regla_requiere_orden_compra" class="form-control">
                            <option value="0" <?php echo (isset($regla) && $regla->requiere_orden_compra == 0 ? 'selected' : (!isset($regla) ? 'selected' : '')); ?>>No</option>
                            <option value="1" <?php echo (isset($regla) && $regla->requiere_orden_compra == 1 ? 'selected' : ''); ?>>Sí</option>
                        </select>
                    </div>
                </div>

                <button class="btn btn-info" onclick="javascript:history.back()">Volver</button>
                <button class="btn btn-success" onclick="javascript:guardarReglaFacturacion()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    guardarReglaFacturacion = async () => {
        let id = $("#regla_id").val()

        let camposObligatorios = [
            $("#regla_cliente_nit"),
            $("#regla_nombre"),
            $("#regla_tipo_frecuencia")
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let tipoFrecuencia = $("#regla_tipo_frecuencia").val()

        // Validar campos condicionales
        if (tipoFrecuencia === 'semanal' && !$("#regla_dia_semana").val()) {
            mostrarAviso('alerta', 'Debe ingresar el día de la semana')
            return false
        }

        if (tipoFrecuencia === 'mensual' && !$("#regla_dia_mes").val()) {
            mostrarAviso('alerta', 'Debe ingresar el día del mes')
            return false
        }

        let datos = {
            tipo: 'facturacion_reglas',
            cliente_nit: $("#regla_cliente_nit").val(),
            nombre: $("#regla_nombre").val(),
            tipo_frecuencia: tipoFrecuencia,
            dia_semana: tipoFrecuencia === 'semanal' ? $("#regla_dia_semana").val() : null,
            dia_mes: tipoFrecuencia === 'mensual' ? $("#regla_dia_mes").val() : null,
            hora_programada: $("#regla_hora_programada").val(),
            activa: $("#regla_activa").val(),
            requiere_orden_compra: $("#regla_requiere_orden_compra").val()
        }

        if (id) {
            datos.id = id
            await consulta('actualizar', datos, false)
        } else {
            await consulta('crear', datos, false)
        }

        mostrarAviso('exito', 'Regla de facturación guardada correctamente')
        setTimeout(() => history.back(), 1500)
    }

    $().ready(() => {
        const actualizarCamposCondicionales = () => {
            let tipo = $("#regla_tipo_frecuencia").val()

            $("#grupo_dia_semana").hide()
            $("#grupo_dia_mes").hide()

            if (tipo === 'semanal') {
                $("#grupo_dia_semana").show()
            } else if (tipo === 'mensual') {
                $("#grupo_dia_mes").show()
            }
        }

        $("#regla_tipo_frecuencia").change(actualizarCamposCondicionales)

        // Inicializar campos condicionales según el valor actual
        actualizarCamposCondicionales()
    })
</script>
