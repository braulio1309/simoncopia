<?php
// Se obtienen todas las solicitudes de cotización disponibles
$solicitudes_disponibles = $this->proveedores_model->obtener('solicitudes_disponibles');

if(empty($solicitudes_disponibles)) {
    echo "
    <div class='container'>
        <div class='alert alert-danger alert-lg mb-3 alert-dismissible fade show'>
            En este momento no hay solicitudes de cotizaciones disponibles. Intenta de nuevo más tarde.
        </div>
    </div>
    ";
    exit;
}
?>

<div class="block">
    <div class="container">
        <div class="card flex-grow-1 mt-4">
            <div class="card-body card-body--padding--2">
                <div class="alert alert-info alert-lg mb-3 alert-dismissible fade show">
                    Estas son las solicitudes de cotización que tenemos disponibles para tí: Haz clic en el botón <b>Quiero enviar mi cotización</b>:
                </div>

                <?php
                foreach ($solicitudes_disponibles as $solicitud) {
                    // Se crea un token a partir del NIT, para validar que la URL no sea alterada deliberadamente
                    $token = substr(md5($solicitud->id.$datos['numero_documento']), 0, 10);

                    $mensaje_cierre = ($solicitud->horas_restantes > 0) ? "$solicitud->horas_restantes horas" : "menos de una hora" ;
                ?>
                    <div class="vehicles-list__body">
                        <div class="vehicles-list__item">
                            <div class="vehicles-list__item-info">
                                <div class="vehicles-list__item-name"><h3><?php echo "Solicitud de precio #$solicitud->id"; ?></h3></div>
                                <div class="vehicles-list__item-details"><h5><?php echo "Se cierra en $mensaje_cierre"; ?></h5></div>
                                <a class="btn btn-success btn-md" href="<?php echo site_url("proveedores/cotizacion/$solicitud->id/{$datos['numero_documento']}/$token") ?>">Quiero enviar mi cotización</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>