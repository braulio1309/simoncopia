<?php
if($this->uri->segment(4)) {
    $tercero = $this->configuracion_model->obtener('contactos', ['id' => $this->uri->segment(4)]);
    echo "<input type='hidden' id='contacto_id' value='$tercero->id' />";
}
?>

<div class="card">
    <div class="card-header">
        <h5>Datos generales</h5>
    </div>
    <div class="card-divider"></div>
    <div class="card-body card-body--padding--2">
        <form class="row no-gutters">
            <div class="col-12">
                <div class="form-row">
                    <div class="form-group col-12">
                        <label for="contacto_modulo_id">Selecciona el módulo al cual quieres asociar los contactos *</label>
                        <select id="contacto_modulo_id" class="form-control">
                            <option value="">Selecciona...</option>
                            <?php foreach($this->configuracion_model->obtener('modulos', ['aplica_importacion_contactos' => 1]) as $modulo) echo "<option value='$modulo->id'>$modulo->descripcion</option>"; ?>
                        </select>
                    </div>

                    <div class="form-group col-12">
                        <label for="contacto_datos">Ingresa los datos en cada línea (NIT, Teléfono y correo electrónico), separados por coma</label>
                        <div class="categories-list__item-products">
                            Ejemplo:<br>
                            <span>81100512, 3135823366, juan.perez@empresa.com&#10;</span><br>
                            <span>1017552663, 3178896655, gerencia@empresa.com</span><br>
                        </div>

                        <textarea class="form-control" id="contacto_datos" rows="10" autofocus></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <a class="btn btn-info" href="<?php echo site_url("configuracion/contactos/ver"); ?>">Volver</a>
                        <button type="submit" class="btn btn-success">Guardar datos</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    guardarContactos = async() => {
        let camposObligatorios = [
            $('#contacto_datos'),
            $('#contacto_modulo_id'),
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let lineas = $('#contacto_datos').val().split("\n")
        let datos = []

        Swal.fire({
            title: 'Creando contactos...',
            text: 'El sistema está eligiendo los contactos que no existen para crearlos.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        for (let i = 0; i < lineas.length; i++) {
            // Elimina espacios en blanco al principio y al final    
            let linea = lineas[i].trim()

            if(!linea) continue

            // Dividir la línea en número de documento y teléfono
            let partes = linea.split(',')
            let numeroDocumento = partes[0].replace(/\s/g, '')
            let telefono = partes[1].replace(/\s/g, '')
            let email = partes[2].replace(/\s/g, '')
            
            let contactoExistente = await consulta('obtener', {tipo: 'contactos', numero: telefono, nit: numeroDocumento})
            
            // Si el contacto no existe, lo almacena en el arreglo
            if(contactoExistente.length == 0) {
                // Agregar los datos al arreglo
                datos.push({
                    nit: numeroDocumento,
                    numero: telefono,
                    email: email,
                    modulo_id: $('#contacto_modulo_id').val(),
                    fecha_creacion: '<?php echo date('Y-m-d H:i:s'); ?>',
                    usuario_id: <?php echo $this->session->userdata('usuario_id'); ?>,
                })
            }
        }

        // Si no hay ningún registro por crear
        if(datos.length == 0) {
            mostrarAviso('alerta', `No hay ningún registro para crear o los que se diligenciaron, ya existen en la base de datos.`)
            return false
        }

        if(datos.length > 400) {
            mostrarAviso('alerta', `Se pueden crear como máximo 400 registros por cada importación.`)
            return false
        }

        let totalCreados = await consulta('crear', {...datos, tipo: 'terceros_contactos'}, false)
        Swal.close()    

        mostrarAviso('exito', `Se crearon ${totalCreados.resultado} contactos exitosamente.`)
        agregarLog(37, `${totalCreados.resultado} registros`)
        $('#contacto_datos, #contacto_modulo_id').val('')
    }

    $().ready(() => {
        $('form').submit(e => {
            e.preventDefault()

            guardarContactos()
        })
    })
</script>