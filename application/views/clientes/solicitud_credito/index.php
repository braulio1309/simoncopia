<?php $usuarios = $this->clientes_model->obtener('clientes_solicitudes_credito_asignaciones', ['meses' => 1]); ?>

<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title p-2">Gestión de solicitudes de crédito</h1>

            <center>
                <div class="status-badge text-center status-badge--style--warning mb-3" id="indicador_estado">
                    <div class="status-badge__body">
                        <div class="status-badge__text">
                            <span id="indicador_estado_texto"><?php echo "Disponible para asignación automática de solicitudes de crédito" ?></span>
                        </div>
                    </div>
                </div>
            </center>
        </div>
    </div>
</div>
<div id="contenedor_modal_usuarios_disponibles"></div>

<div class="pl-5 pr-5">
    <div class="row">
        <div class="col-6">
            <table class="table table-hover">
                <tr>
                    <th class="text-center"></th>
                    <th class="text-center">Este mes</th>
                    <th class="text-center">Esta semana</th>
                    <th class="text-center">Hoy</th>
                <tr/>
                <?php foreach ($usuarios as $usuario) { ?>
                    <tr>
                        <td class="text-center"><?php echo $usuario->razon_social; ?></th>
                        <td class="text-center"><?php echo $usuario->solicitudes_asignadas_este_mes; ?></th>
                        <td class="text-center"><?php echo $usuario->solicitudes_asignadas_esta_semana; ?></th>
                        <td class="text-center"><?php echo $usuario->solicitudes_asignadas_hoy; ?></th>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <div class="col-6">
            <a class="btn btn-primary btn-block mb-3" href="#" onClick="javascript:listarUsuariosDisponibles()">Ver <span id="usuarios_disponibles"></span> usuarios disponibles</a>

            <!-- Botones para activarse para asignación automática de solicitudes -->
            <?php if(isset($permisos) && in_array(['clientes' => 'clientes_asignacion_automatica_solicitudes_credito'], $permisos)) { ?>
                <!-- Botón para marcar si está disponible -->
                <input type="hidden" id="usuario_disponible">
                
                <a class="btn btn-success btn-block mb-3" href="#" id="asignacion_automatica_disponible" onClick="javascript:cambiarEstadoDisponibilidad(true)">
                    <i class="fa fa-check"></i> Marcarse disponible para recibir solicitudes
                </a>

                <a class="btn btn-danger btn-block mb-3" href="#" id="asignacion_automatica_no_disponible" onClick="javascript:cambiarEstadoDisponibilidad(false)">
                    <i class="fa fa-times"></i> Marcarse NO disponible para recibir solicitudes
                </a>
            <?php } ?>
        </div>
    </div>
    
    <div id="contenedor_solicitudes_credito"></div>
    <div id="contenedor_asignar_usuario"></div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>    
    class GestorDisponibilidad {
        constructor() {
            this.intervalo = null
            this.iniciar()
        }

        iniciar() {
            // De entrada se inhabilita la opción para marcarse NO disponible
            $('#asignacion_automatica_no_disponible').hide()

            // Se obtiene la cantidad de usuarios disponibles
            this.obtenerUsuariosDisponibles()
            
            // Actualizar cada 2 minutos
            this.intervalo = setInterval(() => {
                // Si ya se puso como disponible, refresca el estado
                if($('#usuario_disponible').val() == 1) this.marcarDisponibilidad(true, true)
                
                // Se refrescan los usuarios disponibles
                this.obtenerUsuariosDisponibles()
            }, 120000) // Cada 120 segundos

            // Marcado como no disponible al cerrar la página
            window.addEventListener('beforeunload', () => {
                this.marcarDisponibilidad(false)
            })
        }
        
        marcarDisponibilidad(disponible, refrescar = false) {
            fetch(`${$('#site_url').val()}/configuracion/marcar_disponibilidad`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `disponible=${(disponible ? 1 : 0)}`
            })
            .then(response => response.json())
            .then(async resultado => {
                // Se obtienen los usuarios disponibles
                let usuariosDisponibles = await this.obtenerUsuariosDisponibles()

                // Dependiendo de la disponibilidad
                if(disponible) {
                    // Se marca el input del usuario disponible
                    $('#usuario_disponible').val(1)
                    
                    // Ocultar y mostrar botones de conectar/desconectar
                    $('#asignacion_automatica_disponible').hide();
                    $('#asignacion_automatica_no_disponible').show();

                    // Texto y color del indicador del estado
                    $("#indicador_estado").removeClass('status-badge--style--warning').addClass('status-badge--style--success')
                    $("#indicador_estado_texto").text('En línea')
                } else {
                    // Se marca el input del usuario no disponible
                    $('#usuario_disponible').val(0)

                    // Ocultar y mostrar botones de conectar/desconectar
                    $('#asignacion_automatica_no_disponible').hide()
                    $('#asignacion_automatica_disponible').show()

                    // Texto y color del indicador del estado
                    $("#indicador_estado").removeClass('status-badge--style--success').addClass('status-badge--style--warning')
                    $("#indicador_estado_texto").text('Disponible para asignación automática de solicitudes de crédito')
                }

                // Si no es para refrescar, muestra mensaje de éxito y log
                if(!refrescar) {
                    mostrarAviso('exito', 'Estado actualizado', 30000)
                    agregarLog(92, (disponible) ? 'Disponible' : 'No disponible')
                }
            })
        }

        obtenerUsuariosDisponibles() {
            return obtenerPromesa(`${$('#site_url').val()}/configuracion/obtener`, {tipo: 'usuarios_disponibles'})
            .then(resultado => {
                // Se actualiza el texto de los usuarios disponibles
                $('#usuarios_disponibles').text((resultado.length > 0) ? resultado.length : '')

                return resultado
            })
        }
    }

    // Se instancia el gestor de disponibilidad
    const gestor = new GestorDisponibilidad()

    // Cambio de la disponibilidad al selecionar uno de los botones
    cambiarEstadoDisponibilidad = estado => gestor.marcarDisponibilidad(estado)

    cargarAsignarUsuario = id => {
        cargarInterfaz('clientes/solicitud_credito/asignar_usuario', 'contenedor_asignar_usuario', {id: id})
    }

    listarSolicitudesCredito = () => {
        cargarInterfaz('clientes/solicitud_credito/lista', 'contenedor_solicitudes_credito')
    }

    listarUsuariosDisponibles = id => {
        cargarInterfaz('clientes/solicitud_credito/usuarios_disponibles', 'contenedor_modal_usuarios_disponibles')
    }

    $().ready(() => {
        listarSolicitudesCredito()
    })
</script>