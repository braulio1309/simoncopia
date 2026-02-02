<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Perfiles</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div id="contenedor_perfiles"></div>
    </div>

    <div class="block-space block-space--layout--before-footer"></div>
</div>

<script>
    listarPerfiles = () => {
        localStorage.simonBolivar_contador = 0

        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar").val(),
        }

        cargarInterfaz('configuracion/perfiles/lista', 'contenedor_perfiles', datos)
    }

    $().ready(() => {
        listarPerfiles()
    })
</script>