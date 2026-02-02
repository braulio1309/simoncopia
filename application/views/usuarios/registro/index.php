<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Registro de usuario</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--1">
                <div class="tag-badge tag-badge--theme badge_formulario mb-2 mt-2">
                    DATOS BÁSICOS
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-4 col-sm-12 m-2" style="border: 1px solid #EBEBEB;">
                        <label for="usuario_tipo_tercero1">Tipo de persona *</label>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="usuario_tipo_tercero" id="usuario_tipo_tercero1" value="1">
                                    <label class="form-check-label" for="usuario_tipo_tercero1">
                                        Natural
                                    </label>
                                </div>
                            </div>
                        
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="usuario_tipo_tercero" id="usuario_tipo_tercero2" value="2">
                                    <label class="form-check-label" for="usuario_tipo_tercero2">
                                        Jurídica
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-2 col-sm-12 m-2" style="border: 1px solid #EBEBEB;">
                        <label for="usuario_tiene_rut1">¿Tienes RUT? *</label>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="usuario_tiene_rut" id="usuario_tiene_rut1" value="1">
                                    <label class="form-check-label" for="usuario_tiene_rut1">
                                        Sí
                                    </label>
                                </div>
                            </div>
                        
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="usuario_tiene_rut" id="usuario_tiene_rut0" value="0">
                                    <label class="form-check-label" for="usuario_tiene_rut0">
                                        No
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-4 col-sm-12 m-2" style="border: 1px solid #EBEBEB;">
                        <label for="usuario_tiene_rut1">¿Eres responsable de IVA? *</label>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="usuario_responsable_iva" id="usuario_responsable_iva1" value="1" data-responsable_iva="48" data-causante_iva="01">
                                    <label class="form-check-label" for="usuario_responsable_iva1">
                                        Sí
                                    </label>
                                </div>
                            </div>
                        
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="usuario_responsable_iva" id="usuario_responsable_iva0" value="0" data-responsable_iva="49" data-causante_iva="ZY">
                                    <label class="form-check-label" for="usuario_responsable_iva0">
                                        No
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-row mt-2">
                    <div class="form-row col-md-12" id="datos_persona_natural">
                        <div class="form-group col-md-6">
                            <label for="usuario_nombres">Nombres *</label>
                            <input type="text" class="form-control" id="usuario_nombres">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="usuario_primer_apellido">Primer apellido *</label>
                            <input type="text" class="form-control" id="usuario_primer_apellido">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="usuario_segundo_apellido">Segundo apellido <span class="text-muted">(Opcional)</span></label>
                            <input type="text" class="form-control" id="usuario_segundo_apellido">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="usuario_razon_social">Razón social *</label>
                    <input type="text" class="form-control" id="usuario_razon_social">
                </div>
                <div class="form-row">
                    <div class="form-group col-lg-4 col-sm-12">
                        <div class="form-group">
                            <label for="usuario_tipo_documento">Tipo de documento *</label>
                            <select id="usuario_tipo_documento" class="form-control">
                                <option value="">Selecciona...</option>
                                <option value="C" data-tipo_tercero="1">Cédula de ciudadanía</option>
                                <option value="E" data-tipo_tercero="1">Cédula de extranjería</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-4 col-sm-12">
                        <label for="usuario_numero_documento1">Número de documento (sin dígito de verificación) *</label>
                        <input type="text" class="form-control" id="usuario_numero_documento1" placeholder="sin dígito de verificación">
                    </div>

                    <div class="form-group col-lg-4 col-sm-12">
                        <label for="usuario_numero_documento2">Repite el número de documento *</label>
                        <input type="text" class="form-control" id="usuario_numero_documento2" onpaste="return false;">
                    </div>
                </div>

                <!-- Si no es vendedor -->
                <?php if(!$this->session->userdata('codigo_vendedor') || $this->session->userdata('codigo_vendedor') == '0') { ?>
                    <div class="card-divider"></div>
                    <div class="tag-badge tag-badge--theme badge_formulario mb-2 mt-2">
                        Crear usuario y contraseña
                    </div>
                    <div class="alert alert-primary mb-3">
                        Crea tu nombre de usuario y contraseña. Recuerda que con estos datos iniciarás sesión
                    </div>

                    <div class="form-row mt-2" id="datos_usuario_sistema">
                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="usuario_login">Nombre de usuario *</label>
                            <input type="text" class="form-control" id="usuario_login">
                        </div>

                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="usuario_clave1">Contraseña *</label>
                            <input type="password" class="form-control" id="usuario_clave1">
                        </div>

                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="usuario_clave2">Repite la contraseña *</label>
                            <input type="password" class="form-control" id="usuario_clave2">
                        </div>
                    </div>

                    <!-- Si puede ver usuarios -->
                    <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_usuarios_ver'], $permisos)) { ?>
                        <label for="usuario_perfil">Perfil *</label>
                        <select id="usuario_perfil" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach($this->configuracion_model->obtener('perfiles') as $perfil) echo "<option value='$perfil->id'>$perfil->nombre</option>"; ?>
                        </select>
                    <?php } ?>
                <?php } ?>

                <!-- Si es vendedor -->
                <?php if($this->session->userdata('codigo_vendedor') && $this->session->userdata('codigo_vendedor') != '0') { ?>
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="usuario_segmento_id">Segmento *</label>
                            <select id="usuario_segmento_id" class="form-control">
                                <option value="">Seleccione...</option>
                                <?php foreach($this->configuracion_model->obtener('segmentos') as $segmento) echo "<option value='$segmento->id' data-plan='$segmento->plan' data-mayor='$segmento->mayor'>$segmento->plan - $segmento->nombre</option>"; ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1">
                <div class="tag-badge tag-badge--theme badge_formulario mb-2 mt-2">
                    Datos que necesitamos para enviar tus productos
                </div>
                <div class="form-row">
                    <div class="form-group col-lg-3 col-sm-12">
                        <label for="usuario_departamento_id">Departamento *</label>
                        <select id="usuario_departamento_id" class="form-control"></select>
                    </div>

                    <div class="form-group col-lg-3 col-sm-12">
                        <label for="usuario_municipio_id">Municipio *</label>
                        <select id="usuario_municipio_id" class="form-control"></select>
                    </div>

                    <div class="form-group col-lg-3 col-sm-12">
                        <label for="usuario_direccion">Dirección completa *</label>
                        <input type="text" class="form-control" id="usuario_direccion">
                    </div>
                    <div class="form-group col-lg-3 col-sm-12">
                        <label for="usuario_telefono">Número de teléfono *</label>
                        <input type="text" class="form-control" id="usuario_telefono">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-lg-6 col-sm-12">
                        <label for="usuario_contacto">Nombre de un contacto *</label>
                        <input type="text" class="form-control" id="usuario_contacto">
                    </div>
                    <div class="form-group col-lg-6 col-sm-12">
                        <label for="usuario_email">Correo electrónico *</label>
                        <input type="email" class="form-control" id="usuario_email">
                    </div>
                </div>

                <div class="form-group mb-2 mt-2">
                    <button class="btn btn-primary btn-block" onClick="javascript:crearUsuario()">Finalizar mi registro</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let esVendedor = ($('#codigo_vendedor').val() == 0) ? false : true
        
    crearUsuario = async() => {
        let camposObligatorios = [
            $('#usuario_numero_documento1'),
            $('#usuario_tipo_documento'),
            $('#usuario_razon_social'),
            $('#usuario_direccion'),
            $('#usuario_telefono'),
            $('#usuario_email'),
            $('#usuario_municipio_id'),
            $('#usuario_contacto'),
        ]

        // Si tiene perfil, se agrega como dato obligatorio
        if($('#usuario_perfil').val() !== undefined) camposObligatorios.push($('#usuario_perfil'))

        // Si es persona natural
        if ($('#usuario_tipo_tercero1').is(':checked')) {
            camposObligatorios.push($('#usuario_nombres'))
            camposObligatorios.push($('#usuario_primer_apellido'))
        }

        // Si no es un vendedor el que está logueado
        if(!esVendedor) {
            // Los datos de login son obligatorios
            camposObligatorios.push($('#usuario_login'))
            camposObligatorios.push($('#usuario_clave1'))
            camposObligatorios.push($('#usuario_clave2'))
        }

        // Si es vendedor, el segmento es obligatorio
        if(esVendedor) camposObligatorios.push($("#usuario_segmento_id"))

        let camposRadioObligatorios = [
            'usuario_tipo_tercero',
            'usuario_tiene_rut',
            'usuario_responsable_iva',
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false
        if (!validarCamposTipoRadio(camposRadioObligatorios)) return false

        // Si no coinciden los números de documento
        if ($("#usuario_numero_documento1").val() !== $("#usuario_numero_documento2").val()) {
            mostrarAviso('alerta', `Los números de documento no coinciden. Por favor, verifica nuevamente.`, 10000)
            return false
        }

        // Si no coinciden las claves
        if (!esVendedor && $("#usuario_clave1").val() !== $("#usuario_clave2").val()) {
            mostrarAviso('alerta', `Las contraseñas no coinciden. Por favor, verifica nuevamente.`, 10000)
            return false
        }

        // Se consulta si existe un usuario con ese mismo documento y correo electrónico
        let usuarioTerceroExistente = await consulta('obtener', {tipo: 'usuarios', documento_numero: $.trim($('#usuario_numero_documento1').val()), email: $.trim($('#usuario_email').val())})

        // Si no es vendedor y el usuario ya existe
        if(!esVendedor && usuarioTerceroExistente) {
            mostrarAviso('alerta', `
                El usuario con número de documento <b>${$.trim($('#usuario_numero_documento1').val())}</b> y correo <b>${$.trim($('#usuario_email').val())}</b> ya se encuentra registrado en nuestra sistema, por favor verifica nuevamente. Podrás iniciar sesión <a href='${$('#site_url').val()}/sesion'>iniciar sesión haciendo clic aquí</a> o recuperar tu contraseña`,
            10000)
            return false
        }

        let usuarioExistenteLogin = await consulta('obtener', {tipo: 'usuarios', login: $.trim($('#usuario_login').val())})
        
        // Si no es vendedor y el login ya existe
        if(!esVendedor && usuarioExistenteLogin) {
            mostrarAviso('alerta', `
                El nombre de usuario <b>${$.trim($('#usuario_login').val())}</b> ya se encuentra registrado en nuestra sistema, por favor intenta con un nombre diferente.`,
            10000)
            return false
        }

        let responsableIVA = ($(`#usuario_responsable_iva1`).is(':checked'))
            ? $(`#usuario_responsable_iva1`).attr('data-responsable_iva')
            : $(`#usuario_responsable_iva0`).attr('data-responsable_iva')

        let causanteIVA = ($(`#usuario_responsable_iva1`).is(':checked'))
            ? $(`#usuario_responsable_iva1`).attr('data-causante_iva')
            : $(`#usuario_responsable_iva0`).attr('data-causante_iva')

        let datosTerceroSiesa = {
            responsable_iva: responsableIVA,
            causante_iva: causanteIVA,
            tipo_tercero: ($(`#usuario_tipo_tercero1`).is(':checked')) ? 1 : 2, // Natural, jurídica
            documento_tipo: $('#usuario_tipo_documento').val(),
            documento_numero: $('#usuario_numero_documento1').val(),
            nombres: $('#usuario_nombres').val(),
            primer_apellido: $('#usuario_primer_apellido').val(),
            segundo_apellido: $('#usuario_segundo_apellido').val(),
            razon_social: $('#usuario_razon_social').val(),
            id_departamento: $('#usuario_departamento_id').val(),
            id_ciudad: $('#usuario_municipio_id').val(),
            direccion: $('#usuario_direccion').val(),
            contacto: $('#usuario_contacto').val(),
            email: $('#usuario_email').val(),
            telefono: $('#usuario_telefono').val(),
            vendedor: (esVendedor) ? $('#codigo_vendedor').val() : "U003",
            lista_precio: (esVendedor) ? '001' : '<?php echo $this->config->item('lista_precio'); ?>',
        }

        // Si es cédula de extranjería, se envía una entidad dinámica adicional
        // para la creación del tercero en Siesa
        if($('#usuario_tipo_documento').val() == 'E') {
            datosTerceroSiesa.entidad_dinamica_extranjero = {
                f200_id: $('#usuario_numero_documento1').val(),
                f753_id_entidad: 'EUNOECO036',
                f753_id_atributo: 'co036_id_procedencia_org',
                f753_id_maestro: 'MUNOECO043',
                f753_id_maestro_detalle: 11,
            }
        }

        // Si es vendedor, se envía una entidad dinámica adicional
        // para la asignación del segmento
        if(esVendedor) {
            datosTerceroSiesa.criterio_cliente = {
                f207_id_tercero: $('#usuario_numero_documento1').val(),
                f207_id_sucursal: '001',
                f207_id_plan_criterios: $('#usuario_segmento_id option:selected').attr('data-plan'),
                f207_id_criterio_mayor: $('#usuario_segmento_id option:selected').attr('data-mayor'),
            }
        }

        Swal.fire({
            title: 'Estamos creando tu usuario en nuestros sistemas...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        // Se consulta en Siesa el tercero
        var consultaTercero = await consulta('obtener', {tipo: 'terceros', numero_documento: $('#usuario_numero_documento1').val()}, false)

        // Si no es vendedor
        if(!esVendedor) {
            let datosUsuario = {
                tipo: 'usuarios',
                nombres: $('#usuario_nombres').val(),
                primer_apellido: $('#usuario_primer_apellido').val(),
                segundo_apellido: $('#usuario_segundo_apellido').val(),
                razon_social: $('#usuario_razon_social').val(),
                celular: $('#usuario_telefono').val(),
                usuario_tipo_id: ($(`#usuario_tipo_tercero1`).is(':checked')) ? 1 : 2,
                documento_numero: $('#usuario_numero_documento1').val(),
                usuario_identificacion_tipo_id: $('#usuario_tipo_documento option:selected').attr('data-tipo_tercero'),
                email: $('#usuario_email').val(),
                nombre_contacto: $('#usuario_contacto').val(),
                ciudad_id: $('#usuario_municipio_id').val(),
                departamento_id: $('#usuario_departamento_id').val(),
                direccion1: $('#usuario_direccion').val(),
                clave: $('#usuario_clave1').val(),
                login: $('#usuario_login').val(),
                responsable_iva: ($(`#usuario_responsable_iva1`).is(':checked')) ? 1 : 0,
            }

            // Si tiene perfil, se agrega el perfil
            if($('#usuario_perfil').val() !== undefined) datosUsuario.perfil_id = $('#usuario_perfil').val()

            // Se crea el usuario
            let usuarioId = await consulta('crear', datosUsuario, false)

            // Envío de email de confirmación
            obtenerPromesa(`${$('#site_url').val()}interfaces/enviar_email`, {tipo: 'usuario_nuevo', id: usuarioId.resultado})
            
            Swal.close()
            mostrarAviso('exito', `
                ¡Tu usuario ha sido creado correctamente!<br><br>
                Ahora puedes <a href='${$('#site_url').val()}/sesion'>iniciar sesión haciendo clic aquí</a>
            `, 20000)
        }
        
        // Si es un perfil de vendedor, va a crear el tercero en Siesa
        if(esVendedor) {
            // Si el tercero ya existe en Siesa
            if(consultaTercero.codigo == 0) {
                Swal.close()
                mostrarAviso('alerta', `No se creó el tercero en el ERP, porque ya existe`, 20000)
                return false
            }
            
            let creacionTerceroSiesa = crearTerceroCliente(datosTerceroSiesa)
            creacionTerceroSiesa.then(resultado => {
                if(resultado[0].codigo == 1) {
                    agregarLog(82, JSON.stringify(resultado))

                    mostrarAviso('error', `No se pudo crear el tercero en el ERP: <b>${resultado[0].detalle}</b>`, 20000)
                    return false
                }

                agregarLog(51, JSON.stringify(resultado))

                if(esVendedor) mostrarAviso('exito', `El tercero ha sido creado correctamente`, 20000)
            }) 
        }
    }

    $().ready(() => {
        // Cuando se seleccione el tipo de tercero
        $('input[name="usuario_tipo_tercero"]').change(() => {
            // Persona natural
            if ($('#usuario_tipo_tercero1').is(':checked')) {
                $('#datos_persona_natural').show()
                $('#usuario_razon_social').attr('disabled', true)
            }

            // Persona jurídica
            if ($('#usuario_tipo_tercero2').is(':checked')) {
                $('#datos_persona_natural').hide()
                $('#usuario_razon_social').attr('disabled', false)
                $('#usuario_nombres, #usuario_primer_apellido, #usuario_segundo_apellido, #usuario_razon_social').val('')
            }
        })

        // Cuando se seleccione si tiene RUT o no
        $('input[name="usuario_tiene_rut"]').change(() => {
            // Si tiene RUT
            if ($('#usuario_tiene_rut1').is(':checked')) {
                // El tipo de documento tiene que ser NIT
                $("#usuario_tipo_documento").append("<option value='N' data-tipo_tercero='2'>NIT</option>").val('N').attr('disabled', true)
            } else {
                // Se elimina la posibilidad de escoger NIT
                $("#usuario_tipo_documento option[value='N']").remove()
                $('#usuario_tipo_documento').attr('disabled', false)
            }
        })

        // Cuando se escribe nombres o apellidos
        $('#datos_persona_natural').keyup(function(e) {
            // Si es persona natural
            if($('#usuario_tipo_tercero1').is(':checked')) {
                // Se completa la razón social
                $('#usuario_razon_social').val(`${$('#usuario_primer_apellido').val()} ${$('#usuario_segundo_apellido').val()} ${$('#usuario_nombres').val()}`)
            }
        })

        listarDatos('usuario_departamento_id', {tipo: 'departamentos', pais_id: 169})
        
        // Cuando se seleccione un departamento
        $('#usuario_departamento_id').change(() => {
            listarDatos('usuario_municipio_id', {tipo: 'municipios', departamento_id: $('#usuario_departamento_id').val()})
        })

        // Control del input para que registren solamente números
        $(`#usuario_numero_documento1, #usuario_numero_documento2`).keyup(function() {
            $(this).val(limpiarCadena($(this).val()))
        })

        // Control del input para que registren solamente números Y espacios
        $(`#usuario_direccion`).keyup(function() {
            $(this).val(limpiarCadena($(this).val(), true))
        })
    })
</script>