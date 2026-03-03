<style>
    #tabla_campanias tbody td {
        font-size: 0.9em;
        padding: 8px;
        vertical-align: middle;
    }
    .dt-buttons-gap {
        display: flex;
        gap: 5px;
        justify-content: center;
    }
</style>

<div class="table-responsive">
    <table class="table-striped table-bordered w-100" id="tabla_campanias"></table>
</div>

<input type="file" class="d-none" id="importar_archivo" onchange="importarCampanias()" accept=".xlsx,.xls,.csv">


<script>
    let campania_id = null
    let tablaCampanias = null

    // ==========================================
    // SECCIÓN 1: IMPORTACIÓN DE CONTACTOS
    // ==========================================
    const seleccionarCampania = (id) => {
        campania_id = id
        $("#importar_archivo").val(null).trigger('click')
    }

    const importarCampanias = () => {
        Swal.fire({
            title: 'Subiendo archivo...',
            text: 'Importando contactos al sistema. Por favor espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        let archivo = $('#importar_archivo').prop('files')[0]
        let documento = new FormData()

        documento.append("archivo", archivo)
        documento.append("campania_id", campania_id)

        let subida = new XMLHttpRequest()
        subida.open('POST', `${$("#site_url").val()}marketing/importar_campanias`)
        subida.send(documento)

        subida.onload = evento => {
            let respuesta = JSON.parse(evento.target.responseText)
            Swal.close()

            if (respuesta.exito) {
                tablaCampanias.ajax.reload(null, false)
                mostrarAviso('exito', `¡${respuesta.mensaje}!`, 5000)
            } else {
                mostrarAviso('error', `¡${respuesta.mensaje}!`, 5000)
            }
        }
    }

    // ==========================================
    // SECCIÓN 2: MENSAJE DE PRUEBA
    // ==========================================
    const ejecutarEnvioPrueba = (id) => {
        Swal.fire({
            title: 'Enviar prueba',
            input: 'text',
            inputLabel: 'Número de teléfono',
            inputPlaceholder: 'Ej: 573001234567',
            showCancelButton: true,
            confirmButtonText: 'Enviar',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                if (!value) return 'Por favor ingresa un número de teléfono.'
                if (!/^\d{7,15}$/.test(value.trim())) return 'El número debe contener solo dígitos (entre 7 y 15).'
            }
        }).then((result) => {
            if (!result.isConfirmed) return

            Swal.fire({
                title: 'Enviando prueba...',
                text: 'Conectando con WhatsApp API',
                didOpen: () => { Swal.showLoading() },
                allowOutsideClick: false
            })

            $.ajax({
                url: `${$("#site_url").val()}marketing/enviar_prueba_whatsapp`,
                method: 'POST',
                data: { campania_id: id, telefono_prueba: result.value },
                dataType: 'json',
                success: function(respuesta) {
                    Swal.close()
                    if (respuesta.exito) {
                        mostrarAviso('exito', 'Mensaje de prueba enviado correctamente.')
                    } else {
                        mostrarAviso('error', respuesta.mensaje || 'Error al enviar.')
                    }
                },
                error: function() {
                    Swal.close()
                    mostrarAviso('error', 'Error de conexión con el servidor.')
                }
            })
        })
    }

    // ==========================================
    // SECCIÓN 3: ENVÍO MASIVO (PLAY)
    // ==========================================
    const confirmarEnvioMasivo = (id) => {
        Swal.fire({
            title: '¿Iniciar envío masivo?',
            text: "El sistema buscará los contactos pendientes y enviará los mensajes uno por uno. Verifica que la fecha de la campaña esté vigente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107', // Naranja
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-play"></i> Sí, iniciar envío',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                ejecutarEnvioMasivo(id);
            }
        })
    }

    const duplicarCampania = (id) => {
        Swal.fire({
            title: '¿Duplicar campaña?',
            text: 'Se creará una copia con los contactos de la campaña.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, duplicar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (!result.isConfirmed) return

            Swal.fire({
                title: 'Duplicando campaña...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            })

            $.ajax({
                url: `${$("#site_url").val()}marketing/duplicar_campania`,
                method: 'POST',
                data: { campania_id: id },
                dataType: 'json',
                success: (respuesta) => {
                    Swal.close()

                    if (respuesta.exito) {
                        // Se agrega el log correspondiente a la campaña duplicada
                        agregarLog( 108, `Campaña duplicada id original: ${respuesta.id_original} - id duplicado: ${respuesta.id_copia}`)

                        mostrarAviso('exito', respuesta.mensaje)
                        tablaCampanias.ajax.reload(null, false)
                    } else {
                        mostrarAviso('error', respuesta.mensaje)
                    }
                },
                error: () => {
                    Swal.close()
                    mostrarAviso('error', 'Error de conexión.')
                }
            })
        })
    }

    const ejecutarEnvioMasivo = (id) => {
        Swal.fire({
            title: 'Procesando envíos...',
            text: 'Por favor no cierres esta ventana hasta que el proceso termine.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        $.ajax({
            url: `${$("#site_url").val()}marketing/ejecutar_envio_masivo`,
            method: 'POST',
            data: { campania_id: id },
            dataType: 'json',
            success: function(respuesta) {
                Swal.close()
                if (respuesta.exito) {
                    mostrarAviso('exito', respuesta.mensaje, 5000)
                    tablaCampanias.ajax.reload(null, false)
                } else {
                    // Usamos alerta/warning porque puede ser un error de validación (fecha vencida)
                    Swal.fire('Atención', respuesta.mensaje, 'warning');
                }
            },
            error: function() {
                Swal.close()
                mostrarAviso('error', 'Ocurrió un error al conectar con el servidor.')
            }
        })
    }

    const eliminarCampania = (id) => {
        Swal.fire({
            title: '¿Eliminar campaña?',
            text: 'Esta acción eliminará la campaña, sus contactos y su imagen. No se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-trash"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Eliminando campaña...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: `${$("#site_url").val()}marketing/eliminar_campania`,
                method: 'POST',
                data: { campania_id: id },
                dataType: 'json',
                success: (respuesta) => {
                    Swal.close();

                    if (respuesta.exito) {
                        mostrarAviso('exito', respuesta.mensaje);
                        tablaCampanias.ajax.reload(null, false);
                    } else {
                        mostrarAviso('error', respuesta.mensaje);
                    }
                },
                error: () => {
                    Swal.close();
                    mostrarAviso('error', 'Error de conexión con el servidor.');
                }
            });
        });
    };

    // ==========================================
    // INICIALIZACIÓN Y DATATABLES
    // ==========================================
    $().ready(async () => {
        tablaCampanias = $("#tabla_campanias").DataTable({
            ajax: {
                url: `${$("#site_url").val()}marketing/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'campanias'
                    datos.filtros_personalizados = {
                        id: $('#filtro_id').val(),
                        fecha_inicio: $('#filtro_fecha_inicio').val(),
                        cantidad_contactos: $('#filtro_cantidad_contactos').val(),
                        cantidad_envios: $('#filtro_cantidad_envios').val(),
                        nombre: $('#filtro_nombre').val(),
                    }
                }
            },
            columns: [
                { 
                    title: `ID <br><input type="number" id="filtro_id" class="form-control form-control-sm border-secondary mt-1">`,
                    data: 'id',
                    className: 'dt-body-right',
                    width: '5%'
                },
                { 
                    title: `
                        Nombre
                        <input type="text" id="filtro_nombre" class="form-control form-control-sm border-secondary mt-1">
                    `,
                    data: 'nombre',
                },
                { 
                    title: `Inicio <br><input type="date" id="filtro_fecha_inicio" class="form-control form-control-sm border-secondary mt-1">`,
                    data: 'fecha_inicio'
                },
                { 
                    title: `Contactos <br><input type="number" id="filtro_cantidad_contactos" class="form-control form-control-sm border-secondary mt-1">`,
                    data: 'cantidad_contactos',
                    className: 'dt-body-right'
                },
                { 
                    title: `Envíos <br><input type="number" id="filtro_cantidad_envios" class="form-control form-control-sm border-secondary mt-1">`,
                    data: 'cantidad_envios',
                    className: 'dt-body-right'
                },
                {
                    title: 'Acciones',
                    data: null, 
                    orderable: false,
                    render: (data, type, row) => {
                        return `
                            <div class="dt-buttons-gap">
                                <a class="btn btn-sm btn-primary" href="${$("#site_url").val()}marketing/campanias/editar/${data.id}" title="Editar campaña">
                                    <i class="fa fa-pencil"></i>
                                </a>

                                ${
                                    data.cantidad_envios > 0
                                    ? `
                                        <button class="btn btn-sm btn-success" disabled title="No se pueden importar contactos porque la campaña ya tiene envíos realizados">
                                            <i class="fa fa-lock"></i>
                                        </button>
                                    `
                                    : `
                                        <button class="btn btn-sm btn-success" title="Importar contactos (Excel/CSV)"
                                                onclick="seleccionarCampania(${data.id})">
                                            <i class="fa fa-upload"></i>
                                        </button>
                                    `
                                }

                                <button class="btn btn-sm btn-info"
                                        title="Enviar mensaje de prueba"
                                        onclick="ejecutarEnvioPrueba(${data.id})">
                                    <i class="fa fa-paper-plane"></i>
                                </button>

                                <button class="btn btn-sm btn-warning text-white"
                                        title="Ejecutar envío masivo"
                                        onclick="confirmarEnvioMasivo(${data.id})">
                                    <i class="fa fa-play"></i>
                                </button>

                                <button class="btn btn-sm btn-secondary" title="Duplicar campaña" onclick="duplicarCampania(${data.id})">
                                    <i class="fa fa-copy"></i>
                                </button>

                                ${
                                    data.cantidad_envios > 0
                                    ? `
                                        <button class="btn btn-sm btn-danger" disabled title="No se puede eliminar la campaña porque ya tiene envíos realizados">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    `
                                    : `
                                        <button class="btn btn-sm btn-danger" title="Eliminar campaña" onclick="eliminarCampania(${data.id})">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    `
                                }
                            </div>
                        `
                    }
                }
            ],
            columnDefs: [
                { targets: '_all', className: 'dt-head-center p-1' }
            ],
            deferRender: true,
            fixedHeader: true,
            info: true,
            initComplete: function () {
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaCampanias.draw())
            },
            language: {
                decimal: ',',
                thousands: '.',
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            ordering: false,
            orderCellsTop: true,
            pageLength: 50,
            paging: true,
            processing: true,
            scrollCollapse: true,
            scroller: true,
            scrollX: false,
            scrollY: false,
            searching: true, // Buscador global activado
            serverSide: true,
            stateSave: false,
        })

        // Listener global para el trigger de importación
        $(".importar").click(() => $("#importar_archivo").trigger('click'))
    })
</script>