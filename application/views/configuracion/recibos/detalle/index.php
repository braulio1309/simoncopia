<?php
$recibo = $this->productos_model->obtener('recibo', ['token' => $this->uri->segment(4)]);
$mes_recibo = str_pad($recibo->mes, 2, '0', STR_PAD_LEFT);
$dia_recibo = str_pad($recibo->dia, 2, '0', STR_PAD_LEFT);
?>

<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container container--max--xl">
        <input type="hidden" id="total_recibo" value="<?php echo $recibo->valor; ?>">

        <div class="row">
            <div class="col-12 col-lg-3 d-flex">
                <div class="account-nav flex-grow-1">
                    <h4 class="account-nav__title">Opciones</h4>
                    <ul class="account-nav__list">
                        <!-- Resumen -->
                        <li class="recibos_items account-nav__item">
                            <a onClick="cargarInterfaz('configuracion/recibos/detalle/resumen', 'contenedor_recibos_detalle', {token: '<?php echo $this->uri->segment(4); ?>'})">Resumen</a>
                        </li>

                        <!-- Pago -->
                        <?php if($recibo->wompi_datos) { ?>
                            <li class="recibos_wompi account-nav__item">
                                <a onClick="cargarInterfaz('configuracion/recibos/detalle/wompi', 'contenedor_recibos_detalle', {token: '<?php echo $this->uri->segment(4); ?>'})">Pago</a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-lg-9 mt-4 mt-lg-0">
                <div id="contenedor_recibos_detalle"></div>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>
<div id="contenedor_modal_rechazo"></div>

<script>
    agregarCuenta = async(reciboId, datos = null) => {
        let id = Math.floor(Math.random() * 10000)

        $('#contenedor_cuentas').append(`<div id="${id}"></div>`)

        cargarInterfaz('configuracion/recibos/detalle/cuenta', id, {id: id, id_recibo: reciboId, cuenta: datos})
    }
    
    aprobarPago = async reciboId => {
        let confirmacion = await confirmar('aprobar', `¿Estás seguro de aprobar el pago?`)
        if(!confirmacion) return false

        Swal.fire({
            title: 'Creando documento contable en Siesa...',
            text: 'Por favor, espera. No cierres esta ventana ni refresques la página',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        await obtenerPromesa(`${$('#site_url').val()}interfaces/crear`, {tipo: 'factura_documento_contable', 'id_recibo': reciboId})
        .then(async pago => {
            console.log(pago)

            if(!pago.error) {
                mostrarAviso('error', `
                    Ocurrió un error al crear el documento contable en Siesa:
                    <pre>
                        ${JSON.stringify(pago.mensaje.detalle[0])}
                    </pre>
                `, 100000)

                return false
            }

            let datosRecibo = {
                tipo: 'recibos',
                id: reciboId,
                recibo_estado_id: 1,
                usuario_aprobacion_id: '<?php echo $this->session->userdata('usuario_id'); ?>',
            }

            let resultado = await consulta('actualizar', datosRecibo, false)

            if(resultado) {
                mostrarAviso('exito', 'El documento contable se asentó correctamente en Siesa.')

                // setTimeout(() => {
                //     location.href = `<?php // echo site_url("configuracion/recibos/ver/3"); ?>`;
                // }, 1000);\
            }
        }).catch(error => {
            mostrarAviso('error', 'Ocurrió un error al crear el documento contable en Siesa')
            return false
        })
    }

    /**
     * Se puede usar para varias cuentas
     */
    aprobarPagoVariasCuentas = async reciboId => {
        // Arreglo para enviar la imputación
        var movimientosContables = []
        var cuentasRecibo = []
        var totalImputado = 0
        var totalRecibo = parseFloat($('#total_recibo').val())
        var totalCuentas = 0
        var validacion = true

        $('.valor_cuenta_recibo').each(function() {
            totalCuentas++
            
            let id = $(this).attr('data-id')
            let auxiliar = $(`#cuenta_${id} option:selected`).attr('data-codigo') // Código de auxiliar de la cuenta seleccionada
            let valor = $(this).val().replace(/\./g, '')

            let camposObligatorios = [
                $(`#cuenta_${id}`),
                $(`#fecha_pago_${id}`),
                $(`#valor_${id}`),
            ]

            if (!validarCamposObligatorios(camposObligatorios)) validacion = false

            totalImputado += parseFloat(valor)
            let fechaPago = $(`#fecha_pago_${id}`).val().split('-')

            // Se agrega la cuenta al arreglo que irá a Siesa
            movimientosContables.push({
                // Campos para V2
                F_CIA: 1,
                F350_ID_CO: 400,
                F350_ID_TIPO_DOCTO: 'FRC',
                F350_CONSEC_DOCTO: 1,
                F351_ID_AUXILIAR: auxiliar,
                F351_ID_CO_MOV: 400,
                F351_ID_TERCERO: '',
                F351_VALOR_DB: <?php echo number_format($recibo->valor, 0, '', ''); ?>,
                F351_NRO_DOCTO_BANCO: <?php echo "{$recibo->anio}{$mes_recibo}{$dia_recibo}" ?>,
                F351_NOTAS: `<?php echo "Recibo $recibo->id"; ?>`,
                F351_ID_UN: '01',
                F351_ID_CCOSTO: '',
                F351_ID_FE: '1101',
                F351_VALOR_CR: 0,
                F351_VALOR_DB_ALT: 0,
                F351_VALOR_CR_ALT: 0,
                F351_BASE_GRAVABLE: 0,
                F351_DOCTO_BANCO: 'CG',
            })

            // Se agrega la cuenta al arreglo que irá a base de datos
            cuentasRecibo.push({
                recibo_id: reciboId,
                cuenta_bancaria_id: $(`#cuenta_${id}`).val(),
                fecha_documento_banco: $(`#fecha_pago_${id}`).val(),
                valor: parseFloat(valor),
                fecha_creacion: '<?php echo date('Y-m-d H:i:s'); ?>',
                usuario_id: '<?php echo $this->session->userdata('usuario_id'); ?>',
            })
        })

        if(!validacion) {
            mostrarAviso('alerta', 'Por favor diligencie todos los campos.')
            return false
        }

        // Si los valores imputados y del recibo no coinciden
        if(totalCuentas == 0) {
            mostrarAviso('alerta', 'Por favor elija al menos una cuenta.')
            return false
        }

        // Si los valores imputados y del recibo no coinciden
        if(totalRecibo !== totalImputado) {
            mostrarAviso('alerta', 'El valor imputado no es igual al valor total del recibo.')
            return false
        }
        
        // Se crean las cuentas en bases de datos
        await consulta('crear', {tipo: 'recibos_cuentas_bancarias', valores: cuentasRecibo}, false)

        Swal.fire({
            title: 'Creando documento contable en Siesa...',
            text: 'Por favor, espera. No cierres esta ventana ni refresques la página',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })
        
        await consulta('crear', {tipo: 'factura_documento_contable', 'id_factura': reciboId, movimientos_contables: movimientosContables}, false)
        .then(async(pago) => {
            Swal.close()

            if(pago.resultado.error) {
                mostrarAviso('error', `
                    Ocurrió un error al crear el documento contable en Siesa:
                    <pre>
                        ${pago.resultado.mensaje.documento_contable}
                    </pre>
                `, 100000)
                return false
            }

            let datosRecibo = {
                tipo: 'recibos',
                id: reciboId,
                recibo_estado_id: 1,
                usuario_aprobacion_id: '<?php echo $this->session->userdata('usuario_id'); ?>',
            }

            let resultado = await consulta('actualizar', datosRecibo, false)
        
            if(resultado) {
                mostrarAviso('exito', 'El documento contable se asentó correctamente en Siesa.')

                setTimeout(() => {
                    location.href = `<?php echo site_url("configuracion/recibos/ver/3"); ?>`;
                }, 1000)
            }
        })
        .catch(error => {
            mostrarAviso('error', 'Ocurrió un error al crear el documento contable en Siesa')
            return false
        })
    }

    calcularTotalAmortizacion = () => {
        var total = 0
        var faltante = parseFloat($('#total_faltante_amortizacion').val())

        $(`.valor_cuenta_recibo`).each(function() {
            total += parseFloat($(this).val().replace(/\./g, '')) || 0
        })

        faltante -= total
        
        // Se formatea el campo
        $('#total_pago_amortizacion').text(formatearNumero(total))
        $('#total_faltante_amortizacion_formato').text(formatearNumero(faltante))
    }

    /**
     * Almacena los datos que están al final del pago con comprobante
     */
    guardarDatosComprobante = async (id) => {
        let datos = {
            tipo: 'recibos',
            id: id,
            observaciones: $('#comprobante_observaciones').val()
        }

        // Si se chequea el campo para reprocesar
        if($(`#comprobante_reprocesar`).is(':checked')) {
            datos.fecha_actualizacion_bot = null,
            datos.recibo_estado_id = 3
            datos.numero_siesa = null
        }

        let recibo = await consulta('actualizar', datos, false)
        
        if(recibo) mostrarAviso('exito', 'Los datos se han actualizado correctamente')
    }

    rechazarPago = async(reciboId, confirmacion = null) => {
        // Ventana de confirmación
        if(!confirmacion) {
            cargarInterfaz('configuracion/recibos/detalle/rechazo', 'contenedor_modal_rechazo', {id_recibo: reciboId})
            return false
        }

        let camposObligatorios = [
            $('#motivo_rechazo_id'),
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datosRecibo = {
            tipo: 'recibos',
            id: reciboId,
            motivo_rechazo_id: $('#motivo_rechazo_id').val(),
            recibo_estado_id: 4,
            comentarios: $('#rechazo_comentarios').val(),
        }
        
        let resultado = await consulta('actualizar', datosRecibo, false)
        
        if(resultado) {
            mostrarAviso('exito', 'El pago se rechazó correctamente.')

            setTimeout(() => {
                location.href = `<?php echo site_url("configuracion/recibos/ver/3"); ?>`;
            }, 1000)
        }
    }

    $().ready(async () => {
        cargarInterfaz('configuracion/recibos/detalle/resumen', 'contenedor_recibos_detalle', {token: '<?php echo $this->uri->segment(4); ?>'})

        // Se consulta si el recibo ya tiene cuentas creadas
        await consulta('obtener', {tipo: 'recibos_cuentas_bancarias', recibo_id: '<?php echo $recibo->id; ?>'})
        .then(cuentasRecibo => {
            let resultado = cuentasRecibo.resultado
            resultado.forEach(async(cuenta) => {
                await agregarCuenta(<?php echo $recibo->id; ?>, cuenta)
            })
        })
    })
</script>