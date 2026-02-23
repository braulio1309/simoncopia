<?php
$parametros = $this->input->get();
if(isset($parametros['pais'])) echo "<input type='hidden' id='filtro_pais' value='{$parametros['pais']}'>";
if(isset($parametros['moneda'])) echo "<input type='hidden' id='filtro_moneda' value='{$parametros['moneda']}'>";
if(isset($parametros['proveedor'])) echo "<input type='hidden' id='filtro_proveedor' value='{$parametros['razon_social']}'>";
if(isset($parametros['busqueda'])) echo "<input type='hidden' id='filtro_busqueda' value='{$parametros['busqueda']}'>";
?>

<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <nav class="breadcrumb block-header__breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb__list">
                    <li class="breadcrumb__item breadcrumb__item--parent breadcrumb__item--first">
                        <a href="<?php echo site_url('inicio'); ?>" class="breadcrumb__item-link">Inicio</a>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--parent">
                        <a href="<?php echo site_url('importaciones'); ?>" class="breadcrumb__item-link">Importaciones</a>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--current breadcrumb__item--last">
                        <span class="breadcrumb__item-link">Listado</span>
                    </li>
                </ol>
            </nav>
            <h1 class="block-header__title">Gestión de Importaciones</h1>
        </div>
    </div>
</div>

<div class="container mb-4">
    <div class="row">
        <div class="col-12 col-md-9 mb-2 mb-md-0">
            <div class="input-group">
                <input type="text" class="form-control form-control-lg" id="filtro_busqueda_importacion" placeholder="Buscar por Orden #, Proveedor, BL o País...">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="button" onclick="listarImportaciones()">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <a href="<?php echo site_url('importaciones/crear'); ?>" class="btn btn-success btn-lg btn-block">
                <i class="fas fa-plus-circle"></i> Nueva Importación
            </a>
        </div>
    </div>
</div>
<div class="block-split">
    <div class="container">
        <div class="block-split__row row no-gutters">
            <div class="block-split__item block-split__item-content col-auto" style="width: 100%;">
                <div class="products-view">
                    <div class="products-view__options view-options">
                        <div class="view-options__body">
                            <div class="view-options__legend">Mostrando importaciones</div>
                            <div class="view-options__spring"></div>
                            <div class="view-options__select">
                                <label for="view-option-sort">Ordenar por:</label>
                                <select id="view-option-sort" class="form-control form-control-sm">
                                    <option value="fecha_estimada_llegada">Fecha Llegada</option>
                                    <option value="valor_total">Valor Total</option>
                                    <option value="numero_orden_compra">Nro Orden</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="contenedor_importaciones"></div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    function listarImportaciones() {
        // 1. Capturamos el input de forma segura
        var inputBuscar = document.getElementById("filtro_busqueda_importacion");
        var valorBusqueda = "";

        if (inputBuscar) {
            valorBusqueda = inputBuscar.value;
        }

        // 2. Guardamos en localStorage inmediatamente
        localStorage.simonBolivar_buscarImportacion = valorBusqueda;
        localStorage.simonBolivar_contador_importaciones = 0;

        // 3. Construimos el objeto de datos
        var datos = {
            contador: 0,
            busqueda: valorBusqueda, // Usamos la variable local
            cantidad: $('#cantidad_datos').val() || 18
        };

        // 4. Filtros ocultos (si existen)
        if ($('#filtro_pais').val()) datos.pais_origen = $('#filtro_pais').val();
        if ($('#filtro_moneda').val()) datos.moneda_preferida = $('#filtro_moneda').val();
        if ($('#filtro_proveedor').val()) datos.razon_social = $('#filtro_proveedor').val();
        
        var orden = $('#view-option-sort').val();
        if (orden) datos.ordenar_por = orden;

        // 5. Feedback visual y carga
        $('#contenedor_importaciones').html('<div class="text-center p-5"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Buscando...</p></div>');
        
        cargarInterfaz('importaciones/lista', 'contenedor_importaciones', datos);
    }

    $().ready(function() {
        // Al cargar la página, recuperamos del localStorage si existe
        if (localStorage.simonBolivar_buscarImportacion) {
            $('#buscar').val(localStorage.simonBolivar_buscarImportacion);
        }

        // Ejecución inicial
        listarImportaciones();
        
        // Listener para la tecla ENTER
        $('#buscar').on('keyup', function(e) {
            if (e.keyCode === 13) {
                listarImportaciones();
            }
        });

        // Listener para el selector de orden
        $('#view-option-sort').on('change', function() {
            listarImportaciones();
        });
    });
</script>