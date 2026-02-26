<style>
    #tabla_alcance_productos tbody * {
        font-size: 0.9em;
        padding: 3px;
    }
</style>

<?php $productos = $this->productos_model->obtener('productos', $datos); ?>

<div class="table-responsive">
    <table class="table-bordered" id="tabla_alcance_productos">
        <thead>
            <tr>
                <th class="text-center">Id</th>
                <th class="text-center">Referencia</th>
                <th class="text-center">Descripción</th>
                <th class="text-center">Marca</th>
                <th class="text-center">Tipo valor</th>
                <th class="text-center">Valor</th>
                <th class="text-center"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($productos as $producto) { ?>
                <tr>
                    <td class="text-center"><?php echo $producto->id; ?></td>
                    <td class="text-left"><?php echo $producto->referencia; ?></td>
                    <td class="text-left"><?php echo $producto->notas; ?></td>
                    <td class="text-center"><?php echo $producto->marca; ?></td>
                    <td class="text-center">
                        <select class="form-control form-control-sm valor_tipo_input" style="min-width:110px;">
                            <option value="nominal">Nominal</option>
                            <option value="porcentaje">Porcentaje</option>
                        </select>
                    </td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm valor_input" style="min-width:80px;" value="0" min="0" step="0.01">
                    </td>
                    <td class="text-center">
                        <button
                            type="button"
                            onclick="javascript:agregarProductoAlBeneficio(<?php echo $producto->id; ?>, <?php echo json_encode($producto->referencia); ?>, <?php echo json_encode($producto->notas); ?>, this)"
                            class="btn btn-success pl-3 pr-3">
                            +
                        </button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
    $().ready(() => {
        $('#contenedor_mensaje_producto').html('')

        new DataTable('#tabla_alcance_productos', {
            deferRender: true,
            fixedHeader: true,
            info: false,
            language: {
                decimal: ',',
                thousands: '.',
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            ordering: false,
            pageLength: 100,
            paging: false,
            processing: true,
            scrollCollapse: true,
            scroller: true,
            scrollX: false,
            scrollY: '215px',
            searching: false,
            stateSave: false,
        })
    })
</script>
