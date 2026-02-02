<?php foreach ($this->cart->contents() as $item) {
    $producto = $this->productos_model->obtener('productos', [
        'id' => $item['id'],
        'omitir_bodega' => true,
        'omitir_lista_precio' => true,
    ]);
?>
    <tr class="cart-table__row">
        <td class="cart-table__column cart-table__column--image">
            <div class="image image--type--product">
                <a href="<?php echo site_url("productos/ver/$producto->id") ?>" class="image__body">
                    <img class="image__tag" src="<?php echo url_fotos($producto->marca, $producto->referencia); ?>">
                </a>
            </div>
        </td>
        <td class="cart-table__column cart-table__column--product">
            <a href="" class="cart-table__product-name"><?php echo $producto->notas; ?></a>
            <ul class="cart-table__options">
                <li>Marca: <?php echo $producto->marca; ?></li>
                <li>Grupo: <?php echo $producto->grupo; ?></li>
                <li>Línea: <?php echo $producto->linea; ?></li>
            </ul>
        </td>
        <td class="cart-table__column cart-table__column--price" data-title="Precio">
            <?php echo formato_precio($item['price']); ?>
        </td>
        <td class="cart-table__column cart-table__column--quantity" data-title="Cantidad">
            <div class="cart-table__quantity input-number">
                <input class="form-control input-number__input" type="number" min="1" value="<?php echo $item['qty']; ?>" disabled>
                <div class="input-number__add" onClick="javascript:modificarItem('agregar', '<?php echo $item['rowid']; ?>')"></div>
                <div class="input-number__sub" onClick="javascript:modificarItem('remover', '<?php echo $item['rowid']; ?>')"></div>
            </div>
        </td>
        <td class="cart-table__column cart-table__column--total" data-title="Subtotal"><?php echo formato_precio($item['subtotal']); ?></td>
        <td class="cart-table__column cart-table__column--remove">
            <button type="button" class="cart-table__remove btn btn-sm btn-icon btn-muted" onClick="javascript:eliminarProducto('<?php echo $item['rowid']; ?>')">
                <svg width="12" height="12">
                    <path d="M10.8,10.8L10.8,10.8c-0.4,0.4-1,0.4-1.4,0L6,7.4l-3.4,3.4c-0.4,0.4-1,0.4-1.4,0l0,0c-0.4-0.4-0.4-1,0-1.4L4.6,6L1.2,2.6 c-0.4-0.4-0.4-1,0-1.4l0,0c0.4-0.4,1-0.4,1.4,0L6,4.6l3.4-3.4c0.4-0.4,1-0.4,1.4,0l0,0c0.4,0.4,0.4,1,0,1.4L7.4,6l3.4,3.4 C11.2,9.8,11.2,10.4,10.8,10.8z" />
                </svg>
            </button>
        </td>
    </tr>
<?php } ?>