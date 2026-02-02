<?php
$productos = $this->clientes_model->obtener('clientes_facturas_detalle', [
    'f350_consec_docto' => $datos['documento_cruce'],
    'f200_nit_fact' => $datos['numero_documento'],
    'f461_id_sucursal_fact' => str_pad($datos['id_sucursal'], 3, '0', STR_PAD_LEFT),
]);
?>

<div class="modal fade" id="modal_productos" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo "Detalle de los productos en la factura {$datos['documento_cruce']}"; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="block">
                    <div class="container container--max--xl">
                        <div class="wishlist">
                            <table class="wishlist__table">
                                <thead class="wishlist__head">
                                    <tr class="wishlist__row wishlist__row--head">
                                        <th class="wishlist__column wishlist__column--head wishlist__column--image">Foto</th>
                                        <th class="wishlist__column wishlist__column--head wishlist__column--product">Producto</th>
                                        <th class="wishlist__column wishlist__column--head wishlist__column--stock">Precio</th>
                                        <th class="wishlist__column wishlist__column--head wishlist__column--price">Cantidad</th>
                                        <th class="wishlist__column wishlist__column--head wishlist__column--button">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="wishlist__body">
                                    <?php
                                    $total = 0;
                                    $subtotal = 0;
                                    $impuestos = 0;
                                    $retenciones = 0;

                                    foreach ($productos as $detalle) {
                                        $producto = $this->productos_model->obtener('productos', ['id' => $detalle->f120_id]);

                                        $subtotal += $detalle->f470_vlr_bruto;
                                        $impuestos += $detalle->f470_vlr_imp;
                                        $retenciones += $detalle->f461_vlr_ret;
                                        $total += $detalle->f470_vlr_neto;
                                        ?>
                                        <tr class="wishlist__row wishlist__row--body">
                                            <td class="wishlist__column wishlist__column--body wishlist__column--image">
                                                <div class="image image--type--product">
                                                    <a href="<?php echo site_url("productos/ver/$producto->slug"); ?>" class="image__body" target="_blank">
                                                        <img class="image__tag" src="<?php echo url_fotos($producto->marca, $producto->referencia); ?>" alt="Foto del producto">
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="wishlist__column wishlist__column--body wishlist__column--product">
                                                <div class="wishlist__product-name">
                                                    <a href="<?php echo site_url("productos/ver/$producto->slug"); ?>">
                                                        <?php echo $detalle->f120_descripcion; ?>
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="wishlist__column wishlist__column--body wishlist__column--stock">
                                                <?php echo formato_precio($detalle->f470_precio_uni); ?>
                                            </td>
                                            <td class="wishlist__column wishlist__column--body wishlist__column--price">
                                                <?php echo $detalle->f470_cant_base; ?>
                                            </td>
                                            <td class="wishlist__column wishlist__column--body wishlist__column--price">
                                                <?php echo formato_precio($detalle->f470_vlr_neto); ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <table class="cart__totals-table">
                                        <thead>
                                            <tr>
                                                <th>Subtotal</th>
                                                <td><?php echo formato_precio($subtotal); ?></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>Impuestos</th>
                                                <td><?php echo formato_precio($impuestos); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Retenciones</th>
                                                <td><?php echo formato_precio($retenciones); ?></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Total</th>
                                                <td><?php echo formato_precio($total); ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
     $().ready(function() {
        $('#modal_productos').modal({
            backdrop: 'static',
            keyboard: true
        })
    })
</script>