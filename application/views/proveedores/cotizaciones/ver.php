<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Cotizaciones de precio</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div id="contenedor_cotizaciones"></div>
    </div>

    <div class="block-space block-space--layout--before-footer"></div>
</div>

<script>
    listarCotizaciones = () => {
        cargarInterfaz('proveedores/cotizaciones/lista', 'contenedor_cotizaciones', {
            cotizacion_id: <?php echo $cotizacion_id; ?>
        })
    }

    $().ready(() => {
        listarCotizaciones()
    })
</script>