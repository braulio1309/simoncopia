<?php
$producto = $this->productos_model->obtener('productos', ['id' => $datos['id']]);
$item = buscar_item_carrito($producto->id);
?>

<?php if(!empty($item)) { ?>
    <div class="input-number">
        <input class="input-number__input form-control form-control-lg" type="number" min="1" max="<?php echo $producto->disponible; ?>" value="<?php echo $item['qty']; ?>" disabled>
        <div class="input-number__add" onClick="javascript:modificarItem('agregar', '<?php echo $item['rowid']; ?>', <?php echo $producto->id; ?>)"></div>
        <div class="input-number__sub" onClick="javascript:modificarItem('remover', '<?php echo $item['rowid']; ?>', <?php echo $producto->id; ?>)"></div>
    </div>
<?php } else { ?>
    <button
        class="product-card__addtocart-icon"
        type="button"
        aria-label="Agregar al carrito"
        onClick="javascript:agregarProducto({
            id: <?php echo $producto->id; ?>,
            precio: <?php echo $producto->precio; ?>,
            referencia: '<?php echo $producto->referencia; ?>',
            unidad_inventario: '<?php echo $producto->unidad_inventario; ?>',
        })">
        <svg width="20" height="20">
            <circle cx="7" cy="17" r="2" />
            <circle cx="15" cy="17" r="2" />
            <path d="M20,4.4V5l-1.8,6.3c-0.1,0.4-0.5,0.7-1,0.7H6.7c-0.4,0-0.8-0.3-1-0.7L3.3,3.9C3.1,3.3,2.6,3,2.1,3H0.4C0.2,3,0,2.8,0,2.6 V1.4C0,1.2,0.2,1,0.4,1h2.5c1,0,1.8,0.6,2.1,1.6L5.1,3l2.3,6.8c0,0.1,0.2,0.2,0.3,0.2h8.6c0.1,0,0.3-0.1,0.3-0.2l1.3-4.4 C17.9,5.2,17.7,5,17.5,5H9.4C9.2,5,9,4.8,9,4.6V3.4C9,3.2,9.2,3,9.4,3h9.2C19.4,3,20,3.6,20,4.4z" />
        </svg>
    </button>

    <button
        class="product-card__addtocart-full"
        type="button"
        onClick="javascript:agregarProducto({
            id: <?php echo $producto->id; ?>,
            precio: <?php echo $producto->precio; ?>,
            referencia: '<?php echo $producto->referencia; ?>',
            unidad_inventario: '<?php echo $producto->unidad_inventario; ?>',
        })">
        Agregar al carrito
    </button>
<?php } ?>