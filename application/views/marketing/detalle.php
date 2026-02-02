<?php
if (isset($id)) {
    $campania = $this->marketing_model->obtener('marketing_campanias', ['id' => $id]);
    echo "<input type='hidden' id='campania_id' value='$campania->id' />";

    // Carpeta donde se guardan las imágenes
    $carpeta = "./archivos/campanias/{$campania->id}/";
    
    // Se valida si existe la carpeta
    if (is_dir($carpeta)) {
        // Obtener todos los archivos jpg o png
        $archivos = glob($carpeta . "*.{jpg,jpeg,png}", GLOB_BRACE);

        if (!empty($archivos)) {
            // Tomamos el primer archivo encontrado
            $ruta_imagen = base_url("archivos/campanias/{$campania->id}/" . basename($archivos[0]));
        }
    }
}
?>

<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="form-row">
                    <div class="form-group col-lg-8">
                        <label for="campania_nombre">Nombre *</label>
                        <input type="text" class="form-control" id="campania_nombre" value="<?php echo (isset($campania) ? $campania->nombre : ''); ?>">
                    </div>

                    <div class="form-group col-lg-4">
                        <label for="plantilla_whatsapp">Plantilla de WhatsApp *</label>
                        <select id="plantilla_whatsapp" class="form-control">
                            <option value="">Selecciona...</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-3">
                        <label for="fecha_inicio">Fecha de inicio *</label>
                        <input type="date" class="form-control" id="fecha_inicio" value="<?php echo (isset($campania)) ? $campania->fecha_inicio : date('Y-m-d') ; ?>">
                    </div>

                    <div class="form-group col-lg-3">
                        <label for="fecha_finalizacion">Fecha finalización *</label>
                        <input type="date" class="form-control" id="fecha_finalizacion" value="<?php echo (isset($campania)) ? $campania->fecha_finalizacion : date('Y-m-d') ; ?>">
                    </div>
                    
                    <div class="form-group col-lg-6">
                        <label for="campania_descripcion">Descripción</label>
                        <input type="text" class="form-control" id="campania_descripcion" value="<?php echo (isset($campania) ? $campania->descripcion : '')?>" placeholder="Opcional">
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="campania_imagen">Imagen (opcional)</label>
                        <input type="file" class="form-control" id="campania_imagen" accept="image/png, image/jpeg">

                        <!-- Vista previa -->
                        <div class="text-center mt-2">
                            <?php if (!empty($ruta_imagen)) { ?>
                                <img id="vista_previa" src="<?php echo $ruta_imagen ?>" style="max-width:200px; max-height:200px; border-radius:6px; border:1px solid #ddd; padding:4px;">
                                <div class="mt-2">
                                    <button type="button" id="eliminar_imagen" class="btn btn-sm btn-outline-danger" title="Eliminar imagen">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            <?php } else { ?>
                                <img id="vista_previa" src="" class="d-none" style="max-width:200px; max-height:200px; border-radius:6px; border:1px solid #ddd; padding:4px;">
                                <div class="mt-2">
                                    <button type="button" id="eliminar_imagen" class="btn btn-sm btn-outline-danger d-none" title="Eliminar imagen">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="mensaje_campania">Mensaje que se enviará</label>
                        <textarea class="form-control" id="mensaje_campania" rows="5" disabled></textarea>
                    </div>
                </div>

                <button class="btn btn-info" onClick="javascript:history.back()">Volver</button>
                <button class="btn btn-success" onclick="javascript:guardarCampania()">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    // Si ya hay una imagen cargada, eliminarImagen debe ser false
    let eliminarImagen = <?php echo (!empty($ruta_imagen)) ? 'false' : 'true'; ?>;

    guardarCampania = async () => {
        let id = $("#campania_id").val()
        let archivo = $('#campania_imagen').prop('files')

        let camposObligatorios = [
            $("#fecha_inicio"),
            $("#fecha_finalizacion"),
            $("#campania_nombre"),
            $('#plantilla_whatsapp'),
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datos = {
            tipo: 'marketing_campanias',
            fecha_inicio: $("#fecha_inicio").val(),
            fecha_finalizacion: $("#fecha_finalizacion").val(),
            nombre: $("#campania_nombre").val(),
            descripcion: $("#campania_descripcion").val(),
            nombre_plantilla_whatsapp: $('#plantilla_whatsapp').val(),
        }

        // Crear o actualizar campaña
        if (id) {
            datos.id = id
            await consulta('actualizar', datos, false)
            agregarLog(105, `Campaña con id: ${id}`)
        } else {
            let respuesta = await consulta('crear', datos, false)
            id = respuesta.resultado
            $("#campania_id").val(id)
            agregarLog(104, `Campaña con id: ${id}`)
        }

        // Subir imagen solo si existe y no fue eliminada
        if (archivo.length > 0 && !eliminarImagen) {
            let extension = archivo[0].name.split('.').pop()
            let nombreArchivo = `imagen.${extension}`

            let imagen = new FormData()
            imagen.append('name', archivo[0], nombreArchivo)

            let peticion = new XMLHttpRequest()
            peticion.open('POST', `${$('#site_url').val()}marketing/subir_imagen/${id}`)
            peticion.send(imagen)

            peticion.onload = () => {
                let respuesta = JSON.parse(peticion.responseText)

                if (!respuesta.resultado) {
                    mostrarAviso('alerta', 'La campaña se guardó, pero la imagen no pudo subirse')
                    return
                }

                // Se agrega el nombre de la imagen en la campaña
                consulta('actualizar', {
                    id: id,
                    tipo: 'marketing_campanias',
                    nombre_imagen: nombreArchivo,
                })

                mostrarAviso('exito', 'Campaña guardada correctamente')
            }
        } else {
            mostrarAviso('exito', 'Campaña guardada correctamente')
        }
    }

    listarPlantillasWhatsapp = async () => {
        var registros = await consulta('obtener', {
            tipo: 'whatsapp_plantillas'
        })
        
        let plantillas = registros.resultado?.response?.data
        console.log(plantillas)

        for(plantilla in plantillas) {
            let nombre = plantillas[plantilla].name
            let componentes = plantillas[plantilla].components

            for(componente in componentes) {
                if(componentes[componente].type == 'BODY') var mensaje = componentes[componente].text
            }
            
            $(`#plantilla_whatsapp`).append(`<option value="${nombre}" data-mensaje="${mensaje}">${nombre}</option>`)
        }

        if($('#campania_id').val()) {
            $(`#plantilla_whatsapp`).val('<?php // echo $campania->nombre_plantilla_whatsapp; ?>')
            actualizarMensajePlantilla()
        } 
    }

    $().ready(() => {
        actualizarMensajePlantilla = () => $('#mensaje_campania').text($("#plantilla_whatsapp option:selected").attr('data-mensaje'))

        listarPlantillasWhatsapp()

        $("#plantilla_whatsapp").change(() => actualizarMensajePlantilla())

        // Vista previa de imagen
        $("#campania_imagen").on("change", function () {
            const archivo = this.files[0]
            if (!archivo) return
            const extensionesPermitidas = ['image/jpeg', 'image/png']

            if (!extensionesPermitidas.includes(archivo.type)) {
                mostrarAviso('alerta', 'Solo se permiten imágenes JPG o PNG')
                this.value = ''
                $("#vista_previa").addClass('d-none')
                $("#eliminar_imagen").addClass('d-none')
                return
            }

            const url = URL.createObjectURL(archivo)
            $("#vista_previa").attr("src", url).removeClass('d-none')
            $("#eliminar_imagen").removeClass('d-none')

            eliminarImagen = false
        })

        // Eliminar imagen seleccionada
        $("#eliminar_imagen").on("click", function () {
            // Se agrega confirmación antes de eliminarla
            Swal.fire({
                title: '¿Eliminar imagen?',
                text: 'Esta acción eliminará la imagen seleccionada',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (!result.isConfirmed) return
                let id = $("#campania_id").val()

                // Si la campaña ya existe, eliminar en servidor
                if (id) {
                    $.ajax({
                        url: `${$('#site_url').val()}marketing/eliminar_imagen`,
                        type: 'POST',
                        dataType: 'json',
                        data: { id: id },
                        success: function (respuesta) {

                            if (!respuesta.resultado) {
                                Swal.fire(
                                    'Error',
                                    'No se pudo eliminar la imagen',
                                    'error'
                                )
                                return
                            }

                            Swal.fire(
                                'Eliminada',
                                'La imagen fue eliminada correctamente',
                                'success'
                            )
                        }
                    })
                }

                // Limpieza visual
                $("#campania_imagen").val('')
                $("#vista_previa").attr("src", "").addClass('d-none')
                $("#eliminar_imagen").addClass('d-none')
                eliminarImagen = true
            })
        })
    })
</script>