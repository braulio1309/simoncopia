<div class="indicator indicator--trigger--click">
    <a href="<?php echo site_url('sesion'); ?>" class="indicator__button">
        <span class="indicator__icon">
            <svg width="32" height="32">
                <path fill="#19287F" d="M16,18C9.4,18,4,23.4,4,30H2c0-6.2,4-11.5,9.6-13.3C9.4,15.3,8,12.8,8,10c0-4.4,3.6-8,8-8s8,3.6,8,8c0,2.8-1.5,5.3-3.6,6.7 C26,18.5,30,23.8,30,30h-2C28,23.4,22.6,18,16,18z M22,10c0-3.3-2.7-6-6-6s-6,2.7-6,6s2.7,6,6,6S22,13.3,22,10z" />
            </svg>
        </span>
        <span class="indicator__title">Bienvenido</span>

        <!-- Nombre de usuario o Iniciar -->
        <?php if($this->session->userdata('usuario_id')) { ?>
            <span class="indicator__value color_azul_corporativo_primario"><?php echo $this->session->userdata('nombres'); ?></span>
        <?php } else { ?>
            <span class="indicator__value color_azul_corporativo_primario">Iniciar sesión</span>
        <?php } ?>
    </a>
    <div class="indicator__content">
        <div class="account-menu">
            <?php if(!$this->session->userdata('usuario_id')) { ?>
                <form class="account-menu__form">
                    <div class="account-menu__form-title">
                        Inicia sesión en tu cuenta
                    </div>
                    <div class="form-group">
                        <label for="menu_login" class="sr-only">Nombre de usuario</label>
                        <input id="menu_login" type="text" class="form-control form-control-sm" placeholder="Nombre de usuario">
                    </div>
                    <div class="form-group">
                        <label for="header-signin-password" class="sr-only">Clave</label>
                        <div class="account-menu__form-forgot">
                            <input id="menu_clave" type="password" class="form-control form-control-sm" placeholder="Clave">

                            <a href="<?php echo site_url('sesion/recordar_clave'); ?>" class="account-menu__form-forgot-link">Recordar</a>
                        </div>
                    </div>
                    <div class="form-group account-menu__form-button">
                        <button type="submit" class="btn btn-primary btn-sm" onClick="javascript:iniciarSesion(event, null, 'menu_login', 'menu_clave')">Iniciar</button>
                    </div>
                    <div class="account-menu__form-link">
                        <a href="<?php echo site_url('usuarios/registro'); ?>">Crear cuenta</a>
                    </div>
                </form>
            <?php } else { ?>
            <div class="account-menu__divider"></div>
                <a href="" class="account-menu__user">
                    <div class="account-menu__user-avatar">
                        <img src="<?php echo base_url(); ?>images/logo.png" alt="Logo">
                    </div>
                    <div class="account-menu__user-info">
                        <div class="account-menu__user-name"><?php echo "{$this->session->userdata('nombres')} {$this->session->userdata('primer_apellido')}"; ?></div>
                        <div class="account-menu__user-email"><?php echo $this->session->userdata('email'); ?></div>
                    </div>
                </a>
                <div class="account-menu__divider"></div>
                <ul class="account-menu__links">
                    <li><a href="<?php echo site_url('perfil/index/dashboard'); ?>">Dashboard</a></li>

                    <!-- Ver facturas -->
                    <?php if(isset($permisos) && in_array(['proveedores' => 'proveedores_ver_facturas'], $permisos)) { ?>
                        <li><a href="<?php echo site_url("proveedores/facturas?nit={$this->session->userdata('documento_numero')}"); ?>">Mis facturas</a></li>
                    <?php } ?>

                    <!-- Ver certificados tributarios -->
                    <?php if(isset($permisos) && in_array(['proveedores' => 'proveedores_ver_certificados'], $permisos)) { ?>
                        <li><a href="<?php echo site_url("proveedores/certificados?nit={$this->session->userdata('documento_numero')}"); ?>">Certificados tributarios</a></li>
                    <?php } ?>
                    
                    <?php if(ENVIRONMENT == 'development') { ?>
                        <li><a href="<?php echo site_url('perfil/index/garage'); ?>">Garage</a></li>
                        <li><a href="<?php echo site_url('perfil/index/editar'); ?>">Editar perfil</a></li>
                        <li><a href="<?php echo site_url('perfil/index/pedidos'); ?>">Mis pedidos</a></li>
                        <li><a href="<?php echo site_url('perfil/index/direcciones'); ?>">Mis direcciones</a></li>
                    <?php } ?>
                </ul>
                <div class="account-menu__divider"></div>
                <ul class="account-menu__links">
                    <li><a href="<?php echo site_url('sesion/cerrar'); ?>">Cerrar sesión</a></li>
                </ul>
            <?php } ?>
        </div>
    </div>
</div>