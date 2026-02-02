<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container container--max--xl">
        <div class="row">
            <div class="col-12 col-lg-3 d-flex">
                <div class="account-nav flex-grow-1">
                    <h4 class="account-nav__title">Opciones</h4>
                    <ul class="account-nav__list">
                        <li class="perfil_dashboard account-nav__item">
                            <a href="<?php echo site_url('perfil/index/dashboard'); ?>">Dashboard</a>
                        </li>
                        <?php if(ENVIRONMENT == 'development') { ?>
                            <li class="perfil_garage account-nav__item ">
                                <a href="<?php echo site_url('perfil/index/garage'); ?>">Garage</a>
                            </li>
                            <li class="perfil_editar account-nav__item ">
                                <a href="<?php echo site_url('perfil/index/editar'); ?>">Editar perfil</a>
                            </li>
                            <li class="perfil_pedidos account-nav__item ">
                                <a href="<?php echo site_url('perfil/index/pedidos'); ?>">Mis Pedidos</a>
                            </li>
                            <li class="perfil_pedidos_detalle account-nav__item ">
                                <a href="<?php echo site_url('perfil/index/pedidos_detalle'); ?>">Detalle de pedidos</a>
                            </li>
                            <li class="perfil_direcciones account-nav__item ">
                                <a href="<?php echo site_url('perfil/index/direcciones'); ?>">Mis direcciones</a>
                            </li>
                            <li class="perfil_editar_direccion account-nav__item ">
                                <a href="<?php echo site_url('perfil/index/editar_direccion'); ?>">Editar Dirección</a>
                            </li>
                            <li class="perfil_editar_clave account-nav__item ">
                                <a href="<?php echo site_url('perfil/index/editar_clave'); ?>">Editar Clave</a>
                            </li>
                        <?php } ?>
                        <li class="account-nav__divider" role="presentation"></li>
                        
                        <li class="account-nav__item ">
                            <a href="<?php echo site_url('sesion/cerrar'); ?>">Cerrar sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-lg-9 mt-4 mt-lg-0">
                <?php $this->load->view("perfil/$vista"); ?>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    $(`.perfil_${'<?php echo $vista; ?>'}`).addClass('account-nav__item--active')
</script>