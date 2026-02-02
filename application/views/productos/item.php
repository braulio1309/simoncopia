<div class="product-card__image">
    <div class="image image--type--product">
        <a href="<?php echo site_url("productos/ver/$producto->slug"); ?>" class="image__body">
            <img class="image__tag" src="<?php echo url_fotos($producto->marca, $producto->referencia); ?>">
        </a>
    </div>
    <div class="status-badge status-badge--style--<?php echo ($producto->disponible > 0) ? "success" : "failure"; ?> product-card__fit status-badge--has-icon status-badge--has-text">
        <div class="status-badge__body">
            <div class="status-badge__icon">
                <svg width="13" height="13">
                    <?php if ($producto->disponible > 0) { ?>
                        <path d="M12,4.4L5.5,11L1,6.5l1.4-1.4l3.1,3.1L10.6,3L12,4.4z" />
                    <?php } else { ?>
                        <path d="M6.5,0C2.9,0,0,2.9,0,6.5S2.9,13,6.5,13S13,10.1,13,6.5S10.1,0,6.5,0z M6.5,2c0.9,0,1.7,0.3,2.4,0.7L2.7,8.9 C2.3,8.2,2,7.4,2,6.5C2,4,4,2,6.5,2z M6.5,11c-0.9,0-1.7-0.3-2.4-0.7l6.2-6.2C10.7,4.8,11,5.6,11,6.5C11,9,9,11,6.5,11z" />
                    <?php } ?>
                </svg>
            </div>
            
            <div class="status-badge__text"><?php echo ($producto->disponible > 0) ? "$producto->disponible unidades disponibles" : "Agotado"; ?></div>
            <div class="status-badge__tooltip" tabindex="0" data-toggle="tooltip" title="<?php echo $producto->descripcion_corta; ?>"></div>
        </div>
    </div>
</div>

<div class="product-card__info">
    <div class="product-card__meta"><span class="product-card__meta-title">Referencia:</span> <?php echo $producto->referencia; ?></div>
    <div class="product-card__name">
        <div>
            <div class="product-card__badges">
                <?php if($producto->disponible > 0 & $producto->disponible <= 3) echo "<div class='tag-badge tag-badge--sale'>Últimas unidades</div>"; ?>
                <?php // if($producto->disponible > 0 & $producto->bodega == '0008') echo "<div class='tag-badge tag-badge--hot'>Outlet</div>"; ?>
                
                <!-- <div class="tag-badge tag-badge--new">new</div>
                <div class="tag-badge tag-badge--hot">hot</div> -->
            </div>
            <a href="<?php echo site_url("productos/ver/$producto->slug"); ?>"><?php echo substr($producto->notas, 0, 75); ?></a>
        </div>
    </div>
    <div class="product-card__features">
        <ul>
            <li>Marca: <?php echo $producto->marca; ?></li>
            <li>Grupo: <?php echo $producto->grupo; ?></li>
            <li>Línea: <?php echo $producto->linea; ?></li>
            <li><?php echo $producto->notas; ?></li>
        </ul>
    </div>
</div>
<div class="product-card__footer">
    <?php if ($producto->disponible > 0) { ?>
        <div class="product-card__prices">
            <div class="product-card__price product-card__price--current">
                <?php echo formato_precio($producto->precio); ?>
            </div>
        </div>

        <div id="<?php echo "principal_{$producto->id}"; ?>"></div>

        <script>
            // Se cargan los botones para agregar y adicionar el ítem al carrito
            cargarBotones('principal', <?php echo $producto->id; ?>);
        </script>
    <?php } ?>
</div>