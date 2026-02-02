<header class="site__header fixed-top">
    <div class="header">
        <div class="header__topbar-classic-bg"></div>

        <div class="header__navbar">
            <!-- Filtros de marcas -->
            <div class="header__navbar-departments">
                <?php $this->load->view('core/menu_superior/marcas'); ?>
            </div>

            <!-- Opciones del menu -->
            <div class="header__navbar-menu">
                <div class="main-menu">
                    <ul class="main-menu__list">
                        <!-- Tienda -->
                        <li class="main-menu__item">
                            <a href="<?php echo site_url(); ?>" class="main-menu__link">
                                Tienda
                            </a>
                        </li><!-- Tienda -->

                        <!-- Crédito -->
                        <li class="main-menu__item menu_credito" data-nombre="Crédito">
                            <a href="<?php echo site_url('blog/credito'); ?>" class="main-menu__link">
                                Crédito
                            </a>
                        </li><!-- Crédito -->

                        <!-- Talleres aliados -->
                        <li class="main-menu__item menu_talleres_aliados" data-nombre="Talleres aliados">
                            <a href="<?php echo site_url('blog/taller_aliado'); ?>" class="main-menu__link">
                                Talleres aliados
                            </a>
                        </li><!-- Talleres aliados -->

                        <!-- Garantía -->
                        <li class="main-menu__item menu_garantia" data-nombre="Garantía">
                            <a href="<?php echo site_url('logistica/garantias'); ?>" class="main-menu__link">
                                Garantía
                            </a>
                        </li><!-- Garantía -->

                        <!-- Blog -->
                        <li class="main-menu__item menu_blog" data-nombre="Blog">
                            <a href="<?php echo site_url('blog'); ?>" class="main-menu__link">
                                Blog
                            </a>
                        </li><!-- Blog -->

                        <!-- Contacto -->
                        <li class="main-menu__item menu_contacto" data-nombre="Contacto">
                            <a href="<?php echo site_url('blog/contacto'); ?>" class="main-menu__link">
                                Contacto
                            </a>
                        </li><!-- Contacto -->

                        <!-- Más -->
                        <li class="main-menu__item main-menu__item--submenu--megamenu main-menu__item--has-submenu">
                            <a href="#" class="main-menu__link">
                                Más...
                                <svg width="7px" height="5px">
                                    <path d="M0.280,0.282 C0.645,-0.084 1.238,-0.077 1.596,0.297 L3.504,2.310 L5.413,0.297 C5.770,-0.077 6.363,-0.084 6.728,0.282 C7.080,0.634 7.088,1.203 6.746,1.565 L3.504,5.007 L0.262,1.565 C-0.080,1.203 -0.072,0.634 0.280,0.282 Z" />
                                </svg>
                            </a>
                            <div class="main-menu__submenu">
                                <div class="main-menu__megamenu main-menu__megamenu--size--nl">
                                    <div class="megamenu">
                                        <div class="row">
                                            <div class="col-12">
                                                <ul class="megamenu__links megamenu-links megamenu-links--root">
                                                    <li class="megamenu-links__item megamenu-links__item--has-submenu">
                                                        <ul class="megamenu-links">
                                                            <li class="megamenu-links__item menu_mas" data-nombre="Nosotros">
                                                                <a class="megamenu-links__item-link" href="<?php echo site_url('blog/nosotros'); ?>">Nosotros</a>
                                                            </li>
                                                            <li class="megamenu-links__item menu_mas" data-nombre="Distribuidores">
                                                                <a class="megamenu-links__item-link" href="<?php echo site_url('blog/distribuidores'); ?>">Distribuidores</a><br>
                                                            </li>
                                                            <li class="megamenu-links__item menu_mas" data-nombre="Denuncias">
                                                                <a class="megamenu-links__item-link" href="<?php echo site_url('denuncias'); ?>">Denuncias</a>
                                                            </li>
                                                            <li class="megamenu-links__item menu_mas" data-nombre="Trabaja con nosotros">
                                                                <a class="megamenu-links__item-link" href="https://forms.office.com/r/XLcnbb3XcV" target="_blank">Trabaja con nosotros</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li><!-- Más -->

                        <!-- Ajustes -->
                        <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_ver'], $permisos)) { ?>
                            <li class="main-menu__item main-menu__item--submenu--megamenu main-menu__item--has-submenu">
                                <a href="#" class="main-menu__link">
                                    Ajustes
                                    <svg width="7px" height="5px">
                                        <path d="M0.280,0.282 C0.645,-0.084 1.238,-0.077 1.596,0.297 L3.504,2.310 L5.413,0.297 C5.770,-0.077 6.363,-0.084 6.728,0.282 C7.080,0.634 7.088,1.203 6.746,1.565 L3.504,5.007 L0.262,1.565 C-0.080,1.203 -0.072,0.634 0.280,0.282 Z" />
                                    </svg>
                                </a>
                                <div class="main-menu__submenu">
                                    <div class="main-menu__megamenu main-menu__megamenu--size--sm">
                                        <div class="megamenu">
                                            <div class="row">
                                                <div class="col-12">
                                                    <ul class="megamenu__links megamenu-links megamenu-links--root">
                                                        <li class="megamenu-links__item megamenu-links__item--has-submenu">
                                                            <ul class="megamenu-links">
                                                                <?php if(isset($permisos) && in_array(['comercial' => 'comercial_ventas_ver'], $permisos)) { ?>
                                                                    <li class="megamenu-links__item mb-1">
                                                                        <a class="megamenu-links__item-link" href="<?php echo site_url('clientes/ventas'); ?>">Ventas</a>
                                                                    </li>
                                                                <?php } ?>

                                                                <li class="menu__item menu__item--has-submenu mb-1">
                                                                    <a class="megamenu-links__item-link" href="#">
                                                                        Clientes
                                                                        <span class="menu__arrow">
                                                                            <svg width="6px" height="9px">
                                                                                <path d="M0.3,7.4l3-2.9l-3-2.9c-0.4-0.3-0.4-0.9,0-1.3l0,0c0.4-0.3,0.9-0.4,1.3,0L6,4.5L1.6,8.7c-0.4,0.4-0.9,0.4-1.3,0l0,0C-0.1,8.4-0.1,7.8,0.3,7.4z" />
                                                                            </svg>
                                                                        </span>
                                                                    </a>
                                                                    <div class="menu__submenu">
                                                                        <ul class="menu">
                                                                            <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_recibos_ver'], $permisos)) { ?>
                                                                                <li class="menu__item">
                                                                                    <a href="<?php echo site_url("configuracion/recibos/ver/2"); ?>" class="menu__link">
                                                                                        Pagos
                                                                                    </a>
                                                                                </li>
                                                                            <?php } ?>

                                                                            <li class="menu__item">
                                                                                <a href="<?php echo site_url('clientes/credito/ver'); ?>" class="menu__link">
                                                                                    Solicitudes de crédito
                                                                                </a>
                                                                            </li>

                                                                            <li class="menu__item">
                                                                                <a href="<?php echo site_url('clientes/certificados_tributarios/ver'); ?>" class="menu__link">
                                                                                    Certificados tributarios
                                                                                </a>
                                                                            </li>

                                                                            <li class="menu__item">
                                                                                <a href="<?php echo site_url('clientes/pedidos/ver'); ?>" class="menu__link">
                                                                                    Pedidos
                                                                                </a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </li>

                                                                <li class="menu__item menu__item--has-submenu mb-1">
                                                                    <a class="megamenu-links__item-link" href="#">
                                                                        Contabilidad
                                                                        <span class="menu__arrow">
                                                                            <svg width="6px" height="9px">
                                                                                <path d="M0.3,7.4l3-2.9l-3-2.9c-0.4-0.3-0.4-0.9,0-1.3l0,0c0.4-0.3,0.9-0.4,1.3,0L6,4.5L1.6,8.7c-0.4,0.4-0.9,0.4-1.3,0l0,0C-0.1,8.4-0.1,7.8,0.3,7.4z" />
                                                                            </svg>
                                                                        </span>
                                                                    </a>
                                                                    <div class="menu__submenu">
                                                                        <ul class="menu">
                                                                            <li class="menu__item">
                                                                                <a href="<?php echo site_url('contabilidad/comprobantes'); ?>" class="menu__link">
                                                                                    Comprobantes
                                                                                </a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </li>

                                                                <?php if(isset($permisos) && in_array(['marketing' => 'marketing_ver'], $permisos)) { ?>
                                                                    <li class="menu__item menu__item--has-submenu mb-1">
                                                                        <a class="megamenu-links__item-link" href="#">
                                                                            Marketing
                                                                            <span class="menu__arrow">
                                                                                <svg width="6px" height="9px">
                                                                                    <path d="M0.3,7.4l3-2.9l-3-2.9c-0.4-0.3-0.4-0.9,0-1.3l0,0c0.4-0.3,0.9-0.4,1.3,0L6,4.5L1.6,8.7c-0.4,0.4-0.9,0.4-1.3,0l0,0C-0.1,8.4-0.1,7.8,0.3,7.4z" />
                                                                                </svg>
                                                                            </span>
                                                                        </a>

                                                                        <?php if(isset($permisos) && in_array(['marketing' => 'marketing_campanias_ver'], $permisos)) { ?>
                                                                            <div class="menu__submenu">
                                                                                <ul class="menu">
                                                                                    <li class="menu__item">
                                                                                        <a href="<?php echo site_url('marketing/campanias/ver'); ?>" class="menu__link">
                                                                                            Campañas
                                                                                        </a>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </li>
                                                                <?php } ?>

                                                                <li class="megamenu-links__item menu_mas mb-1" data-nombre="Garantía">
                                                                    <a class="megamenu-links__item-link" href="<?php echo site_url('configuracion/contactos/ver'); ?>">Contactos</a>
                                                                </li>

                                                                <li class="menu__item menu__item--has-submenu mb-1">
                                                                    <a class="megamenu-links__item-link" href="#">
                                                                        Logística
                                                                        <span class="menu__arrow">
                                                                            <svg width="6px" height="9px">
                                                                                <path d="M0.3,7.4l3-2.9l-3-2.9c-0.4-0.3-0.4-0.9,0-1.3l0,0c0.4-0.3,0.9-0.4,1.3,0L6,4.5L1.6,8.7c-0.4,0.4-0.9,0.4-1.3,0l0,0C-0.1,8.4-0.1,7.8,0.3,7.4z" />
                                                                            </svg>
                                                                        </span>
                                                                    </a>
                                                                    <div class="menu__submenu">
                                                                        <ul class="menu">
                                                                            <li class="menu__item">
                                                                                <a href="<?php echo site_url('logistica/envios/cotizacion'); ?>" class="menu__link">
                                                                                    Cotizar envío con TCC
                                                                                </a>
                                                                            </li>

                                                                            <li class="menu__item">
                                                                                <a href="<?php echo site_url('logistica/garantias/ver'); ?>" class="menu__link">
                                                                                    Garantías
                                                                                </a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </li>

                                                                <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_usuarios_ver'], $permisos)) { ?>
                                                                    <li class="megamenu-links__item mb-1">
                                                                        <a class="megamenu-links__item-link" href="<?php echo site_url('configuracion/usuarios/ver'); ?>">Usuarios</a>
                                                                    </li>
                                                                <?php } ?>

                                                                <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_perfiles_ver'], $permisos)) { ?>
                                                                    <li class="megamenu-links__item mb-1">
                                                                        <a class="megamenu-links__item-link" href="<?php echo site_url('configuracion/perfiles/ver'); ?>">Perfiles</a>
                                                                    </li>
                                                                <?php } ?>

                                                                <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_productos_ver'], $permisos)) { ?>
                                                                    <li class="megamenu-links__item mb-1">
                                                                        <a class="megamenu-links__item-link" href="<?php echo site_url('configuracion/productos/ver'); ?>">Productos</a>
                                                                    </li>
                                                                <?php } ?>

                                                                <?php if(isset($permisos) && in_array(['proveedores' => 'proveedores_ver'], $permisos)) { ?>
                                                                    <li class="menu__item menu__item--has-submenu mb-1">
                                                                        <a class="megamenu-links__item-link" href="#">
                                                                            Proveedores
                                                                            <span class="menu__arrow">
                                                                                <svg width="6px" height="9px">
                                                                                    <path d="M0.3,7.4l3-2.9l-3-2.9c-0.4-0.3-0.4-0.9,0-1.3l0,0c0.4-0.3,0.9-0.4,1.3,0L6,4.5L1.6,8.7c-0.4,0.4-0.9,0.4-1.3,0l0,0C-0.1,8.4-0.1,7.8,0.3,7.4z" />
                                                                                </svg>
                                                                            </span>
                                                                        </a>
                                                                        <div class="menu__submenu">
                                                                            <ul class="menu">
                                                                                <li class="menu__item">
                                                                                    <a href="<?php echo site_url('proveedores/maestro'); ?>" class="menu__link">
                                                                                        Maestro de marcas
                                                                                    </a>
                                                                                </li>

                                                                                <li class="menu__item">
                                                                                    <a href="<?php echo site_url('proveedores/solicitudes'); ?>" class="menu__link">
                                                                                        Solicitudes de precios
                                                                                    </a>
                                                                                </li>
                                                                                <hr>

                                                                                <li class="menu__item">
                                                                                    <a href="<?php echo site_url('importaciones/maestro'); ?>" class="menu__link">
                                                                                        Maestro de anticipos
                                                                                    </a>
                                                                                </li>
                                                                                <li class="menu__item">
                                                                                    <a href="<?php echo site_url('importaciones'); ?>" class="menu__link">
                                                                                        Importaciones
                                                                                    </a>
                                                                                </li>
                                                                                <li class="menu__item">
                                                                                    <a href="<?php echo site_url('importaciones_pagos/ver'); ?>" class="menu__link">
                                                                                        Pagos
                                                                                    </a>
                                                                                </li>
                                                                                <hr>

                                                                                <?php if(isset($permisos) && in_array(['proveedores' => 'proveedores_ver_facturas'], $permisos)) { ?>
                                                                                    <li class="menu__item">
                                                                                        <a href="<?php echo site_url('proveedores'); ?>" class="menu__link">
                                                                                            Facturas
                                                                                        </a>
                                                                                    </li>
                                                                                <?php } ?>

                                                                                <?php if(isset($permisos) && in_array(['proveedores' => 'proveedores_ver_certificados'], $permisos)) { ?>
                                                                                    <li class="menu__item">
                                                                                        <a href="<?php echo site_url('proveedores/certificados'); ?>" class="menu__link">
                                                                                            Certificados tributarios
                                                                                        </a>
                                                                                    </li>
                                                                                <?php } ?>
                                                                            </ul>
                                                                        </div>
                                                                    </li>
                                                                <?php } ?>

                                                                <li class="megamenu-links__item menu_mas mb-1">
                                                                    <a class="megamenu-links__item-link" href="<?php echo base_url('archivos/manual'); ?>" target="_blank">
                                                                        📘 Manual de Usuario
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li><!-- Ajustes -->
                        <?php } ?>
                    </ul>
                </div>
            </div><!-- Opciones del menu -->

            <!-- Carrito de compras -->
            <div class="indicator indicator--trigger--click">
                <a href="#" class="indicator__button">
                    <span class="indicator__icon">
                        <svg width="32" height="32">
                            <circle fill="#19287F" cx="10.5" cy="27.5" r="2.5" />
                            <circle fill="#19287F" cx="23.5" cy="27.5" r="2.5" />
                            <path fill="#19287F" d="M26.4,21H11.2C10,21,9,20.2,8.8,19.1L5.4,4.8C5.3,4.3,4.9,4,4.4,4H1C0.4,4,0,3.6,0,3s0.4-1,1-1h3.4C5.8,2,7,3,7.3,4.3 l3.4,14.3c0.1,0.2,0.3,0.4,0.5,0.4h15.2c0.2,0,0.4-0.1,0.5-0.4l3.1-10c0.1-0.2,0-0.4-0.1-0.4C29.8,8.1,29.7,8,29.5,8H14 c-0.6,0-1-0.4-1-1s0.4-1,1-1h15.5c0.8,0,1.5,0.4,2,1c0.5,0.6,0.6,1.5,0.4,2.2l-3.1,10C28.5,20.3,27.5,21,26.4,21z" />
                        </svg>
                        <span class="indicator__counter" id="carrito_total_items">0</span>
                    </span>
                    <span class="indicator__title">Tu compra</span>
                    <span class="indicator__value color_azul_corporativo_primario" id="carrito_total">$ 0</span>
                </a>

                <div class="indicator__content" style="max-height: 80vh; overflow-y: auto;">
                    <div class="dropcart" id="contenedor_carrito_detalle"></div>
                </div>
            </div><!-- Carrito de compras -->

            <!-- Botón de pago de facturas -->
            <div class="header__navbar-phone phone">
                <a href="<?php echo site_url('clientes'); ?>" class="phone__body menu_credito" data-nombre="Paga tus facturas (botón)" target="_blank">
                    <img src="<?php echo base_url(); ?>images/boton_pago.png" height="32" alt="Paga tus facturas">
                </a>
            </div><!-- Botón de pago de facturas -->
        </div>

        <!-- Logo -->
        <div class="header__logo">
            <a href="<?php echo site_url(); ?>" class="logo">
                <div class="logo__image">
                    <img src="<?php echo base_url().'images/logo.png'; ?>" height="70">
                </div>
            </a>
        </div><!-- Logo -->

        <!-- Búsqueda dinámica -->
        <div class="header__search">
            <?php $this->load->view('core/menu_superior/busqueda'); ?>
        </div><!-- Búsqueda dinámica -->

        
        <div class="header__indicators">
            <!-- Sesión -->
            <?php $this->load->view('core/menu_superior/sesion'); ?>

            <!-- Redes sociales -->
            <div class="indicator">
                <div class="footer-newsletter__social-links social-links">
                    <ul class="social-links__list" style="margin-right: 11px;">
                        <li class="social-links__item menu_link_redes_sociales" data-nombre="instagram">
                            <a href="https://www.instagram.com/repuestossimonbolivar/" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                    <path fill="#19287F" d="M320.3 205C256.8 204.8 205.2 256.2 205 319.7C204.8 383.2 256.2 434.8 319.7 435C383.2 435.2 434.8 383.8 435 320.3C435.2 256.8 383.8 205.2 320.3 205zM319.7 245.4C360.9 245.2 394.4 278.5 394.6 319.7C394.8 360.9 361.5 394.4 320.3 394.6C279.1 394.8 245.6 361.5 245.4 320.3C245.2 279.1 278.5 245.6 319.7 245.4zM413.1 200.3C413.1 185.5 425.1 173.5 439.9 173.5C454.7 173.5 466.7 185.5 466.7 200.3C466.7 215.1 454.7 227.1 439.9 227.1C425.1 227.1 413.1 215.1 413.1 200.3zM542.8 227.5C541.1 191.6 532.9 159.8 506.6 133.6C480.4 107.4 448.6 99.2 412.7 97.4C375.7 95.3 264.8 95.3 227.8 97.4C192 99.1 160.2 107.3 133.9 133.5C107.6 159.7 99.5 191.5 97.7 227.4C95.6 264.4 95.6 375.3 97.7 412.3C99.4 448.2 107.6 480 133.9 506.2C160.2 532.4 191.9 540.6 227.8 542.4C264.8 544.5 375.7 544.5 412.7 542.4C448.6 540.7 480.4 532.5 506.6 506.2C532.8 480 541 448.2 542.8 412.3C544.9 375.3 544.9 264.5 542.8 227.5zM495 452C487.2 471.6 472.1 486.7 452.4 494.6C422.9 506.3 352.9 503.6 320.3 503.6C287.7 503.6 217.6 506.2 188.2 494.6C168.6 486.8 153.5 471.7 145.6 452C133.9 422.5 136.6 352.5 136.6 319.9C136.6 287.3 134 217.2 145.6 187.8C153.4 168.2 168.5 153.1 188.2 145.2C217.7 133.5 287.7 136.2 320.3 136.2C352.9 136.2 423 133.6 452.4 145.2C472 153 487.1 168.1 495 187.8C506.7 217.3 504 287.3 504 319.9C504 352.5 506.7 422.6 495 452z"/>
                                </svg>
                            </a>
                        </li>

                        <li class="social-links__item menu_link_redes_sociales" data-nombre="facebook">
                            <a href="https://www.facebook.com/RepuestosSimonBolivar" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                    <path fill="#19287F" d="M576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 440 146.7 540.8 258.2 568.5L258.2 398.2L205.4 398.2L205.4 320L258.2 320L258.2 286.3C258.2 199.2 297.6 158.8 383.2 158.8C399.4 158.8 427.4 162 438.9 165.2L438.9 236C432.9 235.4 422.4 235 409.3 235C367.3 235 351.1 250.9 351.1 292.2L351.1 320L434.7 320L420.3 398.2L351 398.2L351 574.1C477.8 558.8 576 450.9 576 320z"/>
                                </svg>
                            </a>
                        </li>

                        <li class="social-links__item menu_link_redes_sociales pt-1" data-nombre="tiktok">
                            <a href="https://www.tiktok.com/@repuestossimonbolivar" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="-100 -50 700 700">
                                    <path fill="#19287F" d="M448 209.9a210.1 210.1 0 0 1 -122.8-39.3V349.4A162.6 162.6 0 1 1 185 188.3V278.2a74.6 74.6 0 1 0 52.2 71.2V0l88 0a121.2 121.2 0 0 0 1.9 22.2h0A122.2 122.2 0 0 0 381 102.4a121.4 121.4 0 0 0 67 20.1z"/>
                                </svg>
                            </a>
                        </li>
                    </ul>
                </div>
            </div><!-- Redes sociales -->
        </div>
    </div>
</header>

<script>
    $().ready(async () => {
        $('.menu_link_redes_sociales .menu_credito, .menu_talleres_aliados, .menu_garantia, .menu_blog, .menu_contacto, .menu_mas').click(function() {
            agregarLog(91, JSON.stringify({
                tipo: 'Acceso a menú',
                detalle: $(this).attr('data-nombre')
            }))
        })
    })
</script>