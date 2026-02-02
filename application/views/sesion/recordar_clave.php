<div class="site__body">
    <div class="block-space block-space--layout--after-header"></div>
    <div class="block">
        <div class="container container--max--lg">
            <div class="row">
                <div class="col-lg-8 offset-2 d-flex">
                    <div class="card flex-grow-1 mb-md-0 mr-0">
                        <div class="card-body card-body--padding--2">
                            <h3 class="card-title">Recordar clave</h3>

                            <div class="alert alert-primary mb-3 solicitud_codigo">
                                Si estás intentando recuperar tu clave, enviaremos un código al correo electrónico que tienes registrado.<br>
                                Por favor completa los siguientes campos para validar tu identidad.
                            </div>

                            <form>
                                <!-- Cuando se encuentre el usuario, este campo se llenará -->
                                <input type="hidden" id="recuperacion_usuario_id">

                                <div class="form-group solicitud_codigo">
                                    <label for="recuperacion_nit">Número de documento (sin dígito de verificación)</label>
                                    <input id="recuperacion_nit" type="text" class="form-control form-control-sm" autofocus>
                                </div>

                                <div class="form-group solicitud_codigo">
                                    <label for="recuperacion_email">Correo electrónico registrado en el sistema</label>
                                    <input id="recuperacion_email" type="email" class="form-control form-control-sm">
                                </div>

                                <div class="form-group nueva_clave" hidden>
                                    <label for="recuperacion_clave1">Escribe tu nueva clave</label>
                                    <input id="recuperacion_clave1" type="password" class="form-control form-control-sm">
                                </div>

                                <div class="form-group nueva_clave" hidden>
                                    <label for="recuperacion_clave2">Repite tu nueva clave</label>
                                    <input id="recuperacion_clave2" type="password" class="form-control form-control-sm">
                                </div>

                                <div class="form-group envio_codigo" hidden>
                                    <label for="recuperacion_codigo">Indícanos el código temporal que recibiste en tu correo electrónico</label>
                                    <input id="recuperacion_codigo" type="text" class="form-control form-control-sm">
                                </div>

                                <div class="form-group mb-0 solicitud_codigo">
                                    <button type="submit" class="btn btn-primary btn-block mt-3" onClick="javascript:validarUsuario(event)">Enviar código de verificación al correo</button>
                                </div>

                                <div class="form-group mb-0 envio_codigo" id="btn_enviar_codigo" hidden>
                                    <button type="submit" class="btn btn-success btn-block mt-3" onClick="javascript:validarCodigo(event)">Validar código</button>
                                </div>

                                <div class="form-group mb-0 nueva_clave" id="btn_enviar_codigo" hidden>
                                    <button type="submit" class="btn btn-success btn-block mt-3" onClick="javascript:actualizarUsuario(event)">Finalizar recuperación de clave</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="block-space block-space--layout--before-footer"></div>
</div>

<script>
    actualizarUsuario = async(evento) => {
        evento.preventDefault()

        let campos = [
            $("#recuperacion_clave1"),
            $("#recuperacion_clave2"),
        ]

        // Validación de campos obligatorios
        if (!validarCamposObligatorios(campos)) {
            mostrarAviso('alerta', 'Hay campos obligatorios por diligenciar')
            return false
        }

        // Si no coinciden las claves
        if ($("#recuperacion_clave1").val() !== $("#recuperacion_clave2").val()) {
            mostrarAviso('alerta', `Las claves no coinciden. Por favor, verifica nuevamente.`, 10000)
            return false
        }

        let datos = {
            tipo: 'usuarios',
            id: $('#recuperacion_usuario_id').val(),
            clave: $('#recuperacion_clave1').val(),
        }

        await consulta('actualizar', datos)

        mostrarAviso('exito', `
            ¡Tu clave se ha actualizado correctamente!<br><br>
            Ahora puedes <a href='${$('#site_url').val()}/sesion'>iniciar sesión haciendo clic aquí</a>
        `, 20000)

        // Envío de email con el código
        obtenerPromesa(`${$('#site_url').val()}interfaces/enviar_email`, {tipo: 'clave_cambiada', id: $('#recuperacion_usuario_id').val()})
    }

    validarCodigo = async(evento) => {
        evento.preventDefault()

        let codigo = $(`#recuperacion_codigo`)
        
        // Validación de campos obligatorios
        if (!validarCamposObligatorios([codigo])) {
            mostrarAviso('alerta', 'Hay campos obligatorios por diligenciar')
            return false
        }

        // Se valida que el código sea válido
        let codigoValido = await consulta('obtener', {
            tipo: 'codigo_temporal_valido',
            usuario_id: $('#recuperacion_usuario_id').val(),
            codigo: codigo.val()
        })

        if(!codigoValido) {
            mostrarAviso('alerta', `El código que ingresaste no es válido o ya está vencido. Por favor, intenta solicitar otro código <a href='${$('#site_url').val()}/sesion/recordar_clave'>haciendo clic aquí</a>.`, 10000)
            return false
        }

        $('.nueva_clave').attr('hidden', false)
        $('.solicitud_codigo, .envio_codigo').attr('hidden', true)
    }

    validarUsuario = async(evento) => {
        evento.preventDefault()

        let numeroDocumento = $(`#recuperacion_nit`)
        let email = $(`#recuperacion_email`)

        let campos = [
            numeroDocumento,
            email,
        ]

        // Validación de campos obligatorios
        if (!validarCamposObligatorios(campos)) {
            mostrarAviso('alerta', 'Hay campos obligatorios por diligenciar')
            return false
        }

        let datos = {
            tipo: 'usuarios',
            documento_numero: $.trim(numeroDocumento.val()),
            email: $.trim(email.val()),
        }

        // Se consulta si existe un usuario con los mismos datos
        let usuario = await consulta('obtener', datos)
        
        if(!usuario) {
            mostrarAviso('alerta', 'No encontramos datos asociados a el correo electrónico y número de documento que nos indicas. Por favor, valida nuevamente los datos que diligenciaste e intenta nuevamente.', 10000)
            return false
        }

        $('#recuperacion_usuario_id').val(usuario.id)

        // Se genera un codigo OTP para el usuario
        let codigoOTP = await consulta('crear', {tipo: 'codigo_otp', usuario_id: usuario.id}, false)

        // Envío de email con el código
        obtenerPromesa(`${$('#site_url').val()}interfaces/enviar_email`, {tipo: 'codigo_otp', id: codigoOTP})

        mostrarAviso('exito', `
            ¡Hemos enviado exitosamente el código!<br><br>
            Por favor revisa tu bandeja de entrada y tu carpeta de No Deseados, publicitarios o SPAM.
        `, 20000)

        $(`.solicitud_codigo`).attr('hidden', true)
        $(`.envio_codigo`).attr('hidden', false)
    }
</script>