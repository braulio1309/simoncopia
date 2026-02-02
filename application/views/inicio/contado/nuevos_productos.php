<div class="block block-products-carousel" data-layout="horizontal">
    <div class="container">
        <div class="section-header">
            <div class="section-header__body">
                <h2 class="section-header__title">Lo último:</h2>
                <div class="section-header__spring"></div>
                <div class="section-header__arrows">
                    <div class="arrow section-header__arrow section-header__arrow--prev arrow--prev">
                        <button class="arrow__button" type="button"><svg width="7" height="11">
                                <path d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z" />
                            </svg>
                        </button>
                    </div>
                    <div class="arrow section-header__arrow section-header__arrow--next arrow--next">
                        <button class="arrow__button" type="button"><svg width="7" height="11">
                                <path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9 C-0.1,9.8-0.1,10.4,0.3,10.7z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="section-header__divider"></div>
            </div>
        </div>
        <div class="block-products-carousel__carousel">
            <div class="block-products-carousel__carousel-loader"></div>
            <div class="owl-carousel">
                <?php
                // Se recorre el arreglo en las posiciones determinadas al inicio
                for ($i=$desde; $i < $hasta; $i++) {
                    // Se obtienen los index y el index de esa posición
                    $posicion = array_keys($productos);
                    $item = $productos[$posicion[$i]];
                    
                    $producto = $this->productos_model->obtener('productos', ['id' => $item->producto_id]);

                    if(isset($producto)) {
                    ?>
                        <div class="block-products-carousel__column">
                            <div class="block-products-carousel__cell">
                                <div class="product-card product-card--layout--horizontal">
                                    <div class="product-card__image">
                                        <div class="image image--type--product">
                                            <a href="<?php echo site_url("productos/ver/$producto->slug"); ?>" class="image__body">
                                                <img class="image__tag" src="<?php echo url_fotos($producto->marca, $producto->referencia); ?>">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="product-card__info">
                                        <div class="product-card__name">
                                            <div>
                                                <!-- <div class="product-card__badges">
                                                    <div class="tag-badge tag-badge--sale">sale</div>
                                                    <div class="tag-badge tag-badge--new">new</div>
                                                    <div class="tag-badge tag-badge--hot">hot</div>
                                                </div> -->
                                                <a href="<?php echo site_url("productos/ver/$producto->slug"); ?>"><?php echo $producto->notas; ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="product-card__footer">
                                        <div class="product-card__prices">
                                            <div class="product-card__price product-card__price--current">
                                                <?php echo formato_precio($producto->precio); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>