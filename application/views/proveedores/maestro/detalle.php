<?php
if(isset($id)) {
    $proveedor_marca = $this->proveedores_model->obtener('proveedores_marcas', ['id' => $id]);
    echo "<input type='hidden' id='proveedor_marca_id' value='$proveedor_marca->id' />";
}
?>

<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="form-row">
                    <div class="form-group col-12 col-sm-6">
                        <label for="codigo_marca">Marca *</label>
                        <select id="codigo_marca" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach ($this->configuracion_model->obtener("marcas") as $marca) echo "<option value='$marca->codigo'>$marca->nombre</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-12 col-sm-6">
                        <label for="proveedor_nit">Proveedor *</label>
                        <select id="proveedor_nit" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach ($this->configuracion_model->obtener("terceros", ["f200_ind_proveedor" => 1]) as $tercero) echo "<option value='$tercero->f200_nit'>$tercero->f200_razon_social</option>"; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group mb-0 pt-3 mt-3">
                    <button class="btn btn-info" onClick="javascript:history.back()">Volver</button>
                    <button class="btn btn-success" onClick="javascript:guardarProveedoresMarcas()">Guardar datos</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<?php if (isset($proveedor_marca)) { ?>
    <script>
        $().ready(() => {
            $('#proveedor_nit').val('<?php echo $proveedor_marca->proveedor_nit; ?>')
            $('#codigo_marca').val('<?php echo $proveedor_marca->marca_codigo; ?>')
        })
    </script>
<?php } ?>

<script>
    guardarProveedoresMarcas = async () => {
        let camposObligatorios = [
            $('#proveedor_nit'),
            $('#codigo_marca')
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datosProveedoresMarcas = {
            tipo: 'proveedores_marcas',
            proveedor_nit: $('#proveedor_nit').val(),
            marca_codigo: $('#codigo_marca').val()
        }

        if(!$('#proveedor_marca_id').val()) {
            await consulta('crear', datosProveedoresMarcas)
            agregarLog(68)
        } else {
            datosProveedoresMarcas.id = $('#proveedor_marca_id').val()
            await consulta('actualizar', datosProveedoresMarcas)
            agregarLog(69, datosProveedoresMarcas.id)
        }
    }

    $().ready(() => {
        $('#proveedor_nit, #codigo_marca').select2()
    })
</script>