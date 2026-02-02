<div class="block-header">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Envíanos tu cotización de una manera fácil y rápida</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0" id="formulario_validar_proveedor">
            <div class="card-body card-body">
                <form class="row">
                    <!-- Número de documento -->
                    <div class="form-group col-md-4 col-sm-12 col-lg-4">
                        <label for="proveedor_cotizacion_numero_documento">Digita tu número de documento o NIT *</label>
                        <input type="number" class="form-control" id="proveedor_cotizacion_numero_documento" placeholder="Sin espacios, guiones ni dígito de verificación" autofocus>
                    </div>

                    <!-- Teléfono -->
                    <div class="form-group col-md-4 col-sm-12 col-lg-4">
                        <label for="proveedor_cotizacion_telefono">Digita el número de celular (opcional)</label>
                        <input type="number" class="form-control" id="proveedor_cotizacion_telefono" placeholder="Opcional">
                    </div>

                    <!-- Email -->
                    <div class="form-group col-md-4 col-sm-12 col-lg-4">
                        <label for="proveedor_cotizacion_email">Indícanos un correo electrónico (opcional)</label>
                        <input type="email" class="form-control" id="proveedor_cotizacion_email" placeholder="Opcional">
                    </div>

                    <div class="form-group col-sm-12 col-lg-12">
                        <button type="submit" class="btn btn-primary btn-block" id="btn_buscar_cotizaciones">Ver ofertas disponibles</button>

                        <div class="mt-2" id="contenedor_mensaje_carga"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="contenedor_cotizaciones_disponibles"></div>

<script>
    var numeroDocumento = $('#proveedor_cotizacion_numero_documento')
    var numeroTelefono = $('#proveedor_cotizacion_telefono')
    var email = $('#proveedor_cotizacion_email')

    validarProveedor = async (nit = null) => {
        let datosObligatorios = [
            numeroDocumento,
        ]

        // Validación de campos obligatorios
        if (!validarCamposObligatorios(datosObligatorios)) return false
        
        let datosProveedor = {
            tipo: 'tercero_contacto',
            nit: numeroDocumento.val(),
        }

        // Se verifica que el contacto exista en la base de datos
        let contacto = await consulta('obtener', datosProveedor)

        // Si tiene número de identificación y de teléfono
        if(numeroDocumento.val() != '' && numeroTelefono.val() != '') {
            let datosContacto = {
                tipo: 'tercero_contacto',
                nit: numeroDocumento.val(),
                numero: numeroTelefono.val(),
                email: email.val(),
                modulo_id: 6, // Proveedores
            }

            // Se consulta si el contacto existe con todos los datos coincidentes
            let contactoCompleto = await consulta('obtener', datosContacto)
            
            // Si no existe, se crea
            if(!contactoCompleto.resultado) await consulta('crear', datosContacto, false)
        }

        // Si no se encontró el contacto
        if(!contacto.resultado) {
            mostrarAviso('alerta', 'El número de documento que nos indicas no ha sido encontrado en nuestra base de datos. Por favor, verifica nuevamente o ponte en contacto con nosotros.', 30000)
            agregarLog(62, JSON.stringify(datosProveedor))
            return false
        }

        $('#formulario_validar_proveedor').remove()
        agregarLog(65, JSON.stringify(datosProveedor))

        cargarInterfaz('proveedores/cotizaciones/listar_disponibles', 'contenedor_cotizaciones_disponibles', {numero_documento: numeroDocumento.val()})
    }

    $().ready(() => {
        $('#formulario_validar_proveedor').submit(async(evento) => {
            evento.preventDefault()

            validarProveedor()
        })

        // Control del input para que registren solamente números
        numeroDocumento.keyup(function() {
            $(this).val(limpiarCadena($(this).val()))
        })
    })
</script>