<?php
if(isset($id)) {
    $anticipo = $this->importaciones_model->obtener('importaciones_maestro_anticipos', ['id' => $id]);
    echo "<input type='hidden' id='anticipo_id' value='$anticipo->id' />";
}
?>

<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="form-row">
                    <div class="form-group col-12 col-sm-4">
                        <label for="proveedor">Proveedor *</label>
                        <select id="proveedor" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach ($this->configuracion_model->obtener("terceros", []) as $tercero): ?>
                                <option value="<?php echo $tercero->f200_razon_social; ?>" 
                                        data-nit="<?php echo $tercero->f200_nit; ?>">
                                    <?php echo $tercero->f200_razon_social; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-12 col-sm-4">
                        <label for="nit">NIT *</label>
                        <input type="text" id="nit" class="form-control" readonly>
                    </div>
                    <div class="form-group col-12 col-sm-4">
                        <label for="porcentaje">Porcentaje Anticipo (%) *</label>
                        <input type="number" id="porcentaje" class="form-control" step="0.01" min="0" max="100">
                    </div>
                </div>
                <div class="form-group mb-0 pt-3 mt-3">
                    <button class="btn btn-info" onClick="javascript:history.back()">Volver</button>
                    <button class="btn btn-success" onClick="javascript:guardarMaestroAnticipo()">Guardar datos</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<?php if (isset($anticipo)): ?>
    <script>
        $().ready(() => {
            $('#proveedor').val('<?php echo $anticipo->proveedor; ?>').trigger('change');
            $('#nit').val('<?php echo $anticipo->nit; ?>');
            $('#porcentaje').val('<?php echo $anticipo->porcentaje; ?>');
        })
    </script>
<?php endif; ?>

<script>
    $('#proveedor').on('change', function() {
        const nit = $(this).find(':selected').data('nit');
        $('#nit').val(nit || '');
    });

    guardarMaestroAnticipo = async () => {
        let camposObligatorios = [
            $('#nit'),
            $('#porcentaje')
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datos = {
            tipo: 'importaciones_maestro_anticipos',
            nit: $('#nit').val(),
            porcentaje: $('#porcentaje').val()
        }

        const idExistente = $('#anticipo_id').val();

        if(!idExistente) {
            await consulta('crear', datos)
        } else {
            datos.id = idExistente;
            await consulta('actualizar', datos)
        }
        
        location.href = '<?php echo site_url("importaciones/maestro"); ?>';
    }

    $().ready(() => {
        $('#proveedor').select2();
    })
</script>