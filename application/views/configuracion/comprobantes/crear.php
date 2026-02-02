<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Creación de comprobante</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0" id="formulario_buscar_cliente">
            <div class="card-body card-body--padding--1">
                <form class="form-row mb-2">
                    <div class="form-group col-md-12">
                        <label for="buscar_cliente">Cliente *</label>
                        <input type="text" class="form-control" id="buscar_cliente" placeholder="Buscar por nombres, apellidos, NIT..." autofocus>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" id="btn_buscar_cliete">Buscar cliente</button>
                </form>
                
                <div id="contenedor_resultado_terceros">
                    <div class="mt-2" id="contenedor_mensaje_cliente"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    var buscarCliente = $('#buscar_cliente')

    seleccionarCliente = numeroDocumento => {
        location.href = `${$('#site_url').val()}clientes?nit=${numeroDocumento}`
    }

    $().ready(() => {
        $('#formulario_buscar_cliente').submit(async(evento) => {
            evento.preventDefault()

            let datosObligatorios = [
                buscarCliente,
            ]

            // Validación de campos obligatorios
            if (!validarCamposObligatorios(datosObligatorios)) return false

            let datosBusqueda = {
                tipo: 'tercero',
                bucar: buscarCliente.val(),
            }

            // Se activa el spinner
            $('#btn_buscar_cliete').addClass('btn-loading').attr('disabled', true)

            agregarLog(58, buscarCliente.val())

            // Mensaje mientras se consultan los datos
            $('#contenedor_mensaje_cliente').html(`<button class='btn btn-muted btn-loading btn-xs btn-icon'></button> Buscando cliente...`)

            let terceros = await consulta('obtener', {tipo: 'terceros_local', busqueda: buscarCliente.val(), f200_ind_cliente: 1}, false)
            
            // Si no se encuentra el estado de cuenta
            if(terceros.resultado.length == 0) {
                $('#btn_buscar_cliete').removeClass('btn-loading').attr('disabled', false)
                $('#contenedor_mensaje_cliente').html('')
                mostrarAviso('alerta', 'No se encontraron resultados con los datos que nos indicas. Por favor, intenta con otros datos.', 30000)
                agregarLog(59, buscarCliente.val())
                return false
            }

            $('#btn_buscar_cliete').removeClass('btn-loading').attr('disabled', false)

            // Se carga la lista de terceros encontrados
            cargarInterfaz('configuracion/comprobantes/resultado', 'contenedor_resultado_terceros', terceros)
        })
    })
</script>