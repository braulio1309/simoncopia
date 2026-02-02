<?php
if(isset($datos['token'])) {
    $perfil = $this->configuracion_model->obtener('perfiles', ['token' => $datos['token']]);
    echo "<input type='hidden' id='perfil_id' value='$perfil->id' />";
}

$modulos = $this->configuracion_model->obtener('modulos');
?>

<div class="card">
    <div class="card-header">
        <h5>Roles</h5>
    </div>
    <div class="card-divider"></div>
    <div class="card-body card-body--padding--2">
        <div class="row no-gutters">
            <div class="col-12 col-lg-10 col-xl-8">
                <ul class="nav nav-tabs">
                    <?php 
                    foreach($modulos as $modulo) echo "
                        <li class='nav-item'>
                            <a class='nav-link modulo$modulo->id' href='#' onClick='javascript:cargarAcciones($modulo->id)'>$modulo->nombre</a>
                        </li>
                    ";
                    ?>
                </ul>
                <div class="tab-pane" role="tabpanel" id="contenedor_acciones"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(`.perfil_roles`).addClass('account-nav__item--active')
    
    cargarAcciones = moduloId => {
        $(".nav-link").removeClass('active')
        $(`.modulo${moduloId}`).addClass('active')

        cargarInterfaz('configuracion/perfiles/detalle/datos', 'contenedor_acciones', {token: '<?php echo $datos['token']; ?>', modulo_id: moduloId})
    }

    $().ready(() => {
        cargarAcciones(1)
    })
</script>