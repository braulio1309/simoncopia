<?php
// Obtenemos las facturas del cliente que seleccionó para pagar
$facturas = $this->clientes_model->obtener('clientes_facturas', [
    'numero_documento' => $datos['numero_documento'],
    'pendientes' => true,
    'mostrar_estado_cuenta'=> true,
]);
?>

<style>
    #tabla_facturas_seleccionadas {
        font-size: 0.8em;
        font-family: Futura;
    }

    .encabezado {
        background-color: #19287F;
        color: white;
    }
</style>

<div class="address-card__row" id="mensaje_inicial">
    <div class="alert alert-primary">
        Selecciona una o varias facturas a pagar, haciendo clic en la casilla de la columna <i class="fa fa-plus"></i>
    </div>
</div>

<div class="table-responsive">
    <table class="table-bordered" id="<?php echo "tabla_facturas_seleccionadas"; ?>">
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
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right" id="subtotal_formato"></td>
                <td class="text-right" id="total_descuento_formato"></td>
                <td class="text-right" id="total_pago_formato"></td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    var detalleFactura = [];
    
    const tablaFacturasSeleccionadas = new DataTable('#tabla_facturas_seleccionadas', {
        info: true,
        // ordering: true,
        // order: [[5, 'desc']],
        // stateSave: true,
        paging: false,
        scrollX: false,
        scrollY: '320px',
        searching: true,
        language: {
            url: '<?php echo base_url(); ?>js/dataTables_espanol.json',
            decimal: ',',
            thousands: '.'
        },
        scrollCollapse: true,
        // Define una configuración específica para varias columnas
        columnDefs: [{
            targets: [7, 8, 9], // índices de las columnas a formatear
            className: 'dt-right',
            render: function (datosMoneda) {
                return new Intl.NumberFormat('es-CO', {
                    style: 'currency',
                    currency: 'COP',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                    useGrouping: true
                }).format(datosMoneda)
            }
        }],
        columns: [
            { title: 'Quitar' },
            { title: 'Sede' },
            { title: 'Doc', className: 'dt-right' },
            { title: 'Cuota', className: 'dt-right' },
            { title: 'Fecha fact' },
            { title: 'Fecha vcto' },
            { title: 'Días venc', className: 'dt-right' },
            { title: 'Valor doc', className: 'dt-right' },
            { title: 'Abonos', className: 'dt-right' },
            { title: 'Saldo inicial', className: 'dt-right' },
            { title: 'Sucursal' },
            { title: 'Valor a pagar' },
            { title: 'Descuento' },
            { title: 'Valor neto a pagar' },
        ],
        createdRow: function (row, data, dataIndex) {
            row.id = 'id_factura_seleccionada_' + $('#id_registro').val();
        }
    })

    limpiarFormulario = () => {
        // Se limpian los campos
        $("#monto").val(0)
        $("#cuenta").val('')
        $("#referencia").val('')
        tablaFacturasSeleccionadas.clear().draw()
        $("#estado_cuenta_archivos").attr('value', '')
        $("#contenedor_imagen").html("📋 Pega aquí una imagen (Ctrl + V)")
    }

    agregarFactura = datos => {
        Swal.fire({
            title: 'Cargando información de la factura...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        let datosConsulta = {
            tipo: 'movimientos_contables',
            documento_cruce: datos.documento_cruce,
            numero_documento: datos.numero_documento,
            id_sucursal: datos.id_sucursal,
        }

        // Se cargan los movimientos de la factura
        consulta('obtener', datosConsulta, false)
        .then(movimientosFactura => {
            // Se insertan en la base de datos todos los movimientos obtenidos de la factura
            consulta('crear', {tipo: 'clientes_facturas_movimientos', valores: movimientosFactura.detalle.Table}, false)
            .then(() => {
                // Se obtiene de los movimientos creados el valor bruto de ingreso ventas 19%
                consulta('obtener', {tipo: 'cliente_factura_movimiento', f200_nit: datos.numero_documento, f350_consec_docto: datos.documento_cruce}, false)
                .then(resultadoMovimiento => {
                    // Se obtiene el valor bruto de ese movimiento, para calcular descuentos posteriormente
                    valorBruto = (resultadoMovimiento.f351_valor_cr) ? resultadoMovimiento.f351_valor_cr : 0

                    Swal.close()

                    // Se oculta la celda
                    $(`#factura_${datos.id}`).hide()

                    // Se oculta el mensaje inicial
                    $('#mensaje_inicial').hide()

                    let diasVencido = (datos.dias_vencido > 0) ? datos.dias_vencido : 0
                    
                    // Si el valor es negativo, no debe dejarse editar
                    let desactivado = (datos.valor < 0) ? 'disabled' : ''

                    // Si es pago con comprobante, el descuento podrá editarse
                    let descuentoDesactivado = ($('#pago_con_comprobante').val()) ? '' : 'disabled'

                    // Al campo oculto se le asigna el id, para que luego sea asignado al nuevo registro en la tabla de seleccionados
                    $('#id_registro').val(datos.id)

                    tablaFacturasSeleccionadas.row.add([
                        `<button type="button" class="vehicles-list__item-remove" onClick="removerFactura(${datos.id})">
                            <svg width="16" height="16">
                                <path d="M2,4V2h3V1h6v1h3v2H2z M13,13c0,1.1-0.9,2-2,2H5c-1.1,0-2-0.9-2-2V5h10V13z" />
                            </svg>
                        </button>`,
                        datos.sede,
                        datos.documento_cruce,
                        datos.numero_cuota,
                        datos.fecha_documento,
                        datos.fecha_vencimiento,
                        diasVencido,
                        datos.valor_aplicado, // Valor doc
                        datos.valor_documento, // Abonos
                        datos.total_cop, // Saldo
                        datos.tipo_credito, // Sucursal
                        // Valor a pagar
                        `
                        <label class="text-success" style="font-size: 0.8em">Puedes modificar el valor a pagar</label>
                        <input
                            type="text"
                            id="${datos.id}"
                            data-id="${datos.id}"
                            data-documento_cruce_numero="${datos.documento_cruce}"
                            data-numero_cuota="${datos.numero_cuota}"
                            data-centro_operativo="${datos.centro_operativo}"
                            data-documento_cruce_tipo="${datos.documento_cruce_tipo}"
                            data-documento_cruce_fecha="${datos.documento_cruce_fecha}"
                            data-descuento_porcentaje="${datos.descuento_porcentaje}"
                            data-valor_bruto="${valorBruto}"
                            class="form-control valor_pago_factura border-success"
                            style="text-align: right; width: 150px;"
                            max="${parseFloat(datos.valor)}"
                            value="${parseFloat(datos.valor)}"
                            ${desactivado}
                            data-valor_aplicado="${datos.valor_aplicado}"
                            data-valor_documento="${datos.valor_documento}"
                            data-total_cop="${datos.total_cop}"
                        >`,
                        // Descuento. El campo se habilita cuando es un pago con comprobante
                        `<input type='text' id="descuento_${datos.id}" class="form-control valor_descuento" placeholder='Descuento' style="text-align: right; width: 150px;" value="0" ${descuentoDesactivado}>`,
                        // Valor final
                        `<input type='text' id="valor_completo_${datos.id}" class="form-control" style="text-align: right; width: 150px;" disabled>`
                    ]).draw()

                    calcularTotal()

                    /**
                     * Esta sección calcula la última cuota, quitándole el valor de más
                     * para que dé el mismo valor del comprobante
                     */
                    let valorReciente = parseFloat(datos.valor) // Valor de la última cuota seleccionada
                    let valorFinal = parseFloat($('#total_pago').val()) // Valor acumulado de todas las facturas seleccionadas
                    let valorFaltante = parseFloat($('#comprobante_valor_faltante').text().replace(/\./g, '')) // Valor faltante por seleccionar
                    let valorCuotaReal = valorReciente + valorFaltante // El valor en el que va a quedar la cuota

                    // Si el valor faltante es negativo (superó al monto del comprobante), se establece el nuevo valor de la cuota
                    if(valorFaltante < 0) {
                        datos.valor = valorCuotaReal
                        mostrarAviso('info', `El valor a pagar de esta factura se ajustó a $${formatearNumero(valorCuotaReal)} para igualarse al monto del recibo.`, 10000)
                    } else {
                        mostrarAviso('exito', '!Bien! En la parte inferior podrás ver tus facturas seleccionadas para pago', 10000)
                    }
                    
                    // Por defecto se formatea el campo
                    $(`#${datos.id}`).val(formatearNumero(datos.valor))
                    
                    calcularTotal()

                    // Si el valor pagado o el descuento (para pagos con comprobantes) cambia
                    $(`.valor_pago_factura, .valor_descuento`).on('keyup', function() {
                        // Se formatea el campo
                        $(this).val(formatearNumero($(this).val()))

                        calcularTotal()
                    })
                })
            })
        })
    }

    calcularTotal = () => {
        var subtotal = 0
        var totalDescuento = 0
        var total = 0
        var detalleFactura = []
        var montoComprobante = ($("#monto").val()) ? parseFloat($("#monto").val().replace(/\./g, '')) : 0 // Monto especificado cuando se sube un comprobante

        $(`.valor_pago_factura`).each(function() {
            let valorAPagar = parseFloat($(this).val().replace(/\./g, ''))
            let valorBruto = parseFloat($(this).attr('data-valor_bruto'))
            let valorTotal = parseFloat($(this).attr('max'))
            let porcentajeDescuento = parseFloat($(this).attr('data-descuento_porcentaje'))

            // Si el pago es con comprobante
            if($('#pago_con_comprobante').val()) {
                // El valor del descuento es lo indicado en el campo Descuento
                valorDescuento = parseFloat($(`#descuento_${$(this).attr('data-id')}`).val().replace(/\./g, '')) || 0
            } else {
                // El descuento es calculado fijo
                valorDescuento = (valorAPagar == valorTotal) ? Math.floor(valorBruto * (porcentajeDescuento / 100)) : 0

                // Se muestra en el campo el valor del descuento
                $(`#descuento_${$(this).attr('data-id')}`).val(formatearNumero(valorDescuento))
            }

            totalDescuento += valorDescuento
            subtotal += valorAPagar
            total += parseFloat($(this).val().replace(/\./g, ''))
            total -= valorDescuento

            $(`#valor_completo_${$(this).attr('data-id')}`).val(formatearNumero((valorAPagar - valorDescuento)))

            detalleFactura.push({
                documento_cruce_numero: $(this).attr('data-documento_cruce_numero'),
                cuota_numero: $(this).attr('data-numero_cuota'),
                documento_cruce_tipo: $(this).attr('data-documento_cruce_tipo'),
                documento_cruce_fecha: $(this).attr('data-documento_cruce_fecha'),
                subtotal: valorAPagar,
                descuento: valorDescuento,
                centro_operativo: $(this).attr('data-centro_operativo'),
                valor_saldo_inicial: $(this).attr('data-total_cop'),
                valor_abonos: $(this).attr('data-valor_documento'),
                valor_factura: $(this).attr('data-valor_aplicado'),
            })
        })

        // Se formatea el campo
        $('#subtotal_formato').text(formatearNumero(subtotal))
        $('#total_descuento_formato').text(formatearNumero(totalDescuento))
        $('#total_pago_formato').text(formatearNumero(total))
        $('#total_pago').val(total)

        // Sección para subida de comprobante
        $('#valor_total_seleccionadas').text(formatearNumero(total))

        // Valor que falta para poder subir el comprobante
        let valorFaltanteComprobante = montoComprobante - total
        $('#comprobante_valor_faltante').text(formatearNumero(valorFaltanteComprobante))

        return detalleFactura
    }

    removerFactura = async(id) => {
        // El registro en el mini carrito se quita
        tablaFacturasSeleccionadas.row(`#id_factura_seleccionada_${id}`).remove().draw(false)

        // Se vuelve a agregar en la tabla
        $(`#factura_${id}`).show()

        calcularTotal()
    }

    vaciarCarritoEstadoCuenta = () => {
        $('#contenedor_lista_carrito').html('')
        $('#estado_cuenta_archivos').val('')
        calcularTotal()
    }

    $().ready(() => {
        $('#contenedor_mensaje_carga').html('')

        // Cuando el monto total sea modificado,
        // se recalcula el total
        $(`#monto`).on('keyup', function() {
            // Se formatea el campo
            $(this).val(formatearNumero($(this).val()))

            calcularTotal()
        })

        $('#btn_pago_en_linea').on('click', e => {
            // Se activa el spinner
            $('#btn_pago_en_linea').addClass('btn-loading').attr('disabled', true)

            guardarReciboEstadoCuenta(true)
            
            // Se desactiva el spinner después de cierto tiempo
            setTimeout(() => $('#btn_pago_en_linea').removeClass('btn-loading').attr('disabled', false), 1000)
        })
    })
</script>