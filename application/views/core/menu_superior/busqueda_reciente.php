<?php 
for ($i=0; $i < count($datos['productos']); $i++) {
    $producto = $this->productos_model->obtener('productos', ['id' => $datos['productos'][$i]]);

    if(!empty($producto)) {
    ?>
        <a class="suggestions__item suggestions__product" href="<?php echo site_url("productos/ver/$producto->slug"); ?>">
            <div class="suggestions__product-image image image--type--product">
                <div class="image__body">
                    <img class="image__tag" src="<?php echo url_fotos($producto->marca, $producto->referencia); ?>">
                </div>
            </div>
            <div class="suggestions__product-info">
                <div class="suggestions__product-name"><?php echo $producto->notas; ?></div>
                <div class="suggestions__product-rating">
                    <div class="suggestions__product-rating-label"><?php echo $producto->marca; ?></div>
                </div>
            </div>
            <div class="suggestions__product-price"><?php echo formato_precio($producto->precio); ?></div>
        </a>
    <?php } ?>
<?php } ?>