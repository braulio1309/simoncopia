<?php 
$menu = ["detalle", "archivos", "bitacora", "opciones"];

if (!$tipo) $tipo = "detalle"; 
if (!in_array($tipo, $menu)) redirect('inicio');

$vista = $tipo;
if($tipo ==="bitacora") $vista = "bitacora/index";
?>

<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de solicitudes de garantía</h1>
        </div>
    </div>
</div>

<div class="pl-5 pr-5">
    <div class="block-zone__widget-header">
        <div class="block-zone__tabs">
            <button type="button" class="block-zone__tabs-button" id="pestana_detalle">
                <a href="<?php echo site_url("logistica/garantias/ver/$solicitud_garantia->id/detalle"); ?>">
                    Formulario
                </a>
            </button>
            <button type="button" class="block-zone__tabs-button" id="pestana_archivos">
                <a href="<?php echo site_url("logistica/garantias/ver/$solicitud_garantia->id/archivos"); ?>">
                    Archivos
                </a>
            </button>
            <button type="button" class="block-zone__tabs-button" id="pestana_bitacora">
                <a href="<?php echo site_url("logistica/garantias/ver/$solicitud_garantia->id/bitacora"); ?>">
                    Bitácora
                </a>
            </button>
            <button type="button" class="block-zone__tabs-button" id="pestana_opciones">
                <a href="<?php echo site_url("logistica/garantias/ver/$solicitud_garantia->id/opciones"); ?>">
                    Opciones
                </a>
            </button>
        </div>
    </div>

    <div id="contenedor_detalle"></div>
</div>
<div class="block-space block-space--layout--before-footer"></div>


<script>
    var solicitudGarantia = JSON.parse('<?php echo addslashes(json_encode($solicitud_garantia)); ?>')

    var datosBitacora = {
        tipo: 'productos_solicitudes_garantia_bitacora',
        solicitud_id: solicitudGarantia.id,
        usuario_id: $('#sesion_usuario_id').val()
    }

    aprobarSolicitudGarantia = async (id) => {
        let confirmacion = await confirmar('aprobar', `¿Estás seguro de aprobar la solicitud?`)
        if(!confirmacion) return false

        let datos = {
            tipo: 'productos_solicitudes_garantia',
            id: id,
            estado_id: 2,
            fecha_cierre: true,
        }

        await consulta('actualizar', datos)

        // Creación del registro en bitácora
        datosBitacora.observaciones = `Solicitud aprobada`
        await consulta('crear', datosBitacora)

        mostrarAviso('exito', `La solicitud ha sido aprobada.`, 20000)
    }
    
    cargarOpcionMenu = () => {
        cargarInterfaz('logistica/solicitudes_garantia/<?php echo $vista; ?>', 'contenedor_detalle', {solicitud_garantia: solicitudGarantia})

        $(`#pestana_<?php echo $tipo; ?>`).addClass('block-zone__tabs-button--active')
    }

    rechazarSolicitudGarantia = async (id, confirmacion = null) => {
        if(!confirmacion) {
            cargarInterfaz('logistica/solicitudes_garantia/rechazo', 'contenedor_modal_gestion_solicitud_garantia', {id: id})
            return false
        }
        
        if (!validarCamposObligatorios([$('#motivo_rechazo_id')])) return false

        let segundaConfirmacion = await confirmar('rechazar', `¿Estás seguro de rechazar la solicitud?`)
        if(!segundaConfirmacion) return false

        let datos = {
            tipo: 'productos_solicitudes_garantia',
            id: id,
            estado_id: 3,
            fecha_cierre: true,
            motivo_rechazo_id: $('#motivo_rechazo_id').val(),
        }

        // Creación del registro en bitácora
        datosBitacora.observaciones = `Solicitud denegada`
        consulta('crear', datosBitacora)

        await consulta('actualizar', datos)

        mostrarAviso('exito', `Solicitud rechazada`, 5000)
    }

    $().ready(() => {
        cargarOpcionMenu()
    })
</script>