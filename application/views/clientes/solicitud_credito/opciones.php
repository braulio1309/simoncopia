<?php $solicitud = $this->clientes_model->obtener("clientes_solicitudes_credito", ["id" => $datos['id']]); ?>

<!-- Contenedor donde se cargará el modal si es aprobada o rechazada la solicitud -->
<div id="contenedor_modal_gestion_solicitud_Credito"></div>
<div id="contenedor_reasignar_usuario"></div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="form-row">
                    <!-- Si la solicitud todavía está en trámite y está sin fecha de envío a firma -->
                    <?php if($solicitud->solicitud_credito_estado_id == 1 && !$solicitud->fecha_envio_firma) { ?>
                        <div class="form-group col-lg-6 col-sm-12">
                            <button class="btn btn-primary btn-block" onClick="javascript:enviarSolicitudAFirma(<?php echo $solicitud->id; ?>)"><i class="fas fa-signature"></i> Enviar para firma</button>
                        </div>
                    <?php } else { ?>
                        <div class="form-group mb-2 alert alert-primary col-lg-12" role="alert">
                            <?php echo "<b>Fecha de envío para firma:</b> $solicitud->fecha_envio_firma"; ?>
                        </div>
                    <?php } ?>

                    <!-- Si la solicitd está pendiente -->
                    <?php if($solicitud->solicitud_credito_estado_id == 1) { ?>
                        <!-- Si la solicitud ya tiene usuario asignado -->
                        <?php if($solicitud->usuario_asignado_id) { ?>
                            <div class="form-group col-lg-6 col-sm-12">
                                <button class="btn btn-info btn-block" onClick="javascript:cargarInterfaz('clientes/solicitud_credito/asignar_usuario', 'contenedor_reasignar_usuario', {id: <?php echo $solicitud->id; ?>, reasignar: true})"><i class="fas fa-user"></i> Reasignar solicitud</button>
                            </div>
                        <?php } ?>
                        <br>

                        <div class="form-group col-lg-6 col-sm-12">
                            <button class="btn btn-danger btn-block" onClick="javascript:rechazarSolicitudCredito(<?php echo $solicitud->id; ?>)"><i class="fas fa-times"></i> Rechazar solicitud</button>
                        </div>

                        <div class="form-group col-lg-6 col-sm-12">
                            <button class="btn btn-success btn-block" onClick="javascript:aprobarSolicitudCredito(<?php echo $solicitud->id; ?>)"><i class="fas fa-check"></i> Aprobar solicitud</button>
                        </div>
                    <?php } else { ?>
                        <div class="form-group mb-2 alert alert-info col-lg-12" role="alert">
                            <?php echo "<b>Estado de la solicitud:</b> $solicitud->estado ($solicitud->motivo_rechazo)"; ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>