<?php
$opciones = [
	"contador" => $datos['contador'],
];

$registros = $this->configuracion_model->obtener('perfiles', $opciones);

if(count($registros) == 0) echo '<li class="list-group-item">No se encontraron registros.</li>';

foreach ($registros as $perfil) { ?>
    <tr class="wishlist__row wishlist__row--body">
        <td class="wishlist__column wishlist__column--body wishlist__column--product">
            <div class="wishlist__product-name">
                <a href=""><?php echo $perfil->nombre; ?></a>
            </div>
            <div class="wishlist__product-rating">
                <div class="wishlist__product-rating-title"><?php echo $perfil->descripcion; ?></div>
            </div>
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--stock">
            <div class="status-badge status-badge--style--<?php echo ($perfil->activo == 1) ? 'success' : 'failure' ; ?> status-badge--has-text">
                <div class="status-badge__body">
                    <div class="status-badge__text"><?php echo $perfil->estado_nombre; ?></div>
                </div>
            </div>
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--price">
            <?php echo $perfil->administrador_nombre; ?>
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--button">
            <a type="button" class="btn btn-sm btn-primary" href="<?php echo site_url("configuracion/perfiles/id/$perfil->token"); ?>">Editar</a>
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