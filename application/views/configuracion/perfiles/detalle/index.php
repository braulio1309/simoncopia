<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container container--max--xl">
        <div class="row">
            <div class="col-12 col-lg-3 d-flex">
                <div class="account-nav flex-grow-1">
                    <h4 class="account-nav__title">Opciones</h4>
                    <ul class="account-nav__list">
                        <li class="perfil_datos_generales account-nav__item">
                            <a onClick="cargarInterfaz('configuracion/perfiles/detalle/datos_generales', 'contenedor_perfiles', {token: '<?php echo $this->uri->segment(4); ?>'})">Datos generales</a>
                        </li>
                        <li class="perfil_roles account-nav__item">
                            <a onClick="cargarInterfaz('configuracion/perfiles/detalle/roles', 'contenedor_perfiles', {token: '<?php echo $this->uri->segment(4); ?>'})">Roles</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-lg-9 mt-4 mt-lg-0">
                <div id="contenedor_perfiles"></div>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    $().ready(() => {
        cargarInterfaz('configuracion/perfiles/detalle/roles', 'contenedor_perfiles', {token: '<?php echo $this->uri->segment(4); ?>'})
    })
</script>