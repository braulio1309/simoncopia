<?php
date_default_timezone_set("America/Bogota");

defined("BASEPATH") OR exit("El acceso directo a este archivo no está permitido");

use chriskacerguis\RestServer\RestController;

/**
 * @author: 	John Steven Salazar
 * Fecha: 		15 de enero de 2025
 * Programa:  	Simón Bolívar | Módulo de Api
 * Email: 		john2001salazar@gmail.com
 */
class Api extends RestController {
    function __construct() {
        parent::__construct();

        $this->load->model(["clientes_model", "configuracion_model", "productos_model"]);
    }

    /**
     * Devuelve el listado de pedidos que fueron obtenidos
     * de la API de Siesa
     */
    function pedidos_get() {
        $datos = [
            "id" => $this->get("id")
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run("pedidos_get")) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $resultado = $this->productos_model->obtener("productos_pedidos", $datos);

        $mensaje = "Registros cargados correctamente";

        if (!is_object($resultado)) {
            $total_registros = count($resultado);
            $mensaje = "Se cargaron correctamente $total_registros registros";
        }

        $this->response([
            "error" => false,
            "mensaje" => $mensaje,
            "resultado" => $resultado
        ], RestController::HTTP_OK);
    }

    /**
     * Devuelve el listado de todos los productos
     * registrados en Siesa, que tienen inventario y precio
     */
    function productos_get() {
        $datos = [
            "id" => $this->get("id"),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run("productos_get")) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $resultado = $this->productos_model->obtener('productos', $datos);

        if (!$resultado) {
            $this->response([
                "error" => false,
                "mensaje" => 'No se han encontrados registros',
                "resultado" => null
            ], RestController::HTTP_OK);
        }

        $mensaje = 'Información cargada exitosamente';

        if (!is_object($resultado)) {
            $total_registros = number_format(count($resultado), 0, '', '.');
            $mensaje = "Se cargaron $total_registros registros exitosamente.";
        }

        $this->response([
            "error" => false,
            "mensaje" => $mensaje,
            "resultado" => $resultado
        ], RestController::HTTP_OK);
    }

    /**
     * Devuelve el listado de recibos
     */
    function recibos_get() {
        $datos = [
            'id' => $this->get("id"),
            'actualizado_bot' => $this->get("actualizado_bot"),
            'id_tipo_recibo' => $this->get("recibo_tipo_id"),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run("recibos_get")) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $resultado = $this->configuracion_model->obtener("recibos", $datos);

        if (!$resultado) {
            $this->response([
                "error" => false,
                "mensaje" => "No han sido encontrados registros",
                "resultado" => null
            ], RestController::HTTP_OK);
        }

        $mensaje = "Registro cargado correctamente";

        if (!is_object($resultado)) {
            $total_registros = count($resultado);
            $mensaje = "Se cargaron correctamente $total_registros registros";
        }

        $this->response([
            "error" => false,
            "mensaje" => $mensaje,
            "resultado" => $resultado
        ], RestController::HTTP_OK);
    }

    /**
     * Devuelve el listado con el detalle de los recibos
     */
    function recibos_detalle_get() {
        $datos = [
            "recibo_id" => $this->get("recibo_id"),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run("recibos_detalle_get")) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $resultado = $this->configuracion_model->obtener("recibos_detalle", $datos);

        $resultado = array_map(function($item) {
            // Si la diferencia en el valor de la factura y el valor pagado es +/- 1 peso, vamos a tener en cuenta la diferencia
            // para que el saldo final quede en cero  
            $diferencia = $item->valor_saldo_inicial - $item->subtotal;
            $valor_diferencia = ($diferencia > -1 && $diferencia < 1 && $diferencia != 0) ? $diferencia : 0 ;

            $item->valor_saldo_inicial = number_format($item->valor_saldo_inicial, 2, '.', ''); // clientes_facturas.totalCop
            $item->valor_abonos = number_format($item->valor_abonos, 2, '.', ''); // clientes_facturas.valorDoc
            $item->valor_factura = number_format($item->valor_factura, 2, '.', ''); // clientes_facturas.valorAplicado
            $item->valor_pagado_bruto = number_format($item->subtotal + $valor_diferencia, 2, '.', '');
            $item->valor_descuento = number_format($item->descuento, 2, '.', '');
            $item->valor_pagado_neto = number_format($item->subtotal - $item->descuento + $valor_diferencia, 2, '.', '');
            $item->valor_saldo_final = number_format($item->valor_saldo_inicial - $item->valor_pagado_bruto, 2, '.', '');
            $item->valor_ajuste_peso = $valor_diferencia ;
            
            return $item;
        }, $resultado);

        if (!$resultado) {
            $this->response([
                "error" => false,
                "mensaje" => "No han sido encontrados registros",
                "resultado" => null
            ], RestController::HTTP_OK);
        }

        $mensaje = "Registro cargado correctamente";

        if (!is_object($resultado)) {
            $total_registros = count($resultado);
            $mensaje = "Se cargaron correctamente $total_registros registros";
        }

        $this->response([
            "error" => false,
            "mensaje" => $mensaje,
            "resultado" => $resultado
        ], RestController::HTTP_OK);
    }

    /**
     * Actualiza un recibo
     */
    function recibo_put() {
        $datos = [
            'id' => $this->input->get('id'),
            'fecha_actualizacion_bot' => $this->put('fecha_actualizacion_bot'),
            'numero_siesa' => $this->put('numero_siesa'),
            'recibo_estado_id' => $this->put('recibo_estado_id'),
            'comentarios' => $this->put('comentarios'),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run('recibo_put')) {
            $this->response([
                'error' => true,
                'mensaje' => 'Parámetros inválidos.',
                'resultado' => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $recibo = $this->configuracion_model->obtener("recibos", ["id" => $datos['id']]);

        if (!$recibo) {
            $this->response([
                'error' => false,
                'mensaje' => 'No ha sido encontrado el recibo.',
                'resultado' => null
            ], RestController::HTTP_OK);
        }

        $resultado = $this->productos_model->actualizar("recibos", ["id" => $datos['id']], $datos);

        if (!$resultado) {
            $this->response([
                'error' => false,
                'mensaje' => 'No se ha actualizado el registro.',
                'resultado' => null
            ], RestController::HTTP_OK);
        }

        $this->response([
            'error' => false,
            'mensaje' => 'Registro actualizado correctamente.',
            'resultado' => $resultado
        ], RestController::HTTP_OK);
    }

    /**
     * Devuelve el listado de una o varias solicitudes de crédito
     */
    function solicitudes_credito_get() {
        $datos = [
            'id' => $this->get("id"),
            'solicitud_credito_estado_id' => $this->get('estado_id'),
            'documentos_validados' => $this->get('documentos_validados'),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run("solicitudes_credito_get")) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $resultado = $this->clientes_model->obtener("clientes_solicitudes_credito", $datos);

        if (!$resultado) {
            $this->response([
                "error" => false,
                "mensaje" => "No se encontraron registros.",
                "resultado" => null
            ], RestController::HTTP_OK);
        }

        $mensaje = "Registros cargados correctamente.";

        if (!is_object($resultado)) {
            $total_registros = count($resultado);
            $mensaje = "$total_registros registros encontrados";
        }
       
        $this->response([
            "error" => false,
            "mensaje" => $mensaje,
            "resultado" => $resultado
        ], RestController::HTTP_OK);
    }

    /**
     * Actualiza los datos de una solicitud de crédito
     */
    function solicitud_credito_put() {
        // Datos para actualizar
        $datos = [
            'id' => $this->input->get('id'),
            'fecha_validacion_documentos' => $this->put('fecha_validacion_documentos'),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run('solicitud_credito_put')) {
            $this->response([
                'error' => true,
                'mensaje' => 'Parámetros inválidos.',
                'resultado' => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        // Se obtiene los datos de la solicitud
        $solicitud_credito = $this->clientes_model->obtener('clientes_solicitudes_credito', ['id' => $datos['id']]);

        if (empty($solicitud_credito)) {
            $this->response([
                'error' => false,
                'mensaje' => "No se encontró la solicitud de crédito con id {$datos['id']}",
                'resultado' => null
            ], RestController::HTTP_OK);
        }

        $resultado = $this->clientes_model->actualizar('clientes_solicitudes_credito', ['id' => $datos['id']], $datos);

        if (!$resultado) {
            $this->response([
                'error' => false,
                'mensaje' => 'No se actualizó el registro.',
                'resultado' => null
            ], RestController::HTTP_OK);
        }

        // Respuesta exitosa
        $this->response([
            'error' => false,
            'mensaje' => "Registro actualizado correctamente.",
            'resultado' => $resultado
        ], RestController::HTTP_OK);
    }

    /**
     * Devuelve el listado con los archivos suministrados
     * por el usuario que solicita el crédito
     */
    function solicitudes_credito_archivos_get() {
        // Helper para manejo de archivos
        $this->load->helper('file');

        $datos = [
            "solicitud_credito_id" => $this->get("solicitud_credito_id"),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run("solicitudes_credito_archivos_get")) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $base_url = FCPATH;
        $ruta_archivos = "archivos/solicitudes_credito/{$datos['solicitud_credito_id']}/";
        $ruta = $base_url.$ruta_archivos;

        // Se leen los archivos
        $lista = get_filenames($ruta);

        // Si no se encuentran archivos
        if (!$lista) {
            $this->response([
                "error" => false,
                "mensaje" => "No se encontraron archivos.",
                "resultado" => null
            ], RestController::HTTP_OK);
        }

        // Arreglo para almacenar los archivos
        $archivos = [];
        
        foreach ($lista as $registro) {
            // Se crea un objeto por cada archivo encontrado
            array_push($archivos, [
                'nombre' => pathinfo($registro, PATHINFO_FILENAME),
                'url' => base_url().$ruta_archivos.$registro,
            ]);
        }

        $mensaje = "Archivos cargados correctamente.";
        $total_registros = count($archivos);
        $mensaje = "Se cargaron correctamente $total_registros registros";

        $this->response([
            "error" => false,
            "mensaje" => $mensaje,
            "resultado" => $archivos
        ], RestController::HTTP_OK);
    }

    /**
     * Devuelve el listado con el detalle de la solicitud de crédito
     */
    function solicitudes_credito_detalle_get() {
        $datos = [
            "solicitud_credito_id" => $this->get("solicitud_credito_id"),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run("solicitudes_credito_detalle_get")) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $resultado = $this->clientes_model->obtener("clientes_solicitudes_credito_detalle", [
            "solicitud_id" => $datos['solicitud_credito_id'],
        ]);

        if (!$resultado) {
            $this->response([
                "error" => false,
                "mensaje" => "No se encontraron registros.",
                "resultado" => null
            ], RestController::HTTP_OK);
        }

        $mensaje = "Registros cargados correctamente.";

        if (!is_object($resultado)) {
            $total_registros = count($resultado);
            $mensaje = "Se cargaron correctamente $total_registros registros";
        }

        $this->response([
            "error" => false,
            "mensaje" => $mensaje,
            "resultado" => $resultado
        ], RestController::HTTP_OK);
    }

    /**
     * Devuelve el listado con el detalle de los terceros
     */
    function terceros_get() {
        $datos = [
            'nit' => $this->get('nit'),
        ];

        $this->form_validation->set_data($datos);
    
        if (!$this->form_validation->run('terceros_get')) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }
    
        $resultado = $this->configuracion_model->obtener('terceros', $datos);
    
        if (!$resultado) {
            $this->response([
                "error" => false,
                "mensaje" => 'No se han encontrados registros',
                "resultado" => null
            ], RestController::HTTP_OK);
        }

        $mensaje = 'Información cargada exitosamente';

        if (!is_object($resultado)) {
            $total_registros = number_format(count($resultado), 0, '', '.');
            $mensaje = "Se cargaron $total_registros registros exitosamente.";
        }

        $this->response([
            "error" => false,
            "mensaje" => $mensaje,
            "resultado" => $resultado
        ], RestController::HTTP_OK);
    }

    /**
     * Devuelve el listado de contactos de un tercero
     */
    function terceros_contactos_get() {
        $datos = [
            'nit' => $this->get('nit'),
            'modulo_id' => $this->get('modulo_id'),
        ];

        $this->form_validation->set_data($datos);
    
        if (!$this->form_validation->run('terceros_contactos_get')) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }
    
        $resultado = $this->configuracion_model->obtener('contactos', $datos);
    
        if (!$resultado) {
            $this->response([
                "error" => false,
                "mensaje" => 'No se han encontrados registros',
                "resultado" => null
            ], RestController::HTTP_OK);
        }

        $mensaje = 'Información cargada exitosamente';

        if (!is_object($resultado)) {
            $total_registros = number_format(count($resultado), 0, '', '.');
            $mensaje = "Se cargaron $total_registros registros exitosamente.";
        }

        $this->response([
            "error" => false,
            "mensaje" => $mensaje,
            "resultado" => $resultado
        ], RestController::HTTP_OK);
    }

    /**
     * Envía mensajes a través de la API de Whatsapp
     */
    function whatsapp_post() {
        $tipo = $this->input->get('tipo');
        $post = file_get_contents('php://input');
        $datos = json_decode($post, true);
        $error = true;

        $numero_telefonico = formatear_numero_telefono($datos['numero_telefonico']);

        switch ($tipo) {
            // Mensaje de prueba
            case 'mensaje_test':
                $peticion = $this->whatsapp_api->enviar_mensaje_con_plantilla(
                    $numero_telefonico,
                    (ENVIRONMENT != 'production') ? 'hello_world' : 'actualizacion_estado',
                    (ENVIRONMENT != 'production') ? 'en_US' : 'es_CO'
                );

                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 101,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => json_encode([
                        'tipo' => $tipo,
                        'resultado' => $peticion
                    ]),
                ]);
                break;
            
            // Generación de orden de compra
            case 'proveedores_orden_compra':
                $parametros = [
                    'orden_numero' => (isset($datos['orden_numero'])) ? $datos['orden_numero'] : null,
                    'proveedor' => (isset($datos['proveedor'])) ? $datos['proveedor'] : null,
                    'url' => (isset($datos['url'])) ? $datos['url'] : null,
                ];

                $this->form_validation->set_data($parametros);

                if (!$this->form_validation->run('whatsapp_proveedores_orden_compra')) {
                    $this->response([
                        "error" => true,
                        "mensaje" => "Parámetros inválidos.",
                        "resultado" => $this->form_validation->error_array(),
                    ], RestController::HTTP_BAD_REQUEST);
                }

                $contenido = [
                    [
                        'type' => 'body',
                        "parameters" => [
                            [
                                'type' => 'text',
                                'parameter_name' => 'orden_numero',
                                "text" => $parametros['orden_numero'],
                            ],
                            [
                                'type' => 'text',
                                'parameter_name' => 'proveedor',
                                "text" => $parametros['proveedor'],
                            ],
                            [
                                'type' => 'text',
                                'parameter_name' => 'url',
                                "text" => $parametros['url'],
                            ]
                        ]
                    ]
                ];

                $peticion = $this->whatsapp_api->enviar_mensaje_con_plantilla($numero_telefonico, 'proveedor_orden_compra', 'es_CO', $contenido);

                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 101,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => json_encode([
                        'tipo' => $tipo,
                        'resultado' => $peticion
                    ]),
                ]);

                $this->response([
                    'error' => !$peticion['status'],
                    'resultado' => $url,
                    'datos' => $peticion['response']
                ], RestController::HTTP_OK);
                break;

            // Orden de compra aprobada
            case 'proveedores_orden_compra_aprobada':
                $parametros = [
                    'orden_compra' => (isset($datos['orden_compra'])) ? $datos['orden_compra'] : null,
                    'proveedor' => (isset($datos['proveedor'])) ? $datos['proveedor'] : null,
                    'url' => (isset($datos['url'])) ? $datos['url'] : null,
                ];

                $this->form_validation->set_data($parametros);

                if (!$this->form_validation->run('whatsapp_proveedores_orden_compra_aprobada')) {
                    $this->response([
                        "error" => true,
                        "mensaje" => "Parámetros inválidos.",
                        "resultado" => $this->form_validation->error_array(),
                    ], RestController::HTTP_BAD_REQUEST);
                }

                $contenido = [
                    [
                        'type' => 'body',
                        "parameters" => [
                            [
                                'type' => 'text',
                                'parameter_name' => 'orden_compra',
                                "text" => $parametros['orden_compra'],
                            ],
                            [
                                'type' => 'text',
                                'parameter_name' => 'proveedor',
                                "text" => $parametros['proveedor'],
                            ],
                            [
                                'type' => 'text',
                                'parameter_name' => 'url',
                                "text" => $parametros['url'],
                            ]
                        ]
                    ]
                ];

                $peticion = $this->whatsapp_api->enviar_mensaje_con_plantilla($numero_telefonico, 'confirmacion_orden_compra', 'es_CO', $contenido);

                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 101,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => json_encode([
                        'tipo' => $tipo,
                        'resultado' => $peticion
                    ]),
                ]);

                $this->response([
                    'error' => !$peticion['status'],
                    'resultado' => $peticion,
                    'datos' => $peticion['response']
                ], RestController::HTTP_OK);
                break;

            // Orden de compra rechazada
            case 'proveedores_orden_compra_rechazada':
                $parametros = [
                    'orden_compra' => (isset($datos['orden_compra'])) ? $datos['orden_compra'] : null,
                    'proveedor' => (isset($datos['proveedor'])) ? $datos['proveedor'] : null,
                    'url' => (isset($datos['url'])) ? $datos['url'] : null,
                ];

                $this->form_validation->set_data($parametros);

                if (!$this->form_validation->run('whatsapp_proveedores_orden_compra_aprobada')) {
                    $this->response([
                        "error" => true,
                        "mensaje" => "Parámetros inválidos.",
                        "resultado" => $this->form_validation->error_array(),
                    ], RestController::HTTP_BAD_REQUEST);
                }

                $contenido = [
                    [
                        'type' => 'body',
                        "parameters" => [
                            [
                                'type' => 'text',
                                'parameter_name' => 'orden_compra',
                                "text" => $parametros['orden_compra'],
                            ],
                            [
                                'type' => 'text',
                                'parameter_name' => 'proveedor',
                                "text" => $parametros['proveedor'],
                            ],
                            [
                                'type' => 'text',
                                'parameter_name' => 'url',
                                "text" => $parametros['url'],
                            ]
                        ]
                    ]
                ];

                $peticion = $this->whatsapp_api->enviar_mensaje_con_plantilla($numero_telefonico, 'orden_compra_rechazada', 'es_CO', $contenido);

                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 101,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => json_encode([
                        'tipo' => $tipo,
                        'resultado' => $peticion
                    ]),
                ]);

                $this->response([
                    'error' => !$peticion['status'],
                    'resultado' => $peticion,
                    'datos' => $peticion['response']
                ], RestController::HTTP_OK);
                break;


            // Generación de una solicitud de diligencia
            case 'logistica_diligencia':
                $parametros = [
                    'identificador' => (isset($datos['identificador'])) ? $datos['identificador'] : null,
                    'solicitante' => (isset($datos['solicitante'])) ? $datos['solicitante'] : null,
                    'observaciones' => (isset($datos['observaciones'])) ? $datos['observaciones'] : null,
                    'tipo_solicitud' => (isset($datos['tipo_solicitud'])) ? $datos['tipo_solicitud'] : null,
                ];

                $this->form_validation->set_data($parametros);

                if (!$this->form_validation->run('whatsapp_logistica_diligencia_post')) {
                    $this->response([
                        "error" => true,
                        "mensaje" => "Parámetros inválidos.",
                        "resultado" => $this->form_validation->error_array(),
                    ], RestController::HTTP_BAD_REQUEST);
                }

                $contenido = [
                    [
                        'type' => 'body',
                        "parameters" => [
                            [
                                'type' => 'text',
                                'parameter_name' => 'identificador',
                                "text" => $parametros['identificador'],
                            ],
                            [
                                'type' => 'text',
                                'parameter_name' => 'solicitante',
                                "text" => $parametros['solicitante'],
                            ],
                            [
                                'type' => 'text',
                                'parameter_name' => 'tipo_solicitud',
                                "text" => $parametros['tipo_solicitud'],
                            ],
                            [
                                'type' => 'text',
                                'parameter_name' => 'observaciones',
                                "text" => $parametros['observaciones'],
                            ]
                        ]
                    ]
                ];

                $peticion = $this->whatsapp_api->enviar_mensaje_con_plantilla($numero_telefonico, 'logistica_diligencia', 'es_CO', $contenido);

                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 101,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => json_encode([
                        'tipo' => $tipo,
                        'resultado' => $peticion
                    ]),
                ]);

                $this->response([
                    'error' => !$peticion['status'],
                    'resultado' => $peticion,
                ], RestController::HTTP_OK);
                break;

            case 'clientes_asignacion_solicitud_credito':
                $parametros = [
                    'nombre_cliente' => (isset($datos['nombre_cliente'])) ? $datos['nombre_cliente'] : null,
                    'url' => (isset($datos['url'])) ? $datos['url'] : null,
                ];

                $this->form_validation->set_data($parametros);

                if (!$this->form_validation->run('whatsapp_clientes_asignacion_solicitud_credito')) {
                    $this->response([
                        "error" => true,
                        "mensaje" => "Parámetros inválidos.",
                        "resultado" => $this->form_validation->error_array(),
                    ], RestController::HTTP_BAD_REQUEST);
                }

                $contenido = [
                    [
                        'type' => 'body',
                        "parameters" => [
                            [
                                'type' => 'text',
                                'parameter_name' => 'nombre_cliente',
                                "text" => $parametros['nombre_cliente'],
                            ],
                            [
                                'type' => 'text',
                                'parameter_name' => 'url',
                                "text" => $parametros['url'],
                            ],
                        ]
                    ]
                ];

                $peticion = $this->whatsapp_api->enviar_mensaje_con_plantilla($numero_telefonico, 'clientes__asignacion_solicitud_credito', 'es_CO', $contenido);

                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 101,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => json_encode([
                        'tipo' => $tipo,
                        'resultado' => $peticion
                    ]),
                ]);

                $this->response([
                    'error' => !$peticion['status'],
                    'resultado' => $peticion,
                ], RestController::HTTP_OK);
                break;
        }

        $this->response([
            'error' => !$peticion['status'],
            'resultado' => $peticion,
            'datos' => $peticion['response']
        ], ($peticion['status']) ? RestController::HTTP_OK : RestController::HTTP_BAD_REQUEST);
    }
}
/* Fin del archivo Api.php */
/* Ubicación: ./application/controllers/Api.php */