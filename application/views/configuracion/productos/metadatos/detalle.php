<?php
if(isset($id)) {
    $producto_metadato = $this->productos_model->obtener('productos_metadatos', ['id' => $id]);
    echo "<input type='hidden' id='producto_metadato_id' value='$producto_metadato->id' />";
}
?>

<div class="block-space block-space--layout--after-header"></div>

<div class="block">
    <div class="container container--max--xl">
        <div class="card">
            <div class="card-header">
                <h5>Datos generales</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-body card-body--padding--2">
                <div class="row no-gutters">
                    <div class="col-12 col-lg-10 col-xl-8">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="metadato_producto">Producto *</label>
                                <select id="metadato_producto" class="form-control">
                                    <option value="">Seleccione...</option>
                                    <?php foreach($this->productos_model->obtener("productos") as $producto) echo "<option value='$producto->id'>$producto->id - $producto->notas</option>"; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="metadato_titulo">Título *</label>
                                <input type="text" class="form-control" id="metadato_titulo" value="<?php if(!empty($producto_metadato)) echo $producto_metadato->titulo; ?>">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="metadato_slug">Slug * </label>
                                <input type="text" class="form-control" id="metadato_slug" value="<?php if(!empty($producto_metadato)) echo $producto_metadato->slug; ?>">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="metadato_palabras_clave">Palabras clave * </label>
                                <textarea rows="3" class="form-control form-control-lg" id="metadato_palabras_clave"><?php if(!empty($producto_metadato)) echo $producto_metadato->palabras_clave; ?></textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="metadato_descripcion">Descripción * </label>
                                <textarea rows="3" class="form-control form-control-lg" id="metadato_descripcion"><?php if(!empty($producto_metadato)) echo $producto_metadato->descripcion; ?></textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="metadato_detalles_tecnicos">Detalles técnicos </label>
                                <textarea rows="3" class="form-control form-control-lg" id="metadato_detalles_tecnicos"><?php if(!empty($producto_metadato)) echo $producto_metadato->detalles_tecnicos; ?></textarea>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="metadato_garantia">Garantía </label>
                                <input type="text" class="form-control" id="metadato_garantia" value="<?php if(!empty($producto_metadato)) echo $producto_metadato->garantia; ?>">
                            </div>
                        </div>
                        <div class="form-group mb-0 pt-3 mt-3">
                            <button class="btn btn-info" onClick="javascript:history.back()">Volver</button>
                            <button class="btn btn-success" onClick="javascript:guardarProductosMetadatos()">Guardar datos</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<?php if (isset($producto_metadato)) { ?>
    <script>
        $().ready(() => {
            $('#metadato_producto').val(<?php echo $producto_metadato->producto_id; ?>)
        })
    </script>
<?php } ?>

<script>
    guardarProductosMetadatos = async () => {
        let camposObligatorios = [
            $('#metadato_producto'),
            $('#metadato_titulo'),
            $('#metadato_slug'),
            $('#metadato_palabras_clave'),
            $('#metadato_descripcion')
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datosProductosMetadatos = {
            tipo: 'productos_metadatos',
            producto_id: $('#metadato_producto').val(),
            titulo: $('#metadato_titulo').val(),
            slug: $('#metadato_slug').val(),
            palabras_clave: $('#metadato_palabras_clave').val(),
            descripcion: $('#metadato_descripcion').val(),
            detalles_tecnicos: $('#metadato_detalles_tecnicos').val(),
            garantia: $('#metadato_garantia').val(),
        }

        if(!$('#producto_metadato_id').val()) {
            await consulta('crear', datosProductosMetadatos)
        } else {
            datosProductosMetadatos.id = $('#producto_metadato_id').val()
            await consulta('actualizar', datosProductosMetadatos)
        }
    }

    $().ready(() => {
        $('#metadato_producto').select2()
    })
</script>