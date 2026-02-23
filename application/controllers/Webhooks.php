<?php
use PhpOffice\PhpSpreadsheet\IOFactory;

date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

ini_set('MAX_EXECUTION_TIME', '-1');
ini_set('memory_limit', '-1');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		31 de agosto de 2023
 * Programa:  	E-Commerce | Módulo de recepción de Webhooks
 *            	Gestión de pedidos, productos y clientes
 * Email: 		johnarleycano@hotmail.com
 */
class Webhooks extends MY_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();
        
        // Todas las respuestas se enviarán en formato JSON
        header('Content-type: application/json');

        $this->load->model(['productos_model', 'clientes_model', 'proveedores_model']);
    }

    function index() {
        redirect('inicio');
    }

    /**
     * Envía notificaciones por correo electrónico a los clientes que tienen
     * certificados tributarios pendientes de carga, y registra el resultado
     * del proceso (enviados y fallidos).
     *
     * - Obtiene los clientes sin fecha de envío registrada.
     * - Envía el correo usando el helper de email masivo.
     * - Actualiza la fecha de envío cuando el correo se envía correctamente.
     * - Genera un log detallado con el estado de cada cliente.
     * - Retorna un JSON con el resumen del proceso y el detalle del log.
     *
     * @return void
     */
    public function envio_certificados_masivo() {
        set_time_limit(0);

        // Obtener límite configurado
        $limite = $this->config->item('cantidad_datos');

        // Obtener solo pendientes (sin fecha_envio)
        $clientes = $this->clientes_model->obtener(
            'clientes_retenciones_informe',
            ['existe_fecha_envio' => true]
        );

        if (empty($clientes)) {
            echo json_encode([
                'exito' => false,
                'mensaje' => 'No hay clientes pendientes'
            ]);
            return;
        }

        //Se consulta de excel la lista de emails excluidos
        $emails_bloqueados = [];
        $nits_excluidos = [];

        $archivo_excel = $this->config->item('ruta_informe_retenciones');

        try {
            $spreadsheet = IOFactory::load($archivo_excel);

            // Hoja bd_no enviar columna D: email
            $hoja_no_enviar = $spreadsheet->getSheetByName('bd_no enviar');
            if ($hoja_no_enviar) {
                $ultima_fila = $hoja_no_enviar->getHighestRow();
                for ($i = 2; $i <= $ultima_fila; $i++) {
                    $email = trim($hoja_no_enviar->getCell("D$i")->getValue());
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $emails_bloqueados[] = strtolower($email);
                    }
                }
            }

            // Hoja retenciones columna M: excluir(si)
            $hoja_retenciones = $spreadsheet->getSheetByName('retenciones');
            if ($hoja_retenciones) {
                $ultima_fila = $hoja_retenciones->getHighestRow();
                for ($i = 2; $i <= $ultima_fila; $i++) {
                    $nit = trim($hoja_retenciones->getCell("A$i")->getValue());
                    $excluir = trim($hoja_retenciones->getCell("M$i")->getValue());

                    if ($nit && preg_match('/^si$/i', $excluir)) $nits_excluidos[] = $nit;
                }
            }

        } catch (Exception $e) {
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error leyendo archivo de exclusiones: ' . $e->getMessage()
            ]);
            return;
        }

        // Se realiza el envio masivo
        $enviados = 0;
        $fallidos = 0;
        $logs = [];
        $contador = 0;

        foreach ($clientes as $cliente) {
            if ($contador >= $limite) break;

            $email_cliente = strtolower(trim($cliente->email ?? ''));

            // Se excluye el nit de la columna M
            if (in_array($cliente->nit, $nits_excluidos)) {
                $logs[] = [
                    'nit' => $cliente->nit,
                    'enviado' => false,
                    'mensaje' => 'Excluido por columna M (retenciones)'
                ];
                continue;
            }

            // Excluir emails encontrados en la hoja bd_no enviar
            if ($email_cliente && in_array($email_cliente, $emails_bloqueados)) {
                $logs[] = [
                    'nit' => $cliente->nit,
                    'enviado' => false,
                    'mensaje' => 'Email en lista bd_no enviar'
                ];
                continue;
            }

            // Se envia al helper para construir el email
            $resultado = enviar_email_masivo_notificacion_certificados($cliente);

            if (isset($resultado['exito']) && $resultado['exito']) {
                // Marcar como enviado
                $this->db
                    ->where('nit', $cliente->nit)
                    ->update('clientes_retenciones_informe', [
                        'fecha_envio' => date('Y-m-d H:i:s')
                    ]);

                $logs[] = [
                    'nit' => $cliente->nit,
                    'enviado' => true
                ];
                $enviados++;
            } else {
                $logs[] = [
                    'nit' => $cliente->nit,
                    'enviado' => false,
                    'mensaje' => $resultado['mensaje'] ?? 'Error desconocido'
                ];
                $fallidos++;
            }

            $contador++;
        }

        echo json_encode([
            'exito' => true,
            'mensaje' => "Proceso terminado. Enviados: $enviados | Fallidos: $fallidos",
            'log' => $logs
        ]);
    }

    function email_test($id) {
        // Se obtienen todos los datos de la factura
        $recibo = $this->productos_model->obtener('recibo', [
            'id' => $id
        ]);

        // enviar_email_pedido($recibo);
        // enviar_email_factura_wompi($recibo);
        // enviar_email_factura_wompi_comprobante($recibo);
    }

    /**
    * Función que captura los objetos JSON con los datos de las guías
    * de Roa Transportes y almacena su información en la base de datos
    **/
    function envios() {
        // Recepción de los datos que llegan
        $post = preg_replace('/(\w+):/', '"$1":', file_get_contents('php://input'));
        $datos = json_decode($post, true);

        $datos_log = [
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observacion' => json_encode($datos),
            'log_tipo_id' => 71,
        ];

        $this->configuracion_model->crear('logs', $datos_log);

        print json_encode([
            // 'errores' => $errores,
            'resultado' => $datos,
            'datos_pedido' => (isset($datos_pedido)) ? $datos_pedido : [],
            'datos_movimiento_contable' => (isset($documento_contable)) ? $documento_contable : [],
        ]);
        
        return (false) ? http_response_code(400) : http_response_code(201);
    }

    function tcc($tipo) {
        switch ($tipo) {
            case 'consultar_liquidacion':
                $datos = '{
                    "tipoenvio": "1",               
                    "idciudadorigen": "05001000",   
                    "idciudaddestino": "11001000",  
                    "valormercancia": "250000",
                    "boomerang": "0",               
                    "identificacion": "900296641",  
                    "cuenta": "5625200",            
                    "fecharemesa": "2025-09-12",    
                    "idunidadnegocio": "2",         
                    "unidades": [
                        {
                            "numerounidades": "1",
                            "pesoreal": "10",
                            "pesovolumen": "15",
                            "alto": "40",           
                            "largo": "40",
                            "ancho": "20",
                            "tipoempaque": ""
                        }
                    ]
                }';

                $peticion = tcc_obtener_datos_api('/tarifas/v5/consultarliquidacion', $datos);
                $resultado = (object)json_decode($peticion, true);
                print_r($resultado);
                echo $resultado->idliquidacion;

                if($resultado->codigoResultado != '0') {
                    echo $resultado->idLiquidacion;
                }
            break;

            case 'grabar_despacho':
                $datos = '{
                    "numerodespacho" : null, // Puede ser nulo de esta manera TCC genera el numero del despacho
                    "fechadespacho" : "2025-09-11", // Fecha en la que se realiza el envío (YYYY-MM-DD)
                    // Se diligencia solo si se va a solicitar la recogida
                    "solicitudrecogida" : {
                        "numero" : null,
                        "fecha" : null,
                        "ventanainicio" : null,
                        "ventanafin" : null
                    },
                    "unidadnegocio" : "1",  // 1: Paqueteria; 2: Mensajeria
                    "cuentaremitente" : "1485100", // Numero de acuerdo comercial asignado (1485100: Paquetería; 5625200: Mensajería)
                    "sederemitente" : null, // Código de la sede del cliente desde donde será generado el despacho, el cual es acordado con TCC
                    "primernombreremitente" : "John",
                    "segundonombreremitente" : "Arley",
                    "primerapellidoremitente" : "Cano",
                    "segundoapellidoremitente" : "Salinas",
                    "razonsocialremitente" : "John Arley Cano Salinas",
                    "contactoremitente" : "John Arley Cano", // Nombre de la persona que se debe contactar al momento de hacer la recogida de mercancia
                    "tipoidentificacionremitente" : "CC", // CC, NIT
                    "identificacionremitente" : "1017177502",
                    "direccionremitente" : "Transversal 38AA # 59A 231",
                    "ciudadorigen" : "05360000",
                    "telefonoremitente" : "3134587623",
                    "emailremitente" : "johnarleycano@hotmail.com",
                    // Máximo 100 destinatarios
                    "destinatarios" : [
                        {
                            "numerocontrol" : "1",  // Consecutivo
                            "numeroremesa" : null,  // Puede ser nulo de esta manera TCC genera el numero del despacho
                            "numeroreferenciacliente" : null,   // Numero de referencia del pedido
                            "tipoidentificaciondestinatario" : "CC",
                            "identificaciondestinatario" : "1017250261",
                            "sededestinatario" : null,  // Código de la sede del cliente destinatario
                            "primernombredestinatario" : "Yasmin",
                            "segundonombredestinatario" : "Daniela",
                            "primerapellidodestinatario" : "Muñoz",
                            "segundoapellidodestinatario" : "Marulanda",
                            "razonsocialdestinatario" : "YASMIN DANIELA MUÑOZ MARULANDA",
                            "contactodestinatario" : "YASMIN DANIELA MUÑOZ MARULANDA",
                            "direcciondestinatario" : "Carera 69B # 25B - 08",
                            "telefonodestinatario" : "3143618016",
                            "ciudaddestino" : "05631000",
                            "formapago" : null,
                            "llevabodega" : null,   // Indicador si el cliente lleva la mercancia a una bodega de TCC para su despacho (SI, NO)
                            "recogebodega" : null,  // Indicador si el cliente destinatario del despacho se acerca a una bodega de TCC a reclamar su mercancia (SI, NO)
                            "centrocostos" : null,  // Centro de costos del remintente
                            /*
                            TCC Paqueteria.
                            TISE_NORMAL_PAQ --> Tipo de servicio Normal (Paqueteria).
                            
                            TCC Mensajeria.
                            TISE_NORMAL_MEN --> Tipo de servicio Normal (Mensajeria). TISE_9AM --> Tipo de servicio 9 AM .
                            TISE_RD_NORMAL --> Tipo de servicio Radicación de Documentos. TISE_RD_DIGITAL --> Tipo de servicio Radicación de Documentos Digital.
                            TISE_RD_MIXTO --> Tipo de servicio Radicación de Documentos Mixto.
                            */
                            "tiposervicio" : "TISE_NORMAL_PAQ",
                            "observaciones" : "Prueba",
                            "recaudoproducto" : null,   // aquí se debe llevar el valor total a recaudar al momento de realizar la entrega de la mercancía al destinatario
                            "unidades" : [
                                {
                                    /*
                                    TIPO_UND_PAQ --> Unidad Tipo Paquete
                                    TIPO_UND_DOCB --> Unidad Tipo Fisico Normal)
                                    TIPO_UND_BOOM_FS --> Unidad Tipo Fisico Normal)
                                    TIPO_UND_DOCD --> Unidad Tipo Dardo 
                                    TIPO_UND_BOOM_DGT --> Unidad Tipo Digital
                                    TIPO_UND_BOOM_MXT --> unidad Tipo Mixto
                                    */
                                    "tipounidad" : "TIPO_UND_PAQ",
                                    /*
                                    Determina la clase de empaque, según unidad de negocio, de la seguiente forma:
                                    
                                    TCC Paqueteria:
                                    CLEM_CAJA
                                    CLEM_SOBRE
                                    CLEM_LIO
                                    
                                    TCC Mensajeria:
                                    CLEM_PEQUENA
                                    CLEM_MEDIANA
                                    CLEM_GRANDE
                                    CLEM_MINI
                                    CLEM_SOBRE_MEN
                                    */
                                    "claseempaque" : "CLEM_CAJA",
                                    "tipoempaque" : null,   // Corresponde al tipo de empaque comercial acordado con el cliente
                                    "dicecontener" : "Repuestos",
                                    "kilosreales" : "10",   // Peso real de la unidad
                                    "largo" : "1",  // Dado en centimetros. Si no se tiene la información debe enviarse un cero (0)
                                    "alto" : "1",   // Dado en centimetros. Si no se tiene la información debe enviarse un cero (0)
                                    "ancho" : "1",  // Dado en centimetros. Si no se tiene la información debe enviarse un cero (0)
                                    // Si son enviadas las dimensiones, estas priman sobre el valor enviado en volumen, tambien si no se tiene el volumen se puede enviar Cero (0), siempre y cuando se envie el peso real(ancho (metros) * largo (metros) * alto (metros)) * 400 -> (0.4 * 0.4 * 0.3) * 400
                                    "pesovolumen" : "10",
                                    "valormercancia" : "1200000",
                                    "codigobarras" : null,
                                    "numerobolsa" : null,   // Opera solo TCC Mensajeria
                                    "referencias" : null,   // Opera solo TCC Mensajeria
                                    "unidadesinternas" : "1"    // Número de unidades internas contenidas
                                }
                            ],
                            "documentosreferencia" : [
                                {
                                    "tipodocumento" : null,
                                    "numerodocumento" : null,
                                    "fechadocumento" : null
                                }
                            ]
                        }
                    ]
                }';

                $peticion = tcc_obtener_datos_api('/remesas/grabardespacho8', $datos);
                $resultado = (object)json_decode($peticion, true);
                print_r($resultado);
            break;

            case 'anular_despacho':
                $datos = '{
                    "numeroremesa": "446837119",    // Número de guía
                    "fechadespacho": "2025-09-11",  // (YYYY-MM-DD)
                    "cuentaremitente": "1485100"    // Numero de acuerdo comercial asignado (1485100: Paquetería; 5625200: Mensajería)
                }';

                $peticion = tcc_obtener_datos_api('/remesas/anulardespacho', $datos);
                $resultado = (object)json_decode($peticion, true);
                print_r($resultado);
            break;
        }
    }

    /**
    * Función que captura el objeto JSON con los datos de la transacción de Wompi
    * Y almacena el id de la transacción, para futuras consultas
    **/
    function pedido($referencia = null) {
        // Si trae referencia, es un pago a crédito
        if($referencia) {
           $datos_pago = [ 'reference' => $referencia ];
        } else {
            $post = file_get_contents('php://input');
            $datos_pago = json_decode($post, true)['data']['transaction'];
        }

        $datos_log = [
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observacion' => $datos_pago['reference'],
        ];

        // Tomamos las iniciales de la referencia para saber qué tipo de documento va a guardar
        $tipo_documento = explode("-", $datos_pago['reference']);

        // Si es pedido, se ejecuta la gestión de un pedido
        if($tipo_documento[0] == 'pe' || $tipo_documento[0] == 'pc') {
            // Se agrega log
            $datos_log['log_tipo_id'] = 50;
            $this->configuracion_model->crear('logs', $datos_log);

            $this->gestionar_pedido($datos_pago);
        // Si es el pago de una factura del estado de cuenta, se ejecuta la gestión de un pedido
        } elseif($tipo_documento[0] == 'ec') {
            // Se agrega log
            $datos_log['log_tipo_id'] = 14;
            $this->configuracion_model->crear('logs', $datos_log);

            $this->gestionar_estado_cuenta($datos_pago);
        // Para el resto de las transacciones de Wompi
        } else {
            // Se agrega log
            $datos_log['log_tipo_id'] = 56;
            $this->configuracion_model->crear('logs', $datos_log);

            $this->gestionar_otras_transacciones_wompi($datos_pago);
        }
    }

    function gestionar_estado_cuenta($datos) {
        $errores = 0;
        $resultado = [];

        // Se obtienen todos los datos del recibo con el token que se almacena como referencia
        $recibo = $this->productos_model->obtener('recibo', ['token' => $datos['reference']]);

        // Si se vuelve a ejecutar el wehbook, se ve mensaje
        if($recibo->actualizado_webhook == 1) array_push($resultado, ['Se volvió a ejecutar el webhook']);

        // Si no ha sido actualizado por el webhook
        if($recibo->actualizado_webhook == 0) {
            // Tabla, condiciones, datos
            $actualizar_recibo = $this->productos_model->actualizar('recibos', ['token' => $datos['reference']], [
                'actualizado_webhook' => 1,
                'wompi_transaccion_id' => $datos['id'],
                'wompi_status' => $datos['status'],
                'wompi_datos' => json_encode($datos),
                'recibo_estado_id' => ($datos['status'] == 'APPROVED') ? 1 : 2,
                'fecha_actualizacion' => date('Y-m-d H:i:s'),
            ]);

            // Se actualiza el recibo con el id de la transacción
            if(!$actualizar_recibo) {
                array_push($resultado, ['El recibo no se pudo actualizar']);
                $errores++;
            }

            // Si el pago fue aprobado
            if($datos['status'] != 'APPROVED') {
                array_push($resultado, ['No se creó documento contable, porque el pago fue rechazado']);

                // Se agrega log
                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 36,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => json_encode($datos),
                ]);
            }
            
            // Si el pago fue aprobado
            if($datos['status'] == 'APPROVED') {
                $documento_contable = crear_documento_contable($recibo->id, $datos);
                array_push($resultado, $documento_contable);
            }
        }

        print json_encode([
            'errores' => $errores,
            'resultado' => $resultado,
            'datos_pedido' => (isset($datos_pedido)) ? $datos_pedido : [],
            'datos_movimiento_contable' => (isset($documento_contable)) ? $documento_contable : [],
        ]);
        
        return ($errores > 0) ? http_response_code(400) : http_response_code(200);
    }

    function gestionar_pedido($datos = null) {
        $errores = 0;
        $resultado = [];
        
        $token = $datos['reference'];
        $id_transaccion = (isset($datos['id'])) ? $datos['id'] : null ;
        $estado_transaccion = (isset($datos['status'])) ? $datos['status'] : null ;

        // Tomamos las iniciales de la referencia para saber qué tipo de documento va a guardar
        $tipo_documento = explode("-", $datos['reference']);

        // Se obtienen todos los datos del recibo
        $recibo = $this->productos_model->obtener('recibo', ['token' => $token]);

        // Si se vuelve a ejecutar el wehbook, se ve mensaje
        if($recibo->actualizado_webhook == 1) array_push($resultado, ['Se volvió a ejecutar el webhook']);
        
        // Si no ha sido actualizado por el webhook
        if($recibo->actualizado_webhook == 0) {
            // Tabla, condiciones, datos
            $actualizar_recibo = $this->productos_model->actualizar('recibos', ['token' => $token], [
                // 'actualizado_webhook' => 1,
                'wompi_transaccion_id' => $id_transaccion,
                'wompi_status' => $estado_transaccion,
                'wompi_datos' => (isset($datos['id'])) ? json_encode($datos) : null ,
                'recibo_estado_id' => ($estado_transaccion == 'APPROVED') ? 1 : 2,
            ]);

            // Se actualiza el recibo con el id de la transacción
            if(!$actualizar_recibo) {
                array_push($resultado, ['El recibo no se pudo actualizar']);
                $errores++;
            }

            // Se obtienen todos los datos del recibo
            $recibo = $this->productos_model->obtener('recibo', ['token' => $token]);

            // Si no existe el recibo
            if(empty($recibo)) {
                array_push($resultado, ['El recibo no se encontró']);
                $errores++;
            }

            // Si es un pedido a crédito o el pago a contado fue aprobado
            if($tipo_documento[0] == 'pc' || $estado_transaccion == 'APPROVED') {
                // Se envía el correo electrónico con la confirmación del pedido (Error o éxito)
                enviar_email_pedido($recibo);

                // Según el tipo de pedido
                $tipo_pedido = ($tipo_documento[0] == 'pc') ? "CPV" : "CPE" ;
                $tipo_cliente = ($tipo_documento[0] == 'pc') ? "C001" : "C005" ;
                $forma_pago = ($tipo_documento[0] == 'pc') ? "C30" : "CNT" ;

                $notas_pedido = substr("- Pedido $recibo->id eCommerce | Referencia: $token | ID de Transacción: $id_transaccion - Dirección de entrega: $recibo->direccion_envio | Tel: $recibo->telefono | $recibo->email_factura_electronica | $recibo->ubicacion_envio | $recibo->comentarios", 0, 254);

                $recibo_detalle = $this->productos_model->obtener('recibos_detalle', ['rd.recibo_id' => $recibo->id]);
                
                $movimientos = [];
                foreach($recibo_detalle as $item) {
                    array_push($movimientos, [
                        "f431_id_co" => "400", // Valida en maestro, código de centro de operación del documento
                        "f431_id_tipo_docto" => $tipo_pedido, // Valida en maestro, código de tipo de documento, tipo de documento del pedido
                        "f431_consec_docto" => 1, // Numero de documento del pedido
                        "f431_nro_registro" => $item->id, // Numero de registro del movimiento
                        "f431_id_item" => $item->producto_id, // Codigo, es obligatorio si no va referencia ni codigo de barras
                        "f431_id_bodega" => "00555", // Valida en maestro, código de bodega
                        "f431_id_motivo" => "01",  // Valida en maestro, código de motivo
                        "f431_id_co_movto" => "400", // Valida en maestro, código de centro de operación del movimiento
                        "f431_id_un_movto" => "", // Valida en maestro, código de unidad de negocio del movimiento. Si es vacio el sistema la calcula
                        "f431_fecha_entrega" => "{$recibo->anio}{$recibo->mes}{$recibo->dia}", // El formato debe ser AAAAMMDD
                        "f431_num_dias_entrega" => 0,
                        "f431_id_lista_precio" => ($item->lista_precio) ? $item->lista_precio : $recibo->lista_precio,
                        "f431_id_unidad_medida" => $item->unidad_inventario, // Valida en maestro, código de unidad de medida del movimiento
                        "f431_cant_pedida_base" => $item->cantidad,
                        "f431_precio_unitario" => floatval($item->precio) - floatval($item->descuento),
                        "f431_notas" => $notas_pedido, // Notas del movimiento
                        "f431_ind_precio" => 2,
                    ]);
                }

                $datos_pedido = [
                    "Pedidos" => [
                        [
                            "f430_id_co" => "400",  // Valida en maestro, código de centro de operación del documento
                            "f430_id_tipo_docto" => $tipo_pedido,  // Valida en maestro, código de tipo de documento
                            "f430_consec_docto" => 1, // Numero de documento
                            "f430_id_fecha" => "{$recibo->anio}{$recibo->mes}{$recibo->dia}", // El formato debe ser AAAAMMDD
                            "f430_id_tercero_fact" => $recibo->documento_numero, // Valida en maestro, código de tercero cliente
                            "f430_id_sucursal_fact" => str_pad($recibo->sucursal_id, 3, '0', STR_PAD_LEFT), // Valida en maestro el codigo de la sucursal del cliente a facturar
                            "f430_id_tercero_rem" => $recibo->documento_numero, // Valida en maestro , codigo del tercero del cliente a despachar
                            "f430_id_sucursal_rem" => str_pad($recibo->sucursal_id, 3, '0', STR_PAD_LEFT), // Valida en maestro el codigo de la sucursal del cliente a despachar
                            "f430_id_tipo_cli_fact" => $tipo_cliente, // Valida en maestro, tipo de clientes. Si es vacio la trae del cliente a facturar
                            "f430_id_co_fact" => "400", // Valida en maestro, código de centro de operación del documento
                            "f430_fecha_entrega" => "{$recibo->anio}{$recibo->mes}{$recibo->dia}", // El formato debe ser AAAAMMDD
                            "f430_num_dias_entrega" => 0, // Valida Nro de dias en que se estima, la entrega del pedido
                            "f430_num_docto_referencia" => $recibo->id, // Valida la orden de compra del documento
                            "f430_id_cond_pago" => $forma_pago, // Valida en maestro, condiciones de pago
                            "f430_notas" => $notas_pedido, // Observaciones
                            "f430_id_tercero_vendedor" => ($recibo->tercero_vendedor_nit) ? $recibo->tercero_vendedor_nit : "22222221", // Si el cliente seleccionó vendedor, se envía en el paquete
                        ]
                    ],
                    "Movimientos" => $movimientos
                ];

                $resultado_pedido = json_decode(importar_pedidos_api($datos_pedido));
                $codigo_resultado_pedido = $resultado_pedido->codigo;
                $detalle_resultado_pedido = json_encode($resultado_pedido->detalle);
                array_push($resultado, ['pedido' => $detalle_resultado_pedido]);

                // Si no se pudo crear el pedido
                if($codigo_resultado_pedido == '1') {
                    // Se agrega log
                    $this->configuracion_model->crear('logs', [
                        'log_tipo_id' => 18,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                        'observacion' => json_encode($detalle_resultado_pedido)
                    ]);

                    $errores++;
                }

                // Si se ejecutó correctamente
                if($codigo_resultado_pedido == '0') {
                    // Se agrega log
                    $this->configuracion_model->crear('logs', [
                        'log_tipo_id' => 15,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                        'observacion' => json_encode($datos_pedido)
                    ]);

                    // Si es un pedido a contado, se crea el documento contable
                    if((isset($datos['id']))) {
                        $documento_contable = crear_documento_contable_pedido($recibo->id, $datos);
                        array_push($resultado, $documento_contable);
                    } else {
                        // Actualización del estado del pedido a Aprobado
                        $this->productos_model->actualizar('recibos', ['token' => $datos['reference']], [ 'recibo_estado_id' => 1 ]);
                    }
                }
            }
        }

        print json_encode([
            'exito' => ($errores == 0),
            'errores' => $errores,
            'resultado' => $resultado,
            'datos_pedido' => (isset($datos_pedido)) ? $datos_pedido : [],
            'datos_movimiento_contable' => (isset($documento_contable)) ? $documento_contable['resultado'] : [],
        ]);

        return ($errores > 0) ? http_response_code(400) : http_response_code(200);
    }

    /**
     * Captura los eventos de Wompi y crea un recibo para las transacciones
     * distintas a las generadas en el Ecommerce
     */
    function gestionar_otras_transacciones_wompi($datos) {
        $errores = 0;
        $resultado = [];
        $datos_cliente = $datos['payment_method']['extra'];

        // Arreglo para almacenamiento del recibo
        $datos_recibo = [
            'actualizado_webhook' => 1,
            'documento_numero' => (isset($datos_cliente['serviceNIT'])) ? $datos_cliente['serviceNIT'] : null,
            'razon_social' => isset($datos_cliente['fullName']) ? strtoupper($datos_cliente['fullName']) : null,
            'direccion' => isset($datos_cliente['address']) ? strtoupper($datos_cliente['address']) : null,
            'email' => isset($datos_cliente['email']) ? $datos_cliente['email'] : null,
            'email_factura_electronica' => isset($datos_cliente['email']) ? $datos_cliente['email'] : null,
            'telefono' => isset($datos_cliente['cellphoneNumber']) ? $datos_cliente['cellphoneNumber'] : null,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'token' => $datos['reference'],
            'valor' => floatval($datos['amount_in_cents']) / 100,
            'wompi_datos' => json_encode($datos),
            'wompi_status' => $datos['status'],
            'wompi_transaccion_id' => $datos['id'],
            'recibo_estado_id' => ($datos['status'] == 'APPROVED') ? 1 : 2,
            'recibo_tipo_id' => 4,
        ];

        // Se almacena el recibo en la base de datos
        $id_recibo = $this->productos_model->crear('recibos', $datos_recibo);

        // Si no se guardó el recibo
        if(!$id_recibo) {
            $errores++;

            print json_encode([
                'errores' => $errores,
                'resultado' => 'No se pudo guardar el recibo',
            ]);
        } else {
            // Se agrega log
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 57,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "Recibo $id_recibo",
            ]);

            print json_encode([
                'errores' => $errores,
                'resultado' => 'Se almacenó el recibo correctamente',
                'id' => $id_recibo
            ]);
        }      

        return ($errores > 0) ? http_response_code(400) : http_response_code(200);
    }

    /**
     * Descarga datos de la base de datos SQL Server
     * del WMS
     *
     */
    function importar_datos_wms($tabla, $fecha = null) {
        $this->load->model(['webhooks_model']);

        $tiempo_inicial = microtime(true);
        $datos = [];

        try {
            $filtro_fecha = ($fecha) ? $fecha : date('Y-m-d') ;

            switch ($tabla) {
                case 'pedidos':
                    // Primero, eliminamos todos los ítems
                    $this->clientes_model->eliminar('wms_pedidos', ['FechaDocumento' => $filtro_fecha]);

                    $resultado = $this->webhooks_model->obtener('wms_pedidos', ['fecha_documento' => $filtro_fecha]);
                    $total_items = count($resultado);
                    
                    if(!empty($resultado)) $this->clientes_model->crear('wms_pedidos', $resultado);

                    $tiempo_final = microtime(true);

                    $respuesta = [
                        'log_tipo_id' => 73,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                        'observacion' => "$total_items registros actualizados",
                        'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
                    ];

                    // Se agrega el registro en los logs
                    $this->configuracion_model->crear('logs', $respuesta);

                    print json_encode($respuesta);
                    return http_response_code(200);
                break;

                case 'pedidos_tracking':
                    // Primero, eliminamos todos los ítems
                    $this->clientes_model->eliminar('wms_pedidos_tracking', ["CAST( Fecha AS DATE ) = " => $filtro_fecha]);

                    $resultado = $this->webhooks_model->obtener('wms_pedidos_tracking', ['fecha' => $filtro_fecha]);
                    $total_items = count($resultado);

                    if(!empty($resultado)) $this->clientes_model->crear('wms_pedidos_tracking', $resultado);

                    $tiempo_final = microtime(true);

                    $respuesta = [
                        'log_tipo_id' => 83,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                        'observacion' => "$total_items registros actualizados",
                        'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
                    ];

                    // Se agrega el registro en los logs
                    $this->configuracion_model->crear('logs', $respuesta);

                    print json_encode($respuesta);
                    return http_response_code(200);
                break;
            }
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 72,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            return http_response_code(400);
        }
    }
}
/* Fin del archivo Webhooks.php */
/* Ubicación: ./application/controllers/Webhooks.php */