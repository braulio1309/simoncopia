<!-- Se captura el nit si viene desde el proveedor logueado que desea hacer la consulta -->
<input type="hidden" id="nit_proveedor" value="<?php echo $this->input->get('nit'); ?>">

<!-- Si viene NIT en la URL y es diferente al NIT de la sesión, se redirecciona al inicio -->
<?php if(ENVIRONMENT != 'development' && $this->input->get('nit') && $this->input->get('nit') != $this->session->userdata('documento_numero')) redirect(); ?>

<div class="block-header" id="contenedor_cabecera_titulo">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Descarga tus certificados tributarios</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0" id="formulario_buscar_proveedor">
            <div class="card-body card-body">
                <form class="row">
                    <div class="form-group col-sm-12">
                        <label for="numero_documento">Digita tu número de documento o NIT *</label>
                        <input type="number" class="form-control" id="numero_documento" placeholder="Sin espacios, guiones ni dígito de verificación" value="<?php if($this->input->get('nit') != '') echo $this->input->get('nit'); ?>" autofocus>
                    </div>

                    <div class="form-group col-sm-12 col-lg-12">
                        <button type="submit" class="btn btn-primary btn-block" id="btn_certificados">Consultar</button>

                        <div class="mt-2" id="contenedor_mensaje_carga"></div>
                    </div>
                </form>
            </div>
        </div>

        <div class="form-row d-none" id="contenedor_generacion_certificado">
            <div class="form-group col-12">
                <label for="certificado_anio">Selecciona el año *</label>
                <select id="certificado_anio" class="form-control">
                    <option value="2025">2025</option>
                </select>
            </div>

            <button class="btn btn-primary btn-block" onClick="javascript:procesarMovimientosContables()">Generar</button>
        </div>

        <div class="block-space block-space--layout--before-footer"></div>
    </div>
</div>

<script>
    let numeroDocumento = $('#numero_documento')

    /**
     * Va al webhook y descarga todos los movimientos contables aplicables a retenciones
     */
    procesarMovimientosContables = async numero => {
        let datosObligatorios = [
            $('#certificado_anio'),
        ]

        // Validación de campos obligatorios
        if (!validarCamposObligatorios(datosObligatorios)) return false

        Swal.fire({
            title: 'Estamos generando el certificado...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        // Ejecución del webhook que extrae los datos del WMS
        await fetch(`${$("#site_url").val()}webhooks/importar_movimientos_contables_api/${numeroDocumento.val()}`)
            .then(respuesta => respuesta.json())
            .catch(error => console.error(error))

        Swal.close()

        // Generación del reporte
        await generarReporte('pdf/proveedores_certificado_retenciones', {
            documento_numero: numeroDocumento.val(),
            anio: $('#certificado_anio').val(),
        });

        mostrarAviso('exito', `El certificado se generó exitosamente. ¡Gracias por usar los servicios de Repuestos Simón Bolívar!`, 20000)
    }

    validarProveedor = async (nit = null) => {
        let datosObligatorios = [
            numeroDocumento,
        ]

        // Si no trae NIT, se deben validar todos los campos
        if(!nit) {
            // datosObligatorios.push(numeroTelefono)
        }

        // Validación de campos obligatorios
        if (!validarCamposObligatorios(datosObligatorios)) return false

        let datosContacto = {
            tipo: 'tercero_contacto',
            nit: numeroDocumento.val(),
        }

        // Si no trae NIT, consulta el contacto
        if(!nit) {
            // Se verifica que el número de teléfono exista en la base de datos
            let contacto = await consulta('obtener', datosContacto)

            // Si no se encontró el contacto
            if(!contacto.resultado) {
                mostrarAviso('alerta', 'El número de teléfono que nos indicas no coincide con el número de documento. Por favor, verifica nuevamente o ponte en contacto con nosotros.', 30000)
                agregarLog(89, JSON.stringify(datosContacto))
                return false
            }
        }

        // Se activa el spinner
        $('#btn_certificados').addClass('btn-loading').attr('disabled', true)

        // Mensaje mientras se consultan los datos
        $('#contenedor_mensaje_carga').html(`<button class='btn btn-muted btn-loading btn-xs btn-icon'></button> Estamos buscando datos...`)

        $('#contenedor_generacion_certificado').removeClass('d-none')
        $('#btn_cuentas_por_pagar').hide()
        numeroDocumento.attr('disabled', true)
        $('#formulario_buscar_proveedor').remove()
    }

    $().ready(() => {
        // Si viene NIT desde el proveedor que quiere ver sus facturas
        if($('#nit_proveedor').val() != '') validarProveedor($('#nit_proveedor').val())

        $('#formulario_buscar_proveedor').submit(async evento => {
            evento.preventDefault()

            validarProveedor()
        })
    })
</script>