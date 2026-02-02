<?php
$recibo = $this->productos_model->obtener('recibo', ['token' => $this->input->get('referencia')]);
$recibo_detalle = $this->productos_model->obtener('recibo_detalle', ['rd.recibo_id' => $recibo->id]);
$wompi = json_decode($recibo->wompi_datos, true);
if(!$wompi) redirect(site_url());

// Dependiendo del estado de la transacción, trae los mensajes
$mensajes_estado_wompi = mostrar_mensajes_estados_wompi($wompi['status']);
?>

<div class="site__body">
    <div class="block-space block-space--layout--spaceship-ledge-height"></div>
    <div class="block order-success">
        <div class="container">
            <div class="order-success__body">
                <div class="order-success__header">
                    <?php if($mensajes_estado_wompi['pedido_completo']) { ?>
                        <div class="order-success__icon">
                            <svg width="100" height="100">
                                <path d="M50,100C22.4,100,0,77.6,0,50S22.4,0,50,0s50,22.4,50,50S77.6,100,50,100z M50,2C23.5,2,2,23.5,2,50 s21.5,48,48,48s48-21.5,48-48S76.5,2,50,2z M44.2,71L22.3,49.1l1.4-1.4l21.2,21.2l34.4-34.4l1.4,1.4L45.6,71 C45.2,71.4,44.6,71.4,44.2,71z" />
                            </svg>
                        </div>
                    <?php } ?>
                    <h1 class="order-success__title"><?php echo $mensajes_estado_wompi['asunto_factura']; ?></h1>
                    <div class="order-success__subtitle"><?php echo $mensajes_estado_wompi['subtitulo']; ?></div>
                    <div class="order-success__actions">
                        <a href="<?php echo site_url('inicio'); ?>" class="btn btn-sm btn-secondary">Volver al inicio</a>
                    </div>
                </div>
                <div class="card order-success__meta">
                    <ul class="order-success__meta-list">
                        <li class="order-success__meta-item">
                            <span class="order-success__meta-title">Referencia:</span>
                            <span class="order-success__meta-value"><?php echo $wompi['reference']; ?></span>
                        </li>
                        <li class="order-success__meta-item">
                            <span class="order-success__meta-title">Creado en:</span>
                            <span class="order-success__meta-value"><?php echo $wompi['created_at']; ?></span>
                        </li>
                        <li class="order-success__meta-item">
                            <span class="order-success__meta-title">Total:</span>
                            <span class="order-success__meta-value"><?php echo formato_precio($wompi['amount_in_cents']/100); ?></span>
                        </li>
                        <li class="order-success__meta-item">
                            <span class="order-success__meta-title">Forma de pago:</span>
                            <span class="order-success__meta-value"><?php echo $wompi['payment_method_type']; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="block-space block-space--layout--before-footer"></div>
</div>