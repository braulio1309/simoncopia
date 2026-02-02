<div class="table-responsive" style="max-height: 270px; overflow-y: auto;">
    <table class="table-bordered" id="tabla_productos_carrito" width="100%">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
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
                            <a href="<?php echo site_url("productos/ver/$producto->id"); ?>" class="image__body" target="_blank">
                                <img class="image__tag" src="<?php echo url_fotos($producto->marca, $producto->referencia); ?>">
                            </a>
                        </div>
                    </td>
                    <td class="cart-table__column cart-table__column--product">
                        <a href="" class="cart-table__product-name"><?php echo $producto->notas; ?></a>
                    </td>
                    <td class="cart-table__column cart-table__column--price" data-title="Precio">
                        <!-- Si la lista de precios es F005 (Personalizada) -->
                        <?php if(isset($item['options']['lista_precio']) && $item['options']['lista_precio'] == '005') { ?>
                            <!-- Posibilidad de editar el precio -->
                            <input type="text" class="form-control" id="ventas_carrito_precio_<?php echo $item['id']; ?>" data-row_id="<?php echo $item['rowid']; ?>" data-id="<?php echo $producto->id; ?>" value="<?php echo $item['price']; ?>" placeholder="$0">
                        <?php } else { ?>
                            <!-- Precio sin edición -->
                            <?php echo formato_precio($item['price']); ?>
                        <?php } ?>
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
        </tbody>
    </table>
</div>

<script>
    $().ready(() => {        
        $(`input[id^='ventas_carrito_precio_']`).on('blur', function() {
            // Se formatea el campo
            $(this).val(formatearNumero($(this).val()))

            // Se obtiene el precio sin formato
            let precio = parseFloat($(this).val().replace(/\./g, ''))
            
            modificarItem('precio', $(this).attr('data-row_id'), $(this).attr('data-id'), precio)
        })

        new DataTable('#tabla_productos_carrito', {
            deferRender: true,
            fixedHeader: true,
            info: false,
            language: {
                decimal: ',',
                thousands: '.',
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            ordering: false,
            orderCellsTop: true,
            pageLength: 100,
            paging: false,
            processing: true,
            scrollCollapse: true,
            // scroller: true,
            scrollX: false,
            // scrollY: '300px',
            searching: false,
            stateSave: false,
        })
    })
</script>