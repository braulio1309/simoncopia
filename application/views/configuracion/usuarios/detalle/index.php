<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container container--max--xl">
        <div class="row">
            <div class="col-12 col-lg-3 d-flex">
                <div class="account-nav flex-grow-1">
                    <h4 class="account-nav__title">Opciones</h4>
                    <ul class="account-nav__list">
                        <li class="tercero_datos_generales account-nav__item">
                            <a href="#">Datos generales</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-lg-9 mt-4 mt-lg-0">
                <?php $this->load->view("configuracion/usuarios/detalle/datos_generales"); ?>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    $(`.tercero_datos_generales`).addClass('account-nav__item--active')
</script>