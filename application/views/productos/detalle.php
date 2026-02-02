<?php
$datos = ['id' => $id];
$producto = $this->productos_model->obtener('productos', $datos);
if(empty($producto)) redirect(site_url(''));
?>

<div class="block-header block-header--has-breadcrumb">
    <div class="container">
        <div class="block-header__body">
            <nav class="breadcrumb block-header__breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb__list">
                    <li class="breadcrumb__spaceship-safe-area" role="presentation"></li>
                    <li class="breadcrumb__item breadcrumb__item--parent breadcrumb__item--first">
                        <a href="<?php echo site_url('inicio'); ?>" class="breadcrumb__item-link">Inicio</a>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--parent">
                        <a href="<?php echo site_url('productos'); ?>" class="breadcrumb__item-link">Productos</a>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--current breadcrumb__item--last" aria-current="page">
                        <span class="breadcrumb__item-link"><?php echo $producto->referencia; ?></span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="block-split">
    <div class="container">
        <div class="block-split__row row no-gutters">
            <div class="block-split__item block-split__item-content col-auto">
                <div class="product product--layout--full">
                    <div class="product__body">
                        <div class="product__card product__card--one"></div>
                        <div class="product__card product__card--two"></div>
                        <div class="product-gallery product-gallery--layout--product-full product__gallery" data-layout="product-full">
                            <div class="product-gallery__featured">
                                <button type="button" class="product-gallery__zoom">
                                    <svg width="24" height="24">
                                        <path d="M15,18c-2,0-3.8-0.6-5.2-1.7c-1,1.3-2.1,2.8-3.5,4.6c-2.2,2.8-3.4,1.9-3.4,1.9s-0.6-0.3-1.1-0.7 c-0.4-0.4-0.7-1-0.7-1s-0.9-1.2,1.9-3.3c1.8-1.4,3.3-2.5,4.6-3.5C6.6,12.8,6,11,6,9c0-5,4-9,9-9s9,4,9,9S20,18,15,18z M15,2 c-3.9,0-7,3.1-7,7s3.1,7,7,7s7-3.1,7-7S18.9,2,15,2z M16,13h-2v-3h-3V8h3V5h2v3h3v2h-3V13z" />
                                    </svg>
                                </button>
                                <div class="owl-carousel">
                                    <!--
                                    The data-width and data-height attributes must contain the size of a larger version
                                    of the product image.

                                    If you do not know the image size, you can remove the data-width and data-height
                                    attribute, in which case the width and height will be obtained from the naturalWidth
                                    and naturalHeight property of img.image__tag.
                                    -->
                                    <a class="image image--type--product" href="<?php echo url_fotos($producto->marca, $producto->referencia); ?>" target="_blank" data-width="700" data-height="700">
                                        <div class="image__body">
                                            <img class="image__tag" src="<?php echo url_fotos($producto->marca, $producto->referencia); ?>">
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="product-gallery__thumbnails">
                                <div class="owl-carousel">
                                    <div class="product-gallery__thumbnails-item image image--type--product">
                                        <div class="image__body">
                                            <img class="image__tag" src="<?php echo url_fotos($producto->marca, $producto->referencia); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="product__header">
                            <h1 class="product__title"><?php echo $producto->notas; ?></h1>
                            <div class="product__subtitle">
                                <div class="status-badge status-badge--style--<?php echo ($producto->disponible > 0) ? "success" : "failure"; ?> product__fit status-badge--has-icon status-badge--has-text">
                                    <div class="status-badge__body">
                                        <div class="status-badge__icon"><svg width="13" height="13">
                                                <path d="M12,4.4L5.5,11L1,6.5l1.4-1.4l3.1,3.1L10.6,3L12,4.4z" />
                                            </svg>
                                        </div>
                                        <div class="status-badge__text"><?php echo ($producto->disponible > 0) ? "$producto->disponible unidades disponibles" : "Agotado"; ?></div>
                                        <div class="status-badge__tooltip" tabindex="0" data-toggle="tooltip" title="Part&#x20;Fit&#x20;for&#x20;2011&#x20;Ford&#x20;Focus&#x20;S"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="product__main">
                            <div class="product__excerpt">
                                <?php echo "$producto->descripcion_corta - $producto->notas"; ?>
                            </div>
                            <div class="product__features">
                                <div class="product__features-title">Características:</div>
                                <ul>
                                    <li>Referencia: <span><?php echo $producto->referencia; ?></span></li>
                                    <li>Marca: <span><?php echo $producto->marca; ?></span></li>
                                    <li>Grupo: <span><?php echo $producto->grupo; ?></span></li>
                                    <li>Línea: <span><?php echo $producto->linea; ?></span></li>
                                </ul>
                                <!-- <div class="product__features-link">
                                    <a href="">See Full Specification</a>
                                </div> -->
                            </div>
                        </div>
                        <div class="product__info">
                            <div class="product__info-card">
                                <div class="product__info-body">
                                    <?php if($producto->disponible > 0 & $producto->disponible <= 3) echo "<div class='tag-badge tag-badge--sale'>Últimas unidades</div>"; ?>
                                    <?php // if($producto->disponible > 0 & $producto->bodega == '0008') echo "<div class='tag-badge tag-badge--hot'>Outlet</div>"; ?>
                                    
                                    <div class="product__prices-stock">
                                        <?php if ($producto->disponible > 0) { ?>
                                            <div class="product__prices">
                                                <div class="product__price product__price--current">
                                                    <?php echo formato_precio($producto->precio); ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        
                                        <div class="status-badge status-badge--style--<?php echo ($producto->disponible > 0) ? "success" : "failure"; ?> product__stock status-badge--has-text">
                                            <div class="status-badge__body">
                                                <div class="status-badge__text"><?php echo ($producto->disponible > 0) ? "Disponible" : "Agotado"; ?></div>
                                                <div class="status-badge__tooltip" tabindex="0" data-toggle="tooltip"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="product__meta">
                                        <table>
                                            <tr>
                                                <th>Marca</th>
                                                <td><?php echo $producto->marca; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Grupo</th>
                                                <td><a href="#"><?php echo $producto->grupo; ?></a></td>
                                            </tr>
                                            <tr>
                                                <th>Línea</th>
                                                <td><?php echo $producto->linea; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                
                                <?php if ($producto->disponible > 0) { ?>
                                    <div id="producto_detalle_<?php echo $producto->id; ?>"></div>
                                    
                                    <div class="product__actions">
                                        <a class="btn btn-primary btn-lg btn-block" href="<?php echo site_url("carrito/finalizar"); ?>">
                                            Finalizar pedido
                                        </a>
                                        
                                        <ul class="shop-features__list">
                                            <li class="shop-features__item">
                                                <div class="shop-features__item-icon">
                                                    <img src="<?php echo base_url(); ?>images/icons/envios_gratis.svg">
                                                </div>
                                                <div class="shop-features__info">
                                                    <div class="shop-features__item-title">Envíos gratis</div>
                                                    <div class="shop-features__item-subtitle">Tus órdenes gratis<br> en todo el Valle de Aburrá</div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Etiquetas del producto -->
                                    <?php if($producto->palabras_clave) { ?>
                                        <div class="product__tags-and-share-links">
                                            <div class="product__tags tags tags--sm">
                                                <div class="tags__list">
                                                    <?php
                                                    $etiquetas = explode(',', $producto->palabras_clave);
                                                    
                                                    // Las palabras clave se muestran como etiquetas
                                                    foreach ($etiquetas as $etiqueta) {
                                                        $url = site_url("productos?busqueda=$etiqueta");
                                                        if($etiqueta != '') echo "<a href='{$url}'>$etiqueta</a>";
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    
                                    <script>
                                        // Se cargan los botones para agregar y adicionar el ítem al carrito
                                        cargarBotones('producto_detalle', <?php echo $producto->id; ?>);
                                    </script>
                                <?php } ?>
                            </div>
                            <div class="product__shop-features shop-features">
                                <ul class="shop-features__list">
                                    <li class="shop-features__item">
                                        <div class="shop-features__item-icon">
                                            <img src="<?php echo base_url(); ?>images/icons/envios_lugares.svg">
                                        </div>
                                        <div class="shop-features__info">
                                            <div class="shop-features__item-title">Envíos a Colombia y Venezuela</div>
                                            <div class="shop-features__item-subtitle">Tus pedidos se envían sin problemas entre ciudades y países</div>
                                        </div>
                                    </li>
                                    <li class="shop-features__divider" role="presentation"></li>
                                    <li class="shop-features__item">
                                        <div class="shop-features__item-icon">
                                            <img src="<?php echo base_url(); ?>images/icons/pago_seguro.svg">
                                        </div>
                                        <div class="shop-features__info">
                                            <div class="shop-features__item-title">Pago seguro</div>
                                            <div class="shop-features__item-subtitle">Tus pagos están seguros con nuestra red de seguridad</div>
                                        </div>
                                    </li>
                                    <li class="shop-features__divider" role="presentation"></li>
                                    <li class="shop-features__item">
                                        <div class="shop-features__item-icon">
                                            <img src="<?php echo base_url(); ?>images/icons/garantia.svg">
                                        </div>
                                        <div class="shop-features__info">
                                            <div class="shop-features__item-title">Garantía</div>
                                            <div class="shop-features__item-subtitle">Cada marca tiene un plazo de garantía diferente. Pregúntale a tu asesor por la garantía de tu producto</div>
                                        </div>
                                    </li>
                                    <li class="shop-features__divider" role="presentation"></li>
                                </ul>
                            </div>
                        </div>
                        <div class="product__tabs product-tabs product-tabs--layout--full">
                            <ul class="product-tabs__list">
                                <li class="product-tabs__item product-tabs__item--active"><a href="#product-tab-specification">Especificaciones</a></li>
                                <!-- <li class="product-tabs__item"><a href="#product-tab-description">Descripción</a></li> -->
                            </ul>
                            <div class="product-tabs__content">
                                <div class="product-tabs__pane product-tabs__pane--active" id="product-tab-specification">
                                    <div class="spec">
                                        <div class="spec__section">
                                            <h4 class="spec__section-title">Detalles técnicos</h4>
                                            <div class="spec__row">
                                                <div class="spec__value"><?php echo ($producto->detalles_tecnicos) ? nl2br($producto->detalles_tecnicos) : "Sin información"; ?></div>
                                            </div>
                                        </div>

                                        <div class="spec__section">
                                            <h4 class="spec__section-title">General</h4>
                                            <div class="spec__row">
                                                <div class="spec__value"><?php echo nl2br($producto->descripcion); ?></div>
                                            </div>
                                        </div>
                                        
                                        <div class="spec__disclaimer">
                                            * La información aquí contenida puede variar al momento de la compra
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="product-tabs__pane" id="product-tab-description">
                                    <div class="typography">
                                        <p>
                                            <?php // echo "$producto->descripcion_corta - $producto->notas"; ?>
                                        </p>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="block-space block-space--layout--divider-nl"></div>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    $().ready(() => {
        // localStorage.removeItem('simonBolivar_productosRecientes')
        var productosRecientes = []
        
        // Si ya existen productos recientes
        if(localStorage.simonBolivar_productosRecientes) {
            // Se almacenan en localStorage
            productosRecientes = JSON.parse(localStorage.getItem('simonBolivar_productosRecientes'))
        }

        // Se almacena en Local Storage los productos recientes
        localStorage.setItem('simonBolivar_productosRecientes', JSON.stringify(productosRecientes))

        var items = JSON.parse(localStorage.getItem('simonBolivar_productosRecientes'))
        
        // Si no existe el ítem dentro del arreglo
        if (items.indexOf(<?php echo $id; ?>) === -1) {
            // Elimina el primer ítem de la lista
            if(items.length == 5) items.shift()

            // Se agrega el ítem al arreglo
            items.push(<?php echo $id; ?>)
        }
        
        localStorage.setItem('simonBolivar_productosRecientes', JSON.stringify(items))

        cargarInterfaz('core/menu_superior/busqueda_reciente', 'contenedor_busqueda_reciente', {
            productos: JSON.parse(localStorage.simonBolivar_productosRecientes)
        })
    })
</script>