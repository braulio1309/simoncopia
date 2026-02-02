<div class="block-header block-header--has-breadcrumb block-header--has-title">
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
                        <span class="breadcrumb__item-link">Carrito de compras</span>
                    </li>
                    <li class="breadcrumb__title-safe-area" role="presentation"></li>
                </ol>
            </nav>
            <h1 class="block-header__title">Carrito de Compras</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container">
        <div class="cart">
            <div class="cart__table cart-table">
                <table class="cart-table__table">
                    <thead class="cart-table__head">
                        <tr class="cart-table__row">
                            <th class="cart-table__column cart-table__column--image">Foto</th>
                            <th class="cart-table__column cart-table__column--product">Producto</th>
                            <th class="cart-table__column cart-table__column--price">Precio</th>
                            <th class="cart-table__column cart-table__column--quantity">Cantidad</th>
                            <th class="cart-table__column cart-table__column--total">Subtotal</th>
                            <th class="cart-table__column cart-table__column--remove"></th>
                        </tr>
                    </thead>
                    <tbody class="cart-table__body" id="contenedor_carrito_compras">
                        <?php $this->load->view("carrito/datos"); ?>
                    </tbody>
                    <tfoot class="cart-table__foot">
                        <tr>
                            <td colspan="6">
                                <div class="cart-table__actions">
                                    <div class="cart-table__update-button">
                                        <a class="btn btn-sm btn-primary" href="#" onClick="javascript:vaciarCarrito()">Vaciar carrito</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="cart__totals" id="contenedor_carrito_compras_totales">
                <div class="card">
                    <?php $this->load->view("carrito/datos"); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    $().ready(() => {
        listarCarrito()
    })
</script>