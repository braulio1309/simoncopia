<div class="block">
    <div class="container container--max--xl">
        <div class="wishlist">
            <table class="wishlist__table">
                <thead class="wishlist__head">
                    <tr class="wishlist__row wishlist__row--head">
                        <th class="wishlist__column wishlist__column--head wishlist__column--image">Foto</th>
                        <th class="wishlist__column wishlist__column--head wishlist__column--product">
                            Producto
                        </th>
                        <th class="wishlist__column wishlist__column--head wishlist__column--price">Cantidad</th>
                        <th class="wishlist__column wishlist__column--head wishlist__column--price">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="wishlist__body">
                    <?php
                    foreach($recibo_detalle as $detalle) {
                        $item = $this->productos_model->obtener('productos', ['id' => $detalle->producto_id]);
                        ?>
                        <tr class="wishlist__row wishlist__row--body">
                            <?php if($recibo->recibo_tipo_id == 1) { ?>
                                <td class="wishlist__column wishlist__column--body wishlist__column--image">
                                    <div class="image image--type--product">
                                        <a href="<?php echo site_url("productos/ver/$item->slug"); ?>" class="image__body" target="_blank">
                                            <img class="image__tag" src="<?php echo url_fotos($item->marca, $item->referencia); ?>">
                                        </a>
                                    </div>
                                </td>
                                <td class="wishlist__column wishlist__column--body wishlist__column--product">
                                    <div class="wishlist__product-name">
                                        <a href="<?php echo site_url("productos/ver/$item->slug"); ?>" target="_blank"><?php echo $item->notas; ?></a>
                                    </div>
                                </td>
                            <?php } else { ?>
                                <td class="wishlist__column wishlist__column--body wishlist__column--image"><?php echo "$item->Tipo_Doc_cruce-$item->Nro_Doc_cruce"; ?></td>
                                <td class="wishlist__column wishlist__column--body wishlist__column--product"></td>
                            <?php } ?>

                            <td class="wishlist__column wishlist__column--body wishlist__column--price">
                                <?php echo $detalle->cantidad; ?>
                            </td>
                            <td class="wishlist__column wishlist__column--body wishlist__column--price">
                                <?php echo formato_precio($detalle->subtotal); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    $(`.facturas_items`).addClass('account-nav__item--active')
</script>