<?php
if($this->uri->segment(4)) {
    $contacto = $this->configuracion_model->obtener('contactos', ['id' => $this->uri->segment(4)]);
    echo "<input type='hidden' id='contacto_id' value='$contacto->id' />";
}
?>

<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container container--max--lg">
        <div class="row">
            <div class="col-md-6 d-flex mt-4 mt-md-0">
                <div class="card flex-grow-1 mb-0 ml-0 ml-lg-3 mr-0 mr-lg-4">
                    <div class="card-body card-body--padding--2">
                        <h3 class="card-title">Gestión de contacto telefónico</h3>
                        <form>
                            <div class="form-group">
                                <label for="contacto_nit">Número de documento</label>
                                <input id="contacto_nit" type="text" class="form-control" value="<?php echo $contacto->nit; ?>" autofocus>
                            </div>
                            <div class="form-group">
                                <label for="contacto_telefono">Teléfono</label>
                                <input id="contacto_telefono" type="text" class="form-control" value="<?php echo $contacto->numero; ?>">
                            </div>
                            <div class="form-group">
                                <label for="contacto_email">Correo electrónico</label>
                                <input id="contacto_email" type="text" class="form-control" value="<?php echo $contacto->email; ?>">
                            </div>
                            <div class="form-group mb-0">
                                <a class="btn btn-info mt-3" href="<?php echo site_url("configuracion/contactos/ver"); ?>">Volver</a>
                                <button type="submit" class="btn btn-primary mt-3">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    actualizarContacto = async() => {
        let camposObligatorios = [
            $('#contacto_nit'),
            $('#contacto_telefono'),
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datos = {
            tipo: 'terceros_contactos',
            nit: $('#contacto_nit').val(),
            numero: $('#contacto_telefono').val(),
            numero: $('#contacto_telefono').val(),
            email: $('#contacto_email').val(),
            fecha_actualizacion: '<?php echo date('Y-m-d H:i:s'); ?>'
        }

        datos.id = $('#contacto_id').val()

        await consulta('actualizar', datos)

        agregarLog(38, `Registro con id ${datos.id}`)
        location.href = '<?php echo site_url("configuracion/contactos/ver"); ?>'
    }

    $().ready(() => {
        $('form').submit(e => {
            e.preventDefault()

            actualizarContacto()
        })
    })
</script>