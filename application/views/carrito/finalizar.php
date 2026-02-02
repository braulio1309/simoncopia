<?php
if($this->session->userdata('usuario_id')) {
    $tercero = $this->configuracion_model->obtener('usuarios', ['id' => $this->session->userdata('usuario_id')]);
    echo "<input type='hidden' id='carrito_tercero_id' value='$tercero->id' />";
}
?>

<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <nav class="breadcrumb block-header__breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb__list">
                    <li class="breadcrumb__spaceship-safe-area" role="presentation"></li>
                    <li class="breadcrumb__item breadcrumb__item--parent breadcrumb__item--first">
                        <a href="<?php echo site_url('inicio'); ?>" class="breadcrumb__item-link">Inicio</a>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--parent">
                        <a href="<?php echo site_url('carrito/ver'); ?>" class="breadcrumb__item-link">Carrito de compras</a>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--current breadcrumb__item--last" aria-current="page">
                        <span class="breadcrumb__item-link">Finalizar pedido</span>
                    </li>
                    <li class="breadcrumb__title-safe-area" role="presentation"></li>
                </ol>
            </nav>
            <h1 class="block-header__title">Confirmación de datos del pedido</h1>
        </div>
    </div>
</div>
<div class="checkout block">
    <div class="container container--max--xl">
        <div class="row">
            <div class="col-12 col-lg-6 col-xl-7">
                <div class="card mb-lg-0">
                    <div class="card-body card-body--padding--2">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="checkout_documento_numero">Número de documento *</label>
                                <input type="number" class="form-control" id="checkout_documento_numero" autofocus>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-block" id="btn_validar_documento">Validar datos</button>

                        <div id="contenedor_datos_cliente"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-5 mt-4 mt-lg-0">
                <div class="card mb-0">
                    <div class="card-body card-body--padding--2">
                        <h3 class="card-title">Detalle del pedido</h3>
                        <table class="checkout__totals">
                            <thead class="checkout__totals-header">
                                <tr>
                                    <th>Producto</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody class="checkout__totals-products">
                                <?php foreach ($this->cart->contents() as $item) {
                                    $producto = $this->productos_model->obtener('productos', [
                                        'id' => $item['id'],
                                        'omitir_bodega' => true,
                                        'omitir_lista_precio' => true,
                                    ]);    
                                ?>
                                    <tr>
                                        <td><?php echo $producto->notas; ?> x <?php echo $item['qty']; ?></td>
                                        <td><?php echo formato_precio($item['subtotal']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tbody class="checkout__totals-subtotals">
                                <tr>
                                    <th>Subtotal</th>
                                    <td><?php echo formato_precio($this->cart->total()); ?></td>
                                </tr>
                                <tr id="descuento"></tr>
                            </tbody>
                            <tfoot class="checkout__totals-footer">
                                <tr>
                                    <th>Total</th>
                                    <td><?php echo formato_precio($this->cart->total()); ?></td>
                                </tr>
                            </tfoot>
                        </table>

                        <?php
                        // Cupo del cliente
                        if(isset($this->data['permisos']) && in_array(['pedidos' => 'pedidos_credito_gestionar'], $this->data['permisos'])) {
                            $this->load->view('carrito/cupo');
                        }
                        ?>

                        <div class="checkout__agree form-group">
                            <div class="form-check">
                                <span class="input-check form-check-input">
                                    <span class="input-check__body">
                                        <input class="input-check__input" type="checkbox" id="checkout_terminos">
                                        <span class="input-check__box"></span>
                                        <span class="input-check__icon">
                                            <svg width="9px" height="7px">
                                                <path d="M9,1.395L3.46,7L0,3.5L1.383,2.095L3.46,4.2L7.617,0L9,1.395Z" />
                                            </svg>
                                        </span>
                                    </span>
                                </span>
                                <label class="form-check-label" for="checkout-terms">
                                    He leído y acepto los <a target="_blank" href="<?php echo site_url('blog/tratamiento_datos'); ?>">términos y condiciones</a>
                                </label>
                            </div>
                        </div>
                        <hr>

                        <div class="row mb-3">
                            <div class="col-3">
                                <div class="block-features__item-icon">
                                    <img src="<?php echo base_url(); ?>images/icons/envios_gratis.svg">
                                </div>
                            </div>
                            <div class="col-9">
                                <div class="block-features__item-info">
                                    <div class="block-features__item-title">Envíos gratis</div>
                                    <div class="block-features__item-subtitle">Tus órdenes gratis en todo el Valle de Aburrá</div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="pedido_total_pago" value="<?php echo $this->cart->total(); ?>">

                        <?php if(isset($this->data['permisos']) && in_array(['pedidos' => 'pedidos_credito_gestionar'], $this->data['permisos'])) { ?>
                            <div class="form-check mb-3">
                                <span class="input-check form-check-input">
                                    <span class="input-check__body">
                                        <input class="input-check__input" type="checkbox" id="checkout_venta_credito">
                                        <span class="input-check__box"></span>
                                        <span class="input-check__icon">
                                            <svg width="9px" height="7px">
                                                <path d="M9,1.395L3.46,7L0,3.5L1.383,2.095L3.46,4.2L7.617,0L9,1.395Z" />
                                            </svg>
                                        </span>
                                    </span>
                                </span>
                                <label class="form-check-label" for="checkout_venta_credito">
                                    Venta a crédito
                                </label>
                            </div>
                        <?php } ?>

                        <button type="submit" class="btn btn-primary btn-xl btn-block" onClick="javascript:guardarFactura()" id="btn_pagar" disabled>
                            Finalizar pedido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<div id="contenedor_pago"></div>

<script>
    let esVendedor = ($('#codigo_vendedor').val() == 0) ? false : true
    
    cargarDatosCliente = async() => {
        if (!validarCamposObligatorios([
            $('#checkout_documento_numero'),
        ])) return false
        
        // Se activa el spinner
        $('#btn_validar_documento').addClass('btn-loading').attr('disabled', true)

        cargarInterfaz('carrito/finalizar_datos_cliente', 'contenedor_datos_cliente', {numero_documento: $.trim($('#checkout_documento_numero').val())})
    }

    guardarFactura = async() => {        
        let total = parseFloat($('#pedido_total_pago').val())
        
        // Alerta cuando no hay ítems en el carrito
        if(<?php echo $this->cart->total(); ?> == 0) {
            mostrarAviso('alerta', 'No hay ningún producto en el carrito.')
            return false
        }

        let camposObligatorios = [
            $('#checkout_tiene_rut'),
            $('#checkout_tipo_tercero'),
            $('#checkout_tipo_documento'),
            $('#checkout_razon_social'),
            $('#checkout_direccion'),
            $('#checkout_telefono'),
            $('#checkout_email'),
            $('#checkout_email_factura_electronica'),
            $('#checkout_municipio_id'),
            $('#checkout_municipio_envio_id'),
            $('#checkout_sucursal'),
            $('#checkout_responsable_iva'),
        ]

        // Si es persona natural
        if ($('#checkout_tipo_tercero').val() == 1) {
            camposObligatorios.push($('#checkout_nombres'))
            camposObligatorios.push($('#checkout_primer_apellido'))
        }

        if(total < 50000) {
            mostrarAviso('alerta', 'Te informamos que si deseas pagar por este medio, el valor debe ser superior o igual a $50.000', 20000)
            return false
        }

        if(!$(`#checkout_terminos`).is(':checked')) {
            mostrarAviso('alerta', 'Por favor lee y acepta los términos y condiciones', 20000)
            return false
        }
        
        // Si tiene sucursales, es obligatorio
        if(parseInt($('#cantidad_sucursales').val()) > 0) camposObligatorios.push($('#checkout_sucursal'))

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datosRecibo = {
            tipo: 'recibos',
            documento_numero: $('#checkout_documento_numero').val(),
            abreviatura: ($(`#checkout_venta_credito`).is(':checked')) ? 'pc' : 'pe',
            nombres: $('#checkout_nombres').val(),
            primer_apellido: $('#checkout_primer_apellido').val(),
            segundo_apellido: $('#checkout_segundo_apellido').val(),
            razon_social: $('#checkout_razon_social').val(),
            direccion: $('#checkout_direccion').val(),
            direccion_envio: $('#checkout_direccion_envio').val(),
            municipio_envio_codigo: $('#checkout_municipio_envio_id').val(),
            departamento_envio_codigo: $('#checkout_departamento_envio_id').val(),
            email: $('#checkout_email').val(),
            telefono: $('#checkout_telefono').val(),
            comentarios: $('#checkout_comentarios').val(),
            valor: $('#total_pedido').val(),
            recibo_tipo_id: 1,
            email_factura_electronica: $('#checkout_email_factura_electronica').val(),
            lista_precio: '<?php echo $this->config->item('lista_precio'); ?>',
        }

        // Se agregan datos de la sucursal al pedido
        datosRecibo.sucursal_id = $('#checkout_sucursal').val()
        datosRecibo.tipo_cliente = $('#checkout_sucursal option:selected').data('tipo_cliente')

        // Se agrega vendedor, si lo eligió
        if($('#checkout_vendedor_nit').val() != '') datosRecibo.tercero_vendedor_nit = $('#checkout_vendedor_nit').val()

        // Se crean las sucursales del tercero en la base de datos
        let sucursales = await gestionarSucursales($('#checkout_documento_numero').val())

        // Se obtienen los datos de la sucursal seleccionada para extraer la lista de precio
        let sucursal = await consulta('obtener', {tipo: 'cliente_sucursal', f200_nit: $('#checkout_documento_numero').val()}, false)

        let recibo = await consulta('crear', datosRecibo, false)
        
        // Una vez creado el recibo
        if (recibo.resultado) {
            // Se crean los ítems de la factura
            let reciboItems = await consulta('crear', {tipo: 'recibos_detalle', 'recibo_id': recibo.resultado, lista_precio: datosRecibo.lista_precio}, false)

            // Si es pedido a contado, se abre modal de Wompi
            if(!$(`#checkout_venta_credito`).is(':checked')) {
                if (reciboItems.resultado) cargarInterfaz('carrito/pago', 'contenedor_pago', {id: recibo.resultado})
            }

            // Si el tercero cliente no existe (sin sucursales), se va a crear
            if($('#cantidad_sucursales').val() == 0) {
                let datosTerceroSiesa = {
                    responsable_iva: $('#checkout_responsable_iva option:selected').attr('data-responsable_iva'), // Sí, No
                    causante_iva: $('#checkout_responsable_iva option:selected').attr('data-causante_iva'), // Sí, No
                    tipo_tercero: $('#checkout_tipo_tercero').val(), // Natural, jurídica
                    documento_tipo: $('#checkout_tipo_documento').val(),
                    documento_numero: $('#checkout_documento_numero').val(),
                    nombres: $('#checkout_nombres').val(),
                    primer_apellido: $('#checkout_primer_apellido').val(),
                    segundo_apellido: $('#checkout_segundo_apellido').val(),
                    razon_social: $('#checkout_razon_social').val(),
                    id_departamento: $('#checkout_departamento_id').val(),
                    id_ciudad: $('#checkout_municipio_id').val(),
                    direccion: $('#checkout_direccion').val(),
                    contacto: '-',
                    email: $('#checkout_email').val(),
                    telefono: $('#checkout_telefono').val(),
                    vendedor: 'U003',
                    lista_precio: (esVendedor) ? '001' : '<?php echo $this->config->item('lista_precio'); ?>',
                }
                
                // Si es cédula de extranjería, se envía una entidad dinámica adicional
                // para la creación del tercero en Siesa
                if($('#checkout_tipo_documento').val() == 'E') {
                    datosTerceroSiesa.entidad_dinamica_extranjero = {
                        f200_id: $('#checkout_documento_numero').val(),
                        f753_id_entidad: 'EUNOECO036',
                        f753_id_atributo: 'co036_id_procedencia_org',
                        f753_id_maestro: 'MUNOECO043',
                        f753_id_maestro_detalle: 11,
                    }
                }

                // Si es vendedor, se envía una entidad dinámica adicional
                // para la asignación del segmento
                // if(esVendedor) {
                //     datosTerceroSiesa.criterio_cliente = {
                //         f207_id_tercero: $('#checkout_documento_numero').val(),
                //         f207_id_sucursal: '001',
                //         f207_id_plan_criterios: $('#usuario_segmento_id option:selected').attr('data-plan'),
                //         f207_id_criterio_mayor: $('#usuario_segmento_id option:selected').attr('data-mayor'),
                //     }
                // }

                await crearTerceroCliente(datosTerceroSiesa)
                .then(resultado => {
                    console.log(resultado)

                    agregarLog(52, JSON.stringify(resultado))
                })
            }

            if($(`#checkout_venta_credito`).is(':checked')) {
                Swal.fire({
                    title: 'Estamos creando el pedido en el ERP...',
                    text: 'Por favor, espera.',
                    imageUrl: `${$('#base_url').val()}images/cargando.webp`,
                    showConfirmButton: false,
                    allowOutsideClick: false
                })

                let reciboCompleto = await consulta('obtener', { tipo: 'recibo', id: recibo.resultado })

                if(!reciboCompleto.resultado) {
                    mostrarAviso('error', `Ocurrió un error al crear el pedido`, 30000)
                    return
                }

                // Ejecución del webhook que envía el pedido al ERP
                await fetch(`${$("#site_url").val()}webhooks/pedido/${reciboCompleto.resultado.token}`)
                .then(respuesta => respuesta.json())
                .then(resultadoPedido => {
                    if(!resultadoPedido.exito) {
                        mostrarAviso('error', `Ocurrió un error al crear el pedido: ${JSON.parse(resultadoPedido.resultado)}`, 30000)
                        return 
                    }
                    mostrarAviso('exito', `¡El pedido se creó correctamente en el ERP!`, 20000)
                })
                .catch(error => {
                    mostrarAviso('error', `Ocurrió un error al crear el pedido`, 30000)
                    return
                })
            }
        }
    }

    $().ready(() => {
        $('#btn_validar_documento').click(() => cargarDatosCliente())

        // Si es un usuario logueado
        if($('#sesion_documento_numero').val() != '') {
            // Pone el número de documento del usuario por defecto
            // $('#checkout_documento_numero').val($('#sesion_documento_numero').val())

            // Carga los datos del cliente
            cargarDatosCliente()
        }
    })
</script>