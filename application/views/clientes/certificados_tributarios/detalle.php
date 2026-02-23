<?php
    $cliente = $this->clientes_model->obtener(
        'clientes_retenciones_informe',
        ['nit' => $nit]
    );
?>

<div class="block-space block-space--layout--after-header"></div>

<div class="block">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Certificados de retención de <?php echo $cliente->razon_social; ?></h1>
        </div>
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">

                <div class="form-row">
                    <div class="form-group col-lg-4">
                        <label for="nit">Numero de documento *</label>
                        <input type="text" class="form-control" id="nit" value="<?php if(isset($cliente->nit)) echo $cliente->nit; ?>">
                    </div>

                    <div class="form-group col-lg-4">
                        <label for="tipo_retencion">Tipo de certificado *</label>
                        <select class="form-control" id="tipo_retencion" name="tipo_retencion" required>
                            <option value="" disabled selected>Seleccione tipo de retención</option>
                            <option value="FUENTE" data-directorio="RETEFUENTE">Retención en la fuente</option>
                            <option value="IVA" data-directorio="RETEIVA">IVA</option>
                            <option value="ICA" data-directorio="RETEICA">ICA</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-4">
                        <label for="monto">Monto *</label>
                        <input type="number" class="form-control" id="monto" step="0.01" min="0" placeholder="0.00">
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="certificado_archivo"> Archivo (PDF, Word o Excel) *</label>
                        <input type="file" class="form-control" id="certificado_archivo" accept=".pdf,.doc,.docx,.xls,.xlsx">
                        <div class="mt-2">
                            <span id="nombre_archivo" class="text-muted d-none"></span>
                            <button type="button" id="eliminar_archivo" class="btn btn-sm btn-outline-danger d-none" title="Eliminar archivo">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="observacion">Observación </label>
                        <textarea class="form-control" id="observacion" rows="5"></textarea>
                    </div>
                </div>

                <button class="btn btn-info" onclick="history.back()">Volver</button>
                <button class="btn btn-success" onclick="guardarCertificado()"> Guardar </button>
            </div>
        </div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    let eliminarArchivo = false

    async function guardarCertificado() {
        let archivos = $("#certificado_archivo").prop("files")

        let camposObligatorios = [
            $("#nit"),
            $("#tipo_retencion"),
            $("#monto"),
            $("#certificado_archivo")
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return

        let datos = {
            tipo: 'clientes_retenciones_detalle',
            tipo_retencion: $("#tipo_retencion").val(),
            monto: $("#monto").val(),
            observacion: $("#observacion").val(),
            nit: $("#nit").val(),
            anio: new Date().getFullYear() - 1
        }
        
        // Crear registro
        let respuesta = await consulta('crear', datos, false)
        
        // Valida que se haya creado con éxito
        if (!respuesta || !respuesta.resultado) {
            mostrarAviso('alerta', 'Error al crear el certificado')
            return
        }

        let id = respuesta.resultado

        // Subir archivo
        if (archivos.length > 0 && !eliminarArchivo) {
            let archivo = archivos[0]
            let extension = archivo.name.split('.').pop().toLowerCase()
            let nombreArchivo = `certificado.${extension}`

            let formData = new FormData()
            formData.append('archivo', archivo, nombreArchivo)

            let peticion = new XMLHttpRequest()
            peticion.open(
                'POST',
                `${$('#site_url').val()}clientes/subir_certificado/${id}/${$("#tipo_retencion option:selected").attr('data-directorio')}`
            )
            peticion.send(formData)

            peticion.onload = () => {
                let respuesta = JSON.parse(peticion.responseText)

                if (!respuesta.resultado) {
                    mostrarAviso('alerta', 'El archivo no pudo subirse')
                    return
                }

                // Se envía un correo electrónico de notificación
                let email =  obtenerPromesa(`${$('#site_url').val()}interfaces/enviar_email`, {tipo: 'certificado_retencion', id: id})

                mostrarAviso('exito', 'El certificado se guardó correctamente')

                // setTimeout(() => { history.back()}, 1500) // retorna a la página anterior
            }
        } else {
            mostrarAviso('exito', 'El certificado se guardó correctamente')
            // setTimeout(() => { history.back()}, 1500) // retorna a la página anterior
        }
    }

    $(document).ready(function () {
        $("#certificado_archivo").on("change", function () {
            const archivo = this.files[0]
            if (!archivo) return

            const extension = archivo.name.split('.').pop().toLowerCase()
            const extensionesPermitidas = ['pdf', 'doc', 'docx', 'xls', 'xlsx']

            if (!extensionesPermitidas.includes(extension)) {
                mostrarAviso( 'alerta', 'Solo se permiten archivos PDF, Word o Excel')

                this.value = ''
                $("#nombre_archivo").addClass('d-none').text('')
                $("#eliminar_archivo").addClass('d-none')
                return
            }

            $("#nombre_archivo").text(`Archivo seleccionado: ${archivo.name}`).removeClass('d-none')

            $("#eliminar_archivo").removeClass('d-none')
            eliminarArchivo = false
        })

        $("#eliminar_archivo").on("click", function () {
            Swal.fire({
                title: '¿Eliminar archivo?',
                text: 'Esta acción eliminará el archivo seleccionado',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (!result.isConfirmed) return

                $("#certificado_archivo").val('')
                $("#nombre_archivo").addClass('d-none').text('')
                $("#eliminar_archivo").addClass('d-none')

                eliminarArchivo = true
            })
        })
    })
</script>
