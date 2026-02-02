<div class="block">
    <div class="container container--max--lg">
        <div class="row">
            <div class="col-md-6 d-flex">
                <div class="card-body--padding--2">
                    <form>
                        <div class="form-group">
                            <?php
                            $perfil = $this->configuracion_model->obtener('perfiles', ['token' => $datos['token']]);
                            $roles = $this->configuracion_model->obtener('roles', $datos['modulo_id']);

                            foreach($roles as $rol) {
                                $item = $this->configuracion_model->obtener('perfil_rol', ['perfil_id' => $perfil->id, 'rol_id' => $rol->id]);
                            ?>
                                <div class="form-check">
                                    <span class="input-check form-check-input">
                                        <span class="input-check__body">
                                            <input class="input-check__input" type="checkbox" id="rol<?php echo $rol->id; ?>" data-id="<?php echo $rol->id; ?>" <?php if(!empty($item)) echo 'checked'; ?>>

                                            <span class="input-check__box"></span>
                                            <span class="input-check__icon">
                                                <svg width="9px" height="7px">
                                                    <path d="M9,1.395L3.46,7L0,3.5L1.383,2.095L3.46,4.2L7.617,0L9,1.395Z" />
                                                </svg>
                                            </span>
                                        </span>
                                    </span>
                                    <label class="form-check-label" for="signin-remember"><?php echo $rol->descripcion; ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $().ready(() => {
        // Cuando se dé clic en un rol
        $('input[id^=rol]').click(async function() {
            let perfilId = '<?php echo $perfil->id; ?>'
            let rolId = $(this).attr('data-id')

            let datos = {
                tipo: 'perfiles_roles',
                rol_id: rolId,
                perfil_id: perfilId,
            }

            // Si el rol se chequea
            if($(this).prop('checked')) {
                // Se crea el permiso
                await consulta('crear', datos)
            } else {
                // Se elimina el permiso
                await consulta('eliminar', datos)
            }
        })
    })
</script>