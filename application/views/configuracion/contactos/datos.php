<?php
$opciones = [
	"contador" => $datos['contador'],
];
if($datos['busqueda']) $opciones['busqueda'] = $datos['busqueda'];

$registros = $this->configuracion_model->obtener('contactos', $opciones);

foreach ($registros as $contacto) { ?>
    <tr class="wishlist__row wishlist__row--body">
        <td class="wishlist__column wishlist__column--body wishlist__column--product">
            <div class="wishlist__product-name">
                <a href=""><?php echo $contacto->nombre; ?></a>
            </div>
            <div class="wishlist__product-rating">
                <div class="wishlist__product-rating-title">
                    <?php echo "$contacto->nit - $contacto->email"; ?>
                </div>
            </div>
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--price">
            <?php echo $contacto->numero; ?>
        </td>
        
        <td class="wishlist__column wishlist__column--body wishlist__column--product">
            <?php echo $contacto->modulo; ?>
        </td>
        
        <td class="wishlist__column wishlist__column--body wishlist__column--button">
            <a type="button" class="btn btn-sm btn-primary" href="<?php echo site_url("configuracion/contactos/id/$contacto->id"); ?>">Ver</a>
            <a type="button" class="btn btn-sm btn-danger" href="#" onClick="javascript:eliminarContacto(<?php echo $contacto->id; ?>)"><i class="fa fa-times"></i></a>
        </td>
    </tr>
<?php } ?>

<script>
	$().ready(() => {
		let totalRegistros = parseInt("<?php echo count($registros); ?>")

		// Si no hay más datos o son menos del total configurado, se oculta el botón
		if(totalRegistros == 0 || totalRegistros < parseInt($('#cantidad_datos').val())) $("#btn_mostrar_mas").hide()
	})
</script>