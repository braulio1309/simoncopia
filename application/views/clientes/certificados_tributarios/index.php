<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de certificados tributarios</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div class="row mb-4">
            <button class="btn btn-success" onclick="confirmarEnvioCertificados()">
                <i class="fa fa-paper-plane"></i> Enviar notificación email
            </button>
        </div>

        <div id="contenedor_certificados_tributarios"></div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    listarCertificadosTrubutarios = () => {
        // Si no hay valor en la búsqueda, pero si en loca storage, lo pone
        if($("#buscar_certificado_tributario").val() == "" && localStorage.simonBolivar_busquedaCertificadoTributario) $("#buscar_certificado_tributario").val(localStorage.simonBolivar_busquedaCertificadoTributario)

        localStorage.simonBolivar_contador = 0

        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar_certificado_tributario").val()
        }

        cargarInterfaz('clientes/certificados_tributarios/lista', 'contenedor_certificados_tributarios', datos)
    }

    confirmarEnvioCertificados = () => {
        Swal.fire({
            title: '¿Desea envíar la notificación para subir certificados de retención?',
            text: 'Se enviarán los correos pendientes. Este proceso puede tardar varios minutos.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, enviar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                ejecutarEnvioCertificados();
            }
        })
    }

    const ejecutarEnvioCertificados = () => {
        Swal.fire({
            title: 'Procesando envíos...',
            text: 'Por favor no cierres esta ventana.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        })

        $.ajax({
            url: `${$("#site_url").val()}webhooks/envio_certificados_masivo`,
            method: 'POST',
            dataType: 'json',
            success: function(respuesta) {
                Swal.close()

                let resultado_log = {
                    fecha: new Date().toISOString(),
                    mensaje: respuesta.mensaje || '',
                    total: 0,
                    enviados: 0,
                    fallidos: 0,
                    detalle: []
                }

                if (respuesta.exito && Array.isArray(respuesta.log)) {
                    resultado_log.total = respuesta.log.length
                    resultado_log.enviados = respuesta.log.filter(l => l.estado === false).length
                    resultado_log.fallidos = respuesta.log.filter(l => l.estado === false).length
                    resultado_log.detalle = respuesta.log

                    Swal.fire({
                        title: 'Proceso terminado',
                        html: `<b>${respuesta.mensaje}</b>`,
                        icon: 'success'
                    })

                } else {
                    Swal.fire('Sin pendientes', respuesta.mensaje, 'info')
                }

                agregarLog(107, JSON.stringify(resultado_log))
            },
            error: function() {
                Swal.close()
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error')

                agregarLog(107, JSON.stringify({ fecha: new Date().toISOString(), error: 'ERROR_CONEXION_WEBHOOK' }))
            }
        })
    }

    $().ready(() => {
        listarCertificadosTrubutarios()

        $("#buscar_certificado_tributario").keyup(() => {
            // Se almacena el valor de búsqueda en local storage
            localStorage.simonBolivar_busquedaCertificadoTributario = $("#buscar_certificado_tributario").val()

            listarCertificadosTrubutarios()
        })
    })
</script>