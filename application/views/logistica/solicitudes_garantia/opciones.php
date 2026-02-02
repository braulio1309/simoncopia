<?php $solicitud = (object)$datos['solicitud_garantia']; ?>

<!-- Contenedor donde se cargará el modal si es aprobada o rechazada la solicitud -->
<div id="contenedor_modal_gestion_solicitud_garantia"></div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="form-row">
                    <!-- Si la solicitud está pendiente -->
                    <?php if($solicitud->estado_id == 1) { ?>
                        <div class="form-group col-lg-6 col-sm-12">
                            <button class="btn btn-danger btn-block" onClick="javascript:rechazarSolicitudGarantia(<?php echo $solicitud->id; ?>)"><i class="fas fa-times"></i> Rechazar solicitud</button>
                        </div>

                        <div class="form-group col-lg-6 col-sm-12">
                            <button class="btn btn-success btn-block" onClick="javascript:aprobarSolicitudGarantia(<?php echo $solicitud->id; ?>)"><i class="fas fa-check"></i> Aprobar solicitud</button>
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