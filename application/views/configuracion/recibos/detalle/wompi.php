<?php
$recibo = $this->productos_model->obtener('recibo', ['token' => $datos['token']]);
$wompi = json_decode($recibo->wompi_datos, true);

// Dependiendo del estado de la transacción, trae los mensajes
$mensajes_estado_wompi = mostrar_mensajes_estados_wompi($wompi['status']);
?>

<div class="card">
    <div class="card-header">
        <h5>Detalles del pago</h5>
    </div>
    <div class="card-divider"></div>
    <div class="card-body card-body--padding--2">
        <div class="row no-gutters">
            <div class="col-12">
                <div class="card order-success__meta">
                    <ul class="order-success__meta-list">
                        <li class="order-success__meta-item">
                            <span class="order-success__meta-title">Identificador:</span>
                            <span class="order-success__meta-value"><?php echo $wompi['id']; ?></span>
                        </li>
                        <li class="order-success__meta-item">
                            <span class="order-success__meta-title">Fecha de creación:</span>
                            <span class="order-success__meta-value"><?php echo $wompi['created_at']; ?></span>
                        </li>
                        <li class="order-success__meta-item">
                            <span class="order-success__meta-title">Valor:</span>
                            <span class="order-success__meta-value"><?php echo formato_precio($wompi['amount_in_cents']/100); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-12">
                <div class="card order-success__meta">
                    <ul class="order-success__meta-list">
                        <li class="order-success__meta-item">
                            <span class="order-success__meta-title">Referencia:</span>
                            <span class="order-success__meta-value"><?php echo $wompi['reference']; ?></span>
                        </li>
                        <li class="order-success__meta-item">
                            <span class="order-success__meta-title">Forma de pago:</span>
                            <span class="order-success__meta-value"><?php echo $wompi['payment_method_type']; ?></span>
                        </li>
                        <li class="order-success__meta-item">
                            <span class="order-success__meta-title">Estado:</span>
                            <div class="status-badge status-badge--style--<?php echo ($recibo->wompi_status == 'APPROVED') ? 'success' : 'failure' ; ?> status-badge--has-text">
                                <div class="status-badge__body">
                                    <div class="status-badge__text"><?php echo $mensajes_estado_wompi['asunto']; ?></div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(`.facturas_wompi`).addClass('account-nav__item--active')
</script>