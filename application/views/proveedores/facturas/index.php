<!-- Se captura el nit si viene desde el proveedor logueado que desea hacer la consulta -->
<input type="hidden" id="nit_proveedor" value="<?php echo $this->input->get('nit'); ?>">

<!-- Si viene NIT en la URL y es diferente al NIT de la sesión, se redirecciona al inicio -->
<?php if(ENVIRONMENT != 'development' && $this->input->get('nit') && $this->input->get('nit') != $this->session->userdata('documento_numero')) redirect(); ?>

<div class="block-header" id="contenedor_cabecera_titulo">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Consulta tus facturas</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0" id="formulario_buscar_proveedor">
            <div class="card-body card-body">
                <form class="row">
                    <div class="form-group col-sm-12 col-lg-6">
                        <label for="numero_documento">Digita tu número de documento o NIT *</label>
                        <input type="number" class="form-control" id="numero_documento" placeholder="Sin espacios, guiones ni dígito de verificación" value="<?php if($this->input->get('nit') != '') echo $this->input->get('nit'); ?>" autofocus>
                    </div>

                    <div class="form-group col-sm-12 col-lg-6">
                        <label for="telefono">Digita el número de celular *</label>
                        <input type="number" class="form-control" id="telefono">
                    </div>

                    <div class="form-group col-sm-12 col-lg-12">
                        <button type="submit" class="btn btn-primary btn-block" id="btn_cuentas_por_pagar">Consultar</button>

                        <div class="mt-2" id="contenedor_mensaje_carga"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="w-100 p-5">
    <div id="contenedor_cuentas_por_pagar_lista"></div>
</div>

<script>
    let numeroDocumento = $('#numero_documento')
    let numeroTelefono = $('#telefono')

    procesarCuentasPorPagar = numero => {
        const opcionesPeticion = {
            method: "GET",
            redirect: "follow"
        }

        $('#contenedor_mensaje_carga').html(`<button class='btn btn-muted btn-loading btn-xs btn-icon'></button> Preparando la visualización de los datos...`)

        // Ejecución del webhook que extrae los datos de la API de Siesa
        fetch(`${$("#site_url").val()}tareas/erp/importar_cuentas_por_pagar/${numero}`, opcionesPeticion)
            .then(respuesta => {
                if (!respuesta.ok) {
                    $('#btn_cuentas_por_pagar').removeClass('btn-loading').attr('disabled', false)
                    $('#contenedor_mensaje_carga').html('')
                    mostrarAviso('alerta', 'No se encontraron resultados con el número de documento que nos indicas. Por favor, asegúrate de que el número sea correcto o no tenga dígito de verificación.', 30000)
                    
                    throw new Error(`Error en la petición: ${respuesta.status}`)
                    return false
                }

                return respuesta.json()
            })
            .then(datos => {
                agregarLog(76, `Número de documento ${numeroDocumento.val()}`)

                cargarInterfaz('proveedores/facturas/lista', 'contenedor_cuentas_por_pagar_lista', {numero_documento: numero})

                $('#btn_cuentas_por_pagar').hide()
                numeroDocumento.attr('disabled', true)
                $('#formulario_buscar_proveedor').remove() 
            })
            .catch(error => {
                agregarLog(77, `Número de documento ${numeroDocumento.val()}`)
                mostrarAviso('error', 'Ocurrió un error consultando las cuentas por pagar. Intenta de nuevo más tarde.', 30000)
                $('#contenedor_mensaje_carga').html('')
                return false
            })
    }

    validarProveedor = async (nit = null) => {
        let datosObligatorios = [
            numeroDocumento,
        ]

        // Si no trae NIT, se deben validar todos los campos
        if(!nit) {
            datosObligatorios.push(numeroTelefono)
        }

        // Validación de campos obligatorios
        if (!validarCamposObligatorios(datosObligatorios)) return false

        let datosContacto = {
            tipo: 'tercero_contacto',
            nit: numeroDocumento.val(),
            numero: numeroTelefono.val(),
        }

        // Si no trae NIT, consulta el contacto
        if(!nit) {
            // Se verifica que el número de teléfono exista en la base de datos
            let contacto = await consulta('obtener', datosContacto)

            // Si no se encontró el contacto
            if(!contacto.resultado) {
                mostrarAviso('alerta', 'El número de teléfono que nos indicas no coincide con el número de documento. Por favor, verifica nuevamente o ponte en contacto con nosotros.', 30000)
                agregarLog(35, JSON.stringify(datosContacto))
                return false
            }
        }

        // Se activa el spinner
        $('#btn_cuentas_por_pagar').addClass('btn-loading').attr('disabled', true)

        agregarLog(78, `Número de documento ${numeroDocumento.val()}`)

        // Mensaje mientras se consultan los datos
        $('#contenedor_mensaje_carga').html(`<button class='btn btn-muted btn-loading btn-xs btn-icon'></button> Estamos buscando tus facturas...`)

        procesarCuentasPorPagar(numeroDocumento.val())
    }

    $().ready(() => {
        // Si viene NIT desde el proveedor que quiere ver sus facturas
        if($('#nit_proveedor').val() != '') validarProveedor($('#nit_proveedor').val())

        $('#formulario_buscar_proveedor').submit(async (evento) => {
            evento.preventDefault()

            validarProveedor()
        })
    })
</script>