<?php
// 1. Lógica para DETECTAR si es EDICIÓN o CREACIÓN
$id_importacion = $this->uri->segment(3); 
$titulo = "Nueva Importación";
$importacion = null;
$data_pago = null; 

if($id_importacion) {
    // Cargar datos de la importación
    $importacion = $this->importaciones_model->obtener('importaciones', ['id' => $id_importacion]);
    
    if($importacion) {
        $titulo = "Editar Importación #" . str_pad($importacion->id, 3, '0', STR_PAD_LEFT);
        
        // Cargar datos del pago de anticipo si existe (Traemos solo el último)
        $this->db->limit(1);
        $this->db->order_by('id', 'DESC');
        $data_pago = $this->importaciones_model->obtener('importaciones_pagos', ['importacion_id' => $importacion->id]);
        
        // Ajuste por si el modelo devuelve array en lugar de objeto directo
        if(is_array($data_pago) && !empty($data_pago)) {
            $data_pago = $data_pago[0];
        }
    }
}
?>

<input type="hidden" id="importacion_id" value="<?php echo ($importacion) ? $importacion->id : ''; ?>" />
<input type="hidden" id="pago_id" value="<?php echo ($data_pago) ? $data_pago->id : ''; ?>" />

<div class="card">
    <div class="card-header">
        <h5><?php echo $titulo; ?></h5>
    </div>
    <div class="card-divider"></div>
    <div class="card-body card-body--padding--2">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="tag-badge tag-badge--new badge_formulario mb-3">
                    Información del Proveedor y Orden
                </div>
            </div>

            <div class="form-group col-md-3">
                <label for="numero_orden_compra">Número Orden de Compra *</label>
                <input type="text" class="form-control" id="numero_orden_compra" value="<?php echo ($importacion) ? $importacion->numero_orden_compra : ''; ?>" autofocus>
            </div>

            <div class="form-group col-md-1">
                <label>&nbsp;</label>
                <button class="btn btn-primary btn-block" href="javascript:;" onClick="javascript:buscarOrdenCompra()">
                    <i class="fa fa-search"></i>
                </button>
            </div>

            <div class="form-group col-md-4">
                <label for="nit_proveedor_search" class="text-primary">NIT (Validación Anticipo)</label>
                <input type="text" class="form-control" id="nit_proveedor_search" autocomplete="off" value="<?php echo ($importacion) ? $importacion->nit : ''; ?>" disabled>
                <small class="text-muted">El sistema buscará si este proveedor requiere anticipo.</small>
            </div>

            <div class="form-group col-md-4">
                <label for="razon_social">Proveedor (Razón Social) *</label>
                <input type="text" class="form-control" id="razon_social" value="<?php echo ($importacion) ? $importacion->razon_social : ''; ?>" disabled>
            </div>

            <div class="form-group col-md-4">
                <label for="contacto_principal">Contacto Principal</label>
                <input type="text" class="form-control" id="contacto_principal" value="<?php echo ($importacion) ? $importacion->contacto_principal : ''; ?>" placeholder="Persona de contacto">
            </div>

            <div class="form-group col-md-4">
                <label for="email_contacto">Email de Contacto</label>
                <input type="email" class="form-control" id="email_contacto" value="<?php echo ($importacion) ? $importacion->email_contacto : ''; ?>" placeholder="correo@proveedor.com">
            </div>

            <div class="form-group col-md-4">
                <label for="telefono_contacto">Teléfono de Contacto</label>
                <input type="text" class="form-control" id="telefono_contacto" value="<?php echo ($importacion) ? $importacion->telefono_contacto : ''; ?>">
            </div>

            <div class="form-group col-md-12">
                <label for="direccion">Dirección Física</label>
                <textarea class="form-control" id="direccion" rows="1" placeholder="Dirección completa..."><?php echo ($importacion) ? $importacion->direccion : ''; ?></textarea>
            </div>

            <div class="col-12 mb-3 mt-2">
                <div class="tag-badge tag-badge--new badge_formulario badge_formulario_azul mb-3">
                    Datos Logísticos
                </div>
            </div>

            <div class="form-group col-md-3">
                <label for="pais_origen">País de Origen *</label>
                <select id="pais_origen" class="form-control" data-valor-actual="<?php echo ($importacion) ? $importacion->pais_origen : ''; ?>">
                    <option value="">Cargando países...</option>
                </select>
            </div>

            <div class="form-group col-md-3">
                <label for="fecha_estimada_llegada">Fecha Estimada Llegada</label>
                <input type="date" class="form-control" id="fecha_estimada_llegada" value="<?php echo ($importacion) ? date('Y-m-d', strtotime($importacion->fecha_estimada_llegada)) : ''; ?>">
            </div>

            <div class="form-group col-md-3">
                <label for="fecha_ingreso_siesa">Fecha Ingreso SIESA</label>
                <input type="date" class="form-control" id="fecha_ingreso_siesa" value="<?php echo ($importacion && $importacion->fecha_ingreso_siesa) ? date('Y-m-d', strtotime($importacion->fecha_ingreso_siesa)) : ''; ?>">
                <small class="form-text text-muted">Opcional</small>
            </div>

            <div class="form-group col-md-3">
                <label for="bl_awb">BL / AWB</label>
                <input type="text" class="form-control" id="bl_awb" value="<?php echo ($importacion) ? $importacion->bl_awb : ''; ?>" placeholder="Bill of Lading / Air Waybill">
            </div>

            <div class="form-group col-md-3">
                <label for="estado_id">Estado Actual</label>
                <select id="estado_id" class="form-control" data-valor-actual="<?php echo ($importacion) ? $importacion->importacion_estado_id : ''; ?>">
                    <option value="">Cargando estados...</option>
                </select>
            </div>

            <div class="col-12 mb-3 mt-2">
                <div class="tag-badge tag-badge--new badge_formulario badge_formulario_azul mb-3">
                    Valores y Moneda
                </div>
            </div>

            <div class="form-group col-md-3">
                <label for="moneda_preferida">Moneda *</label>
                <select id="moneda_preferida" class="form-control">
                    <option value="USD" <?php echo ($importacion && $importacion->moneda_preferida == 'USD') ? 'selected' : ''; ?>>Dólar (USD)</option>
                    <option value="COP" <?php echo ($importacion && $importacion->moneda_preferida == 'COP') ? 'selected' : ''; ?>>Peso Col (COP)</option>
                    <option value="EUR" <?php echo ($importacion && $importacion->moneda_preferida == 'EUR') ? 'selected' : ''; ?>>Euro (EUR)</option>
                </select>
            </div>

            <div class="form-group col-md-3">
                <label for="valor_total">Valor Total (Moneda Extranjera) *</label>
                <input type="number" step="0.01" class="form-control" id="valor_total" value="<?php echo ($importacion) ? $importacion->valor_total : ''; ?>" placeholder="0.00">
            </div>

            <div class="form-group col-md-3">
                <label for="valor_trm">Valor TRM</label>
                <input type="number" step="0.01" class="form-control" id="valor_trm" value="<?php echo ($importacion) ? $importacion->valor_trm : ''; ?>" placeholder="0.00">
            </div>

            <div class="form-group col-md-3">
                <label for="valor_total_cop">Valor Aprox (COP)</label>
                <input type="number" step="0.01" class="form-control" id="valor_total_cop" value="<?php echo ($importacion) ? $importacion->valor_total_cop : ''; ?>" placeholder="0">
            </div>

            <div class="form-group col-md-3">
                <label for="impuestos_dian">Impuestos DIAN</label>
                <input type="number" step="0.01" class="form-control" id="impuestos_dian" value="<?php echo ($importacion) ? $importacion->impuestos_dian : ''; ?>" placeholder="0.00">
            </div>

            <div class="col-12 mt-3">
                <div class="tag-badge tag-badge--new badge_formulario badge_formulario_azul mb-3">
                    Observaciones y Condiciones
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="condiciones_pago">Condiciones de Pago</label>
                    <textarea class="form-control" id="condiciones_pago" rows="3" placeholder="Ej: 50% anticipo..."><?php echo ($importacion) ? $importacion->condiciones_pago : ''; ?></textarea>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="notas_internas">Notas Internas</label>
                    <textarea class="form-control" id="notas_internas" rows="3"><?php echo ($importacion) ? $importacion->notas_internas : ''; ?></textarea>
                </div>
            </div>

            <div class="col-12 text-right mt-3">
                <a class="btn btn-secondary" href="<?php echo site_url("importaciones"); ?>">Cancelar</a>
                <button type="button" class="btn btn-primary" onClick="guardarImportacion()">
                    <i class="fas fa-save"></i> GUARDAR DATOS
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    buscarOrdenCompra = async () => {
        Swal.fire({
            title: 'Estamos Buscando la orden de compra en el ERP...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        let estructuraOrdenCompra = $('#numero_orden_compra').val().split('-')

        let datosOrdenCompra = {
            tipo: 'ordenes_compra',
            centro_operativo: estructuraOrdenCompra[0],
            tipo_documento: estructuraOrdenCompra[1],
            numero_orden: estructuraOrdenCompra[2],
        }

        // Se consulta en el ERP el pedido
        var resultadoOrdenCompra = await consulta('obtener', datosOrdenCompra)

        // Si no se encontró el pedido
        if(resultadoOrdenCompra.codigo == 1) {
            mostrarAviso('alerta', `La orden de compra ${$('#numero_orden_compra').val()} no se encontró en el ERP. Por favor, verifica nuevamente o ponte en contacto con nosotros.`, false)
            return false
        }

        let ordenCompra = resultadoOrdenCompra.detalle.Table[0]

        // Ponemos los valores en los campos requeridos
        $('#nit_proveedor_search').val(ordenCompra.f200_nit_prov)
        $('#razon_social').val(ordenCompra.f200_razon_social_prov)

        Swal.close()
    }

    guardarImportacion = async () => {
        // 1. Validar campos visuales obligatorios
        let camposObligatorios = [
            $('#numero_orden_compra'),
            $('#razon_social'),
            $('#valor_total'),
            $('#moneda_preferida'),
            $('#pais_origen')
        ];

        if (!validarCamposObligatorios(camposObligatorios)) return false;

        Swal.fire({ title: 'Procesando...', text: 'Validando reglas de negocio...', didOpen: () => { Swal.showLoading() } });

        // =========================================================
        // A. LÓGICA AUTOMÁTICA DE ANTICIPO (Busca por NIT)
        // =========================================================
        let nitBuscar = $('#nit_proveedor_search').val();
        let porcentajeAutomatico = 0;
        let requiereAnticipoAuto = 0; // 0 = No, 1 = Si

        // Si el usuario escribió un NIT, verificamos en el maestro
        if(nitBuscar && nitBuscar.length > 0) {
            try {
                // Hacemos una petición AJAX síncrona (esperamos respuesta)
                // Asegúrate de tener la función buscar_configuracion_anticipo en tu controlador
                const configAnticipo = await new Promise((resolve) => {
                    $.post('<?php echo site_url("importaciones/buscar_configuracion_anticipo"); ?>', 
                        { nit: nitBuscar }, 
                        (data) => {
                            try { resolve(JSON.parse(data)); } catch(e) { resolve(null); }
                        }
                    );
                });

                // Si encontramos datos en el maestro
                if (configAnticipo && parseFloat(configAnticipo.porcentaje) > 0) {
                    porcentajeAutomatico = parseFloat(configAnticipo.porcentaje);
                    requiereAnticipoAuto = 1;
                    console.log(`Sistema: Anticipo automático detectado para NIT ${nitBuscar}: ${porcentajeAutomatico}%`);
                }

            } catch (e) {
                console.warn("No se pudo validar el NIT, se guardará sin anticipo automático.", e);
            }
        }
        // =========================================================

        // 2. Preparar datos
        let valorTotal = parseFloat($('#valor_total').val()) || 0;
        let fechaSiesa = $('#fecha_ingreso_siesa').val() || null;

        var datos = {
            id: $('#importacion_id').val(),
            tipo: 'importaciones', 
            
            // Datos básicos
            numero_orden_compra: $('#numero_orden_compra').val(),
            razon_social:        $('#razon_social').val(), 
            // NOTA: El NIT de búsqueda NO se guarda en la tabla importaciones, solo sirvió para la lógica
            
            contacto_principal:  $('#contacto_principal').val(), 
            email_contacto:      $('#email_contacto').val(),     
            telefono_contacto:   $('#telefono_contacto').val(),  
            direccion:           $('#direccion').val(),          
            
            pais_origen:         $('#pais_origen').val(),
            fecha_estimada_llegada: $('#fecha_estimada_llegada').val(),
            fecha_ingreso_siesa:    fechaSiesa,                  
            bl_awb:              $('#bl_awb').val(),
            importacion_estado_id:  $('#estado_id').val(),
            
            moneda_preferida:    $('#moneda_preferida').val(),
            valor_total:         valorTotal,
            valor_total_cop:     parseFloat($('#valor_total_cop').val()) || 0,
            impuestos_dian:      parseFloat($('#impuestos_dian').val()) || 0, 
            valor_trm:           parseFloat($('#valor_trm').val()) || 0,
            
            // --- ASIGNACIÓN AUTOMÁTICA ---
            requiere_anticipo:   requiereAnticipoAuto, 
            porcentaje_anticipo: porcentajeAutomatico,
            
            condiciones_pago:    $('#condiciones_pago').val(),   
            notas_internas:      $('#notas_internas').val()
        }

        let idImportacion = $('#importacion_id').val();
        let pagoIdExistente = $('#pago_id').val();

        try {
            // 3. Guardar la Importación
            let respuestaImp = null;
            if (idImportacion && idImportacion !== "") {
                // Obtener datos actuales antes de actualizar para comparar cambios
                let datosAnteriores = await consulta('obtener', {
                    tipo: 'importaciones',
                    id: idImportacion
                }, false);
                
                // Actualizar la importación
                await consulta('actualizar', datos);
                respuestaImp = { resultado: { resultado: idImportacion } };
                
                // Detectar y registrar cambios en bitácora
                if (datosAnteriores && datosAnteriores.resultado && datosAnteriores.resultado[0]) {
                    let anterior = datosAnteriores.resultado[0];
                    let cambios = [];
                    
                    // Comparar campos importantes
                    if (anterior.numero_orden_compra != datos.numero_orden_compra) 
                        cambios.push('OC: ' + anterior.numero_orden_compra + ' → ' + datos.numero_orden_compra);
                    if (anterior.razon_social != datos.razon_social) 
                        cambios.push('Proveedor: ' + anterior.razon_social + ' → ' + datos.razon_social);
                    if (anterior.pais_origen != datos.pais_origen) 
                        cambios.push('País: ' + anterior.pais_origen + ' → ' + datos.pais_origen);
                    if (anterior.fecha_estimada_llegada != datos.fecha_estimada_llegada) 
                        cambios.push('Fecha llegada: ' + anterior.fecha_estimada_llegada + ' → ' + datos.fecha_estimada_llegada);
                    if (anterior.importacion_estado_id != datos.importacion_estado_id) 
                        cambios.push('Estado: ' + anterior.importacion_estado_id + ' → ' + datos.importacion_estado_id);
                    if (anterior.moneda_preferida != datos.moneda_preferida) 
                        cambios.push('Moneda: ' + anterior.moneda_preferida + ' → ' + datos.moneda_preferida);
                    if (parseFloat(anterior.valor_total) != parseFloat(datos.valor_total)) 
                        cambios.push('Valor: ' + anterior.valor_total + ' → ' + datos.valor_total);
                    if (parseFloat(anterior.valor_trm) != parseFloat(datos.valor_trm)) 
                        cambios.push('TRM: ' + anterior.valor_trm + ' → ' + datos.valor_trm);
                    if (anterior.bl_awb != datos.bl_awb) 
                        cambios.push('BL/AWB: ' + anterior.bl_awb + ' → ' + datos.bl_awb);
                    if (anterior.contacto_principal != datos.contacto_principal) 
                        cambios.push('Contacto: ' + anterior.contacto_principal + ' → ' + datos.contacto_principal);
                    if (anterior.email_contacto != datos.email_contacto) 
                        cambios.push('Email: ' + anterior.email_contacto + ' → ' + datos.email_contacto);
                    if (anterior.condiciones_pago != datos.condiciones_pago) 
                        cambios.push('Condiciones pago modificadas');
                    if (anterior.notas_internas != datos.notas_internas) 
                        cambios.push('Notas internas modificadas');
                    
                    // Si hubo cambios, registrar en bitácora
                    if (cambios.length > 0) {
                        let datosBitacoraEdicion = {
                            tipo: 'importaciones_bitacora',
                            importacion_id: idImportacion,
                            usuario_id: '<?php echo $this->session->userdata("usuario_id"); ?>',
                            observaciones: 'Importación editada - Cambios: ' + cambios.join(', ')
                        };
                        await consulta('crear', datosBitacoraEdicion, false);
                    }
                }
            } else {
                datos.fecha_creacion = '<?php echo date("Y-m-d H:i:s"); ?>';
                datos.usuario_id = '<?php echo $this->session->userdata("usuario_id"); ?>';
                respuestaImp = await consulta('crear', datos, false);
                idImportacion = respuestaImp.resultado.resultado;
                
                // Crear registro en bitácora para CREACIÓN
                let datosBitacoraCreacion = {
                    tipo: 'importaciones_bitacora',
                    importacion_id: idImportacion,
                    usuario_id: '<?php echo $this->session->userdata("usuario_id"); ?>',
                    observaciones: 'Importación creada - OC: ' + $('#numero_orden_compra').val() + ', Proveedor: ' + $('#razon_social').val() + ', Valor: ' + $('#moneda_preferida').val() + ' ' + valorTotal
                };
                await consulta('crear', datosBitacoraCreacion, false);
            }

            // 4. Lógica de Pago Automático (Si el sistema decidió que lleva anticipo)
            if (requiereAnticipoAuto === 1 && porcentajeAutomatico > 0 && idImportacion) {
                
                let montoAnticipo = valorTotal * (porcentajeAutomatico / 100);
                
                let datosPago = {
                    tipo: 'importaciones_pagos',
                    importacion_id: idImportacion,
                    estado_id: 1, // 1 = Pendiente
                    observaciones: 'Anticipo generado automáticamente por Maestro (' + porcentajeAutomatico + '%)',
                    valor_moneda_extranjera: montoAnticipo
                };

                if (pagoIdExistente && pagoIdExistente !== "") {
                    datosPago.id = pagoIdExistente;
                    await consulta('actualizar', datosPago, false);
                    
                    // Crear registro en bitácora para ACTUALIZACIÓN DE PAGO
                    let datosBitacoraPagoActualizado = {
                        tipo: 'importaciones_bitacora',
                        importacion_id: idImportacion,
                        usuario_id: '<?php echo $this->session->userdata("usuario_id"); ?>',
                        observaciones: 'Pago automático actualizado - Anticipo del ' + porcentajeAutomatico + '% (' + $('#moneda_preferida').val() + ' ' + montoAnticipo.toFixed(2) + ')'
                    };
                    await consulta('crear', datosBitacoraPagoActualizado, false);
                } else {
                    datosPago.fecha_creacion = '<?php echo date("Y-m-d H:i:s"); ?>';
                    datosPago.usuario_id = '<?php echo $this->session->userdata("usuario_id"); ?>';
                    await consulta('crear', datosPago, false);
                    
                    // Crear registro en bitácora para PAGO AUTOMÁTICO
                    let datosBitacoraPago = {
                        tipo: 'importaciones_bitacora',
                        importacion_id: idImportacion,
                        usuario_id: '<?php echo $this->session->userdata("usuario_id"); ?>',
                        observaciones: 'Pago automático creado - Anticipo del ' + porcentajeAutomatico + '% (' + $('#moneda_preferida').val() + ' ' + montoAnticipo.toFixed(2) + ') - ' + datosPago.observaciones
                    };
                    await consulta('crear', datosBitacoraPago, false);
                }
                
                Swal.close();
                mostrarAviso('exito', 'Importación guardada. Anticipo del '+porcentajeAutomatico+'% generado.');
            } else {
                Swal.close();
                mostrarAviso('exito', 'Importación guardada correctamente.');
            }

            // 5. Redireccionar
            setTimeout(() => {
                window.location.href = '<?php echo site_url("importaciones"); ?>';
            }, 1500);

        } catch(error) {
            Swal.close();
            console.error("ERROR:", error);
            alert("Error al procesar: " + error);
        }
    }

    $(document).ready(async function() {
        try {
            await listarDatos('pais_origen', { tipo: 'paises' }, $('#pais_origen').data('valor-actual'));
            await listarDatos('estado_id', { tipo: 'importaciones_estados' }, $('#estado_id').data('valor-actual'));
        } catch (e) {
            console.warn("Error cargando países", e);
        }
    });
</script>