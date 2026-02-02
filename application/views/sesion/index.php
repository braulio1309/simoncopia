<div class="site__body">
    <div class="block">
        <div class="container container--max--lg">
            <div class="row">
                <!-- Inicio de sesión -->
                <div class="col-lg-5">
                    <div class="card flex-grow-1 mb-md-0 mr-0 mr-lg-3 ml-0 ml-lg-4">
                        <div class="card-body card-body--padding--2">
                            <h3 class="card-title">Iniciar sesión</h3>

                            <h5 class="d-block d-md-none mb-4">Accede a todos nuestros servicios y precios exclusivos</h5>

                            <form>
                                <div class="form-group">
                                    <label for="sesion_login">Nombre de usuario</label>
                                    <input id="sesion_login" type="text" class="form-control form-control-sm" placeholder="Nombre de usuario">
                                </div>
                                <div class="form-group">
                                    <label for="sesion_clave">Clave</label>
                                    <div class="account-menu__form-forgot">
                                        <input id="sesion_clave" type="password" class="form-control form-control-sm" placeholder="Clave">
                                        
                                        <a href="<?php echo site_url('sesion/recordar_clave'); ?>" class="account-menu__form-forgot-link">Recordar clave</a>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="form-group account-menu__form-button">
                                        <button type="submit" class="btn btn-primary mt-2" onClick="javascript:iniciarSesion(event, '<?php echo $url; ?>', 'sesion_login', 'sesion_clave')">Iniciar</button>
                                    </div>

                                    <div class="account-menu__form-link">
                                        <a href="<?php echo site_url('usuarios/registro'); ?>">Crear cuenta</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div><!-- Inicio de sesión -->

                <!-- Banner -->
                <div class="col-lg-7 d-none d-lg-block">
                    <img src="<?php echo base_url(); ?>archivos/banners/sesion.webp" width="100%">
                </div><!-- Banner -->
            </div>
        </div>
    </div>
    <div class="block-space block-space--layout--before-footer"></div>
</div>