<style>
    #tabla_productos_seleccionados tbody * {
        font-size: 0.9em;
        padding: 5px;
        vertical-align: middle;
    }
</style>

<?php $productos_seleccionados = $this->marketing_model->obtener('marketing_beneficios_productos', ['beneficio_id' => $datos['beneficio_id']]); ?>

<?php if (empty($productos_seleccionados)) { ?>
    <div class="text-center p-4 text-muted">
        <i class="fa fa-info-circle"></i> No hay productos seleccionados. Use la búsqueda para agregar productos a este beneficio.
    </div>
<?php } else { ?>
    <div class="table-responsive">
        <table class="table-striped table-bordered" id="tabla_productos_seleccionados">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Referencia</th>
                    <th class="text-center">Descripción</th>
                    <th class="text-center">Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($productos_seleccionados as $item) { ?>
                    <tr>
                        <td class="text-center"><?php echo $item->producto_id; ?></td>
                        <td class="text-left"><?php echo $item->referencia; ?></td>
                        <td class="text-left"><?php echo $item->notas; ?></td>
                        <td class="text-center">
                            <button
                                type="button"
                                onclick="javascript:eliminarProductoDelBeneficio(<?php echo $item->id; ?>)"
                                class="btn btn-sm btn-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<script>
    eliminarProductoDelBeneficio = async (itemId) => {
        let confirmado = await confirmar('eliminar', '¿Está seguro de eliminar este producto del beneficio?')
        if (!confirmado) return

        await consulta('eliminar', {
            tipo: 'marketing_beneficios_productos',
            id: itemId
        }, false)

        mostrarAviso('exito', 'Producto eliminado del beneficio')
        listarProductosSeleccionados()
    }
</script>
