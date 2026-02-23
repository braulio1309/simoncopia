<?php
$opciones = [
	"contador" => $datos['contador'],
];
if($datos['busqueda']) $opciones['busqueda'] = $datos['busqueda'];

$registros = $this->configuracion_model->obtener('usuarios', $opciones);

if(count($registros) == 0) echo '<li class="list-group-item">No se encontraron registros.</li>';

foreach ($registros as $usuario) { ?>
    <tr class="wishlist__row wishlist__row--body">
        <td class="wishlist__column wishlist__column--body wishlist__column--product">
            <div class="wishlist__product-name">
                <a href=""><?php echo $usuario->razon_social; ?></a>
            </div>
            <div class="wishlist__product-rating">
                <div class="wishlist__product-rating-title"><?php echo $usuario->documento_numero; ?></div>
                <div class="wishlist__product-rating-title"><?php if ($usuario->perfil_id == 7) echo "Lista $usuario->lista_precio"; ?></div>
            </div>
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--stock">
            <div class="status-badge status-badge--style--<?php echo ($usuario->estado == 1) ? 'success' : 'failure' ; ?> status-badge--has-text">
                <div class="status-badge__body">
                    <div class="status-badge__text"><?php echo $usuario->estado_nombre; ?></div>
                </div>
            </div>
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--product">
        <?php echo $usuario->perfil; ?>
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--button">
            <a type="button" class="btn btn-sm btn-primary" href="<?php echo site_url("configuracion/usuarios/id/$usuario->token"); ?>">Ver</a>
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