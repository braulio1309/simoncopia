<div class="block-header" id="contenedor_cabecera_titulo">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Validación automática de los comprobantes contables</h1>
        </div>
    </div>
</div>

<div class="block pl-5 pr-5">
    <div class="card mb-lg-0" id="formulario_validacion_comprobantes">
        <div class="card-body">
            <form class="row">
                <!-- Tipo de comprobante -->
                <div class="col-lg-3 col-sm-12">
                    <label for="comprobante_tipo_id">Tipo de comprobante *</label>
                    <select id="comprobante_tipo_id" class="form-control">
                        <option value="">Seleccione...</option>
                        <?php foreach($this->configuracion_model->obtener('comprobantes_contables_tipos') as $tipo_comprobante) echo "<option value='$tipo_comprobante->id'  >$tipo_comprobante->nombre</option>"; ?>
                    </select>
                </div>

                <!-- Centro operativo -->
                <div class="col-lg-2 col-sm-12">
                    <label for="comprobante_sede_id">Sede *</label>
                    <select id="comprobante_sede_id" class="form-control">
                        <option value="">Seleccione...</option>
                        <?php foreach($this->configuracion_model->obtener('centros_operacion', ['ruta !=' => 'NULL']) as $sede) echo "<option value='$sede->id'>$sede->codigo - $sede->nombre</option>"; ?>
                    </select>
                </div>

                <!-- Año -->
                <div class="col-lg-1 col-sm-12">
                    <label for="comprobante_anio">Año *</label>
                    <select id="comprobante_anio" class="form-control">
                        <option value="">Seleccione...</option>
                        <option value="2026" selected>2026</option>
                        <option value="2025">2025</option>
                    </select>
                </div>

                <!-- Mes -->
                <div class="col-lg-2 col-sm-12">
                    <label for="comprobante_mes">Mes *</label>
                    <select id="comprobante_mes" class="form-control">
                        <option value="">Seleccione...</option>
                        <?php foreach($this->configuracion_model->obtener('periodos', ['nombre_comprobante_contable !=' => 'NULL']) as $mes) echo "<option value='$mes->id'>$mes->nombre</option>"; ?>
                    </select>
                </div>

                <!-- Consecutivo inicial -->
                <div class="col-lg-2 col-sm-12">
                    <label for="comprobante_consecutivo_inicial">Consecutivo inicial *</label>
                    <input type="number" class="form-control" id="comprobante_consecutivo_inicial">
                </div>

                <!-- Consecutivo final -->
                <div class="col-lg-2 col-sm-12">
                    <label for="comprobante_consecutivo_final">Consecutivo final *</label>
                    <input type="number" class="form-control" id="comprobante_consecutivo_final">
                </div>

                <div class="col-lg-12 mt-3">
                    <button type="submit" class="btn btn-primary btn-block">Programar tarea</button>
                </div>
            </form>

        </div>

        <div class="card-body" id="contenedor_comprobantes_contables"></div>
    </div>
</div>
<div id="contenedor_modal"></div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    const iniciarValidacion = async () => {
        let datosObligatorios = [
            $('#comprobante_tipo_id'),
            $('#comprobante_anio'),
            $('#comprobante_sede_id'),
            $('#comprobante_mes'),
            $('#comprobante_consecutivo_inicial'),
            $('#comprobante_consecutivo_final'),
        ]

        // Validación de campos obligatorios
        if (!validarCamposObligatorios(datosObligatorios)) return false

        let datos = {
            tipo: 'comprobantes_contables_tareas',
            comprobante_contable_tipo_id: $('#comprobante_tipo_id').val(),
            anio: $('#comprobante_anio').val(),
            centro_operacion_id: $('#comprobante_sede_id').val(),
            mes: $('#comprobante_mes').val(),
            consecutivo_inicial: $('#comprobante_consecutivo_inicial').val(),
            consecutivo_final: $('#comprobante_consecutivo_final').val(),
        }

        let confirmacion = await confirmar('continuar', `¿Está seguro de programar la tarea para validar los comprobantes?`)
        if(!confirmacion) return

        await consulta('crear', datos)

        mostrarAviso("exito", `Se ha creado la tarea programada exitosamente. En cuanto finalice la revisión, enviaremos una notificación.`, 30000)

        listarComprobantesContables()

        // await obtenerPromesa(`${$('#site_url').val()}contabilidad/procesar_comprobantes`, datos).then(respuesta => {
        //     Swal.close()
        //     mostrarAviso((respuesta.resultado) ? 'exito' : 'alerta', respuesta.mensaje, 20000)
        //     console.log(respuesta)

        //     listarComprobantesContables()

        //     // Log de éxito
        //     //
        // })
        // .catch(error => {
        //     Swal.close()
        //     mostrarAviso('error', 'Ocurrió un error al procesar las carpetas.', 20000)
        //     console.error(error)
        //     // Log de error
        //     //
        // })
    }

    cargarConsecutivos = async (datos = null) => {
        cargarInterfaz('contabilidad/validacion_comprobantes/consecutivos_detalle', 'contenedor_modal', datos)
    }

    const listarComprobantesContables = () => {
        cargarInterfaz('contabilidad/validacion_comprobantes/lista', 'contenedor_comprobantes_contables')
    }

    $().ready(() => {
        $('#comprobante_mes').val(<?php echo date('m'); ?>)

        listarComprobantesContables()

        $('#formulario_validacion_comprobantes').submit(async(evento) => {
            evento.preventDefault()

            iniciarValidacion()
        })
    })
</script>