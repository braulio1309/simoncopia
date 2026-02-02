<?php $recibos = $this->configuracion_model->obtener('recibos', ['documento_numero' => $datos['nit'], 'id_tipo_recibo' => 1]); ?>

<?php if(empty($recibos)) { ?>
    <div class="alert alert-secondary mb-3 mb-md-5">Todavía no tienes pedidos realizados desde la página.</div>
<?php } ?>

<?php if(!empty($recibos)) { ?>
    <div style="max-height: 450px; overflow-y: auto;">
        <table class="wishlist__table">
            <tbody class="wishlist__body">
                <?php foreach ($recibos as $recibo) { ?>
                    <tr class="wishlist__row wishlist__row--body">
                        <td class="wishlist__column wishlist__column--body wishlist__column--product">
                            <div class="wishlist__product-name">
                                <a href=""><?php echo "#$recibo->id"; ?></a>
                            </div>
                            <div class="wishlist__product-rating">
                                <div class="wishlist__product-rating-title"><?php echo $recibo->token; ?></div>
                            </div>
                        </td>
                        <td class="wishlist__column wishlist__column--body wishlist__column--price">
                            <?php echo formato_precio($recibo->valor); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>