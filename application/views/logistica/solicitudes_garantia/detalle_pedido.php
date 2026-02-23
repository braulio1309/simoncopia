<?php
$pedido_detalle = json_decode($datos['pedido']);

// Ordenamiento de los datos
sort($pedido_detalle);

if(isset($datos['solicitud']) && $datos['solicitud']) $solicitud = json_decode(json_encode($datos['solicitud']));

$tabla = 
"<table class='table-striped table-bordered w-100'>
    <thead>
        <th class='text-center'>Ítem</th>
        <th class='text-center'>Referencia</th>
        <th class='text-center'>Descripción</th>
        <th class='text-center'>Cantidad comprada</th>
    </thead>
    <tbody>
";

$lista_productos = '';
foreach ($pedido_detalle as $registro) {
    $producto = (object) $registro;
    $fecha_pedido = date('Y-m-d', strtotime($producto->f430_id_fecha));
    $vendedor_nit = $producto->f200_id_pedido_vend;
    $vendedor_razon_social = $producto->f200_razon_social_pedido_vend;
    $sede = $this->configuracion_model->obtener('centros_operacion', ['codigo' => $producto->f430_id_co_fact]);
    $tercero = $producto->f200_razon_social_pedido_fact;

    $tabla .= "
        <tr>
            <td class='text-right'>$producto->f120_id</td>
            <td>$producto->f120_referencia</td>
            <td>$producto->f120_descripcion</td>
            <td class='text-right'>$producto->f431_cant1_pedida</td>
        </tr>
    ";

    // Se agrega una opción en la lista desplegable
    $lista_productos .= "<option value='$producto->f120_id' data-cantidad='$producto->f431_cant1_pedida'>$producto->f120_descripcion</option>";
}

$tabla .= '</tbody></table>';
?>

<div class="form-row">
    <input type="hidden" class="form-control" id="solicitud_vendedor_nit" value="<?php echo $vendedor_nit; ?>" disabled>
    
    <div class="form-group col-lg-12">
        <label for="solicitud_cliente_razon_social">Razón social del cliente *</label>
        <input type="text" class="form-control" id="solicitud_cliente_razon_social" value="<?php echo $tercero; ?>" disabled>
    </div>

    <div class="form-group col-lg-3">
        <label for="solicitud_pedido_fecha">Fecha del pedido</label>
        <input type="text" class="form-control" id="solicitud_pedido_fecha" value="<?php echo $fecha_pedido; ?>" disabled>
    </div>

    <div class="form-group col-lg-5">
        <label for="solicitud_pedido_vendedor">Asesor comercial</label>
        <input type="text" class="form-control" id="solicitud_pedido_vendedor" value="<?php echo $vendedor_razon_social; ?>" disabled>
    </div>

    <div class="form-group col-lg-4">
        <label for="solicitud_pedido_sede">Sede de despacho</label>
        <input type="text" class="form-control" id="solicitud_pedido_sede" value="<?php echo $sede->nombre; ?>" disabled>
    </div>

    <div class="form-group col-lg-12">
        <?php echo $tabla; ?>
    </div>
</div>

<div class="form-row">
    <div class="tag-badge tag-badge--new badge_formulario mb-3 mt-2">
        3 - Identificación del producto
    </div>

    <div class="form-group col-lg-12">
        <label for="solicitud_producto_id">Selecciona el producto sobre el cuál vas a pedir la garantía *</label>
        <select id="solicitud_producto_id" class="form-control">
            <option value="">Selecciona un producto...</option>
            <?php echo $lista_productos; ?>
        </select>
    </div>

    <div class="form-group col-lg-6">
        <label for="solicitud_cantidad_reclamada">Cantidad reclamada *</label>
        <input type="number" class="form-control" id="solicitud_cantidad_reclamada" value="<?php if(!empty($solicitud)) echo $solicitud->producto_cantidad_reclamada; ?>">
    </div>

    <div class="form-group col-lg-6">
        <label for="solicitud_producto_serial">Número de serie del producto (opcional)</label>
        <input type="text" class="form-control" id="solicitud_producto_serial" value="<?php if(!empty($solicitud)) echo $solicitud->producto_numero_serie; ?>">
    </div>

    <div class="form-group col-lg-6">
        <label for="solicitud_fecha_instalacion">Fecha de instalación (opcional)</label>
        <input type="date" class="form-control" id="solicitud_fecha_instalacion" value="<?php if(!empty($solicitud)) echo $solicitud->producto_fecha_instalacion; ?>">
    </div>

    <div class="form-group col-lg-6">
        <label for="solicitud_uso">Kilometraje o tiempo de uso (opcional)</label>
        <input type="text" class="form-control" id="solicitud_uso" value="<?php if(!empty($solicitud)) echo $solicitud->producto_uso; ?>">
    </div>
</div>

<?php if(!empty($solicitud)) { ?>
    <script>
        var solicitud = JSON.parse('<?php echo addslashes(json_encode($solicitud)); ?>')
        
        $().ready(async () => {
            $("#solicitud_producto_id").val(solicitud.producto_id)
        })
    </script>
<?php } ?>

<script>
    $().ready(async () => {
        $('#solicitud_producto_id').select2()
    })
</script>