<div class="block block-sale">
    <div class="block-sale__content">
        <div class="block-sale__header">
            <img src="<?php echo base_url(); ?>images/banners/promociones.png" alt="Promociones" width="100%" class="p-2">
            <!-- <div class="block-sale__timer">
                <div class="timer">
                    <div class="timer__part">
                        <div class="timer__part-value timer__part-value--days">02</div>
                        <div class="timer__part-label">Days</div>
                    </div>
                    <div class="timer__dots"></div>
                    <div class="timer__part">
                        <div class="timer__part-value timer__part-value--hours">23</div>
                        <div class="timer__part-label">Hrs</div>
                    </div>
                    <div class="timer__dots"></div>
                    <div class="timer__part">
                        <div class="timer__part-value timer__part-value--minutes">07</div>
                        <div class="timer__part-label">Mins</div>
                    </div>
                    <div class="timer__dots"></div>
                    <div class="timer__part">
                        <div class="timer__part-value timer__part-value--seconds">54</div>
                        <div class="timer__part-label">Secs</div>
                    </div>
                </div>
            </div> -->
            <div class="block-sale__controls">
                <div class="arrow block-sale__arrow block-sale__arrow--prev arrow--prev">
                    <button class="arrow__button" type="button"><svg width="7" height="11">
                            <path d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z" />
                        </svg>
                    </button>
                </div>
                <div class="block-sale__link"><a href="<?php echo site_url('productos?busqueda=promocion'); ?>">Ver todas las ofertas</a></div>
                <div class="arrow block-sale__arrow block-sale__arrow--next arrow--next">
                    <button class="arrow__button" type="button"><svg width="7" height="11">
                            <path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9 C-0.1,9.8-0.1,10.4,0.3,10.7z" />
                        </svg>
                    </button>
                </div>
                <div class="decor block-sale__header-decor decor--type--center">
                    <div class="decor__body">
                        <div class="decor__start"></div>
                        <div class="decor__end"></div>
                        <div class="decor__center"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="block-sale__body">
            <div class="decor block-sale__body-decor decor--type--bottom">
                <div class="decor__body">
                    <div class="decor__start"></div>
                    <div class="decor__end"></div>
                    <div class="decor__center"></div>
                </div>
            </div>
            <div class="block-sale__image" style="background-image: url('<?php echo base_url(); ?>images/banners/outlet.jpg');"></div>
            <div class="container">
                <div class="block-sale__carousel">
                    <div class="owl-carousel">
                        <?php
                        foreach($productos as $item) {
                            $producto = $this->productos_model->obtener('productos', ['id' => $item->producto_id]);

                            if(isset($producto)) {
                            ?>
                                <div class="block-sale__item">
                                    <div class="product-card">
                                        <?php
                                        // Detalle del producto
                                        $this->data['producto'] = $producto;
                                        $this->load->view('productos/item', $this->data)
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>