<?php
$recibo = $this->productos_model->obtener('recibo', ['id' => $id]);
$recibo_detalle = $this->productos_model->obtener('recibos_detalle', ['rd.recibo_id' => $recibo->id]);
$wompi = json_decode($recibo->wompi_datos, true);

// Colores corporativos
$azul_corporativo_primario = '#19287F';
$amarillo_corporativo_primario = '#FFD400';
$rojo_corporativo_secundario = '#FF1B1C';
$azul_claro_corporativo_secundario = '#8D9EBC';
$amarillo_corporativo_apoyo = '#F8A815';
$azul_corporativo_apoyo = '#1F2B50';
?>

<!-- Detalle del cliente -->
<table class="row row-3" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
    <tbody>
        <tr>
            <td>
                <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000; width: 680px; margin: 0 auto;" width="680">
                    <tbody>
                        <tr>
                            <td class="column column-1" width="50%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                <table class="paragraph_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                    <tr>
                                        <td class="pad" style="padding-bottom:5px;padding-left:30px;padding-right:30px;padding-top:10px;">
                                            <div style="color:<?php echo $azul_corporativo_primario; ?>;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:16px;line-height:180%;text-align:left;mso-line-height-alt:28.8px;">
                                                <p style="margin: 0; word-break: break-word;"><strong><span>Aquí está el detalle del pedido:</span></strong></p>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <table class="paragraph_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                    <tr>
                                        <td class="pad" style="padding-bottom:10px;padding-left:30px;padding-right:30px;padding-top:10px;">
                                            <div style="color:<?php echo $azul_corporativo_primario; ?>;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:16px;line-height:180%;text-align:left;mso-line-height-alt:28.8px;">
                                                <p style="margin: 0; word-break: break-word;"><span><strong><span><?php echo $recibo->razon_social; ?></span></strong></span></p>
                                                <p style="margin: 0; word-break: break-word;"><span><span><?php echo $recibo->documento_numero; ?></span></span></p>
                                                <p style="margin: 0; word-break: break-word;"><span><span><?php echo $recibo->direccion; ?></span></span></p>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>

                            <?php if($wompi) { ?>
                                <td class="column column-2" width="50%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                    <table class="paragraph_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad" style="padding-bottom:5px;padding-left:30px;padding-right:30px;padding-top:10px;">
                                                <div style="color:<?php echo $azul_corporativo_primario; ?>;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:16px;line-height:180%;text-align:left;mso-line-height-alt:28.8px;">
                                                    <p style="margin: 0; word-break: break-word;"><strong><span>Información del Pago:</span></strong></p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="paragraph_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad" style="padding-left:30px;padding-right:30px;padding-top:10px;">
                                                <div style="color:<?php echo $amarillo_corporativo_apoyo; ?>;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:26px;line-height:180%;text-align:left;mso-line-height-alt:46.800000000000004px;">
                                                    <p style="margin: 0; word-break: break-word;"><span><strong><?php echo formato_precio(($wompi['amount_in_cents'] / 100)); ?></strong></span></p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="paragraph_block block-3" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad" style="padding-bottom:10px;padding-left:30px;padding-right:30px;padding-top:10px;">
                                                <div style="color:<?php echo $azul_corporativo_primario; ?>;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:16px;line-height:180%;text-align:left;mso-line-height-alt:28.8px;">
                                                    <p style="margin: 0; word-break: break-word;"><span><?php echo $wompi['payment_method_type']; ?></span></p>
                                                    <p style="margin: 0; word-break: break-word;"><span><?php if(isset($wompi['payment_method']['extra']['last_four'])) echo "**** **** **** {$wompi['payment_method']['extra']['last_four']}"; ?></span></p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            <?php } ?>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>

<!-- Divisor -->
<table class="row row-4" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
    <tbody>
        <tr>
            <td>
                <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000; width: 680px; margin: 0 auto;" width="680">
                    <tbody>
                        <tr>
                            <td class="column column-1" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                <div class="spacer_block block-1" style="height:20px;line-height:20px;font-size:1px;">&#8202;</div>
                                <table class="divider_block block-2" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                    <tr>
                                        <td class="pad">
                                            <div class="alignment" align="center">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                                    <tr>
                                                        <td class="divider_inner" style="font-size: 1px; line-height: 1px; border-top: 1px solid #BBBBBB;"><span>&#8202;</span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <div class="spacer_block block-3" style="height:20px;line-height:20px;font-size:1px;">&#8202;</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>

<!-- Detalle de la orden -->
<table class="row row-5" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
    <tbody>
        <tr>
            <td>
                <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000; width: 680px; margin: 0 auto;" width="680">
                    <tbody>
                        <tr>
                            <td class="column column-1" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                <table class="paragraph_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                    <tr>
                                        <td class="pad" style="padding-bottom:5px;padding-left:30px;padding-right:30px;padding-top:10px;">
                                            <div style="color:<?php echo $azul_corporativo_primario; ?>;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:16px;line-height:180%;text-align:left;mso-line-height-alt:28.8px;">
                                                <p style="margin: 0; word-break: break-word;"><strong><span>Lo que incluye tu orden</span></strong></p>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>

<?php
$subtotal = 0;
$descuento = 0;

foreach($recibo_detalle as $detalle) {
    $producto = $this->productos_model->obtener('productos', ['id' => $detalle->producto_id]);
    $subtotal += $detalle->precio * $detalle->cantidad;
    $descuento += $detalle->descuento;

    // Si trae un producto
    if($detalle->producto_id) {
    ?>
        <!-- Uno -->
        <table class="row row-6" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
            <tbody>
                <tr>
                    <td>
                        <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff; color: #000; width: 680px; margin: 0 auto;" width="680">
                            <tbody>
                                <tr>
                                    <td class="column column-1" width="25%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-left: 10px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                        <table class="image_block block-1" width="100%" border="0" cellpadding="20" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                            <tr>
                                                <td class="pad">
                                                    <div class="alignment" align="center" style="line-height:10px">
                                                        <a href='<?php echo site_url("productos/ver/$producto->slug"); ?>' target="_blank" style="outline:none" tabindex="-1">
                                                            <img class="fullWidth" src="<?php echo url_fotos($producto->marca, $producto->referencia); ?>" style="display: block; height: auto; border: 0; max-width: 120px; width: 100%;" width="120" alt="<?php echo $producto->referencia; ?>" title="Fatty Burger">
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="column column-2" width="50%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                        <div class="spacer_block block-1" style="height:25px;line-height:25px;font-size:1px;">&#8202;</div>
                                        <table class="paragraph_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                            <tr>
                                                <td class="pad" style="padding-bottom:10px;padding-left:30px;padding-right:10px;padding-top:10px;">
                                                    <div style="color:#232323;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:17px;line-height:120%;text-align:left;mso-line-height-alt:20.4px;">
                                                        <p style="margin: 0; word-break: break-word;"><span><?php echo "$producto->notas x $detalle->cantidad"; ?></span></p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <table class="paragraph_block block-3" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                            <tr>
                                                <td class="pad" style="padding-left:30px;padding-right:10px;padding-top:10px;">
                                                    <div style="color:#848484;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:14px;line-height:150%;text-align:left;mso-line-height-alt:21px;">
                                                        <p style="margin: 0; word-break: break-word;">
                                                            <span><?php echo $producto->marca; ?> |</span>
                                                            <span><?php echo $producto->grupo; ?> |</span>
                                                            <span><?php echo $producto->linea; ?> |</span>
                                                            <span><?php echo $producto->referencia; ?> |</span>
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="column column-3" width="25%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                        <div class="spacer_block block-1 mobile_hide" style="height:30px;line-height:30px;font-size:1px;">&#8202;</div>
                                        <table class="paragraph_block block-2" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                            <tr>
                                                <td class="pad" style="padding-bottom:10px;padding-left:30px;padding-right:10px;padding-top:10px;">
                                                    <div style="color:#555555;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:14px;line-height:120%;text-align:left;mso-line-height-alt:16.8px;">
                                                        <p style="margin: 0; word-break: break-word;"><?php echo formato_precio($detalle->precio); ?></p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php } ?>
<?php } ?>

<!-- Línea -->
<table class="row row-9" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
    <tbody>
        <tr>
            <td>
                <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000; width: 680px; margin: 0 auto;" width="680">
                    <tbody>
                        <tr>
                            <td class="column column-1" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                <div class="spacer_block block-1" style="height:20px;line-height:20px;font-size:1px;">&#8202;</div>
                                <table class="divider_block block-2" width="100%" border="0" cellpadding="10" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                    <tr>
                                        <td class="pad">
                                            <div class="alignment" align="center">
                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                                    <tr>
                                                        <td class="divider_inner" style="font-size: 1px; line-height: 1px; border-top: 1px solid #BBBBBB;"><span>&#8202;</span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <div class="spacer_block block-3" style="height:20px;line-height:20px;font-size:1px;">&#8202;</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>

<?php if($wompi) { ?>
    <!-- Subtotal -->
    <table class="row row-10" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tbody>
            <tr>
                <td>
                    <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff; color: #000; width: 680px; margin: 0 auto;" width="680">
                        <tbody>
                            <tr>
                                <td class="column column-1" width="25%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-left: 10px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                    <div class="spacer_block block-1 mobile_hide" style="height:25px;line-height:25px;font-size:1px;">&#8202;</div>
                                </td>
                                <td class="column column-2" width="50%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                    <table class="paragraph_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad" style="padding-left:35px;padding-right:10px;padding-top:10px;">
                                                <div style="color:#232323;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:14px;line-height:120%;text-align:left;mso-line-height-alt:16.8px;">
                                                    <p style="margin: 0; word-break: break-word;"><span>Subtotal:</span></p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="column column-3" width="25%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                    <table class="paragraph_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad" style="padding-left:30px;padding-right:10px;padding-top:10px;">
                                                <div style="color:#555555;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:14px;line-height:120%;text-align:left;mso-line-height-alt:16.8px;">
                                                    <p style="margin: 0; word-break: break-word;"><?php echo formato_precio($subtotal); ?></p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Descuento -->
    <table class="row row-11" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tbody>
            <tr>
                <td>
                    <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff; color: #000; width: 680px; margin: 0 auto;" width="680">
                        <tbody>
                            <tr>
                                <td class="column column-1" width="25%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-left: 10px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                    <div class="spacer_block block-1 mobile_hide" style="height:25px;line-height:25px;font-size:1px;">&#8202;</div>
                                </td>
                                <td class="column column-2" width="50%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                    <table class="paragraph_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad" style="padding-left:35px;padding-right:10px;padding-top:10px;">
                                                <div style="color:#232323;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:14px;line-height:120%;text-align:left;mso-line-height-alt:16.8px;">
                                                    <p style="margin: 0; word-break: break-word;"><span>Descuento:</span></p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="column column-3" width="25%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                    <table class="paragraph_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad" style="padding-left:30px;padding-right:10px;padding-top:10px;">
                                                <div style="color:#555555;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:14px;line-height:120%;text-align:left;mso-line-height-alt:16.8px;">
                                                    <p style="margin: 0; word-break: break-word;"><?php echo formato_precio($descuento); ?></p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Impuestos -->
    <!-- <table class="row row-12" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tbody>
            <tr>
                <td>
                    <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff; color: #000; width: 680px; margin: 0 auto;" width="680">
                        <tbody>
                            <tr>
                                <td class="column column-1" width="25%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-left: 10px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                    <div class="spacer_block block-1 mobile_hide" style="height:25px;line-height:25px;font-size:1px;">&#8202;</div>
                                </td>
                                <td class="column column-2" width="50%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                    <table class="paragraph_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad" style="padding-left:35px;padding-right:10px;padding-top:10px;">
                                                <div style="color:#232323;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:14px;line-height:120%;text-align:left;mso-line-height-alt:16.8px;">
                                                    <p style="margin: 0; word-break: break-word;"><span>Impuestos:</span></p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="column column-3" width="25%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                    <table class="paragraph_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad" style="padding-left:30px;padding-right:10px;padding-top:10px;">
                                                <div style="color:#555555;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:14px;line-height:120%;text-align:left;mso-line-height-alt:16.8px;">
                                                    <p style="margin: 0; word-break: break-word;">---</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table> -->

    <!-- Total -->
    <table class="row row-13" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tbody>
            <tr>
                <td>
                    <table class="row-content stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #fff; color: #000; width: 680px; margin: 0 auto;" width="680">
                        <tbody>
                            <tr>
                                <td class="column column-1" width="25%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-left: 10px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                    <div class="spacer_block block-1 mobile_hide" style="height:25px;line-height:25px;font-size:1px;">&#8202;</div>
                                </td>
                                <td class="column column-2" width="50%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                    <table class="paragraph_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad" style="padding-left:35px;padding-right:10px;padding-top:10px;">
                                                <div style="color:#232323;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:18px;line-height:120%;text-align:left;mso-line-height-alt:21.599999999999998px;">
                                                    <p style="margin: 0; word-break: break-word;"><span>Total</span></p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="column column-3" width="25%" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 5px; padding-top: 5px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;">
                                    <table class="paragraph_block block-1" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;">
                                        <tr>
                                            <td class="pad" style="padding-left:30px;padding-right:10px;padding-top:10px;">
                                                <div style="color:<?php echo $amarillo_corporativo_apoyo; ?>;font-family:'Roboto', Tahoma, Verdana, Segoe, sans-serif;font-size:18px;line-height:120%;text-align:left;mso-line-height-alt:21.599999999999998px;">
                                                    <p style="margin: 0; word-break: break-word;"><span><?php echo formato_precio(($wompi['amount_in_cents'] / 100)); ?></span></p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
<?php } ?>