<div class="search">
    <form id="formulario_buscar" class="search__body">
        <div class="search__shadow"></div>
        
        <!-- Búsqueda por palabra clave -->
        <input class="search__input" type="text" placeholder="Buscar por palabra clave o referencia" id="buscar">
        
        <button class="search__button search__button--end" type="submit">
            <span class="search__button-icon">
                <svg width="20" height="20">
                    <path fill="#19287F" d="M19.2,17.8c0,0-0.2,0.5-0.5,0.8c-0.4,0.4-0.9,0.6-0.9,0.6s-0.9,0.7-2.8-1.6c-1.1-1.4-2.2-2.8-3.1-3.9C10.9,14.5,9.5,15,8,15 c-3.9,0-7-3.1-7-7s3.1-7,7-7s7,3.1,7,7c0,1.5-0.5,2.9-1.3,4c1.1,0.8,2.5,2,4,3.1C20,16.8,19.2,17.8,19.2,17.8z M8,3C5.2,3,3,5.2,3,8 c0,2.8,2.2,5,5,5c2.8,0,5-2.2,5-5C13,5.2,10.8,3,8,3z" />
                </svg>
            </span>
        </button>
        <div class="search__box"></div>
        <div class="search__decor">
            <div class="search__decor-start"></div>
            <div class="search__decor-end"></div>
        </div>
        
        <!-- Productos recientes -->
        <div class="search__dropdown search__dropdown--suggestions suggestions">
            <div class="suggestions__group">
                <div class="suggestions__group-title">Productos recientes</div>
                <div class="suggestions__group-content">
                    <div id="contenedor_busqueda_reciente"></div>
                </div>
            </div>
        </div><!-- Productos recientes -->
    </form>
</div>

<script>
    $('#formulario_buscar').submit(e => {
        e.preventDefault()

        localStorage.simonBolivar_buscarProducto = $('#buscar').val()

        agregarLog(91, JSON.stringify({
            tipo: 'Búsqueda por palabra clave',
            detalle: `Búsqueda: ${$('#buscar').val()}`
        }))

        location.href = '<?php echo site_url("productos?busqueda="); ?>' + $('#buscar').val()
    })

    $().ready(async () => {
        if(localStorage.simonBolivar_productosRecientes) {
            cargarInterfaz('core/menu_superior/busqueda_reciente', 'contenedor_busqueda_reciente', {productos: JSON.parse(localStorage.simonBolivar_productosRecientes)})
        }

        $('#buscar').click(evento => {
            agregarLog(91, JSON.stringify({
                tipo: 'Búsqueda por palabra clave',
                detalle: 'Clic en el campo'
            }))
        })
    })
</script>