<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Descargar Correos con Adjuntos</h1>
            <p class="text-muted">Selecciona la carpeta de correo para descargar los adjuntos</p>
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
                                <label for="carpeta_id">Carpeta de Correo *</label>
                                <select class="form-control" id="carpeta_id" required>
                                    <option value="">-- Cargando carpetas... --</option>
                                </select>
                                <small class="form-text text-muted">
                                    Selecciona la carpeta de correo a descargar
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="fecha_inicio">Fecha Desde</label>
                                <input type="date" class="form-control" id="fecha_inicio">
                            </div>

                            <div class="form-group">
                                <label for="fecha_fin">Fecha Hasta</label>
                                <input type="date" class="form-control" id="fecha_fin">
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
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="tag-badge tag-badge--new badge_formulario badge_formulario_verde">
                                ARCHIVOS DESCARGADOS
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" onclick="listarArchivosDescargados()">
                                <i class="fas fa-sync-alt"></i> Actualizar
                            </button>
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
     * Carga las carpetas de correo desde la API en el select
     */
    function cargarCarpetas() {
        $('#carpeta_id').html('<option value="">-- Cargando carpetas... --</option>').prop('disabled', true);

        $.ajax({
            url: `${$('#site_url').val()}correos/obtener_carpetas`,
            type: 'GET',
            dataType: 'json',
            success: function(respuesta) {
                if(respuesta.error || !respuesta.carpetas || respuesta.carpetas.length === 0) {
                    $('#carpeta_id').html('<option value="">-- No se pudieron cargar las carpetas --</option>');
                    return;
                }

                let options = '<option value="">-- Seleccionar carpeta --</option>';
                $.each(respuesta.carpetas, function(i, carpeta) {
                    options += `<option value="${carpeta.id}" data-nombre="${carpeta.nombre}">${carpeta.nombre} (${carpeta.total})</option>`;
                });
                $('#carpeta_id').html(options).prop('disabled', false);
            },
            error: function() {
                $('#carpeta_id').html('<option value="">-- Error al cargar carpetas --</option>');
            }
        });
    }

    /**
     * Procesa el formulario de descarga de correos
     */
    $('#form_descargar_correos').submit(function(e) {
        e.preventDefault();
        
        const carpetaId    = $('#carpeta_id').val();
        const carpetaNombre = $('#carpeta_id option:selected').data('nombre') || carpetaId;

        if(!carpetaId) {
            mostrarAviso('alerta', 'Debe seleccionar una carpeta', 3000);
            return;
        }

        // Deshabilitar botón y mostrar loading
        $('#btn_descargar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Descargando...');
        $('#mensaje_resultado').html('');

        const datos = {
            carpeta_id:     carpetaId,
            nombre_carpeta: carpetaNombre,
            fecha_inicio:   $('#fecha_inicio').val(),
            fecha_fin:      $('#fecha_fin').val()
        };

        $.ajax({
            url: `${$('#site_url').val()}correos/procesar`,
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

    // Cargar carpetas y lista al iniciar
    $().ready(function() {
        cargarCarpetas();
        listarArchivosDescargados();
    });
</script>