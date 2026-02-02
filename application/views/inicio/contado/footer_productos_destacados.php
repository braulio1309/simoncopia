<div class="col-4">
    <div class="block-products-columns__title"><?php echo $titulo; ?></div>
    <div class="block-products-columns__list">
        <?php
        for ($i=$desde; $i < $hasta; $i++) {
            $posicion = array_keys($productos);
            $item = $productos[$posicion[$i]];
            $producto = $this->productos_model->obtener('productos', ['id' => $item->producto_id]);

            if(isset($producto)) {
            ?>
                <div class="block-products-columns__list-item">
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