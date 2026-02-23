<?php
if($this->uri->segment(4)) {
    $tercero = $this->configuracion_model->obtener('usuarios', ['token' => $this->uri->segment(4)]);
    echo "<input type='hidden' id='tercero_id' value='$tercero->id' />";
}
?>

<div class="card">
    <div class="card-header">
        <h5>Datos generales</h5>
    </div>
    <div class="card-divider"></div>
    <div class="card-body card-body--padding--2">
        <div class="row no-gutters">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="tercero_razon_social">Razón Social *</label>
                        <input type="text" class="form-control" id="tercero_razon_social" value="<?php if(!empty($tercero)) echo $tercero->razon_social; ?>" autofocus>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="tercero_nombres">Nombres</label>
                        <input type="text" class="form-control" id="tercero_nombres" value="<?php if(!empty($tercero)) echo $tercero->nombres; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tercero_primer_apellido">Primer apellido</label>
                        <input type="text" class="form-control" id="tercero_primer_apellido" value="<?php if(!empty($tercero)) echo $tercero->primer_apellido; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tercero_segundo_apellido">Segundo apellido</label>
                        <input type="text" class="form-control" id="tercero_segundo_apellido" value="<?php if(!empty($tercero)) echo $tercero->segundo_apellido; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tercero_fecha_nacimiento">Fecha de nacimiento</label>
                        <input type="date" class="form-control" id="tercero_fecha_nacimiento" value="<?php if(!empty($tercero)) echo $tercero->fecha_nacimiento; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tercero_telefono">Teléfono</label>
                        <input type="text" class="form-control" id="tercero_telefono" value="<?php if(!empty($tercero)) echo $tercero->telefono; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tercero_celular">Celular</label>
                        <input type="text" class="form-control" id="tercero_celular" value="<?php if(!empty($tercero)) echo $tercero->celular; ?>">
                    </div>
                </div>
                <hr>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tercero_tipo_id">Tipo de tercero *</label>
                        <select id="tercero_tipo_id" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach($this->configuracion_model->obtener('usuarios_tipos') as $usuario_tipo) echo "<option value='$usuario_tipo->id'>$usuario_tipo->nombre</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tercero_tipo_identificacion_id">Tipo de documento * </label>
                        <select id="tercero_tipo_identificacion_id" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach($this->configuracion_model->obtener('usuarios_identificacion_tipos') as $identificacion_tipo) echo "<option value='$identificacion_tipo->id'>$identificacion_tipo->nombre</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-8">
                        <label for="tercero_numero_documento">Número de documento *</label>
                        <input type="text" class="form-control" id="tercero_numero_documento" value="<?php if(!empty($tercero)) echo $tercero->documento_numero; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="tercero_numero_documento_digito_verificacion">Dígito de verificación</label>
                        <input type="text" class="form-control" id="tercero_numero_documento_digito_verificacion" value="<?php if(!empty($tercero)) echo $tercero->digito_verificacion; ?>">
                    </div>
                    <div class="form-group col-md-12 mb-0">
                        <label for="tercero_email">Correo electrónico *</label>
                        <input type="email" class="form-control" id="tercero_email" value="<?php if(!empty($tercero)) echo $tercero->email; ?>">
                    </div>
                    <div class="form-group col-md-12 mb-0">
                        <label for="tercero_nombre_contacto">Nombre de contacto</label>
                        <input type="text" class="form-control" id="tercero_nombre_contacto" value="<?php if(!empty($tercero)) echo $tercero->nombre_contacto; ?>">
                    </div>
                </div>
                <hr>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="tercero_estado">Estado *</label>
                        <select id="tercero_estado" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="usuario_perfil">Perfil *</label>
                        <select id="usuario_perfil" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach($this->configuracion_model->obtener('perfiles') as $perfil) echo "<option value='$perfil->id'>$perfil->nombre</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="usuario_lista_precio">Lista de precios</label>
                        <select id="usuario_lista_precio" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach($this->configuracion_model->obtener('erp_listas_precios') as $lista_precio) echo "<option value='$lista_precio->id'>$lista_precio->nombre</option>"; ?>
                        </select>
                    </div>
                    <!-- <div class="form-group col-md-6 mb-2">
                        <label for="usuario_login">Nombre de usuario</label>
                        <input type="text" class="form-control" id="usuario_login" value="<?php // if(!empty($tercero)) echo $tercero->login; ?>">
                    </div>
                    <div class="form-group col-md-6 mb-2">
                        <label for="usuario_clave">Contraseña</label>
                        <input type="password" class="form-control" id="usuario_clave">
                    </div> -->
                </div>
                <div class="form-group mb-0 pt-3 mt-3">
                    <button class="btn btn-info" onClick="javascript:history.back()">Volver</button>

                    <?php if(!empty($tercero)) { ?>
                        <button class="btn btn-success" onClick="javascript:guardarTercero()">Guardar datos</button>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(isset($tercero)) { ?>
    <script>
        $('#tercero_tipo_id').val(<?php echo $tercero->usuario_tipo_id; ?>)
        $('#tercero_tipo_identificacion_id').val(<?php echo $tercero->usuario_identificacion_tipo_id; ?>)
        $('#tercero_estado').val(<?php echo $tercero->estado; ?>)
        $('#usuario_perfil').val(<?php echo $tercero->perfil_id; ?>)
        $('#usuario_lista_precio').val('<?php echo $tercero->lista_precio; ?>')
    </script>
<?php } ?>

<script>
    guardarTercero = async() => {
        let camposObligatorios = [
            $('#tercero_razon_social'),
            $('#tercero_tipo_id'),
            $('#tercero_tipo_identificacion_id'),
            $('#tercero_numero_documento'),
            $('#tercero_email'),
            $('#tercero_estado'),
            $('#usuario_perfil'),
        ]

        // Si el perfil es compra a crédito, la lista de precios es obligatoria
        if($('#usuario_perfil').val() == 7) camposObligatorios.push($('#usuario_lista_precio'))

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datosTercero = {
            tipo: 'usuarios',
            razon_social: $('#tercero_razon_social').val(),
            nombres: $('#tercero_nombres').val(),
            primer_apellido: $('#tercero_primer_apellido').val(),
            segundo_apellido: $('#tercero_segundo_apellido').val(),
            fecha_nacimiento: $('#tercero_fecha_nacimiento').val(),
            email: $('#tercero_email').val(),
            telefono: $('#tercero_telefono').val(),
            celular: $('#tercero_celular').val(),
            usuario_tipo_id: $('#tercero_tipo_id').val(),
            usuario_identificacion_tipo_id: $('#tercero_tipo_identificacion_id').val(),
            documento_numero: $('#tercero_numero_documento').val(),
            digito_verificacion: $('#tercero_numero_documento_digito_verificacion').val(),
            nombre_contacto: $('#tercero_nombre_contacto').val(),
            estado: $('#tercero_estado').val(),
            perfil_id: $('#usuario_perfil').val(),
            login: $('#usuario_login').val(),
            clave: $('#usuario_clave').val(),
            lista_precio: $('#usuario_lista_precio').val(),
        }

        if(!$('#tercero_id').val()) {
            // Se consulta si existe un usuario con ese mismo login
            let terceroExistente = await consulta('obtener', {tipo: 'usuarios', documento_numero: $.trim($('#tercero_numero_documento').val())})
            
            if(terceroExistente) {
                mostrarAviso('alerta', `El tercero con número de documento <b>${$('#tercero_numero_documento').val()}</b> ya existe en la base de datos.`)
                return false
            }

            await consulta('crear', datosTercero)
        } else {
            datosTercero.id = $('#tercero_id').val()

            await consulta('actualizar', datosTercero)
        }
    }
</script>