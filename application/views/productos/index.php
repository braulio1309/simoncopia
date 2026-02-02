<?php
$parametros = $this->input->get();
if(isset($parametros['marca'])) echo "<input type='hidden' id='filtro_marca' value='{$parametros['marca']}'>";
if(isset($parametros['grupo'])) echo "<input type='hidden' id='filtro_grupo' value='{$parametros['grupo']}'>";
if(isset($parametros['linea'])) echo "<input type='hidden' id='filtro_linea' value='{$parametros['linea']}'>";
if(isset($parametros['busqueda'])) echo "<input type='hidden' id='filtro_busqueda' value='{$parametros['busqueda']}'>";
?>

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
                        <span class="breadcrumb__item-link">Página actual</span>
                    </li>
                    <li class="breadcrumb__title-safe-area" role="presentation"></li>
                </ol>
            </nav>
            <h1 class="block-header__title">Productos</h1>
        </div>
    </div>
</div>

<div class="block-split">
    <div class="container">
        <div class="block-split__row row no-gutters">
            <div class="block-split__item block-split__item-content col-auto" style="width: 100%;">
                <div class="block">
                    <div class="products-view">
                        <div class="products-view__options view-options view-options--offcanvas--mobile">
                            <div class="view-options__body">
                                <button type="button" class="view-options__filters-button filters-button">
                                    <span class="filters-button__icon">
                                        <svg width="16" height="16">
                                            <path d="M7,14v-2h9v2H7z M14,7h2v2h-2V7z M12.5,6C12.8,6,13,6.2,13,6.5v3c0,0.3-0.2,0.5-0.5,0.5h-2 C10.2,10,10,9.8,10,9.5v-3C10,6.2,10.2,6,10.5,6H12.5z M7,2h9v2H7V2z M5.5,5h-2C3.2,5,3,4.8,3,4.5v-3C3,1.2,3.2,1,3.5,1h2 C5.8,1,6,1.2,6,1.5v3C6,4.8,5.8,5,5.5,5z M0,2h2v2H0V2z M9,9H0V7h9V9z M2,14H0v-2h2V14z M3.5,11h2C5.8,11,6,11.2,6,11.5v3 C6,14.8,5.8,15,5.5,15h-2C3.2,15,3,14.8,3,14.5v-3C3,11.2,3.2,11,3.5,11z" />
                                        </svg>
                                    </span>
                                    <span class="filters-button__title">Filtros</span>
                                    <span class="filters-button__counter">3</span>
                                </button>
                                <div class="view-options__layout layout-switcher">
                                    <div class="layout-switcher__list">
                                        <button type="button" class="layout-switcher__button layout-switcher__button--active" data-layout="grid" data-with-features="false">
                                            <svg width="16" height="16">
                                                <path d="M15.2,16H9.8C9.4,16,9,15.6,9,15.2V9.8C9,9.4,9.4,9,9.8,9h5.4C15.6,9,16,9.4,16,9.8v5.4C16,15.6,15.6,16,15.2,16z M15.2,7 H9.8C9.4,7,9,6.6,9,6.2V0.8C9,0.4,9.4,0,9.8,0h5.4C15.6,0,16,0.4,16,0.8v5.4C16,6.6,15.6,7,15.2,7z M6.2,16H0.8 C0.4,16,0,15.6,0,15.2V9.8C0,9.4,0.4,9,0.8,9h5.4C6.6,9,7,9.4,7,9.8v5.4C7,15.6,6.6,16,6.2,16z M6.2,7H0.8C0.4,7,0,6.6,0,6.2V0.8 C0,0.4,0.4,0,0.8,0h5.4C6.6,0,7,0.4,7,0.8v5.4C7,6.6,6.6,7,6.2,7z" />
                                            </svg>
                                        </button>
                                        <button type="button" class="layout-switcher__button" data-layout="grid" data-with-features="true">
                                            <svg width="16" height="16">
                                                <path d="M16,0.8v14.4c0,0.4-0.4,0.8-0.8,0.8H9.8C9.4,16,9,15.6,9,15.2V0.8C9,0.4,9.4,0,9.8,0l5.4,0C15.6,0,16,0.4,16,0.8z M7,0.8 v14.4C7,15.6,6.6,16,6.2,16H0.8C0.4,16,0,15.6,0,15.2L0,0.8C0,0.4,0.4,0,0.8,0l5.4,0C6.6,0,7,0.4,7,0.8z" />
                                            </svg>
                                        </button>
                                        <button type="button" class="layout-switcher__button" data-layout="list" data-with-features="false">
                                            <svg width="16" height="16">
                                                <path d="M15.2,16H0.8C0.4,16,0,15.6,0,15.2V9.8C0,9.4,0.4,9,0.8,9h14.4C15.6,9,16,9.4,16,9.8v5.4C16,15.6,15.6,16,15.2,16z M15.2,7 H0.8C0.4,7,0,6.6,0,6.2V0.8C0,0.4,0.4,0,0.8,0h14.4C15.6,0,16,0.4,16,0.8v5.4C16,6.6,15.6,7,15.2,7z" />
                                            </svg>
                                        </button>
                                        <button type="button" class="layout-switcher__button" data-layout="table" data-with-features="false">
                                            <svg width="16" height="16">
                                                <path d="M15.2,16H0.8C0.4,16,0,15.6,0,15.2v-2.4C0,12.4,0.4,12,0.8,12h14.4c0.4,0,0.8,0.4,0.8,0.8v2.4C16,15.6,15.6,16,15.2,16z M15.2,10H0.8C0.4,10,0,9.6,0,9.2V6.8C0,6.4,0.4,6,0.8,6h14.4C15.6,6,16,6.4,16,6.8v2.4C16,9.6,15.6,10,15.2,10z M15.2,4H0.8 C0.4,4,0,3.6,0,3.2V0.8C0,0.4,0.4,0,0.8,0h14.4C15.6,0,16,0.4,16,0.8v2.4C16,3.6,15.6,4,15.2,4z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="view-options__legend">
                                    Mostrando productos
                                </div>
                                <div class="view-options__spring"></div>
                                
                                <?php if(ENVIRONMENT == 'development') { ?>
                                    <div class="view-options__select">
                                        <label for="view-option-sort">Ordenar por:</label>
                                        <select id="view-option-sort" class="form-control form-control-sm" name="">
                                            <option value="">Precio</option>
                                        </select>
                                    </div>
                                    <div class="view-options__select">
                                        <label for="view-option-limit">Mostrar:</label>
                                        <select id="view-option-limit" class="form-control form-control-sm" name="">
                                            <option value="">18</option>
                                            <option value="">36</option>
                                        </select>
                                    </div>
                                <?php } ?>
                            </div>
                            <!-- <div class="view-options__body view-options__body--filters">
                                <div class="view-options__label">Filtros activos</div>
                                <div class="applied-filters">
                                    <ul class="applied-filters__list">
                                        <li class="applied-filters__item">
                                            <a href="" class="applied-filters__button applied-filters__button--filter">
                                                Sales: Top Sellers
                                                <svg width="9" height="9">
                                                    <path d="M9,8.5L8.5,9l-4-4l-4,4L0,8.5l4-4l-4-4L0.5,0l4,4l4-4L9,0.5l-4,4L9,8.5z" />
                                                </svg>
                                            </a>
                                        </li>
                                        <li class="applied-filters__item">
                                            <a href="" class="applied-filters__button applied-filters__button--filter">
                                                Color: True Red
                                                <svg width="9" height="9">
                                                    <path d="M9,8.5L8.5,9l-4-4l-4,4L0,8.5l4-4l-4-4L0.5,0l4,4l4-4L9,0.5l-4,4L9,8.5z" />
                                                </svg>
                                            </a>
                                        </li>
                                        <li class="applied-filters__item">
                                            <button type="button" class="applied-filters__button applied-filters__button--clear">Clear All</button>
                                        </li>
                                    </ul>
                                </div>
                            </div> -->
                        </div>

                        <!-- Lista de productos -->
                        <div id="contenedor_productos"></div>

                        <button type="button" class="btn btn-primary btn-lg btn-block mt-3" onClick="javascript:cargarMasDatos('productos')" id="btn_mostrar_mas">Mostrar más resultados</button>
                        
                        <!-- <div class="products-view__pagination">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination">
                                    <li class="page-item disabled">
                                        <a class="page-link page-link--with-arrow" href="" aria-label="Previous">
                                            <span class="page-link__arrow page-link__arrow--left" aria-hidden="true"><svg width="7" height="11">
                                                    <path d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z" />
                                                </svg>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">
                                            2
                                            <span class="sr-only">(current)</span>
                                        </span>
                                    </li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item"><a class="page-link" href="#">4</a></li>
                                    <li class="page-item page-item--dots">
                                        <div class="pagination__dots"></div>
                                    </li>
                                    <li class="page-item"><a class="page-link" href="#">9</a></li>
                                    <li class="page-item">
                                        <a class="page-link page-link--with-arrow" href="" aria-label="Next">
                                            <span class="page-link__arrow page-link__arrow--right" aria-hidden="true"><svg width="7" height="11">
                                                    <path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9 C-0.1,9.8-0.1,10.4,0.3,10.7z" />
                                                </svg>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                            <div class="products-view__pagination-legend">
                                Showing 6 of 98 products
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="block-space block-space--layout--before-footer"></div>
    </div>
</div>

<script>
    listarProductos = async() => {
        localStorage.simonBolivar_contador = 0

        if($('#filtro_busqueda').val()) localStorage.simonBolivar_buscarProducto = $('#filtro_busqueda').val()

        if(localStorage.simonBolivar_buscarProducto) $('#buscar').val(localStorage.simonBolivar_buscarProducto)

        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar").val(),
        }

        if($('#filtro_marca').val() != undefined) datos.marca = $('#filtro_marca').val()
        if($('#filtro_grupo').val() != undefined) datos.grupo = $('#filtro_grupo').val()
        if($('#filtro_linea').val() != undefined) datos.linea = $('#filtro_linea').val()

        cargarInterfaz('productos/lista', 'contenedor_productos', datos)
        // cargarInterfaz('productos/contenedor/paginacion', 'contenedor_paginacion', datosPaginacion)
    }

    $().ready(() => {
        listarProductos()

        // $('#view-option-limit').change(() => listarProductos)

        // console.log(localStorage.simonBolivar_productosFiltroVista)
        // let tipoVista = (localStorage.simonBolivar_productosFiltroVista) ? localStorage.simonBolivar_productosFiltroVista : 'list'
        // // $('button[]').attr('data-layout').addClass('layout-switcher__button--active')

        // $('.layout-switcher__button').click(evento => {
        //     localStorage.simonBolivar_productosFiltroVista = evento.currentTarget.attributes['data-layout'].nodeValue
        // })
    })
</script>