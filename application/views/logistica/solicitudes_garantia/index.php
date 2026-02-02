<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de garantías</h1>
        </div>
    </div>
</div>

<div class="pl-5 pr-5">
    <a class="btn btn-success mb-3" href="<?php echo site_url('logistica/garantias'); ?>">Crear solicitud</a>

    <div id="contenedor_solicitudes_garantia"></div>
    <div id="contenedor_asignar_usuario"></div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    cargarAsignarUsuario = (id) => {
        cargarInterfaz('logistica/solicitudes_garantia/asignar_usuario', 'contenedor_asignar_usuario', {id: id})
    }

    listarSolicitudesGarantia = () => {
        cargarInterfaz('logistica/solicitudes_garantia/lista', 'contenedor_solicitudes_garantia')
    }

    $().ready(() => {
        listarSolicitudesGarantia()
    })
</script>