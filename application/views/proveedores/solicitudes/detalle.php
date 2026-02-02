<?php
if (isset($id)) {
    $solicitud = $this->proveedores_model->obtener('proveedores_cotizaciones_solicitudes', ['id' => $id]);
    $detalle = $this->proveedores_model->obtener('proveedores_cotizaciones_solicitudes_detalle', ['cotizacion_id' => $solicitud->id]);
    echo "<input type='hidden' id='proveedor_solicitud_id' value='$solicitud->id' />";
}
?>

<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="form-row">
                    <div class="form-group col-6 col-md-6">
                        <label for="fecha_inicio">Fecha de inicio *</label>
                        <input type="datetime-local" class="form-control" id="fecha_inicio" value="<?php echo (isset($solicitud)) ? $solicitud->fecha_inicio : date('Y-m-d') ; ?>">
                    </div>
                    <div class="form-group col-6 col-md-6">
                        <label for="fecha_finalizacion">Fecha finalización *</label>
                        <input type="datetime-local" class="form-control" id="fecha_finalizacion" value="<?php echo (isset($solicitud)) ? $solicitud->fecha_fin : date('Y-m-d') ; ?>">
                    </div>
                    <div class="form-group col-6 col-sm-8">
                        <label for="cotizacion_producto">Producto *</label>
                        <select id="cotizacion_producto" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach ($this->proveedores_model->obtener("productos") as $producto) echo "<option value='$producto->id'>$producto->valor</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-6 col-md-4">
                        <label for="cotizacion_cantidad">Cantidad *</label>
                        <input type="number" class="form-control" id="cotizacion_cantidad">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-info" onClick="javascript:history.back()">Volver</button>
                        <button class="btn btn-primary" onclick="javascript:agregarCotizacionProducto()">Agregar</button>
                    </div>
                </div>

                <div class="row <?php if (!isset($detalle) || isset($detalle) && !$detalle) echo 'd-none'; ?>" id="contenedor_cotizacion_detalle">
                    <div class="table-responsive mt-3 p-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Producto</th>
                                    <th scope="col">Cantidad</th>
                                    <th scope="col">Opciones</th>
                                </tr>
                            </thead>
                            <tbody id="listado_cotizacion_detalle">
                                <?php 
                                if (isset($detalle)) {
                                    foreach ($detalle as $registro) { 
                                        echo "
                                        <tr id='listado_producto_$registro->producto_id' data-producto-id='$registro->producto_id' data-creado='true'>
                                            <td>$registro->producto</td>
                                            <td>$registro->cantidad</td>
                                            <td>
                                                <a type='button' class='btn btn-sm btn-danger' href='javascript:eliminarCotizacionProducto($registro->id, $registro->producto_id)'>
                                                    Quitar
                                                </a>
                                            </td>
                                        </tr>
                                        ";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>

                        <button class="btn btn-success" onclick="javascript:guardarCotizacionProductos()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    agregarCotizacionProducto = () => {
        let camposObligatorios = [
            $('#cotizacion_producto'),
            $('#cotizacion_cantidad'),
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let $contenedor = $("#contenedor_cotizacion_detalle")
        let $listado = $("#listado_cotizacion_detalle")
        let productoId = $("#cotizacion_producto").val()
        let producto = $("#cotizacion_producto option:selected").text()
        let cantidad = $("#cotizacion_cantidad").val()

        $contenedor.removeClass("d-none")

        let productosAgregados = obtenerCotizacionProductos(true)
        let productoAgregado = productosAgregados.filter((registro) => registro.producto_id == productoId)

        if (productoAgregado.length > 0) {
            mostrarAviso('alerta', `¡El producto seleccionado ya fue agregado!`, 20000)
            return
        }

        $listado.append(`
            <tr id="listado_producto_${productoId}"
                data-producto-id="${productoId}"
                data-cantidad="${cantidad}"
            />
                <td>${producto}</td>
                <td>${cantidad}</td>
                <td>
                    <a type="button" class="btn btn-sm btn-danger" href="javascript:quitarCotizacionProducto(${productoId})">
                        Quitar
                    </a>
                </td>
            </tr>
        `)
    }

    eliminarCotizacionProducto = async (id, productoId) => {
        let eliminar = await consulta('eliminar', {tipo: 'proveedores_cotizaciones_solicitudes_detalle', id: id})

        if (eliminar) {
            quitarCotizacionProducto(productoId)
        }
    }

    guardarCotizacionProductos = async() => {
        let id = $("#proveedor_solicitud_id").val()

        let camposObligatorios = [
            $("#fecha_inicio"),
            $("#fecha_finalizacion")
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let cotizacionProductos = obtenerCotizacionProductos()

        let datos = {
            tipo: 'proveedores_cotizaciones_solicitudes',
            fecha_inicio: $("#fecha_inicio").val(),
            fecha_fin: $("#fecha_finalizacion").val(),
            cotizacion_detalle: cotizacionProductos
        }

        if (!id) {
            await consulta('crear', datos)
            agregarLog(66)
        } else {
            datos.id = id
            await consulta('actualizar', datos)
            agregarLog(67, id)
        }
    }

    obtenerCotizacionProductos = (validar = false) => {
        let cotizacionProductos = []

        $("#listado_cotizacion_detalle tr").each(function () {
            let creado = $(this).data("creado")
            let cotizacionId = $("#proveedor_solicitud_id").val()

            let datos = {
                producto_id: $(this).data("producto-id"),
                cantidad: $(this).data("cantidad"),
                cotizacion_id: null
            }

            if (cotizacionId) datos.cotizacion_id = cotizacionId

            if (!creado || validar) cotizacionProductos.push(datos)
        })

        return cotizacionProductos
    }

    quitarCotizacionProducto = (productoId) => {
        let $contenedor = $("#contenedor_cotizacion_detalle")
        let $registro = $(`#listado_producto_${productoId}`)
        let numero = $("#listado_cotizacion_detalle tr").length

        if (numero === 0) $contenedor.addClass("d-none")

        $registro.remove()
    }

    $().ready(() => {
        $('#cotizacion_producto').select2()
    })
</script>