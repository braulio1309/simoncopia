<!-- Sección 1 -->
<header class="site__mobile-header">
    <div class="mobile-header">
        <div class="container">
            <div class="mobile-header__body">
                <!-- Ícono Menú -->
                <button class="mobile-header__menu-button" type="button">
                    <svg width="18px" height="14px">
                        <path fill="#19287F" d="M-0,8L-0,6L18,6L18,8L-0,8ZM-0,-0L18,-0L18,2L-0,2L-0,-0ZM14,14L-0,14L-0,12L14,12L14,14Z" />
                    </svg>
                </button><!-- Ícono Menú -->

                <!-- Logo -->
                <a class="mobile-header__logo" href="<?php echo site_url(); ?>">
                    <img src="<?php echo base_url().'images/logo.png'; ?>" height="40">
                </a><!-- Logo -->

                <!-- Búsqueda -->
                <div class="mobile-header__search mobile-search">
                    <form class="mobile-search__body" id="formulario_buscar_movil">
                        <input type="text" class="mobile-search__input" placeholder="Escriba un producto, marca, línea, etc." id="buscar_movil">

                        <button type="submit" class="mobile-search__button mobile-search__button--search">
                            <svg width="20" height="20">
                                <path fill="#19287F" d="M19.2,17.8c0,0-0.2,0.5-0.5,0.8c-0.4,0.4-0.9,0.6-0.9,0.6s-0.9,0.7-2.8-1.6c-1.1-1.4-2.2-2.8-3.1-3.9C10.9,14.5,9.5,15,8,15 c-3.9,0-7-3.1-7-7s3.1-7,7-7s7,3.1,7,7c0,1.5-0.5,2.9-1.3,4c1.1,0.8,2.5,2,4,3.1C20,16.8,19.2,17.8,19.2,17.8z M8,3C5.2,3,3,5.2,3,8 c0,2.8,2.2,5,5,5c2.8,0,5-2.2,5-5C13,5.2,10.8,3,8,3z" />
                            </svg>
                        </button>
                        <button type="button" class="mobile-search__button mobile-search__button--close">
                            <svg width="20" height="20">
                                <path fill="#19287F" d="M16.7,16.7L16.7,16.7c-0.4,0.4-1,0.4-1.4,0L10,11.4l-5.3,5.3c-0.4,0.4-1,0.4-1.4,0l0,0c-0.4-0.4-0.4-1,0-1.4L8.6,10L3.3,4.7 c-0.4-0.4-0.4-1,0-1.4l0,0c0.4-0.4,1-0.4,1.4,0L10,8.6l5.3-5.3c0.4-0.4,1-0.4,1.4,0l0,0c0.4,0.4,0.4,1,0,1.4L11.4,10l5.3,5.3 C17.1,15.7,17.1,16.3,16.7,16.7z" />
                            </svg>
                        </button>
                        <div class="mobile-search__field"></div>
                    </form>
                </div><!-- Búsqueda -->
                
                <div class="mobile-header__indicators">
                    <div class="mobile-indicator mobile-indicator--search d-md-none">
                        <button type="button" class="mobile-indicator__button">
                            <span class="mobile-indicator__icon">
                                <svg width="20" height="20">
                                    <path fill="#19287F" d="M19.2,17.8c0,0-0.2,0.5-0.5,0.8c-0.4,0.4-0.9,0.6-0.9,0.6s-0.9,0.7-2.8-1.6c-1.1-1.4-2.2-2.8-3.1-3.9C10.9,14.5,9.5,15,8,15 c-3.9,0-7-3.1-7-7s3.1-7,7-7s7,3.1,7,7c0,1.5-0.5,2.9-1.3,4c1.1,0.8,2.5,2,4,3.1C20,16.8,19.2,17.8,19.2,17.8z M8,3C5.2,3,3,5.2,3,8 c0,2.8,2.2,5,5,5c2.8,0,5-2.2,5-5C13,5.2,10.8,3,8,3z" />
                                </svg>
                            </span>
                        </button>
                    </div>

                    <div class="mobile-indicator d-none d-md-block">
                        <a href="<?php echo site_url('sesion'); ?>" class="mobile-indicator__button">
                            <span class="mobile-indicator__icon">
                                <svg width="20" height="20">
                                    <path fill="#19287F" d="M20,20h-2c0-4.4-3.6-8-8-8s-8,3.6-8,8H0c0-4.2,2.6-7.8,6.3-9.3C4.9,9.6,4,7.9,4,6c0-3.3,2.7-6,6-6s6,2.7,6,6 c0,1.9-0.9,3.6-2.3,4.7C17.4,12.2,20,15.8,20,20z M14,6c0-2.2-1.8-4-4-4S6,3.8,6,6s1.8,4,4,4S14,8.2,14,6z" />
                                </svg>
                            </span>
                        </a>
                    </div>

                    <div class="mobile-indicator">
                        <a href="<?php echo site_url('carrito/ver'); ?>" class="mobile-indicator__button">
                            <span class="mobile-indicator__icon">
                                <svg width="20" height="20">
                                    <circle cx="7" cy="17" r="2" />
                                    <circle cx="15" cy="17" r="2" />
                                    <path fill="#19287F" d="M20,4.4V5l-1.8,6.3c-0.1,0.4-0.5,0.7-1,0.7H6.7c-0.4,0-0.8-0.3-1-0.7L3.3,3.9C3.1,3.3,2.6,3,2.1,3H0.4C0.2,3,0,2.8,0,2.6 V1.4C0,1.2,0.2,1,0.4,1h2.5c1,0,1.8,0.6,2.1,1.6L5.1,3l2.3,6.8c0,0.1,0.2,0.2,0.3,0.2h8.6c0.1,0,0.3-0.1,0.3-0.2l1.3-4.4 C17.9,5.2,17.7,5,17.5,5H9.4C9.2,5,9,4.8,9,4.6V3.4C9,3.2,9.2,3,9.4,3h9.2C19.4,3,20,3.6,20,4.4z" />
                                </svg>
                                <span class="mobile-indicator__counter" id="carrito_movil2_total_items">0</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header><!-- Sección 1 -->

<!-- Sección 2 -->
<div class="mobile-menu">
    <div class="mobile-menu__backdrop"></div>
    <div class="mobile-menu__body">
        <!-- Ícono cerrar -->
        <button class="mobile-menu__close" type="button">
            <svg width="12" height="12">
                <path d="M10.8,10.8L10.8,10.8c-0.4,0.4-1,0.4-1.4,0L6,7.4l-3.4,3.4c-0.4,0.4-1,0.4-1.4,0l0,0c-0.4-0.4-0.4-1,0-1.4L4.6,6L1.2,2.6 c-0.4-0.4-0.4-1,0-1.4l0,0c0.4-0.4,1-0.4,1.4,0L6,4.6l3.4-3.4c0.4-0.4,1-0.4,1.4,0l0,0c0.4,0.4,0.4,1,0,1.4L7.4,6l3.4,3.4 C11.2,9.8,11.2,10.4,10.8,10.8z" />
            </svg>
        </button><!-- Ícono cerrar -->

        <div class="mobile-menu__panel">
            <div class="mobile-menu__panel-header">
                <div class="mobile-menu__panel-title">Menú</div>
            </div>

            <div class="mobile-menu__panel-body">
                <!-- Marcas -->
                <div class="mobile-menu__settings-list">
                    <div class="mobile-menu__setting" data-mobile-menu-item>
                        <button class="mobile-menu__setting-button" title="Compra por categoría" data-mobile-menu-trigger>
                            <span class="mobile-menu__setting-title">Compra por categoría</span>
                            <span class="mobile-menu__setting-arrow">
                                <svg width="6px" height="9px">
                                    <path d="M0.3,7.4l3-2.9l-3-2.9c-0.4-0.3-0.4-0.9,0-1.3l0,0c0.4-0.3,0.9-0.4,1.3,0L6,4.5L1.6,8.7c-0.4,0.4-0.9,0.4-1.3,0l0,0C-0.1,8.4-0.1,7.8,0.3,7.4z" />
                                </svg>
                            </span>
                        </button>
                        <div class="mobile-menu__setting-panel" data-mobile-menu-panel>
                            <div class="mobile-menu__panel mobile-menu__panel--hidden">
                                <div class="mobile-menu__panel-header">
                                    <button class="mobile-menu__panel-back" type="button">
                                        <svg width="7" height="11">
                                            <path d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z" />
                                        </svg>
                                    </button>
                                    <div class="mobile-menu__panel-title">Seleccionar marca</div>
                                </div>
                                <div class="mobile-menu__panel-body">
                                    <ul class="mobile-menu__links">
                                        <li data-mobile-menu-item>
                                            <a href="<?php echo site_url("productos"); ?>" type="button" class="" data-mobile-menu-trigger>
                                                TODAS
                                            </a>
                                        </li>

                                        <?php foreach ($this->configuracion_model->obtener('marcas', ['activo' => 1]) as $marca) { ?>
                                            <li data-mobile-menu-item onClick="javascript:location.href='<?php echo site_url("productos?marca=$marca->nombre"); ?>'">
                                                <button type="button" data-mobile-menu-trigger>
                                                    <?php echo $marca->nombre; ?>
                                                </button>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- Marcas -->
                <div class="mobile-menu__divider"></div>

                <!-- Íconos -->
                <div class="mobile-menu__indicators">
                    <!-- Sesión -->
                    <?php if(!$this->session->userdata('usuario_id')) { ?>
                        <a class="mobile-menu__indicator" href="<?php echo site_url('sesion'); ?>">
                            <span class="mobile-menu__indicator-icon">
                                <svg width="20" height="20">
                                    <path fill="#19287F" d="M20,20h-2c0-4.4-3.6-8-8-8s-8,3.6-8,8H0c0-4.2,2.6-7.8,6.3-9.3C4.9,9.6,4,7.9,4,6c0-3.3,2.7-6,6-6s6,2.7,6,6 c0,1.9-0.9,3.6-2.3,4.7C17.4,12.2,20,15.8,20,20z M14,6c0-2.2-1.8-4-4-4S6,3.8,6,6s1.8,4,4,4S14,8.2,14,6z" />
                                </svg>
                            </span>
                            <span class="mobile-menu__indicator-title">INICIAR</span>
                        </a>
                    <?php } else { ?>
                        <a class="mobile-menu__indicator" href="<?php echo site_url('perfil/index/dashboard'); ?>">
                            <span class="mobile-menu__indicator-icon">
                                <svg width="20" height="20">
                                    <path fill="#19287F" d="M20,20h-2c0-4.4-3.6-8-8-8s-8,3.6-8,8H0c0-4.2,2.6-7.8,6.3-9.3C4.9,9.6,4,7.9,4,6c0-3.3,2.7-6,6-6s6,2.7,6,6 c0,1.9-0.9,3.6-2.3,4.7C17.4,12.2,20,15.8,20,20z M14,6c0-2.2-1.8-4-4-4S6,3.8,6,6s1.8,4,4,4S14,8.2,14,6z" />
                                </svg>
                            </span>
                            <span class="mobile-menu__indicator-title">
                                <?php echo $this->session->userdata('nombres'); ?>
                            </span>
                        </a>
                    <?php } ?><!-- Sesión -->

                    <!-- Carrito -->
                    <a class="mobile-menu__indicator" href="<?php echo site_url('carrito/ver'); ?>">
                        <span class="mobile-menu__indicator-icon">
                            <svg width="20" height="20">
                                <circle cx="7" cy="17" r="2" />
                                <circle cx="15" cy="17" r="2" />
                                <path fill="#19287F" d="M20,4.4V5l-1.8,6.3c-0.1,0.4-0.5,0.7-1,0.7H6.7c-0.4,0-0.8-0.3-1-0.7L3.3,3.9C3.1,3.3,2.6,3,2.1,3H0.4C0.2,3,0,2.8,0,2.6 V1.4C0,1.2,0.2,1,0.4,1h2.5c1,0,1.8,0.6,2.1,1.6L5.1,3l2.3,6.8c0,0.1,0.2,0.2,0.3,0.2h8.6c0.1,0,0.3-0.1,0.3-0.2l1.3-4.4 C17.9,5.2,17.7,5,17.5,5H9.4C9.2,5,9,4.8,9,4.6V3.4C9,3.2,9.2,3,9.4,3h9.2C19.4,3,20,3.6,20,4.4z" />
                            </svg>
                            <span class="mobile-menu__indicator-counter" id="carrito_movil_total_items">0</span>
                        </span>
                        <span class="mobile-menu__indicator-title">Carrito</span>
                    </a><!-- Carrito -->
                </div><!-- Íconos -->
                <div class="mobile-menu__divider"></div>

                <!-- Opciones del menú -->
                <ul class="mobile-menu__links">
                    <li data-mobile-menu-item>
                        <a href="<?php echo site_url(); ?>" data-mobile-menu-trigger>
                            Tienda
                        </a>
                    </li>

                    <li data-mobile-menu-item>
                        <a href="<?php echo site_url('blog/credito'); ?>" data-mobile-menu-trigger>
                            Crédito
                        </a>
                    </li>

                    <li data-mobile-menu-item>
                        <a href="<?php echo site_url('blog/taller_aliado'); ?>" data-mobile-menu-trigger>
                            Talleres aliados
                        </a>
                    </li>

                    <li data-mobile-menu-item>
                        <a href="<?php echo site_url('logistica/garantias'); ?>" data-mobile-menu-trigger>
                            Garantía
                        </a>
                    </li>

                    <li data-mobile-menu-item>
                        <a href="<?php echo site_url('blog'); ?>" data-mobile-menu-trigger>
                            Blog
                        </a>
                    </li>

                    <li data-mobile-menu-item>
                        <a href="<?php echo site_url('blog/contacto'); ?>" data-mobile-menu-trigger>
                            Contacto
                        </a>
                    </li>

                    <li data-mobile-menu-item>
                        <a href="#" class="" data-mobile-menu-trigger>
                            Más
                            <svg width="7" height="11">
                                <path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9 C-0.1,9.8-0.1,10.4,0.3,10.7z" />
                            </svg>
                        </a>
                        <div class="mobile-menu__links-panel" data-mobile-menu-panel>
                            <div class="mobile-menu__panel mobile-menu__panel--hidden">
                                <div class="mobile-menu__panel-header">
                                    <button class="mobile-menu__panel-back" type="button">
                                        <svg width="7" height="11">
                                            <path d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z" />
                                        </svg>
                                    </button>
                                    <div class="mobile-menu__panel-title">Más</div>
                                </div>
                                <div class="mobile-menu__panel-body">
                                    <ul class="mobile-menu__links">
                                        <li data-mobile-menu-item>
                                            <a href="<?php echo site_url('blog/nosotros'); ?>" class="" data-mobile-menu-trigger>
                                                Nosotros
                                            </a>
                                        </li>
                                        <li data-mobile-menu-item>
                                            <a href="<?php echo site_url('blog/distribuidores'); ?>" class="" data-mobile-menu-trigger>
                                                Distribuidores
                                            </a>
                                        </li>
                                        <li data-mobile-menu-item>
                                            <a href="<?php echo site_url('denuncias'); ?>" class="" data-mobile-menu-trigger>
                                                Denuncias
                                            </a>
                                        </li>
                                        <li data-mobile-menu-item>
                                            <?php $this->load->view('bitrix/empleo'); ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- Botón para pago de facturas -->
                    <p class="m-2">
                        <a href="<?php echo site_url('clientes'); ?>" class="ml-1">
                            <img src="<?php echo base_url(); ?>images/boton_pago.png" height="32" alt="Paga tus facturas">
                        </a>
                    </p>

                    <?php if(isset($permisos) && in_array(['proveedores' => 'proveedores_ver_facturas'], $permisos)) { ?>
                        <li data-mobile-menu-item>
                            <a href="<?php echo site_url("proveedores/facturas?nit={$this->session->userdata('documento_numero')}"); ?>" data-mobile-menu-trigger>
                                Ver mis facturas
                            </a>
                        </li>
                    <?php } ?>

                    <?php if($this->session->userdata('usuario_id')) { ?>
                        <li data-mobile-menu-item>
                            <a href="<?php echo site_url("proveedores/certificados?nit={$this->session->userdata('documento_numero')}"); ?>" data-mobile-menu-trigger>
                                Generar certificados tributarios
                            </a>
                        </li>
                    <?php } ?>

                    <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_recibos_ver'], $permisos)) { ?>
                        <li data-mobile-menu-item>
                            <a href="<?php echo site_url("configuracion/recibos/ver/2"); ?>" data-mobile-menu-trigger>
                                Pagos
                            </a>
                        </li>
                    <?php } ?>

                    <?php if($this->session->userdata('usuario_id')) { ?>
                        <li data-mobile-menu-item>
                            <a href="<?php echo site_url('sesion/cerrar'); ?>" data-mobile-menu-trigger>
                                Cerrar sesión
                            </a>
                        </li>
                    <?php } ?>
                </ul><!-- Opciones del menú -->

                <div class="mobile-menu__spring"></div>
                <div class="mobile-menu__divider"></div>
                <a class="mobile-menu__contacts" href="<?php echo site_url('blog/contacto'); ?>">
                    <div class="mobile-menu__contacts-subtitle">Llámanos</div>
                    <div class="mobile-menu__contacts-title"><?php echo $this->config->item('telefono'); ?></div>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Sección 2 -->

<script>
    $().ready(async () => {
        $('#formulario_buscar_movil').submit(e => {
            e.preventDefault()

            localStorage.simonBolivar_buscarProducto = $('#buscar_movil').val()

            location.href = '<?php echo site_url("productos?busqueda="); ?>' + $('#buscar_movil').val()
        })
    })
</script>