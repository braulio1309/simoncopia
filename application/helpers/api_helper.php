<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Consume el endpoint para la creación del
 * documento contable para recibos en Siesa
 */
function crear_documento_contable($id_recibo, $datos_pago = null, $datos_movimientos_contables = null) {
    $CI =& get_instance();
    $errores = 0;

    $recibo = $CI->productos_model->obtener('recibo', ['id' => $id_recibo]);

    $notas_movimiento_contable = "Recibo $recibo->id";
    $id_auxiliar_movimiento_contable = (isset($metodo_pago) && $metodo_pago == 'PSE') ? '11100504' : '11200505';

    // Si es un recibo de Wompi
    if($recibo->wompi_datos) {
        $wompi = json_decode($recibo->wompi_datos, true);
        $metodo_pago = $wompi['payment_method_type'];
        $notas_recibo = "Pago a través de Wompi. Referencia $recibo->wompi_transaccion_id. Medio: $metodo_pago";
        if($metodo_pago == 'CARD') $notas_recibo .= ' ('.$wompi['payment_method']['extra']['name'].')';
    // Si es un pago con archivo plano
    } else if ($recibo->recibo_tipo_id == 5) {
        // Las observaciones se traen del recibo
        $notas_recibo = $recibo->observaciones;
    } else if ($recibo->recibo_tipo_id == 3) {
        $notas_recibo = "PAGA FACTURAS CONSIGNACION DEL DIA $recibo->dia_consignacion/$recibo->mes_consignacion/$recibo->anio_consignacion";
        $notas_movimiento_contable = $notas_recibo;
        
        // Consulta de la cuenta bancaria
        $cuenta_bancaria = $CI->configuracion_model->obtener('cuenta_bancaria', ['id' => $recibo->cuenta_bancaria_id]);
        $id_auxiliar_movimiento_contable = $cuenta_bancaria->codigo;
    } else {
        $notas_recibo = "Recibo cargado desde la página web por el cliente";
    }

    // Se obtienen los ítems del recibo
    $items = $CI->productos_model->obtener('recibos_detalle', ['recibo_id' => $recibo->id]);

    $movimientos_cxc = [];
    $descuento = 0;
    $mes_recibo = str_pad($recibo->mes, 2, '0', STR_PAD_LEFT);
    $dia_recibo = str_pad($recibo->dia, 2, '0', STR_PAD_LEFT);

    /**
     * Centro operativo
     * 100: Si la fecha de recaudo es del mismo mes o estamos en fecha posterior al día 5 del mes
     * 500: En los días 1 a 5 del mes, cuando la fecha de recaudo sea de meses anteriores 
     */
    $centro_operativo = 400; // eCommerce (por defecto)

    // Si es pago con importación de archivo plano o con comprobantes
    if($recibo->recibo_tipo_id == 5 || $recibo->recibo_tipo_id == 3) {
        if($recibo->mes_consignacion == date('m')) $centro_operativo = 100; // Si el mes de consignación es este mes
        if($recibo->dia_consignacion >= 6) $centro_operativo = 100; // Si el día de consignación es a partir del día 6
        if($recibo->mes_consignacion != date('m') && $recibo->dia_consignacion <= 5) $centro_operativo = 500; // Si el mes de consignación es diferente al actual y el día está entre 1 y 5
    }

    // Se recorre cada ítem
    foreach ($items as $item) {
        // Consulta de la factura del cliente correspondiente al pago realizado
        $factura_cliente = $CI->clientes_model->obtener('clientes_facturas', [
            'Tipo_Doc_cruce' => $item->documento_cruce_tipo,
            'Nro_Doc_cruce' => $item->documento_cruce_numero,
            'Cliente' => $recibo->documento_numero,
            'pendientes' => true,
        ]);

        // Si trae descuento, se va acumulando
        if($item->descuento > 0) $descuento += $item->descuento;

        $mes_vencimiento = str_pad($factura_cliente->mes_vencimiento, 2, '0', STR_PAD_LEFT);
        $dia_vencimiento = str_pad($factura_cliente->dia_vencimiento, 2, '0', STR_PAD_LEFT);
        
        $movimiento_cxc = [
            "F_CIA" => 1,
            "F350_ID_CO" => $centro_operativo,
            "F350_ID_TIPO_DOCTO" => 'FRC',
            "F350_CONSEC_DOCTO" => 1,
            "F351_ID_AUXILIAR" => $factura_cliente->codigo_auxiliar,
            "F351_ID_TERCERO" => $factura_cliente->Cliente,
            "F351_NOTAS" => $notas_movimiento_contable,
            "F351_ID_CO_MOV" => $factura_cliente->centro_operativo_codigo,
            "F351_VALOR_CR" => ($item->subtotal >= 0) ? number_format($item->subtotal, 0, '', '') : 0,
            "F353_ID_SUCURSAL" => str_pad($factura_cliente->sucursal_id, 3, '0', STR_PAD_LEFT),
            "F353_ID_TIPO_DOCTO_CRUCE" => $factura_cliente->Tipo_Doc_cruce,
            "F353_CONSEC_DOCTO_CRUCE" => $factura_cliente->Nro_Doc_cruce,
            "F353_NRO_CUOTA_CRUCE" => $item->cuota_numero,
            "F353_FECHA_VCTO" => "{$factura_cliente->anio_vencimiento}{$mes_vencimiento}{$dia_vencimiento}",
            "F353_FECHA_DSCTO_PP" => "{$factura_cliente->anio_vencimiento}{$mes_vencimiento}{$dia_vencimiento}",
            "F351_ID_UN" => '01',
            "F351_ID_CCOSTO" => '',
            "F351_VALOR_DB" => ($item->subtotal <= 0) ? number_format(($item->subtotal*-1), 0, '', '') : 0, // Saldos a favor
            "F351_VALOR_DB_ALT" => 0,
            "F351_VALOR_CR_ALT" => 0,
            "F353_VLR_DSCTO_PP" => 0,
            "F354_VALOR_APLICADO_PP" => 0,
            "F354_VALOR_APLICADO_PP_ALT" => 0,
            "F354_VALOR_APROVECHA" => 0,
            "F354_VALOR_APROVECHA_ALT" => 0,
            "F354_VALOR_RETENCION" => 0,
            "F354_VALOR_RETENCION_ALT" => 0,
            "F354_TERCERO_VEND" => '22222221',
            "F354_NOTAS" => $notas_recibo,
        ];

        array_push($movimientos_cxc, $movimiento_cxc);
    }

    // Si trae cuentas imputadas, las toma para ingresarlas al documento contable,
    // sino, toma los valores básicos del pago de Wompi
    $movimientos_contables = 
        ($datos_movimientos_contables) 
        ? $datos_movimientos_contables 
        : [
            [
                // Primer movimiento -> Bancos
                "F_CIA" => 1,
                "F350_ID_CO" => $centro_operativo,
                "F350_ID_TIPO_DOCTO" => 'FRC',
                "F350_CONSEC_DOCTO" => 1,
                "F351_ID_AUXILIAR" => $id_auxiliar_movimiento_contable,
                "F351_ID_CO_MOV" => $factura_cliente->centro_operativo_codigo,
                "F351_ID_TERCERO" => '',
                "F351_VALOR_DB" =>  number_format($recibo->valor, 0, '', ''),
                "F351_NRO_DOCTO_BANCO" => "{$recibo->anio_consignacion}{$recibo->mes_consignacion}{$recibo->dia_consignacion}",
                "F351_NOTAS" => "Recibo $recibo->id",
                "F351_ID_UN" => '01',
                "F351_ID_CCOSTO" => '',
                "F351_ID_FE" => ($recibo->wompi_datos) ? '1102' : '1101',
                "F351_VALOR_CR" => 0,
                "F351_VALOR_DB_ALT" => 0,
                "F351_VALOR_CR_ALT" => 0,
                "F351_BASE_GRAVABLE" => 0,
                "F351_DOCTO_BANCO" => 'CG',
            ]
        ];
    
    // Si hay un valor pagado mayor en el recibo (una diferencia) se ejecutará el asiento de ese valor
    if($recibo->valor_pagado_mayor) {
        $estructura_valor_pagado_mayor = procesar_valor_mayor_pagado($recibo, $centro_operativo, $notas_recibo);

        if($estructura_valor_pagado_mayor['tipo'] == 'movimiento_contable') $movimientos_contables[] = $estructura_valor_pagado_mayor['datos'];
        if($estructura_valor_pagado_mayor['tipo'] == 'movimiento_cxc') $movimientos_cxc[] = $estructura_valor_pagado_mayor['datos'];
    }
        
    /**
     * Paquete a enviar al API 
     **/    
    $paquete_documento_contable = [
        "Inicial" => [
            [
                "F_CIA" => 1,
            ]
        ],
        // Un solo documento contable para toda la transacción
        "documentoContable" => [
            [
                "F_CIA" => 1,
                "F_CONSEC_AUTO_REG" => 1,
                "F350_ID_CO" => $centro_operativo,
                "F350_ID_TIPO_DOCTO" => 'FRC',
                "F350_CONSEC_DOCTO" => 1,
                "F350_FECHA" => date('Ymd'),
                "F350_ID_TERCERO" => $recibo->documento_numero,
                "F350_ID_CLASE_DOCTO" => 30,
                "F350_IND_ESTADO" => ($recibo->recibo_tipo_id == 3 || $recibo->recibo_tipo_id == 5) ? 0 : 1, // 0: En elaboración; 1: Aprobado; 2: Anulado
                "F350_IND_IMPRESION" => 1,
                "F350_NOTAS" => "Recibo $recibo->id - $notas_recibo",
                "F350_ID_MANDATO" => '',
            ]
        ],
        // Primer movimiento -> Bancos
        "movimientoContable" => $movimientos_contables,
        // Cada factura que se va a pagar
        "movimientoCxC" => $movimientos_cxc,
        "Final" => [
            [
                "F_CIA" => 1,
            ]
        ],
    ];
    
    // si tiene descuentos
    if($descuento > 0) {
        // Segundo movimiento -> Auxiliar del recibo (Usar para retenciones y descuentos)
        array_push($paquete_documento_contable['movimientoContable'], [
            "F_CIA" => '1',
            "F350_ID_CO" => $centro_operativo,
            "F350_ID_TIPO_DOCTO" => 'FRC',
            "F350_CONSEC_DOCTO" => 1,
            "F351_ID_AUXILIAR" => '41750120',
            "F351_ID_CO_MOV" => $factura_cliente->centro_operativo_codigo,
            "F351_ID_TERCERO" => $recibo->documento_numero,
            "F351_VALOR_DB" => number_format($descuento, 0, '', ''),
            "F351_NRO_DOCTO_BANCO" => 0,
            "F351_NOTAS" => "PAGO FACTURA $factura_cliente->doccruce",
            "F351_VALOR_CR" => 0,
            "F351_VALOR_DB_ALT" => 0,
            "F351_VALOR_CR_ALT" => 0,
            "F351_BASE_GRAVABLE" => 0,
            "F351_DOCTO_BANCO" => '',
        ]);
    }

    // return $paquete_documento_contable;

    $resultado_documento_contable = json_decode(importar_documento_contable_api($paquete_documento_contable));
    $codigo_resultado_documento_contable = $resultado_documento_contable->codigo;
    $detalle_resultado_documento_contable = json_encode($resultado_documento_contable->detalle);

    if($codigo_resultado_documento_contable != '0') {
        $errores++;

        // Se agrega log
        $CI->configuracion_model->crear('logs', [
            'log_tipo_id' => 19,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observacion' => json_encode([
                $detalle_resultado_documento_contable,
                $paquete_documento_contable
            ])
        ]);
    }

    // Si no se pudo crear el documento contable
    if($codigo_resultado_documento_contable == '1') {
        $errores++;

        // Se agrega log
        $CI->configuracion_model->crear('logs', [
            'log_tipo_id' => 19,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observacion' => [
                $detalle_resultado_documento_contable,
                $paquete_documento_contable
            ]
        ]);
        
        $respuesta['documento_contable'] = $detalle_resultado_documento_contable;
    } else {
        // Se agrega log
        $CI->configuracion_model->crear('logs', [
            'log_tipo_id' => 20,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observacion' => json_encode($paquete_documento_contable),
        ]);

        $respuesta['documento_contable'] = $resultado_documento_contable;

        $CI->productos_model->actualizar('recibos', ['id' => $id_recibo], [
            'numero_siesa' => obtener_numero_recibo_caja($recibo),
            'fecha_actualizacion_bot' => date('Y-m-d H:i:s')
        ]);

        // Generación del comprobante
        $CI->data['token'] = $recibo->token;
        $CI->data['almacenar_archivo'] = true;
        $CI->load->view('reportes/pdf/recibo', $CI->data);

        // Movimiento del soporte
        $ruta_origen = "archivos/recibos/$recibo->id";
        $ruta_destino = obtener_ruta_documento_contable($recibo);
        copy("$ruta_origen/$recibo->archivo_soporte", "$ruta_destino/SOPORTE $recibo->numero_siesa $recibo->archivo_soporte"); // El archivo se copia del origen al destino
    }

    // Si vienen datos aquí, es un comprobante y se enviará email
    if($datos_movimientos_contables && $errores == 0) enviar_email_factura_wompi_comprobante($recibo);

    if($recibo->wompi_datos && $errores == 0) enviar_email_factura_wompi($recibo);

    print json_encode([
        'exito' => $errores == 0,
        'errores' => $errores,
        'mensaje' => $resultado_documento_contable,
        'datos' => $paquete_documento_contable,
    ]);
}

/**
 * Consume el endpoint para la creación del
 * documento contable para pedidos en Siesa
 */
function crear_documento_contable_pedido($id_recibo, $datos_pago = null) {
    $CI =& get_instance();
    $errores = 0;

    $recibo = $CI->productos_model->obtener('recibo', ['id' => $id_recibo]);

    $wompi = json_decode($recibo->wompi_datos, true);
    $metodo_pago = $wompi['payment_method_type'];

    $notas_recibo = "- Pedido $recibo->id E-Commerce - Referencia Wompi: {$wompi['reference']} - ID de Transacción Wompi: {$wompi['id']} - {$wompi['payment_method']['type']}";
    if($metodo_pago == 'CARD') $notas_recibo .= ' ('.$wompi['payment_method']['extra']['name'].')';
    
    // Se obtienen los ítems del recibo
    $items = $CI->productos_model->obtener('recibos_detalle', ['recibo_id' => $recibo->id]);

    $movimientos_cxc = [];
    $mes_recibo = str_pad($recibo->mes, 2, '0', STR_PAD_LEFT);
    $dia_recibo = str_pad($recibo->dia, 2, '0', STR_PAD_LEFT);

    // Se recorre cada ítem
    foreach ($items as $item) {
        $movimiento_cxc = [
            "F_CIA" => 1,
            "F350_ID_CO" => 400, 
            "F350_ID_TIPO_DOCTO" => 'FRC',
            "F350_CONSEC_DOCTO" => $recibo->id,
            "F351_ID_AUXILIAR" => '28050505',
            "F351_ID_TERCERO" => $recibo->documento_numero,
            "F351_NOTAS" => "Recibo $recibo->id",
            "F351_ID_CO_MOV" => 400,
            "F351_VALOR_CR" => ($item->subtotal >= 0) ? number_format(($item->subtotal - $item->descuento), 0, '', '') : 0,
            "F353_ID_SUCURSAL" => '001',
            "F353_ID_TIPO_DOCTO_CRUCE" => 'CPE',
            "F353_CONSEC_DOCTO_CRUCE" => $recibo->id,
            "F353_NRO_CUOTA_CRUCE" => $item->cuota_numero,
            "F353_FECHA_VCTO" => "{$recibo->anio}{$mes_recibo}{$dia_recibo}",
            "F353_FECHA_DSCTO_PP" => "{$recibo->anio}{$mes_recibo}{$dia_recibo}",
            "F351_ID_UN" => '01',
            "F351_ID_CCOSTO" => '',
            "F351_VALOR_DB" => ($item->subtotal <= 0) ? number_format((($item->subtotal - $item->descuento) * -1), 0, '', '') : 0, // Saldos a favor
            "F351_VALOR_DB_ALT" => 0,
            "F351_VALOR_CR_ALT" => 0,
            "F353_VLR_DSCTO_PP" => 0,
            "F354_VALOR_APLICADO_PP" => 0,
            "F354_VALOR_APLICADO_PP_ALT" => 0,
            "F354_VALOR_APROVECHA" => 0,
            "F354_VALOR_APROVECHA_ALT" => 0,
            "F354_VALOR_RETENCION" => 0,
            "F354_VALOR_RETENCION_ALT" => 0,
            "F354_TERCERO_VEND" => '22222221',
            "F354_NOTAS" => $notas_recibo,
        ];

        array_push($movimientos_cxc, $movimiento_cxc);
    }

    /**
     * Paquete a enviar al API 
     **/    
    $paquete_documento_contable = [
        "Inicial" => [
            [
                "F_CIA" => 1,
            ]
        ],
        // Un solo documento contable para toda la transacción
        "documentoContable" => [
            [
                "F_CIA" => 1,
                "F_CONSEC_AUTO_REG" => 1,
                "F350_ID_CO" => 400,
                "F350_ID_TIPO_DOCTO" => 'FRC',
                "F350_CONSEC_DOCTO" => $recibo->id,
                "F350_FECHA" => date('Ymd'),
                "F350_ID_TERCERO" => $recibo->documento_numero,
                "F350_ID_CLASE_DOCTO" => 30,
                "F350_IND_ESTADO" => ($recibo->recibo_tipo_id == 5 || $recibo->recibo_tipo_id == 3) ? 0 : 1, // 0: En elaboración; 1: Aprobado; 2: Anulado
                "F350_IND_IMPRESION" => 1,
                "F350_NOTAS" => $notas_recibo,
                "F350_ID_MANDATO" => '',
            ]
        ],
        // Primer movimiento -> Bancos
        "movimientoContable" => [
            [
                // Primer movimiento -> Bancos
                "F_CIA" => 1,
                "F350_ID_CO" => 400,
                "F350_ID_TIPO_DOCTO" => 'FRC',
                "F350_CONSEC_DOCTO" => $recibo->id,
                "F351_ID_AUXILIAR" => '11100504',
                "F351_ID_CO_MOV" => 400,
                "F351_ID_TERCERO" => '',
                "F351_VALOR_DB" =>  number_format($recibo->valor, 0, '', ''),
                "F351_NRO_DOCTO_BANCO" => "{$recibo->anio}{$mes_recibo}{$dia_recibo}",
                "F351_NOTAS" => "Recibo $recibo->id",
                "F351_ID_UN" => '01',
                "F351_ID_CCOSTO" => '',
                "F351_ID_FE" => ($recibo->wompi_datos) ? '1102' : '1101',
                "F351_VALOR_CR" => 0,
                "F351_VALOR_DB_ALT" => 0,
                "F351_VALOR_CR_ALT" => 0,
                "F351_BASE_GRAVABLE" => 0,
                "F351_DOCTO_BANCO" => 'CG',
            ]
        ],
        // Cada factura que se va a pagar
        "movimientoCxC" => $movimientos_cxc,
        "Final" => [
            [
                "F_CIA" => 1,
            ]
        ],
    ];

    $resultado_documento_contable = json_decode(importar_documento_contable_api($paquete_documento_contable));
    $codigo_resultado_documento_contable = $resultado_documento_contable->codigo;
    $detalle_resultado_documento_contable = json_encode($resultado_documento_contable->detalle);

    // Si no se pudo crear el documento contable
    if($codigo_resultado_documento_contable == '1') {
        $errores++;

        // Se agrega log
        $CI->configuracion_model->crear('logs', [
            'log_tipo_id' => 48,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observacion' => $detalle_resultado_documento_contable
        ]);
        
        $respuesta['documento_contable'] = $detalle_resultado_documento_contable;
    } else {
        // Se agrega log
        $CI->configuracion_model->crear('logs', [
            'log_tipo_id' => 49,
            'fecha_creacion' => date('Y-m-d H:i:s'),
        ]);

        $respuesta['documento_contable'] = $resultado_documento_contable;

        $CI->productos_model->actualizar('recibos', ['id' => $id_recibo], [
            'numero_siesa' => obtener_numero_recibo_caja($recibo),
        ]);
    }

    print json_encode([
        'errores' => $errores,
        'resultado' => $respuesta,
    ]);
}

/**
 * Crea el tercero cliente en Siesa
 */
function importar_tercero_cliente($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];
    
    $client = new \GuzzleHttp\Client();

    try {
        $response = $client->post("$url/api/v3/conectoresimportar", [
            'body' => json_encode($datos),
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'idDocumento' => $CI->config->item('api_siesa')['idDocumento'],
                'idInterface' => $CI->config->item('api_siesa')['idInterface'],
                'nombreDocumento' => 'TERCERO_CLIENTE',
            ],
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    };
    
    return $response->getBody();
}

/**
 * Cuando un recibo recibió un pago por un monto mayor al total de las facturas,
 * debe procesarse la diferencia en el documento contable
 *
 * @param [type] $valor
 * @return void
 */
function procesar_valor_mayor_pagado($recibo, $centro_operativo, $notas_recibo) {
    $valor = $recibo->valor_pagado_mayor * -1;

    // Ajuste al peso -> Hasta 1.000 pesos
    if($valor <= 1000) {
        // Se creará un movimiento contable
        return [ 
            'tipo' => 'movimiento_contable',
            'datos' => [
                "F_CIA" => 1,
                "F350_ID_CO" => $centro_operativo,
                "F350_ID_TIPO_DOCTO" => 'FRC',
                "F350_CONSEC_DOCTO" => 1,
                "F351_ID_CO_MOV" => $centro_operativo,
                "F351_NRO_DOCTO_BANCO" => "0",
                "F351_NOTAS" => $notas_recibo,
                "F351_ID_UN" => '01',
                "F351_ID_CCOSTO" => '',
                "F351_ID_FE" => "",
                "F351_VALOR_DB_ALT" => 0,
                "F351_VALOR_CR_ALT" => 0,
                "F351_BASE_GRAVABLE" => 0,
                "F351_DOCTO_BANCO" => "",
                "F351_ID_AUXILIAR" => "42958105",
                "F351_VALOR_DB" => 0,
                "F351_VALOR_CR" => $valor,
                "F351_BASE_GRAVABLE" => 0,
                "F351_ID_TERCERO" => $recibo->documento_numero,
            ]
        ];
    }

    // Aprovechamientos -> Hasta 10.000 pesos
    if($valor <= 10000) {
        // Se creará un movimiento contable
        return [ 
            'tipo' => 'movimiento_contable',
            'datos' => [
                "F_CIA" => 1,
                "F350_ID_CO" => $centro_operativo,
                "F350_ID_TIPO_DOCTO" => 'FRC',
                "F350_CONSEC_DOCTO" => 1,
                "F351_ID_CO_MOV" => $centro_operativo,
                "F351_NRO_DOCTO_BANCO" => "0",
                "F351_NOTAS" => $notas_recibo,
                "F351_ID_UN" => '01',
                "F351_ID_CCOSTO" => '',
                "F351_ID_FE" => "",
                "F351_VALOR_DB_ALT" => 0,
                "F351_VALOR_CR_ALT" => 0,
                "F351_BASE_GRAVABLE" => 0,
                "F351_DOCTO_BANCO" => "",
                "F351_ID_AUXILIAR" => "42300505",
                "F351_VALOR_DB" => 0,
                "F351_VALOR_CR" => $valor,
                "F351_BASE_GRAVABLE" => 0,
                "F351_ID_TERCERO" => $recibo->documento_numero,
            ]
        ];
    }

    // Saldo a favor -> Más de 10.000 pesos
    if($valor > 10000) {
        // Se creará un movimiento CxC
        return [
            'tipo' => 'movimiento_cxc',
            'datos' => [
                'F_CIA' => 1,
                'F350_ID_CO' => $centro_operativo,
                'F350_ID_TIPO_DOCTO' => 'FRC',
                'F350_CONSEC_DOCTO' => 1,
                'F351_ID_AUXILIAR' => '28050505',
                'F351_ID_TERCERO' => $recibo->documento_numero,
                'F351_NOTAS' => $notas_recibo,
                'F351_ID_CO_MOV' => $centro_operativo,
                'F351_VALOR_CR' => $valor,
                'F353_ID_SUCURSAL' => '001',
                'F353_ID_TIPO_DOCTO_CRUCE' => 'FRC',
                'F353_CONSEC_DOCTO_CRUCE' => "{$recibo->anio_consignacion}{$recibo->mes_consignacion}{$recibo->dia_consignacion}",
                'F353_NRO_CUOTA_CRUCE' => 0,
                'F353_FECHA_VCTO' => "{$recibo->anio_consignacion}{$recibo->mes_consignacion}{$recibo->dia_consignacion}",
                'F353_FECHA_DSCTO_PP' => "{$recibo->anio_consignacion}{$recibo->mes_consignacion}{$recibo->dia_consignacion}",
                'F351_ID_UN' => '01',
                'F351_ID_CCOSTO' => '',
                'F351_VALOR_DB' => 0,
                'F351_VALOR_DB_ALT' => 0,
                'F351_VALOR_CR_ALT' => 0,
                'F353_VLR_DSCTO_PP' => 0,
                'F354_VALOR_APLICADO_PP' => 0,
                'F354_VALOR_APLICADO_PP_ALT' => 0,
                'F354_VALOR_APROVECHA' => 0,
                'F354_VALOR_APROVECHA_ALT' => 0,
                'F354_VALOR_RETENCION' => 0,
                'F354_VALOR_RETENCION_ALT' => 0,
                'F354_TERCERO_VEND' => '22222221',
                'F354_NOTAS' => $notas_recibo,
            ]
        ];
    }
}

function tcc_obtener_datos_api($tipo, $datos) {
    $CI =& get_instance();
    $url = $CI->config->item('api_tcc')['url'];

    $client = new \GuzzleHttp\Client();

    try {
        $response = $client->post("{$url}/{$tipo}", [
            'body' => $datos,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'AccessToken' => $CI->config->item('api_tcc')['access_token'],
            ],
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    };
    
    return $response->getBody();
}

/**
 * Obtiene las bodegas creadas en el ERP de Siesa
 */
function obtener_bodegas_api($datos) {
    $filtro_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1 ;

    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');
    $parametros = "f150_rowid IS NOT NULL";

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsultaestandar", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'API_v2_Bodegas',
                'paginacion' => "numPag=$filtro_pagina|tamPag=100",
                'parametros' => $parametros,
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

/**
 * Obtiene todos los clientes creados en Siesa
 */
function obtener_clientes_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');

    $filtro_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1 ;

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsultaestandar", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'API_v2_Clientes',
                'paginacion' => "numPag=$filtro_pagina|tamPag=100",
                'parametros' => "f200_nit=''{$datos['numero_documento']}''",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

/**
 * Obtiene las cuentas por pagar a terceros
 */
function obtener_cuentas_por_pagar_api($datos) {
    $filtro_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1 ;

    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsultaestandar", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'API_v2_CxP_General',
                'paginacion' => "numPag=$filtro_pagina|tamPag=100",
                'parametros' => "f200_id=''{$datos['numero_documento']}''",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

/**
 * Obtiene el estado de cuenta de un cliente en Siesa
 */
function obtener_estado_cuenta_cliente_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsulta", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'Estado_Cuenta_cliente',
                'parametros' => "f200_id='{$datos['numero_documento']}'|f353_consec_docto_cruce='-1'",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_facturas_desde_pedido_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');

    $sucursal = str_pad($datos['id_sucursal'], 3, '0', STR_PAD_LEFT);

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsultaestandar", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'API_v2_Ventas_Facturas_DesdePedido',
                'paginacion' => 'numPag=1|tamPag=100',
                'parametros' => "f200_nit_fact=''{$datos['numero_documento']}'' and f350_consec_docto=''{$datos['documento_cruce']}'' and f461_id_sucursal_fact=''$sucursal''",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

/**
 * Obtiene las liastas de precio creadas en el ERP de Siesa
 */
function obtener_listas_precios_api($datos) {
    $filtro_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1 ;

    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');
    $parametros = "f112_ts IS NOT NULL";

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsultaestandar", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'API_v2_ListasDePrecios',
                'paginacion' => "numPag=$filtro_pagina|tamPag=100",
                'parametros' => $parametros,
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_movimientos_contables_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');
    $filtro_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1 ;

    $parametros = "f200_nit=''{$datos['numero_documento']}''";
    if(isset($datos['documento_cruce'])) $parametros .= "and f350_consec_docto=''{$datos['documento_cruce']}''";
    if(isset($datos['fecha'])) $parametros .= "and f350_fecha=''{$datos['fecha']}T00:00:00''";
    if(isset($datos['notas'])) $parametros .= "and f351_notas=''{$datos['notas']}''";
    if(isset($datos['notas_parciales'])) $parametros .= "and f351_notas LIKE ''{$datos['notas_parciales']}%''";
    if(isset($datos['estado'])) $parametros .= "and f350_ind_estado=''{$datos['estado']}''";
    if(isset($datos['fecha_inicial'])) $parametros .= "and f350_fecha>=''{$datos['fecha_inicial']}''";
    if(isset($datos['fecha_final'])) $parametros .= "and f350_fecha<=''{$datos['fecha_final']}''";
    if(isset($datos['filtro_retenciones'])) $parametros .= "and f253_id LIKE ''2365%''";

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsultaestandar", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'API_v2_MovtosContables_General',
                'paginacion' => "numPag=$filtro_pagina|tamPag=100",
                'parametros' => $parametros,
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_terceros_api($datos = null) {
    $nit_tercero = (isset($datos['numero_documento'])) ? "f200_nit=''{$datos['numero_documento']}''" : '' ;
    $filtro_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1 ;

    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsultaestandar", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'API_v2_Terceros',
                'paginacion' => "numPag=$filtro_pagina|tamPag=100",
                'parametros' => $nit_tercero,
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_transaccion_wompi($id) {
    $CI =& get_instance();
    $url = $CI->config->item('api_wompi_gateway')['url'];

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/transactions/$id");
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_inventario_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');

    $filtro_id = (isset($datos['id'])) ? $datos['id'] : '-1' ;
    $filtro_bodega = (isset($datos['bodega'])) ? $datos['bodega'] : '-1' ;

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsulta", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'Inventarios_V2',
                'parametros' => "IdItem='$filtro_id'|Bodega='$filtro_bodega'",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

/**
 * Obtiene las órdenes de compra creadas en Siesa
 */
function obtener_ordenes_compra($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');
    $filtro_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1 ;

    $parametros = "f420_rowid IS NOT NULL";
    if(isset($datos['numero_documento'])) $parametros .= " and f200_nit_prov=''{$datos['numero_documento']}''";
    if(isset($datos['id_producto'])) $parametros .= " and f120_id=''{$datos['id_producto']}''";
    if(isset($datos['fecha_final'])) $parametros .= " and f420_fecha<=''{$datos['fecha_final']}''";
    if(isset($datos['bodega'])) $parametros .= " and f150_id=''{$datos['bodega']}''";
    if(isset($datos['centro_operativo'])) $parametros .= " and f420_id_co=''{$datos['centro_operativo']}''";
    if(isset($datos['tipo_documento'])) $parametros .= " and f420_id_tipo_docto=''{$datos['tipo_documento']}''";
    if(isset($datos['numero_orden'])) $parametros .= " and f420_consec_docto=''{$datos['numero_orden']}''";

    // Enviaremos un filtro para que solo obtenga registros con rango de 5 minutos
    if(isset($datos['filtro_fecha'])) $parametros .= " and ".generar_filtro_ultimos_minutos('f420_fecha_ts_aprobacion');

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsultaestandar", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'API_v2_Compras_Ordenes',
                'paginacion' => "numPag=$filtro_pagina|tamPag=100",
                'parametros' => $parametros,
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_precios_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion_conekta');

    $filtro_id = (isset($datos['id'])) ? $datos['id'] : '-1' ;
    $filtro_lista_precio = (isset($datos['lista_precio'])) ? $datos['lista_precio'] : '-1' ;

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/connekta/v3/ejecutarconsulta", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa_connekta')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa_connekta')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa_connekta')['idCompania'],
                'descripcion' => 'muellesyfrenos_Price_list',
                'parametros' => "IdItem=$filtro_id|Lista_precio=$filtro_lista_precio",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_productos_api($datos = []) {
    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');

    $filtro_id = (isset($datos['id'])) ? $datos['id'] : '-1' ;
    $filtro_marca = (isset($datos['marca'])) ? trim($datos['marca']) : '-1' ;
    $filtro_grupo = (isset($datos['grupo'])) ? trim($datos['grupo']) : '-1' ;
    $filtro_linea = (isset($datos['linea'])) ? trim($datos['linea']) : '-1' ;

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsulta", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'Productos_V2',
                'parametros' => "Id_item='$filtro_id'|Marca='$filtro_marca'|Grupo='$filtro_grupo'|Linea='$filtro_linea'",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_pedidos_api($fecha = null) {
    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsulta", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'Pedidos_V2',
                'parametros' => "Fechaini='$fecha'|Fechafin='$fecha'|Nro_documento='-1'|Id_Tercero='-1'",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_pedidos_api_estandar($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');
    $filtro_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1 ;

    $parametros = "f430_rowid IS NOT NULL";
    if(isset($datos['numero_documento'])) $parametros .= " and f200_nit_pedido_fact=''{$datos['numero_documento']}''";
    if(isset($datos['centro_operativo'])) $parametros .= " and f430_id_co=''{$datos['centro_operativo']}''";
    if(isset($datos['tipo_documento'])) $parametros .= " and f430_id_tipo_docto=''{$datos['tipo_documento']}''";
    if(isset($datos['documento_cruce'])) $parametros .= " and f430_consec_docto=''{$datos['documento_cruce']}''";
    if(isset($datos['estado_id'])) $parametros .= " and f430_ind_estado=''{$datos['estado_id']}''";
    
    // Enviaremos un filtro para que solo obtenga registros con rango de 5 minutos
    if(isset($datos['filtro_fecha'])) $parametros .= " and ".generar_filtro_ultimos_minutos('f430_fecha_ts_aprobacion');

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsultaestandar", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'API_v2_Ventas_Pedidos',
                'paginacion' => "numPag=$filtro_pagina|tamPag=100",
                'parametros' => $parametros,
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

/**
 * Crea un pedido en Siesa
 */
function importar_pedidos_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];

    $client = new \GuzzleHttp\Client();

    try {
        $response = $client->post("$url/api/v3/conectoresimportar", [
            'body' => json_encode($datos),
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'idDocumento' => $CI->config->item('api_siesa')['idDocumentoImportacionPedido'],
                'idInterface' => $CI->config->item('api_siesa')['idInterface'],
                'nombreDocumento' => 'PEDIDOS',
            ],
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    };
    
    return $response->getBody();
}

/**
 * Crea un documento contable en Siesa
 */
function importar_documento_contable_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];

    $client = new \GuzzleHttp\Client();

    try {
        $response = $client->post("$url/api/v3/conectoresimportar", [
            'body' => json_encode($datos),
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'idDocumento' => $CI->config->item('api_siesa')['idDocumentoImportacionDocumentoContableV2'],
                'idInterface' => $CI->config->item('api_siesa')['idInterface'],
                'nombreDocumento' => 'API_v1_DocumentoContable', // DOCUMENTO_CONTABLE
                'validarEstructura' => 'false',
            ],
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    };
    
    return $response->getBody();
}