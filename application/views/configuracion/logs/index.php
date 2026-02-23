<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Visualización de Logs</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div class="row mb-4">
            <div class="input-field col s3">
                <label for="fecha_inicial" class="active">
                    <i class="fa fa-calendar-alt"></i> Fecha inicial
                </label>
                <input type="date" id="fecha_inicial">
            </div>

            <div class="input-field col s3">
                <label for="fecha_final" class="active">
                    <i class="fa fa-calendar-check"></i> Fecha final
                </label>
                <input type="date" id="fecha_final">
            </div>

            <div class="col s6 d-flex align-items-end">
                <span class="grey-text text-darken-1" style="font-size: 0.9rem;">
                    <i class="fa fa-info-circle"></i>
                    Selecciona el rango de fechas para filtrar los logs
                </span>
            </div>
        </div>
        
        <div id="contenedor_logs"></div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    listarLogs = () => {
        // Si no hay valor en la búsqueda, pero si en loca storage, lo pone
        if ($("#fecha_inicial").val() == "" && localStorage.simonBolivar_fecha_inicial) $("#fecha_inicial").val(localStorage.simonBolivar_fecha_inicial)
        if ($("#fecha_final").val() == "" && localStorage.simonBolivar_fecha_final) $("#fecha_final").val(localStorage.simonBolivar_fecha_final)

        localStorage.simonBolivar_contador = 0

        let datos = {
            contador: localStorage.simonBolivar_contador,
            fecha_inicial: $("#fecha_inicial").val(),
            fecha_final: $("#fecha_final").val(),
        }

        cargarInterfaz('configuracion/logs/lista', 'contenedor_logs', datos)
    }

    const hoy = () => {
        const d = new Date()
        return d.toISOString().split("T")[0] // yyyy-mm-dd
    }

    $().ready(() => {
        // Primera carga: si no hay fechas guardadas, usa la actual
        if (!localStorage.simonBolivar_fecha_inicial) localStorage.simonBolivar_fecha_inicial = hoy()
        if (!localStorage.simonBolivar_fecha_final) localStorage.simonBolivar_fecha_final = hoy()

        // Asignar valores a los inputs
        $("#fecha_inicial").val(localStorage.simonBolivar_fecha_inicial)
        $("#fecha_final").val(localStorage.simonBolivar_fecha_final)

        listarLogs()

        $("#fecha_inicial, #fecha_final").on("change", function () {
            localStorage.simonBolivar_fecha_inicial = $("#fecha_inicial").val()
            localStorage.simonBolivar_fecha_final = $("#fecha_final").val()

            if (window.tablaLogs) tablaLogs.ajax.reload()
        })
    })
</script>