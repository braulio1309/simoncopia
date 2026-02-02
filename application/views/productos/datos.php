<?php
$productos = $this->productos_model->obtener('productos', $datos);

if(empty($productos)) { ?>
    <div class="container">
        <div class="not-found">
            <div class="not-found__content">
                <h3 class="not-found__title">Sin resultados para esta búsqueda</h3>
                <p class="not-found__text">
                    Puede haber un error en la palabra ingresada o el repuesto aún no está visible en el catálogo
                </p>
                <p class="not-found__text">
                    Manejamos <strong>más de 4.000 referencias activas</strong>, así que es probable que sí lo tengamos
                </p>
                <a class="btn btn-primary btn-xl mb-3" href="https://wa.me/573114914780">Contáctanos</a>
                <p class="not-found__text">
                    Te confirmaremos disponibilidad de inmediato
                </p>
            </div>
        </div>
    </div>
<?php } ?>

<?php foreach($productos as $producto) { ?>
    <div class="products-list__item">
        <div class="product-card">
            <?php
            // Detalle del producto
            $this->data['producto'] = $producto;
            $this->load->view('productos/item', $this->data)
            ?>
        </div>
    </div>
<?php } ?>

<script>
	$().ready(() => {
		let totalRegistros = parseInt("<?php echo count($productos); ?>")

		// Si no hay más datos o son menos del total configurado, se oculta el botón
		if(totalRegistros == 0 || totalRegistros < parseInt($('#cantidad_datos').val())) $("#btn_mostrar_mas").hide()
	})
</script>