<?php
$beneficio = $this->marketing_model->obtener('marketing_beneficios', ['id' => $id]);
$alcance_tipo = isset($beneficio->alcance_tipo) ? $beneficio->alcance_tipo : 'toda_la_tienda';
?>
<input type="hidden" id="beneficio_id" value="<?php echo $beneficio->id; ?>">

<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Alcance del beneficio: <?php echo htmlspecialchars($beneficio->nombre, ENT_QUOTES, 'UTF-8'); ?></h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container-fluid">
        <div class="row">
            <!-- Columna izquierda: selector de alcance y búsqueda -->
            <div class="col-lg-3">
                <div class="card mb-3">
                    <div class="card-body card-body--padding--2">
                        <div class="form-group">
                            <label for="alcance_tipo">Alcance del beneficio *</label>
                            <select id="alcance_tipo" class="form-control">
                                <option value="toda_la_tienda" <?php echo ($alcance_tipo == 'toda_la_tienda' ? 'selected' : ''); ?>>Toda la tienda</option>
                                <option value="productos_especificos" <?php echo ($alcance_tipo == 'productos_especificos' ? 'selected' : ''); ?>>Productos específicos</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Formulario de búsqueda (solo para productos específicos) -->
                <div class="card" id="panel_buscar_productos" style="display: <?php echo ($alcance_tipo == 'productos_especificos' ? 'block' : 'none'); ?>;">
                    <div class="card-body card-body--padding--2">
                        <form id="formulario_buscar_productos">
                            <div class="form-group">
                                <label for="buscar_producto">Buscar por nombre, referencia, marca... *</label>
                                <input type="text" class="form-control" id="buscar_producto" autofocus>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block" id="btn_buscar_producto">Buscar</button>
                        </form>
                        <div class="mt-2" id="contenedor_mensaje_producto"></div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: resultados -->
            <div class="col-lg-9">
                <!-- Mensaje para toda la tienda -->
                <div id="panel_toda_tienda" style="display: <?php echo ($alcance_tipo == 'toda_la_tienda' ? 'block' : 'none'); ?>;">
                    <div class="card mb-3">
                        <div class="card-body card-body--padding--2 text-center p-5">
                            <i class="fa fa-store fa-3x text-success mb-3"></i>
                            <h4 class="text-success">Este beneficio aplica a toda la tienda</h4>
                            <p class="text-muted">El beneficio se aplicará a todos los productos disponibles.</p>
                        </div>
                    </div>
                </div>

                <!-- Panel de productos específicos -->
                <div id="panel_productos_especificos" style="display: <?php echo ($alcance_tipo == 'productos_especificos' ? 'block' : 'none'); ?>;">
                    <!-- Resultados de búsqueda -->
                    <div class="card mb-3">
                        <div class="card-body card-body--padding--1">
                            <div class="tag-badge tag-badge--new badge_formulario badge_formulario_azul">
                                Resultados de búsqueda
                            </div>
                            <div id="contenedor_resultado_productos" style="height: 30vh; overflow-y: auto;"></div>
                        </div>
                    </div>

                    <!-- Productos seleccionados -->
                    <div class="card">
                        <div class="card-body card-body--padding--1">
                            <div class="tag-badge tag-badge--new badge_formulario badge_formulario_azul">
                                Productos seleccionados para este beneficio
                            </div>
                            <div id="contenedor_productos_seleccionados" style="height: 30vh; overflow-y: auto;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-info" onClick="javascript:history.back()">Volver</button>
            <button class="btn btn-success" onclick="javascript:guardarAlcanceTipo(true)">Guardar</button>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    guardarAlcanceTipo = async (redirigir = false) => {
        let beneficioId = $("#beneficio_id").val()
        let alcanceTipo = $("#alcance_tipo").val()

        await consulta('actualizar', {
            tipo: 'marketing_beneficios',
            id: beneficioId,
            alcance_tipo: alcanceTipo
        }, false)

        if (alcanceTipo === 'toda_la_tienda') {
            $("#panel_buscar_productos").hide()
            $("#panel_toda_tienda").show()
            $("#panel_productos_especificos").hide()
        } else {
            $("#panel_buscar_productos").show()
            $("#panel_toda_tienda").hide()
            $("#panel_productos_especificos").show()
            listarProductosSeleccionados()
        }

        mostrarAviso('exito', 'Alcance guardado correctamente')
        if (redirigir) {
            setTimeout(() => window.location.href = `${$('#site_url').val()}marketing/beneficios/ver`, 1500)
        }
    }

    listarProductosSeleccionados = () => {
        let beneficioId = $("#beneficio_id").val()
        cargarInterfaz('marketing/beneficios/alcance_seleccionados', 'contenedor_productos_seleccionados', {
            beneficio_id: beneficioId
        })
    }

    agregarProductoAlBeneficio = async (productoId, referencia, descripcion, btn) => {
        let beneficioId = $("#beneficio_id").val()
        let fila = $(btn).closest('tr')
        let valorTipo = fila.find('.valor_tipo_input').val()
        let valor = fila.find('.valor_input').val()

        let respuesta = await consulta('crear', {
            tipo: 'marketing_beneficios_productos',
            beneficio_id: beneficioId,
            producto_id: productoId,
            valor_tipo: valorTipo,
            valor: valor
        }, false)

        if (respuesta && respuesta.resultado) {
            mostrarAviso('exito', `Producto "${referencia}" agregado al beneficio`)
            listarProductosSeleccionados()
        } else {
            mostrarAviso('error', `No se pudo agregar el producto "${referencia}"`)
        }
    }

    $().ready(function() {
        let beneficioId = $("#beneficio_id").val()

        // Mostrar/ocultar paneles al cambiar el select y guardar automáticamente
        $("#alcance_tipo").change(function() {
            if ($(this).val() === 'productos_especificos') {
                $("#panel_buscar_productos").show()
                $("#panel_toda_tienda").hide()
                $("#panel_productos_especificos").show()
            } else {
                $("#panel_buscar_productos").hide()
                $("#panel_toda_tienda").show()
                $("#panel_productos_especificos").hide()
            }
            guardarAlcanceTipo()
        })

        // Si ya es productos específicos, cargar los seleccionados
        if ($("#alcance_tipo").val() === 'productos_especificos') {
            listarProductosSeleccionados()
        }

        // Formulario de búsqueda de productos
        $("#formulario_buscar_productos").submit(async function(evento) {
            evento.preventDefault()

            let buscarProducto = $("#buscar_producto")

            if (!validarCamposObligatorios([buscarProducto])) return false

            $("#btn_buscar_producto").addClass('btn-loading').attr('disabled', true)
            $("#contenedor_mensaje_producto").html(`<button class='btn btn-muted btn-loading btn-xs btn-icon'></button> Buscando coincidencias con ${buscarProducto.val()}...`)
            $("#btn_buscar_producto").removeClass('btn-loading').attr('disabled', false)

            let datos = {
                tipo: 'productos',
                busqueda: buscarProducto.val(),
                mostrar_agotados: true,
            }

            cargarInterfaz('marketing/beneficios/alcance_productos', 'contenedor_resultado_productos', datos)
        })
    })
</script>
