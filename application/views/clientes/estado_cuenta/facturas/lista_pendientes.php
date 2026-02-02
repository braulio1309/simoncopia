<?php
// Obtenemos las facturas del cliente pendientes por pagar
$facturas = $this->clientes_model->obtener('clientes_facturas', [
    'numero_documento' => $datos['numero_documento'],
    'pendientes' => true,
    'mostrar_estado_cuenta'=> true,
]);

$facturas_invalidas = $this->clientes_model->obtener('clientes_facturas', [
    'numero_documento' => $datos['numero_documento'],
    'pendientes' => true,
    'mostrar_alerta'=> true,
]);

$recibos_pendientes_por_aplicar = $this->configuracion_model->obtener('recibos_detalle', ['documento_numero' => $datos['numero_documento'], 'recibo_estado_id' => 3]);
?>

<!-- Si tiene alguna factura no válida para pago en línea -->
<?php if(count($facturas_invalidas) > 0) { ?>
    <div class="alert alert-danger alert-lg alert-dismissible fade show">
        Te informamos que el número de documento consultado presenta facturas que no se pueden reflejar en este módulo. Por favor, comunícate al teléfono 604 444 7232 - Extensión 110.
    </div>
<!-- Si no tiene facturas pendientes por pagar -->
<?php } elseif(empty($facturas)) { ?>
    <script>
        $('#contenedor_mensaje_carga').html('')
        $('#contenedor_carrito_facturas').hide('')
    </script>

    <div class="alert alert-success alert-lg alert-dismissible fade show">
        <?php
        echo 'No tienes ninguna factura pendiente por pagar';
        if(isset($opciones['busqueda'])) echo " con la búsqueda <b>{$opciones['busqueda']}</b>";
        exit();
        ?>
    </div>
<?php } ?>

<div class="mt-2 mb-2">
    <button class="btn btn-success btn-md btn-block" onClick="javascript:generarReporte('excel/facturas', {numero_documento: '<?php echo $datos['numero_documento']; ?>'})">
        <i class="fa fa-file-excel"></i>
        Descargar listado de facturas o estado de cuenta
    </button>

    <!-- Si tiene recibos pendientes por aplicar -->
    <?php if(!empty($recibos_pendientes_por_aplicar)) { ?>
        <button class="btn btn-secondary btn-md btn-block" id="btn_recibos_pendientes" onClick="javascript:cargarRecibosPorProcesar({numero_documento: '<?php echo $datos['numero_documento']; ?>'})">
            <i class="fa fa-search"></i>
            Ver recibos pendientes por procesar en el ERP
        </button>
    <?php } ?>
</div>

<style>
    #tabla_facturas_pendientes {
        font-size: 0.8em;
        font-family: Futura;
    }

    .encabezado {
        background-color: #19287F;
        color: white;
    }
</style>

<div class="table-responsive">
    <table class="table-striped table-bordered" id="<?php echo "tabla_facturas_pendientes"; ?>">
        <thead>
            <tr>
                <th class="text-center encabezado">
                    <b><i class="fa fa-plus fa-2x"></i></b>
                </th>
                <th class="text-center encabezado">Recibo pendiente</th>
                <th class="text-center encabezado">Sede</th>
                <th class="text-center encabezado">Doc</th>
                <th class="text-center encabezado">Cuota</th>
                <th class="text-center encabezado">Fecha fact</th>
                <th class="text-center encabezado">Fecha vcto</th>
                <th class="text-center encabezado">Días venc</th>
                <th class="text-center encabezado">Valor Doc</th>
                <th class="text-center encabezado">Abonos</th>
                <th class="text-center encabezado">Saldo</th>
                <th class="text-center encabezado">Retenciones</th>
                <th class="text-center encabezado">Sucursal</th>
                <th class="text-center encabezado">Tipo crédito</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_facturas = 0;
            $total_pagado = 0;
            $total_saldo = 0;
            $contador = 1;

            foreach($facturas as $factura) {
                $sucursal = explode(' ', $factura->RazonSocial_Sucursal);

                $total_facturas += $factura->ValorAplicado;
                $total_pagado += $factura->valorDoc;
                $total_saldo += $factura->totalCop;
            ?>
                <tr id="factura_<?php echo $factura->id; ?>">
                    <td>
                        <div class="form-check" style="height: 13px;">
                            <input class="form-check-input" type="radio" onClick="javascript:agregarFactura({
                                contador: '<?php echo $contador; ?>',
                                id: '<?php echo $factura->id; ?>',
                                documento_cruce: '<?php echo $factura->Nro_Doc_cruce; ?>',
                                numero_documento: '<?php echo $factura->Cliente; ?>',
                                fecha_documento: '<?php echo $factura->Fecha_doc_cruce; ?>',
                                dias_vencido: '<?php echo $factura->dias_vencido; ?>',
                                fecha_vencimiento: '<?php echo $factura->Fecha_venc; ?>',
                                numero_cuota: '<?php echo $factura->Nro_cuota; ?>',
                                centro_operativo: '<?php echo $factura->CentroOperaciones; ?>',
                                documento_cruce_tipo: '<?php echo $factura->Tipo_Doc_cruce; ?>',
                                documento_cruce_fecha: '<?php echo $factura->Fecha_doc_cruce; ?>',
                                valor: `<?php echo number_format($factura->totalCop, 0, '', ''); ?>`,
                                sede: `<?php echo $factura->centro_operativo; ?>`,
                                tipo_credito: `<?php echo $factura->nombre_homologado; ?>`,
                                descuento_porcentaje: `<?php echo $factura->descuento_porcentaje; ?>`,
                                id_sucursal: '<?php echo $factura->sucursal_id; ?>',
                                valor_aplicado: `<?php echo $factura->ValorAplicado; // Enviado para almacenar en el detalle del recibo ?>`,
                                valor_documento: `<?php echo $factura->valorDoc; // Enviado para almacenar en el detalle del recibo ?>`,
                                total_cop: `<?php echo $factura->totalCop; // Enviado para almacenar en el detalle del recibo ?>`,
                            })" style="padding: 2px 5px 2px 5px;">

                            
                        </div>
                    </td>
                    <td class="text-center">
                        <!-- Si está pendiente por aplicar -->
                        <?php if($factura->por_aplicar_archivo_pendiente) { ?>
                            <a class="mb-2" target="_blank" onClick="window.open('<?php echo base_url()."archivos/recibos/$factura->por_aplicar_archivo_pendiente"; ?>', this.target, 'width=800,height=600'); return false;" title="Ver comprobante" style="cursor: pointer;">
                                <i class="fa fa-search"></i>
                            </a>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo $factura->centro_operativo; ?>
                    </td>
                    <td class="text-right">
                        <a href="javascript:;" onClick="javascript:cargarProductos({
                            documento_cruce: '<?php echo $factura->Nro_Doc_cruce; ?>',
                            numero_documento: '<?php echo $factura->Cliente; ?>',
                            id_sucursal: '<?php echo $factura->sucursal_id; ?>',
                        });"><?php echo $factura->Nro_Doc_cruce; ?></a>
                    </td>
                    <td class="text-right"><?php echo $factura->Nro_cuota; ?></td>
                    <td><?php echo $factura->Fecha_doc_cruce; ?></td>
                    <td><?php echo $factura->Fecha_venc; ?></td>
                    <td class="text-right">
                        <?php
                        if($factura->dias_vencido > 0) {
                            echo "
                            <div class='status-badge status-badge--style--failure status-badge--has-text'>
                                <div class='status-badge__body'>
                                    <div class='status-badge__text'>$factura->dias_vencido</div>
                                </div>
                            </div>
                            ";
                        } else {
                            echo 0;
                        }
                        ?>
                    </td>
                    <td class="text-right"><?php echo formato_precio($factura->ValorAplicado);?></td><!-- Valor doc -->
                    <td class="text-right"><?php echo formato_precio($factura->valorDoc);?></td><!-- Abonos -->
                    <td class="text-right">
                        <?php
                        // Saldo
                        if($factura->totalCop < 0) {
                            echo "
                            <div class='status-badge status-badge--style--failure status-badge--has-text'>
                                <div class='status-badge__body'>
                                    <div class='status-badge__text'>".formato_precio($factura->totalCop)."</div>
                                </div>
                            </div>
                            ";
                        } else {
                            echo formato_precio($factura->totalCop);
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <a href="javascript:;" onClick="javascript:cargarMovimientos({
                            documento_cruce: '<?php echo $factura->Nro_Doc_cruce; ?>',
                            numero_documento: '<?php echo $factura->Cliente; ?>',
                            id_sucursal: '<?php echo $factura->sucursal_id; ?>',
                        });">Ver</a>
                    </td>
                    <td>
                        <?php echo substr($sucursal[0], 0, 10); ?>
                    </td>
                    <td><?php echo $factura->nombre_homologado; ?></td>
                </tr>
            <?php
                $contador++;
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right"><?php echo formato_precio($total_facturas); ?></td>
                <td class="text-right"><?php echo formato_precio($total_pagado); ?></td>
                <td class="text-right"><?php echo formato_precio($total_saldo); ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    $().ready(() => {
        new DataTable('#tabla_facturas_pendientes', {
            info: true,
            paging: false,
            scrollY: '320px',
            searching: true,
            language: {
                decimal: ',',
                thousands: '.',
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            scrollX: false,
            scrollCollapse: true,
        })
       
        $('#contenedor_mensaje_carga').html('')

        if($('#pago_con_comprobante').val() != 1) $("#btn_recibos_pendientes").hide()
            console.log($('#pago_con_comprobante').val())
    })
</script>