
<?php 
$solicitud = $this->clientes_model->obtener("clientes_solicitudes_credito", ["id" => $id]);
$menu = ["detalle", "archivos", "bitacora", "opciones"];

if (!$tipo) $tipo = "detalle"; 
if (!in_array($tipo, $menu)) redirect("inico");

$vista = $tipo;
if($tipo ==="bitacora") $vista = "bitacora/index";
?>

<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de solicitudes de crédito</h1>
        </div>
    </div>
</div>

<div class="pl-5 pr-5">
    <div class="block-zone__widget-header">
        <div class="block-zone__tabs">
            <button type="button" class="block-zone__tabs-button" id="pestana_detalle">
                <a href="<?php echo base_url("clientes/credito/ver/$id/detalle"); ?>">
                    Formulario
                </a>
            </button>
            <button type="button" class="block-zone__tabs-button" id="pestana_archivos">
                <a href="<?php echo base_url("clientes/credito/ver/$id/archivos"); ?>">
                    Archivos
                </a>
            </button>
            <button type="button" class="block-zone__tabs-button" id="pestana_bitacora">
                <a href="<?php echo base_url("clientes/credito/ver/$id/bitacora"); ?>">
                    Bitácora
                </a>
            </button>
            <button type="button" class="block-zone__tabs-button" id="pestana_opciones">
                <a href="<?php echo base_url("clientes/credito/ver/$id/opciones"); ?>">
                    Opciones
                </a>
            </button>
        </div>
    </div>

    <div id="contenedor_detalle"></div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    var solicitud = JSON.parse('<?php echo addslashes(json_encode($solicitud)); ?>')

    var datosBitacora = {
        tipo: 'clientes_solicitudes_credito_bitacora',
        solicitud_id: solicitud.id,
        usuario_id: $('#sesion_usuario_id').val()
    }

    aprobarSolicitudCredito = async (id, confirmacion) => {
        if(!confirmacion) {
            cargarInterfaz('clientes/solicitud_credito/aprobacion', 'contenedor_modal_gestion_solicitud_Credito', {id: id})
            return false
        }

        let segundaConfirmacion = await confirmar('aprobar', `¿Estás seguro de aprobar la solicitud?`)
        if(!segundaConfirmacion) return false

        if (!validarCamposObligatorios([$('#aprobacion_cupo'), $('#aprobacion_responsable_iva')])) return false

        let datos = {
            tipo: 'clientes_solicitudes_credito',
            id: id,
            solicitud_credito_estado_id: 2,
            fecha_cierre: true,
            cupo_asignado: $('#aprobacion_cupo').val().replace(/\./g, ''),
        }

        await consulta('actualizar', datos)

        // Creación del registro en bitácora
        datosBitacora.observaciones = `Solicitud aprobada`
        await consulta('crear', datosBitacora)

        Swal.fire({
            title: 'Creando el tercero en el ERP...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        let responsableIVA = $('#aprobacion_responsable_iva option:selected').attr('data-responsable_iva')
        let causanteIVA = $('#aprobacion_responsable_iva option:selected').attr('data-causante_iva')
        
        let datosTerceroSiesa = {
            responsable_iva: responsableIVA,
            causante_iva: causanteIVA,
            tipo_tercero: solicitud.persona_tipo_id,
            documento_tipo: solicitud.tipo_identificacion_codigo,
            documento_numero: solicitud.documento_numero,
            nombres: solicitud.nombre,
            primer_apellido: solicitud.primer_apellido,
            segundo_apellido: solicitud.segundo_apellido,
            razon_social: solicitud.razon_social,
            id_departamento: solicitud.departamento_id,
            id_ciudad: solicitud.ciudad_id,
            direccion: solicitud.direccion,
            contacto: solicitud.razon_social,
            email: solicitud.email,
            telefono: solicitud.telefono,
            vendedor: solicitud.vendedor_codigo,
            lista_precio: '001',
            condiciono_pago: 'C30',
            tipo_cliente: 'C001',
            cupo: datos.cupo_asignado,
            dias_gracia: 26,
            bloqueo_cupo: 1,
        }

        // Se consulta en el ERP el tercero y el cliente
        var consultaTercero = await consulta('obtener', {tipo: 'terceros', numero_documento: solicitud.documento_numero}, false)
        var consultaTerceroCliente = await consulta('obtener', {tipo: 'clientes_sucursales', numero_documento: solicitud.documento_numero}, false)

        // Si el tercero ya existe en el ERP
        if(consultaTercero && consultaTercero.codigo == 0) {
            // Se mantiene la fecha de nacimiento
            datosTerceroSiesa.fecha_nacimiento = consultaTercero.detalle.Table[0].f200_fecha_nacimiento.split("T")[0].replaceAll("-", "")
        }

        // Si el cliente existe en el ERP
        if(consultaTerceroCliente && consultaTerceroCliente.codigo == 0) {
            // Se mantiene la fecha de ingreso
            datosTerceroSiesa.fecha_ingreso = consultaTerceroCliente.detalle.Table[0].f201_fecha_ingreso.split("T")[0].replaceAll("-", "")
        }

        let creacionTerceroSiesa = crearTerceroCliente(datosTerceroSiesa)

        creacionTerceroSiesa.then(resultado => {
            if(resultado[0].codigo == 1) {
                agregarLog(80, JSON.stringify(resultado))
                mostrarAviso('error', `No se pudo crear el tercero en el ERP: <b>${resultado[0].detalle}</b>`, 20000)
                return false
            }

            agregarLog(81, JSON.stringify(resultado))
            mostrarAviso('exito', `La solicitud ha sido aprobada y el tercero ha sido creado correctamente`, 20000)

            // Creación del registro en bitácora
            datosBitacora.observaciones = `Tercero creado en Siesa`
            consulta('crear', datosBitacora)

            Swal.close()
        })
    }

    cargarOpcionMenu = () => {
        cargarInterfaz('clientes/solicitud_credito/<?php echo $vista; ?>', 'contenedor_detalle', {id: <?php echo $id; ?>})

        $(`#pestana_<?php echo $tipo; ?>`).addClass('block-zone__tabs-button--active')
    }
    
    enviarSolicitudAFirma = async id => {
        confirmacion = await confirmar('enviar', `¿Estás seguro de enviar la solicitud para firma?`)
        if(!confirmacion) return false

        let datos = {
            tipo: 'clientes_solicitudes_credito',
            fecha_envio_firma: true,
            id: id
        }

        // Creación del registro en bitácora
        datosBitacora.observaciones = `Enviado para firma`
        consulta('crear', datosBitacora)

        await consulta('actualizar', datos)

        mostrarAviso('exito', `Cambios guardados exitosamente`, 5000)
    }

    rechazarSolicitudCredito = async (id, confirmacion = null) => {
        if(!confirmacion) {
            cargarInterfaz('clientes/solicitud_credito/rechazo', 'contenedor_modal_gestion_solicitud_Credito', {id: id})
            return false
        }
        
        if (!validarCamposObligatorios([$('#motivo_rechazo_id')])) return false

        let segundaConfirmacion = await confirmar('rechazar', `¿Estás seguro de rechazar la solicitud?`)
        if(!segundaConfirmacion) return false

        let datos = {
            tipo: 'clientes_solicitudes_credito',
            id: id,
            solicitud_credito_estado_id: 3,
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