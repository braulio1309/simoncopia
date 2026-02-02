<?php
if(isset($datos['token'])) {
    $perfil = $this->configuracion_model->obtener('perfiles', ['token' => $datos['token']]);
    echo "<input type='hidden' id='perfil_id' value='$perfil->id' />";
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
                        <label for="perfil_nombre">Nombre *</label>
                        <input type="text" class="form-control" id="perfil_nombre" value="<?php if(!empty($perfil)) echo $perfil->nombre; ?>">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="perfil_descripcion">Descripción *</label>
                        <textarea rows="3" class="form-control form-control-lg" id="perfil_descripcion"><?php if(!empty($perfil)) echo $perfil->descripcion; ?></textarea>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="perfil_es_administrador">¿Es administrador? *</label>
                        <select id="perfil_es_administrador" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="form-group mb-0 pt-3 mt-3">
                    <button class="btn btn-info" onClick="javascript:history.back()">Volver</button>
                    <button class="btn btn-success" onClick="javascript:guardarPerfil()">Guardar datos</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(isset($perfil)) { ?>
    <script>
        $('#perfil_es_administrador').val(<?php echo $perfil->es_administrador; ?>)
    </script>
<?php } ?>

<script>
    $(`.perfil_datos_generales`).addClass('account-nav__item--active')
    
    guardarPerfil = async() => {
        let camposObligatorios = [
            $('#perfil_nombre'),
            $('#perfil_descripcion'),
            $('#perfil_es_administrador'),
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datosPerfil = {
            tipo: 'perfiles',
            nombre: $('#perfil_nombre').val(),
            descripcion: $('#perfil_descripcion').val(),
            es_administrador: $('#perfil_es_administrador').val(),
        }

        if(!$('#perfil_id').val()) {
            // Se consulta si existe un registro con ese mismo nombre
            let perfilExistente = await consulta('obtener', {tipo: 'perfiles', nombre: $.trim($('#perfil_nombre').val())})
            
            if(perfilExistente) {
                mostrarAviso('alerta', `El perfil <b>${$('#perfil_nombre').val()}</b> ya existe en la base de datos.`)
                return false
            }

            await consulta('crear', datosPerfil)
        } else {
            datosPerfil.id = $('#perfil_id').val()

            await consulta('actualizar', datosPerfil)
        }
    }
</script>