<?php
$marketing_banners = $this->marketing_model->obtener('marketing_banners');
?>
<div class="block-space block-space--layout--after-header"></div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="banner_tipo_id">Tipo de banner *</label>
                        <select id="banner_tipo_id" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach($this->marketing_model->obtener('marketing_banners_tipos') as $banner_tipo) echo "<option value='$banner_tipo->id'>$banner_tipo->nombre </option>"; ?>
                        </select>
                    </div>

                    <div class="form-group col-lg-6">
                        <label for="banner_tipo_archivo"> Iamgen (Formato WEBP) *</label>
                        <input type="file" class="form-control" id="banner_tipo_archivo" accept=".webp">
                        <div class="mt-2">
                            <span id="nombre_archivo" class="text-muted d-none"></span>
                            <button type="button" id="eliminar_archivo" class="btn btn-sm btn-outline-danger d-none" title="Eliminar archivo">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button class="btn btn-info" onclick="history.back()">Volver</button>
                <button class="btn btn-success" onclick="guardarBanner()"> Guardar </button>
            </div>
        </div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    // Se trae el arreglo de datos en un json
    const marketingBanners = <?php echo json_encode($marketing_banners) ?>;
    
    let eliminarArchivo = false

    async function guardarBanner() {
        let archivos = $("#banner_tipo_archivo").prop("files")

        let camposObligatorios = [
            $("#banner_tipo_id"),
            $("#banner_tipo_archivo")
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return

        let bannerTipoId = $("#banner_tipo_id").val()

        // Nombre original del archivo
        let nombreArchivo = null
        if (archivos.length > 0) nombreArchivo = archivos[0].name

        // Buscar si ya existe banner para este tipo
        let bannerExistente = marketingBanners.find(
            b => b.banner_tipo_id == bannerTipoId
        )

        let datos = {
            tipo: 'marketing_banners',
            banner_tipo_id: bannerTipoId,
            nombre_archivo: nombreArchivo
        }

        let id = null

        // Crear o actualizar
        if (bannerExistente) {
            datos.id = bannerTipoId // Existente en el arreglo de marketing_banners
            await consulta('actualizar', datos, false)
            id = bannerExistente.id
        } else {
            let respuesta = await consulta('crear', datos, false)

            if (!respuesta || !respuesta.resultado) {
                mostrarAviso('alerta', 'Error al crear el banner')
                return
            }

            id = respuesta.resultado
        }

        // Subir archivo
        if (archivos.length > 0 && !eliminarArchivo) {
            let archivo = archivos[0]

            let formData = new FormData()
            formData.append('archivo', archivo)

            let peticion = new XMLHttpRequest()
            peticion.open(
                'POST',
                `${$('#site_url').val()}marketing/subir_banner/${id}`
            )
            peticion.send(formData)

            peticion.onload = () => {
                let respuesta = JSON.parse(peticion.responseText)

                if (!respuesta.resultado) {
                    mostrarAviso('alerta', 'El archivo no pudo subirse')
                    return
                }

                mostrarAviso('exito', 'El banner se guardó correctamente')
                setTimeout(() => history.back(), 1500)
            }
        } else {
            mostrarAviso('exito', 'El banner se guardó correctamente')
            setTimeout(() => history.back(), 1500)
        }
    }

    $(document).ready(function () {

        $("#banner_tipo_archivo").on("change", function () {
            const archivo = this.files[0]
            if (!archivo) return

            const extension = archivo.name.split('.').pop().toLowerCase()
            const extensionesPermitidas = ['webp']

            if (!extensionesPermitidas.includes(extension)) {
                mostrarAviso('alerta', 'Solo se permiten archivos webp')

                this.value = ''
                $("#nombre_archivo").addClass('d-none').text('')
                $("#eliminar_archivo").addClass('d-none')
                return
            }

            $("#nombre_archivo")
                .text(`Archivo seleccionado: ${archivo.name}`)
                .removeClass('d-none')

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

                $("#banner_tipo_archivo").val('')
                $("#nombre_archivo").addClass('d-none').text('')
                $("#eliminar_archivo").addClass('d-none')

                eliminarArchivo = true
            })
        })

    })
</script>
