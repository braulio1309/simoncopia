<?php 
$datos = [
    'cotizacion_id' => $cotizacion_id,
    'nit' => $nit
];

// Se obtienen los datos de la solicitud de cotización
$solicitud_cotizacion = $this->proveedores_model->obtener('proveedores_cotizaciones_solicitudes', ['id' => $cotizacion_id]);
$solicitud_detalle = $this->proveedores_model->obtener('proveedores_maestro_solicitudes_detalle', $datos);

// Se obtiene el detalle de la cotización, en caso de que el proveedor ya la haya hecho
$cotizacion_detalle = $this->proveedores_model->obtener('proveedores_cotizaciones_detalle', $datos);

echo "<input type='hidden' id='cotizacion_id' value='$cotizacion_id'>";
echo "<input type='hidden' id='proveedor_nit' value='$nit'>";
echo "<input type='hidden' id='solicitud_fecha_fin' value='$solicitud_cotizacion->fecha_fin'>";
if(!empty($cotizacion_detalle)) echo "<input type='hidden' id='cotizacion_detalle' value='1'>";
?>

<div class="block">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Productos requeridos en la solicitud de cotización</h1>
        </div>

        <div class="card mb-lg-0">
            <?php
            if(empty($solicitud_detalle)) { ?>
                <div class='container'>
                    <div class='alert alert-danger alert-lg mb-3 alert-dismissible fade show'>
                        Para la solicitud de cotización que elegiste, no hay productos disponibles de las marcas que distribuyes.
                    </div>
                </div>
            <?php } ?>

            <?php
            if($solicitud_cotizacion->activa == 0) { ?>
                <div class='container'>
                    <div class='alert alert-danger alert-lg mb-3 alert-dismissible fade show'>
                        La cotización ya está cerrada. Haz <a href="<?php echo site_url('proveedores'); ?>">clic aquí</a> para buscar una nueva cotización.
                    </div>
                </div>
            <?php } ?>

            <?php if(!empty($solicitud_detalle) && $solicitud_cotizacion->activa == 1) { ?>
                <div class="card-body card-body--padding--2">
                    <div class="alert alert-info alert-lg mb-3 alert-dismissible fade show">
                        Indícanos el precio que ofreces al lado de cada producto que tengas disponible.
                    </div>

                    <div class="row" id="contenedor_cotizacion_detalle">
                        <div class="table-responsive p-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Id</th>
                                        <th class="text-center">Marca</th>
                                        <th class="text-center">Referencia</th>
                                        <th class="text-center">Descripción</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-center">Precio inicial</th>
                                        <th class="text-center">Descuento</th>
                                        <th class="text-center">Valor final</th>
                                        <th class="text-center">Observación</th>
                                    </tr>
                                </thead>
                                <tbody id="listado_cotizacion_detalle">
                                    <?php
                                    $contador = 1;

                                    // Se recorren los registros que se deben diligenciar
                                    foreach ($solicitud_detalle as $registro) {
                                            $crear = empty($cotizacion_detalle);
                                            $index = array_search($registro->producto_id, array_column($cotizacion_detalle, "producto_id"));
                                            if (!$crear && gettype($index) != "integer") continue;

                                            $precio_item = (!$crear) ? floatval($cotizacion_detalle[$index]->precio) : 0;
                                            $descuento_porcentaje_item = (!$crear) ? $cotizacion_detalle[$index]->descuento_porcentaje : 0;
                                            $descuento_valor_item = (!$crear) ? $cotizacion_detalle[$index]->descuento_valor : 0;
                                            $observacion_item = (!$crear) ? $cotizacion_detalle[$index]->observacion : '';
                                            $precio_total = (!$crear) ? $precio_item - $descuento_valor_item : 0;
                                            $cotizacion_detalle_id = (!$crear) ? $cotizacion_detalle[$index]->id : 0;
                                    ?>
                                        <tr id="listado_producto_<?php echo $registro->id; ?>"
                                            data-producto-id="<?php echo $registro->producto_id; ?>"
                                            data-solicitud-detalle-id="<?php echo $registro->id; ?>"
                                            <?php if (!isset($cotizacion_detalle)) echo " data-cotizacion-detalle-id='$cotizacion_detalle_id' "?>
                                        />
                                            <td><?php echo $contador++; ?></td>
                                            <td><?php echo $registro->producto_id; ?></td>
                                            <td><?php echo $registro->producto_marca; ?></td>
                                            <td><?php echo $registro->producto_referencia; ?></td>
                                            <td><?php echo $registro->producto_notas; ?></td>
                                            <td class="text-center"><?php echo $registro->cantidad; ?></td>
                                            <td>
                                                <input type="text" class="form-control text-right" data-id="<?php echo $registro->id; ?>" id="precio_<?php echo $registro->id; ?>" value="<?php echo $precio_item; ?>" style="width: 120px;">
                                                <small style="color: grey; font-size: 0.7em">Antes de IVA</small>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control text-right" data-id="<?php echo $registro->id; ?>" id="descuento_<?php echo $registro->id; ?>" value="<?php echo $descuento_porcentaje_item; ?>" style="width: 60px;">
                                                <small style="color: grey; font-size: 0.7em">En porcentaje</small>

                                                <!-- Almacenamiento del descuento en pesos -->
                                                <input type="hidden" class="form-control text-right" data-id="<?php echo $registro->id; ?>" id="descuento_valor_<?php echo $registro->id; ?>" value="0">

                                            </td>
                                            <td>
                                                <input type="text" class="form-control text-right" id="precio_total_<?php echo $registro->id; ?>" value="<?php echo number_format($precio_total, 0, '', '.'); ?>" style="width: 120px;" disabled>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" id="observacion_<?php echo $registro->id; ?>" value="<?php echo $observacion_item; ?>" style="width: 120px;">
                                            </td>
                                        </tr>

                                        <script>
                                            // Por defecto se formatea el campo
                                            $(`#precio_<?php echo $registro->id; ?>`).val(formatearNumero(<?php echo $precio_item; ?>))
                                        </script>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <button class="btn btn-success btn-lg w-100" onClick="javascript:guardarCotizacionProductos()">Enviar cotización</button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    guardarCotizacionProductos = async() => {
        let cotizacionProductos = await obtenerCotizacionProductos()

        let actualizar = ($('#cotizacion_detalle').val()) ? true : false

        var datos = {
            tipo: 'proveedores_cotizaciones_detalle',
            cotizacion_id: <?php echo $cotizacion_id; ?>,
            proveedor_nit: <?php echo $nit; ?>,
            registros: cotizacionProductos
        }

        let datosLog = {
            id: '<?php echo $cotizacion_id; ?>',
            nit: '<?php echo $nit; ?>',
        }

        // Se valida que la fecha no haya vencido
        const fechaActual = new Date()
        const fechaFinSolicitud = new Date($('#solicitud_fecha_fin').val().replace(' ', 'T'));
       
        // Cuando ya se haya vencido la solicitud
        if(fechaFinSolicitud < fechaActual) {
            mostrarAviso('error', 'La solicitud de precios ya veció y no podemos recibir más cotizaciones o cambios. Muchas gracias por tu interés. Te esperamos para la siguiente solicitud de precios.', 30000)
            return false
        }

        if (actualizar) {
            agregarLog(64, JSON.stringify(datos))
            let resultado = await consulta('crear', datos)
        } else {
            agregarLog(63, JSON.stringify(datos))
            let resultado = await consulta('crear', datos)
            location.reload()
        }
    }

    obtenerCotizacionProductos = () => {
        var cotizacionProductos = []

        $("#listado_cotizacion_detalle tr").each(function () {
            var id = $(this).data("cotizacion-detalle-id")
            var solicitudDetalleId = $(this).data("solicitud-detalle-id")
            var observacion = $(`#observacion_${solicitudDetalleId}`).val()

            var datos = {
                cotizacion_id: $("#cotizacion_id").val(),
                proveedor_nit: $("#proveedor_nit").val(),
                producto_id: $(this).data("producto-id"),
                precio: parseFloat($(`#precio_${solicitudDetalleId}`).val().replace(/\./g, '')),
                descuento_valor: parseFloat($(`#descuento_valor_${solicitudDetalleId}`).val().replace(/\./g, '')),
                descuento_porcentaje: parseFloat($(`#descuento_${solicitudDetalleId}`).val().replace(/\./g, '')),
                precio_final: parseFloat($(`#precio_total_${solicitudDetalleId}`).val().replace(/\./g, '')),
                observacion: observacion,
            }

            cotizacionProductos.push(datos)
        })

        return cotizacionProductos
    }

    $().ready(() => {
        // Si el precio cambia
        $(`input[id^='precio_'], input[id^='descuento_']`).on('keyup', function() {
            let registroId = $(this).attr('data-id')

            // Cálculo de valores
            let precioInicial = parseFloat($(`#precio_${registroId}`).val().replace(/\./g, ''))
            let descuento = parseFloat($(`#descuento_${registroId}`).val())
            let valorDescuento = Math.floor(precioInicial * (descuento / 100))
            let valorTotal = precioInicial - valorDescuento
            
            // Los valores se establecen en los campos
            $(`#precio_${registroId}`).val(formatearNumero(precioInicial))
            $(`#descuento_${registroId}`).val(descuento)
            $(`#descuento_valor_${registroId}`).val(valorDescuento)
            $(`#precio_total_${registroId}`).val(formatearNumero(valorTotal))
        })
    })
</script>