<!-- Se captura el nit si viene desde el formulario de comprobante -->
<input type="hidden" id="nit_comprobante" value="<?php echo $this->input->get('nit'); ?>">

<div class="block-header" id="contenedor_cabecera_titulo">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Pagar tus facturas de una manera fácil y rápida</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0" id="formulario_buscar_cliente">
            <div class="card-body card-body">
                <form class="row">
                    <!-- Número de documento -->
                    <div class="form-group col-sm-12 col-lg-12">
                        <label for="estado_cuenta_numero_documento">Digita tu número de documento o NIT *</label>
                        <input type="number" class="form-control" id="estado_cuenta_numero_documento" placeholder="Sin espacios, guiones ni dígito de verificación" value="<?php if($this->input->get('nit') != '') echo $this->input->get('nit'); ?>" autofocus>
                    </div>

                    <!-- Si trae un NIT desde la subida de comprobantes, carga los campos -->
                    <?php if($this->input->get('nit') == '') { ?>
                        <!-- Teléfono -->
                        <div class="form-group col-sm-12 col-lg-6">
                            <label for="estado_cuenta_telefono">Digita el número de celular *</label>
                            <input type="number" class="form-control" id="estado_cuenta_telefono" value="<?php if(ENVIRONMENT == 'development') echo '3218524528'; ?>">
                        </div>

                        <!-- Email -->
                        <div class="form-group col-sm-12 col-lg-6">
                            <label for="estado_cuenta_email">Correo electrónico al que deseas que lleguen los soportes *</label>
                            <input type="email" class="form-control" id="estado_cuenta_email">
                        </div>
                    <?php } ?>

                    <div class="form-group col-sm-12 col-lg-12">
                        <button type="submit" class="btn btn-primary btn-block" id="btn_estado_cuenta_cliente">Consultar mis facturas</button>

                        <div class="mt-2" id="contenedor_mensaje_carga"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="contenedor_estado_cuenta"></div>

<script>
    var numeroDocumento = $('#estado_cuenta_numero_documento')
    var numeroTelefono = $('#estado_cuenta_telefono')
    var email = $('#estado_cuenta_email')

    procesarBusqueda = async (nit = null) => {
        let datosObligatorios = [
            numeroDocumento,
        ]

        // Si no trae NIT, se deben validar todos los campos
        if(!nit) {
            datosObligatorios.push(numeroTelefono)
            datosObligatorios.push(email)
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

            // En localStorage se almacena el email de contacto
            localStorage.simonBolivar_emailContacto = email.val()
        }

        // Se activa el spinner
        $('#btn_estado_cuenta_cliente').addClass('btn-loading').attr('disabled', true)

        agregarLog(22, `Número de documento ${numeroDocumento.val()}`)

        // Mensaje mientras se consultan los datos
        $('#contenedor_mensaje_carga').html(`<button class='btn btn-muted btn-loading btn-xs btn-icon'></button> Consultando los datos del cliente...`)
        
        let consultaTercero = consulta('obtener', {tipo: 'terceros', numero_documento: numeroDocumento.val()}, false)
        let consultaEstadoCuentaCliente = consulta('obtener', {tipo: 'estado_cuenta_cliente', numero_documento: numeroDocumento.val()}, false)
        
        Promise.all([consultaTercero, consultaEstadoCuentaCliente])
        .then(async(resultado) => {
            // Obtenemos e insertamos las sucursales por aparte, para recorrer la paginación
            await gestionarSucursales(numeroDocumento.val())

            $('#contenedor_mensaje_carga').html(`<button class='btn btn-muted btn-loading btn-xs btn-icon'></button> Preparando la visualización de los datos...`)

            let tercero = resultado[0]
            let facturas = resultado[1]

            // Si no se encuentra el estado de cuenta
            if(facturas.codigo && facturas.codigo == 1) {
                $('#btn_estado_cuenta_cliente').removeClass('btn-loading').attr('disabled', false)
                $('#contenedor_mensaje_carga').html('')
                mostrarAviso('alerta', 'No se encontraron resultados con el número de documento que nos indicas. Por favor, asegúrate de que el número sea correcto o no tenga dígito de verificación.', 30000)
                agregarLog(23, `Número de documento ${numeroDocumento.val()}`)
                return false
            }

            let creacionTercero = consulta('crear', {tipo: 'tercero', valores: tercero.detalle.Table}, false)
            let creacionFacturas = consulta('crear', {tipo: 'clientes_facturas', valores: facturas.detalle.Table}, false)

            Promise.all([creacionTercero, creacionFacturas])
            .then(async(resultado) => {                    
                agregarLog(24, `Número de documento ${numeroDocumento.val()}`)
                
                // location.href = `${$('#site_url').val()}clientes/estado_cuenta/${numeroDocumento.val()}`
                cargarInterfaz('clientes/estado_cuenta/facturas/index', 'contenedor_estado_cuenta', {numero_documento: numeroDocumento.val(), nit_comprobante: $('#nit_comprobante').val()})

                $('#btn_estado_cuenta_cliente').hide()
                numeroDocumento.attr('disabled', true)
                $('#formulario_buscar_cliente').remove() // hide()
            })
            .catch(error => {
                agregarLog(26, `Número de documento ${numeroDocumento.val()}`)
                mostrarAviso('error', 'Ocurrió un error consultando las facturas del cliente. Intenta de nuevo más tarde.', 30000)
                $('#contenedor_mensaje_carga').html('')
                return false
            })
        })
        .catch(error => {
            agregarLog(25, `Número de documento ${numeroDocumento.val()}`)
            mostrarAviso('error', 'Ocurrió un error consultando los datos del cliente. Por favor, intenta más tarde.', 30000)
            return false
        })
    }
    
    $().ready(() => {
        // Si viene NIT del formulario de comprobante
        if($('#nit_comprobante').val() != '') procesarBusqueda($('#nit_comprobante').val())

        $('#formulario_buscar_cliente').submit(async(evento) => {
            evento.preventDefault()

            procesarBusqueda()
        })

        // Control del input para que registren solamente números
        numeroDocumento.keyup(function() {
            $(`#${$(this).attr('id')}`).val(limpiarCadena($(this).val()))
        })

        // Si hay un email previamente creado, lo pone en el input
        if(localStorage.simonBolivar_emailContacto) email.val(localStorage.simonBolivar_emailContacto)
    })
</script>