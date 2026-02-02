<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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
    public function envio_certificados_masivo()
    {
        set_time_limit(0);

        // Obtener límite configurado
        $limite = $this->config->item('cantidad_datos');

        // Obtener solo pendientes (sin fecha_envio)
        $clientes = $this->clientes_model->obtener('clientes_retenciones_informe', ['existe_fecha_envio' => true]);

        if (empty($clientes)) {
            echo json_encode(['exito' => false, 'mensaje' => 'No hay clientes pendientes']);
            return;
        }

        $enviados = 0;
        $fallidos = 0;
        $logs = [];
        $contador = 0;

        foreach ($clientes as $cliente) {
            // Controlar cantidad máxima
            if ($contador >= $limite) break;

            $resultado = enviar_email_masivo_notificacion_certificados($cliente);

            // Se valida si el envio fue exitoso
            if (isset($resultado['exito']) && $resultado['exito']) {
                // Marcar como enviado
                $this->db
                    ->where('nit', $cliente->nit)
                    ->update('clientes_retenciones_informe', [
                        'fecha_envio' => date('Y-m-d H:i:s')
                    ])
                ;

                $logs[] = [
                    'nit' => $cliente->nit,
                    'enviado' => true
                ];
                $enviados++;
            } else {
                $logs[] = [
                    'nit'   => $cliente->nit,
                    'enviado'=> false,
                    'mensaje' => $resultado['mensaje'] ?? 'Desconocido'
                ];
                $fallidos++;
            }

            $contador++;
        }

        echo json_encode([
            'exito'   => true,
            'mensaje' => "Proceso terminado. Enviados: $enviados | Fallidos: $fallidos",
            'log'     => $logs
        ]);
    }

    /**
     * Lee un archivo de Excel con las retenciones de los clientes
     * y crea o actualiza los registros en la base de datos.
     * También procesa los contactos adicionales (celular y email)
     * 
     * @return void
     */
    function importar_retenciones_clientes()
    {
        $errores = 0;
        $resultado = [];
        $inicio = microtime(true);
        $fecha_inicio = date('Y-m-d H:i:s');

        // Ruta
        $archivo_excel = $this->config->item('ruta_informe_retenciones');
        
        if (!file_exists($archivo_excel)) {
            $errores++;
            $resultado[] = 'El archivo de informe de retenciones no existe en la ruta configurada';
        }

        $procesados = 0;
        $contactos_creados = 0;
        $contactos_omitidos = 0;
        $anio = date('Y') - 1;

        if ($errores === 0) {
            try {
                $spreadsheet = IOFactory::load($archivo_excel);
                
                // Cargar la hoja específica "retenciones"
                $hoja = $spreadsheet->getSheetByName('retenciones');
                
                if (!$hoja) throw new Exception('No se encontró la hoja "retenciones" en el archivo Excel');
                
                $ultima_fila = $hoja->getHighestRow();

                for ($fila = 2; $fila <= $ultima_fila; $fila++) {
                    $nit = trim($hoja->getCell('A' . $fila)->getValue());
                    
                    if (!$nit || !is_numeric($nit)) continue;

                    // Datos para la tabla clientes_retenciones_informe
                    $datos = [
                        'anio' => $anio,
                        'nit' => $nit,
                        'razon_social' => trim($hoja->getCell("B$fila")->getValue()),
                        'vendedor' => trim($hoja->getCell("C$fila")->getValue()),
                        'valor_retencion_fuente' => (float) $hoja->getCell("D$fila")->getValue(),
                        'valor_retencion_ica' => (float) $hoja->getCell("E$fila")->getValue(),
                        'valor_retencion_iva' => (float) $hoja->getCell("F$fila")->getValue(),
                    ];

                    // Validar si existe el registro de retención
                    $existe = $this->clientes_model->obtener('clientes_retenciones_informe', [ 'anio' => $anio, 'nit' => $nit]);

                    if ($existe) {
                        $datos['fecha_actualizacion'] = date('Y-m-d H:i:s');
                        $this->clientes_model->actualizar('clientes_retenciones_informe', ['id' => $existe->id], $datos);
                    } else {
                        $datos['fecha_creacion'] = date('Y-m-d H:i:s');
                        $this->clientes_model->crear('clientes_retenciones_informe', $datos);
                    }

                    $procesados++;

                    // Procesar contactos adicionales
                    $celular_adicional = trim($hoja->getCell("J$fila")->getValue());
                    $email_adicional = trim($hoja->getCell("K$fila")->getValue());

                    // Validar datos
                    $tiene_celular = !empty($celular_adicional) && is_numeric($celular_adicional);
                    $tiene_email = !empty($email_adicional) && filter_var($email_adicional, FILTER_VALIDATE_EMAIL);

                    // Si tiene al menos uno de los dos, procesar
                    if ($tiene_celular || $tiene_email) {
                        
                        // Preparar los datos base del contacto
                        $datos_contacto = [
                            'nit' => $nit,
                            'modulo_id' => 8,
                            'fecha_creacion' => date('Y-m-d H:i:s')
                        ];

                        // Agregar el número si existe
                        if ($tiene_celular) $datos_contacto['numero'] = $celular_adicional;

                        // Agregar el email si existe
                        if ($tiene_email) $datos_contacto['email'] = $email_adicional;

                        // Verificar si ya existe un contacto con estos datos exactos
                        $debe_crear = true;
                        
                        // Si tiene ambos, verificar que no exista la combinación
                        if ($tiene_celular && $tiene_email) {
                            $existe_combinacion = $this->db
                                ->where('nit', $nit)
                                ->where('numero', $celular_adicional)
                                ->where('email', $email_adicional)
                                ->where('modulo_id', 8)
                                ->get('terceros_contactos')
                                ->row();
                            
                            if ($existe_combinacion) {
                                $debe_crear = false;
                            }
                        }

                        // Si solo tiene celular, verificar que no exista el número
                        elseif ($tiene_celular) {
                            $existe_numero = $this->configuracion_model->obtener('contactos', ['nit' => $nit,'numero' => $celular_adicional, 'modulo_id' => 8]);
                            
                            if ($existe_numero) $debe_crear = false;
                        }

                        // Si solo tiene email, verificar que no exista el email
                        elseif ($tiene_email) {
                            $existe_email = $this->configuracion_model->obtener('contactos', ['nit' => $nit, 'email' => $email_adicional, 'modulo_id' => 8 ]);
                            
                            if ($existe_email) $debe_crear = false;
                        }

                        // Crear el contacto con TODOS los datos que tenga
                        if ($debe_crear) {
                            $this->configuracion_model->crear('tercero_contacto', $datos_contacto);
                            $contactos_creados++;
                        } else {
                            $contactos_omitidos++;
                        }
                    }
                }
            } catch (Exception $e) {
                $errores++;
                $resultado[] = 'Error al procesar el archivo: ' . $e->getMessage();
            }
        }

        $fin = microtime(true);
        $fecha_fin = date('Y-m-d H:i:s');

        // Log del proceso
        $this->configuracion_model->crear('logs', [
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observacion' => json_encode([
                'inicio' => $fecha_inicio,
                'fin' => $fecha_fin,
                'registros_procesados' => $procesados,
                'contactos_creados' => $contactos_creados,
                'contactos_omitidos' => $contactos_omitidos
            ]),
            'log_tipo_id' => 103
        ]);

        $resultado[] = [
            'registros_procesados' => $procesados,
            'contactos_creados' => $contactos_creados,
            'contactos_omitidos' => $contactos_omitidos,
            'tiempo_ejecucion' => round($fin - $inicio, 2) . ' segundos'
        ];

        print json_encode([
            'errores' => $errores,
            'resultado' => $resultado,
        ]);

        return ($errores > 0) ? http_response_code(400) : http_response_code(200);
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
                // enviar_email_pedido($recibo);

                // $tipo_pedido = ($tipo_documento[0] == 'pc') ? "CPV" : "CPE" ;
                $tipo_pedido = "CPE";

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
                            "f430_id_tipo_cli_fact" => "C005", // Valida en maestro, tipo de clientes. Si es vacio la trae del cliente a facturar
                            "f430_id_co_fact" => "400", // Valida en maestro, código de centro de operación del documento
                            "f430_fecha_entrega" => "{$recibo->anio}{$recibo->mes}{$recibo->dia}", // El formato debe ser AAAAMMDD
                            "f430_num_dias_entrega" => 0, // Valida Nro de dias en que se estima, la entrega del pedido
                            "f430_num_docto_referencia" => $recibo->id, // Valida la orden de compra del documento
                            "f430_id_cond_pago" => "CNT", // Valida en maestro, condiciones de pago
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

    /**
     * Importa de Siesa los productos y su información básica
     */
    function importar_productos_detalle() {
        $tiempo_inicial = microtime(true);

        try {
            $resultado_productos = json_decode(obtener_productos_api());
            $codigo_producto = $resultado_productos->codigo;
            $productos = ($codigo_producto == 0) ? $resultado_productos->detalle->Table : 0 ;
            $fecha_actualizacion = date('Y-m-d H:i:s');
            $datos = [];
            $total_items = 0;

            // Si encontró datos
            if($codigo_producto != 1) {
                foreach($productos as $producto) {
                    $nuevo_producto = [
                        'id' => $producto->IdItem,
                        'descripcion_corta' => $producto->Descripcion_Corta,
                        'referencia' => str_replace("*", "x", $producto->Referencia), // Cambia los * por letra x, pues Codeigniter no los permite por defecto
                        'unidad_inventario' => $producto->Unidad_Inventario,
                        'notas' => $producto->Notas,
                        'tipo_inventario' => $producto->Tipo_Inventario,
                        'marca' => $producto->Marca,
                        'linea' => $producto->Linea,
                        'grupo' => $producto->Grupo,
                        'fecha_actualizacion' => $fecha_actualizacion,
                        'fecha_actualizacion_api' => $producto->Fecha_Actualizacion,
                    ];

                    array_push($datos, $nuevo_producto);
                }

                // Si hay datos, se borran los registros anteriores
                if(!empty($datos)) {
                    $this->productos_model->eliminar('productos', 'id is  NOT NULL');
                    $total_items = $this->productos_model->crear('productos', $datos);
                } 
            }

            $tiempo_final = microtime(true);

            $resultado = [
                'items' => number_format($total_items, 0, '', '.'),
                'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos"
            ];

            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 4,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => json_encode($resultado)
            ]);

            print json_encode($resultado);

            $this->db->close();

            return http_response_code(200);
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 5,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            print json_encode([
                'error' => true,
                'descripcion' => 'Ocurrió un error al ejecutar el script'
            ]);

            return http_response_code(400);
        }
    }

    /**
     * Importa de Siesa el inventario disponible de cada producto
     */
    function importar_productos_inventario() {
        $tiempo_inicial = microtime(true);
        
        try {
            // Filtro de la bodega
            $filtro_bodega = $this->input->get('bodega');
            $bodega = ($filtro_bodega) ? $filtro_bodega : $this->config->item('bodega_principal');
            $resultado = json_decode(obtener_inventario_api(['bodega' => $bodega]));
            $codigo_resultado = $resultado->codigo;
            $inventario = ($resultado->codigo == 0) ? $resultado->detalle->Table : 0 ;
            $fecha_actualizacion = date('Y-m-d H:i:s');
            $datos = [];
            $total_items = 0;

            // Si encontró datos
            if($codigo_resultado != 1) {
                foreach($inventario as $item) {
                    $nuevo_item = [
                        'producto_id' => $item->Iditem,
                        'referencia' => $item->Referencia,
                        'bodega' => $item->Bodega,
                        'descripcion_corta' => $item->Descripcion_Corta,
                        'unidad_inventario' => $item->Unidad_Inventario,
                        'disponible' => $item->Disponible,
                        'fecha_actualizacion' => $fecha_actualizacion,
                    ];

                    array_push($datos, $nuevo_item);
                }
            
                // Si hay datos, se borran los registros anteriores
                if(!empty($datos)) {
                    $this->productos_model->eliminar('productos_inventario', ['id !=' => null, 'bodega' => $bodega]);
                    $total_items = $this->productos_model->crear('productos_inventario', $datos);
                }
            }

            $tiempo_final = microtime(true);

            $resultado = [
                'bodega' => $bodega,
                'items' => number_format($total_items, 0, '', '.'),
                'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos"
            ];
            
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 6,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => json_encode($resultado)
            ]);

            print json_encode($resultado);

            $this->db->close();

            return http_response_code(200);
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 7,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            print json_encode([
                'error' => true,
                'descripcion' => 'Ocurrió un error al ejecutar el script'
            ]);

            return http_response_code(400);
        }
    }

    /**
     * Importa de la API estándar del ERP Siesa 
     * los precios configurados de cada producto 
     */
    function importar_productos_precios() {
        $tiempo_inicial = microtime(true);
        $total_items = 0;

        try {
            // Filtro de la lista de precios
            $filtro_lista_precios = $this->input->get('lista_precio');
            $lista_precio = ($filtro_lista_precios) ? $filtro_lista_precios : $this->config->item('lista_precio');
            $resultado = json_decode(obtener_precios_api(['lista_precio' => $lista_precio]));
            $codigo_resultado = $resultado->codigo;
            $precios = ($resultado->codigo == 0) ? $resultado->detalle->Table : 0 ;
            $fecha_actualizacion = date('Y-m-d H:i:s');
            $datos = [];
            $total_items = 0;

            // Si encontró datos
            if($codigo_resultado != 1) {
                foreach($precios as $precio) {
                    $nuevo_item = [
                        'producto_id' => $precio->IdItem,
                        'referencia' => $precio->Referencia,
                        'descripcion_corta' => $precio->Descripcion_Corta,
                        'lista_precio' => $precio->Lista_precio,
                        'precio' => $precio->PrecioSugerido, // Precio oficial
                        'precio_maximo' => $precio->PrecioMaximo,
                        'precio_minimo' => $precio->PrecioMinimo,
                        'precio_sugerido' => $precio->PrecioSugerido,
                        'fecha_actualizacion' => $fecha_actualizacion,
                    ];

                    array_push($datos, $nuevo_item);
                }

                // Primero, eliminamos todos los ítems de la lista de precios (Solo si hay datos disponibles para actualizar)
                if(!empty($datos)) {
                    $this->productos_model->eliminar('productos_precios', ['lista_precio' => $lista_precio]);
                    $total_items = $this->productos_model->crear('productos_precios', $datos);
                }
            }

            $tiempo_final = microtime(true);

            $resultado = [
                'lista_precio' => $lista_precio,
                'items' => number_format($total_items, 0, '', '.'),
                'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos"
            ];

            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 34,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => json_encode($resultado)
            ]);

            print json_encode($resultado);

            $this->db->close();

            return http_response_code(200);
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 33,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            print json_encode([
                'error' => true,
                'descripcion' => 'Ocurrió un error al ejecutar el script'
            ]);

            return http_response_code(400);
        }
    }

    /**
     * Importa de Siesa los detalles de pedidos del día anterior
     * o del día seleccionado, con el fin de mostrar en la tienda
     * Los productos destacados y/o más vendidos
     */
    function importar_productos_pedidos($fecha = null) {
        $tiempo_inicial = microtime(true);

        try {
            $filtro_fecha = ($fecha) ? $fecha : date('Y-m-d') ;
            $resultado_pedidos = json_decode(obtener_pedidos_api($filtro_fecha));
            $codigo_resultado = $resultado_pedidos->codigo;
            $pedidos = ($codigo_resultado == 0) ? $resultado_pedidos->detalle->Table : 0 ;
            $fecha_creacion = date('Y-m-d H:i:s');
            $datos = [];
            $total_items = 0;

            // Si encontró datos
            if($codigo_resultado != 1) {
                foreach($pedidos as $item) {
                    $nuevo_item = [
                        'centro_operaciones' => $item->Centro_Operaciones,
                        'documento_tipo' => $item->Tipo_Documento,
                        'documento_numero' => $item->Nro_Documento,
                        'tercero_id' => $item->Id_Tercero,
                        'tercero_razon_social' => $item->Razon_Social,
                        'sucursal_descripcion' => $item->Descripcion_Sucursal,
                        'fecha_documento' => $item->Fecha_Documento,
                        'producto_id' => $item->Item,
                        'referencia' => $item->Referencia,
                        'descripcion' => $item->Descripcion,
                        'precio_unitario' => $item->Precio_Unitario,
                        'cantidad' => $item->Cantidad_Pedida,
                        'valor' => $item->Valor_Bruto,
                        'descuento' => $item->Descuento,
                        'fecha_creacion' => $fecha_creacion,
                    ];
                    
                    array_push($datos, $nuevo_item);
                }

                // Si hay datos, se borran los registros anteriores
                if(!empty($datos)) {
                    $this->productos_model->eliminar('productos_pedidos', ["fecha_documento" => $filtro_fecha]);
                    $total_items = $this->productos_model->crear('productos_pedidos', $datos);
                }
            }

            $tiempo_final = microtime(true);

            $resultado = [
                'fecha' => $filtro_fecha,
                'items' => number_format($total_items, 0, '', '.'),
                'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos"
            ];
            
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 10,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => json_encode($resultado)
            ]);

            print json_encode($resultado);

            $this->db->close();

            return http_response_code(200);
        } catch (\Throwable $error) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 11,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            print json_encode([
                'error' => true,
                'descripcion' => 'Ocurrió un error al ejecutar el script'
            ]);

            return http_response_code(400);
        }
    }

    /**
     * Importa de Siesa V2 las cuentas por pagar de los proveedores
     */
    function importar_proveedores_cuentas_por_pagar($numero_documento) {
        $tiempo_inicial = microtime(true);
        $total_items = 0;

        try {
            $codigo = 0;
            $pagina = 1;
            $items_almacenados = 0;

            // Eliminamos todos los ítems asociados al proveedor
            $this->proveedores_model->eliminar('api_cuentas_por_pagar', ['f200_id' => $numero_documento]);

            // Mientras la API de Siesa retorne código 0 (Registros encontrados)
            while ($codigo == 0) {
                $resultado = json_decode(obtener_cuentas_por_pagar_api(['pagina' => $pagina, 'numero_documento' => $numero_documento]));

                $codigo = $resultado->codigo;
                $nuevas_cuentas = [];

                if($codigo == 0) {
                    $cuentas = $resultado->detalle->Table;

                    foreach($cuentas as $cuenta) {
                        array_push($nuevas_cuentas, $cuenta);

                        $total_items++;
                    }

                    $items_almacenados += $this->proveedores_model->insertar_batch('api_cuentas_por_pagar', $nuevas_cuentas);
                    
                    $pagina++;
                } else {
                    $codigo = '-1';
                    break;
                }
            }
            
            $tiempo_final = microtime(true);

            $respuesta = [
                'log_tipo_id' => 75,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "$items_almacenados registros actualizados",
                'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
            ];

            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', $respuesta);

            print json_encode($respuesta);

            return http_response_code(200);
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 74,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            return http_response_code(400);
        }
    }

    /**
     * Descarga todos los terceros de Siesa,
     * recorriendo cada página e insertando en
     * la base de datos el resultado por cada página
     */
    function importar_terceros_api() {
        $tiempo_inicial = microtime(true);

        $codigo = 0;
        $pagina = 1;
        $total_items = 0;

        try {
            // Primero, eliminamos todos los ítems
            $this->configuracion_model->eliminar('terceros', ['id']);

            // Mientras obtenga resultados la consulta
            while ($codigo == 0) {
                $resultado = json_decode(obtener_terceros_api(['pagina' => $pagina]));
                $codigo = $resultado->codigo;

                // Si el resultado es exitoso
                if($codigo == 0) {
                    $terceros = $resultado->detalle->Table;

                    $total_items += count($terceros);

                    // Recorrido de todos los registros de la página
                    $this->configuracion_model->crear('terceros_api', $terceros);
                    
                    $pagina++;
                } else {
                    $codigo = '-1';
                    break;
                }
            }

            $tiempo_final = microtime(true);

            $respuesta = [
                'log_tipo_id' => 40,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "$total_items registros actualizados",
                'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
            ];

            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', $respuesta);

            print json_encode($respuesta);
            return http_response_code(200);
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 41,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            return http_response_code(400);
        }
    }
    
    /**
     * Importa de Siesa los movimientos contables
     * de un tercero
     */
    function importar_movimientos_contables_api($numero_documento) {
        $tiempo_inicial = microtime(true);
        
        $anio_anterior = date('Y') - 1;
        $codigo = 0;
        $pagina = 1;
        $total_items = 0;
        $datos = [
            'numero_documento' => $numero_documento,
            'fecha_inicial' => "$anio_anterior-01-01",
            'fecha_final' => "$anio_anterior-12-31",
            'filtro_retenciones' => true,
        ];

        try {
            // Eliminamos todos los ítems asociados al tercero
            $this->clientes_model->eliminar('clientes_facturas_movimientos', ['f200_nit' => $numero_documento]);

            // Mientras la API de Siesa retorne código 0 (Registros encontrados)
            while ($codigo == 0) {
                $datos['pagina'] = $pagina; 
                $resultado = json_decode(obtener_movimientos_contables_api($datos));
                $codigo = $resultado->codigo;
                
                if($codigo == 0) {
                    $movimientos = $resultado->detalle->Table;
                    $total_items += count($movimientos);

                    $this->clientes_model->crear('clientes_facturas_movimientos_proveedores', $movimientos);

                    $pagina++;
                } else {
                    $codigo = '-1';
                    break;
                }
            }

            $tiempo_final = microtime(true);

            $respuesta = [
                'log_tipo_id' => 86,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "$total_items registros actualizados",
                'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
            ];

            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', $respuesta);

            print json_encode($respuesta);
            return http_response_code(200);
        } catch (\Throwable $th) {
            print_r($th);
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 85,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            return http_response_code(400);
        }
    }

    function erp($tipo) {
        switch ($tipo) {
            // Desde la API estándar API_v2_Ventas_Pedidos importa los pedidos
            // para que posteriormente puedan ser creadas facturas de manera automática
            case 'importar_ventas_pedidos':
                $tiempo_inicial = microtime(true);
                $total_items = 0;

                try {
                    $codigo = 0;
                    $pagina = 1;
                    $items_almacenados = 0;

                    // Mientras la API de Siesa retorne código 0 (Registros encontrados)
                    while ($codigo == 0) {
                        $resultado = json_decode(obtener_pedidos_api_estandar(['pagina' => $pagina, 'estado_id' => 3, 'filtro_fecha' => true]));
                        $codigo = $resultado->codigo;
                        $items = [];

                        if($codigo == 0) {
                            $registros = $resultado->detalle->Table;

                            foreach($registros as $item) {
                                // Antes de agregar el ítem, se consulta primero si existe el ítem ya creado
                                $existe_item = $this->configuracion_model->obtener('erp_ventas_pedidos', ['f430_rowid' => $item->f430_rowid, 'f120_id' => $item->f120_id]);

                                // Si no existe todavía en la base de datos, se agrega al arreglo para que se cree
                                if(empty($existe_item)) array_push($items, $item);

                                $total_items++;
                            }

                            // Si hay datos en el arreglo, se crean
                            if(!empty($items)) $items_almacenados += $this->configuracion_model->crear('erp_ventas_pedidos_batch', $items);
                            
                            $pagina++;
                        } else {
                            $codigo = '-1';
                            break;
                        }
                    }

                    $tiempo_final = microtime(true);

                    $respuesta = [
                        'log_tipo_id' => 93,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                        'observacion' => "$items_almacenados registros creados",
                        'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
                    ];

                    // Se agrega el registro en los logs
                    $this->configuracion_model->crear('logs', $respuesta);

                    print json_encode($respuesta);

                    return http_response_code(200);
                } catch (\Throwable $th) {
                    // Se agrega el registro en los logs
                    $this->configuracion_model->crear('logs', [
                        'log_tipo_id' => 94,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                    ]);

                    return http_response_code(400);
                }
            break;

            // Desde la API estándar API_v2_Compras_Ordenes importa las órdenes de compra
            case 'importar_compras_ordenes':
                $tiempo_inicial = microtime(true);
                $total_items = 0;

                try {
                    $codigo = 0;
                    $pagina = 1;
                    $items_almacenados = 0;

                    // Mientras la API de Siesa retorne código 0 (Registros encontrados)
                    while ($codigo == 0) {
                        $resultado = json_decode(obtener_ordenes_compra(['pagina' => $pagina, 'filtro_fecha' => false]));

                        $codigo = $resultado->codigo;
                        $items = [];

                        if($codigo == 0) {
                            $registros = $resultado->detalle->Table;

                            foreach($registros as $item) {
                                // Antes de agregar el ítem, se consulta primero si existe el ítem ya creado
                                $existe_item = $this->configuracion_model->obtener('erp_compras_ordenes', ['f420_rowid' => $item->f420_rowid, 'f120_id' => $item->f120_id]);

                                // Si no existe todavía en la base de datos, se agrega al arreglo para que se cree
                                if(empty($existe_item)) array_push($items, $item);

                                $total_items++;
                            }

                            // Si hay datos en el arreglo, se crean
                            if(!empty($items)) $items_almacenados += $this->configuracion_model->crear('erp_compras_ordenes_batch', $items);
                            
                            $pagina++;
                        } else {
                            $codigo = '-1';
                            break;
                        }
                    }

                    $tiempo_final = microtime(true);

                    $respuesta = [
                        'log_tipo_id' => 95,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                        'observacion' => "$items_almacenados registros creados",
                        'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
                    ];

                    // Se agrega el registro en los logs
                    $this->configuracion_model->crear('logs', $respuesta);

                    print json_encode($respuesta);

                    return http_response_code(200);
                } catch (\Throwable $th) {
                    // Se agrega el registro en los logs
                    $this->configuracion_model->crear('logs', [
                        'log_tipo_id' => 96,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                    ]);

                    return http_response_code(400);
                }
            break;
            
            // Desde la API estándar API_v2_Bodegas importa las bodegas existentes
            case 'importar_bodegas':
                $tiempo_inicial = microtime(true);
                $total_items = 0;
                
                try {
                    $codigo = 0;
                    $pagina = 1;
                    $items_almacenados = 0;

                    // Mientras la API de Siesa retorne código 0 (Registros encontrados)
                    while ($codigo == 0) {
                        $resultado = json_decode(obtener_bodegas_api(['pagina' => $pagina]));
                        $codigo = $resultado->codigo;
                        $items = [];

                        if($codigo == 0) {
                            $registros = $resultado->detalle->Table;

                            foreach($registros as $item) {
                                // Antes de agregar el ítem, se consulta primero si existe el ítem en la base de datos
                                $existe_item = $this->configuracion_model->obtener('erp_bodegas', ['f150_rowid' => $item->f150_rowid]);

                                // Si no existe todavía en la base de datos, se agrega al arreglo para que se cree
                                if(empty($existe_item)) array_push($items, $item);

                                $total_items++;
                            }

                            // Si hay datos en el arreglo, se crean
                            if(!empty($items)) {
                                $items_almacenados += $this->configuracion_model->crear('erp_bodegas_batch', $items);
                            }
                            
                            $pagina++;
                        } else {
                            $codigo = '-1';
                            break;
                        }
                    }

                    $tiempo_final = microtime(true);

                    $respuesta = [
                        'log_tipo_id' => 97,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                        'observacion' => "$items_almacenados registros creados",
                        'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
                    ];

                    // Se agrega el registro en los logs
                    $this->configuracion_model->crear('logs', $respuesta);

                    print json_encode($respuesta);

                    return http_response_code(200);
                } catch (\Throwable $th) {
                    // Se agrega el registro en los logs
                    $this->configuracion_model->crear('logs', [
                        'log_tipo_id' => 98,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                    ]);

                    return http_response_code(400);
                }
            break;

            // Desde la API estándar API_v2_ListasDePrecios importa las listas de precios existentes
            case 'importar_listas_precios':
                $tiempo_inicial = microtime(true);
                $total_items = 0;
                
                try {
                    $codigo = 0;
                    $pagina = 1;
                    $items_almacenados = 0;

                    // Mientras la API de Siesa retorne código 0 (Registros encontrados)
                    while ($codigo == 0) {
                        $resultado = json_decode(obtener_listas_precios_api(['pagina' => $pagina]));
                        $codigo = $resultado->codigo;
                        $items = [];

                        if($codigo == 0) {
                            $registros = $resultado->detalle->Table;

                            foreach($registros as $item) {
                                // Antes de agregar el ítem, se consulta primero si existe el ítem en la base de datos
                                $existe_item = $this->configuracion_model->obtener('erp_listas_precios', ['f112_descripcion' => $item->f112_descripcion]);

                                // Si no existe todavía en la base de datos, se agrega al arreglo para que se cree
                                if(empty($existe_item)) array_push($items, $item);

                                $total_items++;
                            }

                            // Si hay datos en el arreglo, se crean
                            if(!empty($items)) {
                                $items_almacenados += $this->configuracion_model->crear('erp_listas_precios_batch', $items);
                            }
                            
                            $pagina++;
                        } else {
                            $codigo = '-1';
                            break;
                        }
                    }

                    $tiempo_final = microtime(true);

                    $respuesta = [
                        'log_tipo_id' => 99,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                        'observacion' => "$items_almacenados registros creados",
                        'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
                    ];

                    // Se agrega el registro en los logs
                    $this->configuracion_model->crear('logs', $respuesta);

                    print json_encode($respuesta);

                    return http_response_code(200);
                } catch (\Throwable $th) {
                    // Se agrega el registro en los logs
                    $this->configuracion_model->crear('logs', [
                        'log_tipo_id' => 100,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                    ]);

                    return http_response_code(400);
                }
            break;
            
            default:
                print json_encode([
                    'exito' => false,
                    'mensaje' => 'Ningún webhook encontrado'
                ]);

                return http_response_code(400);
        }
    }
}
/* Fin del archivo Webhooks.php */
/* Ubicación: ./application/controllers/Webhooks.php */