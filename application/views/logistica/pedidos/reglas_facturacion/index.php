<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Reglas de facturación</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div class="row mb-4">
            <div class="col-3">
                <a class="btn btn-success" href="<?php echo site_url('logistica/pedidos/reglas_facturacion/crear'); ?>">Crear regla</a>
            </div>
        </div>

        <div id="contenedor_reglas_facturacion"></div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    listarReglasFacturacion = () => {
        let datos = {
            contador: 0,
            busqueda: ''
        }

        cargarInterfaz('logistica/pedidos/reglas_facturacion/lista', 'contenedor_reglas_facturacion', datos)
    }

    $().ready(() => {
        listarReglasFacturacion()
    })
</script>
