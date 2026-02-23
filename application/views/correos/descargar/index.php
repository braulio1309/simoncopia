<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Descargar Correos con Adjuntos</h1>
            <p class="text-muted">Ingresa el nombre de la carpeta de correo para descargar los adjuntos</p>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div class="row">
            <!-- Formulario de descarga -->
            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-body card-body--padding--2">
                        <div class="tag-badge tag-badge--new badge_formulario badge_formulario_azul">
                            CONFIGURACIÓN DE DESCARGA
                        </div>

                        <form id="form_descargar_correos">
                            <div class="form-group">
                                <label for="nombre_carpeta">Nombre de la Carpeta *</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="nombre_carpeta" 
                                    placeholder="Ejemplo: Inbox, Facturas, Pagos"
                                    autofocus
                                    required
                                >
                                <small class="form-text text-muted">
                                    Ingresa el nombre exacto de la carpeta de correo
                                </small>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block" id="btn_descargar">
                                <i class="fas fa-download"></i> Descargar Correos
                            </button>
                        </form>

                        <div class="mt-3" id="mensaje_resultado"></div>

                        <!-- Información adicional -->
                        <div class="alert alert-info mt-3">
                            <h6><i class="fas fa-info-circle"></i> Información</h6>
                            <ul class="mb-0 pl-3">
                                <li>Se descargarán solo correos con adjuntos</li>
                                <li>Formato: NIT_Nombre_Monto</li>
                                <li>Los archivos se guardarán en: archivos/correos/</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de archivos descargados -->
            <div class="col-lg-8 col-md-12">
                <div class="card">
                    <div class="card-body card-body--padding--2">
                        <div class="tag-badge tag-badge--new badge_formulario badge_formulario_verde">
                            ARCHIVOS DESCARGADOS RECIENTEMENTE
                        </div>

                        <div id="contenedor_lista_archivos" style="max-height: 500px; overflow-y: auto;">
                            <!-- Aquí se cargará la lista de archivos -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="block-space block-space--layout--before-footer"></div>
</div>

<script>
    /**
     * Procesa el formulario de descarga de correos
     */
    $('#form_descargar_correos').submit(function(e) {
        e.preventDefault();
        
        const nombreCarpeta = $('#nombre_carpeta').val().trim();
        
        if(!nombreCarpeta) {
            mostrarAviso('alerta', 'Debe ingresar el nombre de la carpeta', 3000);
            return;
        }

        // Deshabilitar botón y mostrar loading
        $('#btn_descargar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Descargando...');
        $('#mensaje_resultado').html('');

        const datos = {
            nombre_carpeta: nombreCarpeta
        };

        $.ajax({
            url: `${$('#site_url').val()}/correos/procesar`,
            type: 'POST',
            dataType: 'json',
            data: { datos: JSON.stringify(datos) },
            success: function(respuesta) {
                if(respuesta.error) {
                    mostrarAviso('error', respuesta.mensaje, 5000);
                    $('#mensaje_resultado').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i> ${respuesta.mensaje}
                        </div>
                    `);
                } else {
                    mostrarAviso('exito', respuesta.mensaje, 5000);
                    $('#mensaje_resultado').html(`
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> ${respuesta.mensaje}
                            <br><strong>Archivos descargados:</strong> ${respuesta.archivos_descargados}
                        </div>
                    `);
                    
                    // Recargar lista de archivos
                    listarArchivosDescargados();
                    
                    // Limpiar formulario
                    $('#nombre_carpeta').val('');
                }
            },
            error: function(xhr, status, error) {
                mostrarAviso('error', 'Error al procesar la solicitud', 5000);
                $('#mensaje_resultado').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> Error al procesar la solicitud
                    </div>
                `);
            },
            complete: function() {
                $('#btn_descargar').prop('disabled', false).html('<i class="fas fa-download"></i> Descargar Correos');
            }
        });
    });

    /**
     * Carga la lista de archivos descargados
     */
    function listarArchivosDescargados() {
        cargarInterfaz('correos/listar', 'contenedor_lista_archivos');
    }

    // Cargar lista al iniciar
    $().ready(function() {
        listarArchivosDescargados();
    });
</script>