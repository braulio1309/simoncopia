<?php
$recibo = $this->productos_model->obtener('recibo', ['token' => $this->input->get('referencia')]);
$recibo_detalle = $this->productos_model->obtener('recibos_detalle', ['rd.recibo_id' => $recibo->id]);
$wompi = json_decode($recibo->wompi_datos, true);

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
                    <h1 class="order-success__title"><?php echo $mensajes_estado_wompi['titulo']; ?></h1>
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
                <div class="card">
                    <div class="order-list">
                        <table>
                            <thead class="order-list__header">
                                <tr>
                                    <th class="order-list__column-label" colspan="2">Producto</th>
                                    <th class="order-list__column-quantity">Cantidad</th>
                                    <th class="order-list__column-total">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="order-list__products">
                                <?php
                                $subtotal = 0;
                                $descuento = 0;

                                foreach($recibo_detalle as $detalle) {
                                    $producto = $this->productos_model->obtener('productos', ['id' => $detalle->producto_id]);
                                    
                                    $subtotal += $detalle->precio * $detalle->cantidad;
                                    $descuento += $detalle->descuento;
                                    ?>
                                    <tr>
                                        <td class="order-list__column-image">
                                            <div class="image image--type--product">
                                                <a href="product-full.html" class="image__body">
                                                    <img class="image__tag" src="<?php echo url_fotos($producto->marca, $producto->referencia); ?>">
                                                </a>
                                            </div>
                                        </td>
                                        <td class="order-list__column-product">
                                            <a href="<?php echo site_url("productos/ver/$producto->slug"); ?>" target="_blank">
                                                <?php echo $producto->notas; ?>
                                            </a>
                                            <div class="order-list__options">
                                                <ul class="order-list__options-list">
                                                    <li class="order-list__options-item">
                                                        <span class="order-list__options-label">
                                                            Marca:
                                                        </span>
                                                        <span class="order-list__options-value">
                                                            <?php echo $producto->marca; ?>
                                                        </span>
                                                    </li>
                                                    <li class="order-list__options-item">
                                                        <span class="order-list__options-label">
                                                            Grupo:
                                                        </span>
                                                        <span class="order-list__options-value">
                                                            <?php echo $producto->grupo; ?>
                                                        </span>
                                                    </li>
                                                    <li class="order-list__options-item">
                                                        <span class="order-list__options-label">
                                                            Línea:
                                                        </span>
                                                        <span class="order-list__options-value">
                                                            <?php echo $producto->linea; ?>
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td class="order-list__column-quantity" data-title="Quantity:">
                                            <?php echo $detalle->cantidad; ?>
                                        </td>
                                        <td class="order-list__column-total">
                                            <?php echo formato_precio($detalle->precio); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tbody class="order-list__subtotals">
                                <tr>
                                    <th class="order-list__column-label" colspan="3">Subtotal</th>
                                    <td class="order-list__column-total"><?php echo formato_precio($subtotal); ?></td>
                                </tr>
                                <tr>
                                    <th class="order-list__column-label" colspan="3">Descuento</th>
                                    <td class="order-list__column-total"><?php echo formato_precio($descuento); ?></td>
                                </tr>
                            </tbody>
                            <tfoot class="order-list__footer">
                                <tr>
                                    <th class="order-list__column-label" colspan="3">Total</th>
                                    <td class="order-list__column-total"><?php echo formato_precio(($wompi['amount_in_cents'] / 100)); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- <div class="order-success__addresses">
                    <div class="order-success__address card address-card">
                        <div class="address-card__badge tag-badge tag-badge--theme">
                            Shipping Address
                        </div>
                        <div class="address-card__body">
                            <div class="address-card__name">Ryan Ford</div>
                            <div class="address-card__row">
                                Random Federation<br>
                                115302, Moscow<br>
                                ul. Varshavskaya, 15-2-178
                            </div>
                            <div class="address-card__row">
                                <div class="address-card__row-title">Phone Number</div>
                                <div class="address-card__row-content">38 972 588-42-36</div>
                            </div>
                            <div class="address-card__row">
                                <div class="address-card__row-title">Email Address</div>
                                <div class="address-card__row-content">stroyka@example.com</div>
                            </div>
                        </div>
                    </div>
                    <div class="order-success__address card address-card">
                        <div class="address-card__badge tag-badge tag-badge--theme">
                            Billing Address
                        </div>
                        <div class="address-card__body">
                            <div class="address-card__name">Ryan Ford</div>
                            <div class="address-card__row">
                                Random Federation<br>
                                115302, Moscow<br>
                                ul. Varshavskaya, 15-2-178
                            </div>
                            <div class="address-card__row">
                                <div class="address-card__row-title">Phone Number</div>
                                <div class="address-card__row-content">38 972 588-42-36</div>
                            </div>
                            <div class="address-card__row">
                                <div class="address-card__row-title">Email Address</div>
                                <div class="address-card__row-content">stroyka@example.com</div>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
    <div class="block-space block-space--layout--before-footer"></div>
</div>