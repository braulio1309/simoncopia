<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		3 de febrero de 2026
 * Programa:  	eCommerce | Módulo de tareas programadas
 * Email: 		johnarleycano@hotmail.com
 */
class Tareas extends MY_Controller {
    function __construct() {
        parent::__construct();
        
        // Todas las respuestas se enviarán en formato JSON
        header('Content-type: application/json');

        $this->load->model(['clientes_model', 'contabilidad_model', 'productos_model', 'proveedores_model']);

        $this->config->load('microsoft_graph', TRUE);
    }

    /**
     * De cada documento contable, busca si tiene más documentos como soporte
     *
     * @param string $ruta_carpeta
     * @param string $nombre_base
     * @return void
     */
    function buscar_soportes($ruta_carpeta, $nombre_base) {
        $soportes = [];
        
        // Se escanean todos los archivos de la carpeta
        $archivos = scandir($ruta_carpeta);
        
        foreach ($archivos as $archivo) {
            if ($archivo === '.' || $archivo === '..') continue;
            
            $ruta_completa = $ruta_carpeta . '/' . $archivo;
            
            // Si no es el archivo principal, es un documento adicional
            if (is_file($ruta_completa) && $archivo != "$nombre_base.pdf") {
            // if ($archivo != "$nombre_base.pdf") {
                $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                $tipo = obtener_tipo_archivo($extension);
                
                $soportes[] = [
                    'directorio' => $nombre_base,
                    'archivo' => $archivo,
                    'ruta_completa' => $ruta_completa,
                    'fecha' => date('Y-m-d'),
                    'tipo' => $tipo,
                    'tamanio' => filesize($ruta_completa),
                    'tamanio_formateado' => formatear_tamanio(filesize($ruta_completa))
                ];
            }
        }

        return $soportes;
    }

    /**
     * Lee un archivo de Excel con las retenciones de los clientes
     * y crea o actualiza los registros en la base de datos.
     * También procesa los contactos adicionales (celular y email)
     * 
     * @return void
     */
    function clientes_importar_retenciones() {
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

    /**
     * A partir de un texto plano, obtiene la información consiguiente
     *
     * @param string $texto
     * @param string $valor_buscado
     * @return void
     */
    function extraer_texto_mensaje($texto, $valor_buscado) {
        // Versión 1: Maneja espacios normales, &nbsp; y cualquier combinación
        if (preg_match($valor_buscado, $texto, $matches)) return $matches[1];
        
        // Versión 2: Si la primera no funciona, intenta con una más permisiva
        if (preg_match($valor_buscado, $texto, $matches)) return $matches[1];
        
        return null;
    }

    /**
     * Permite descargr los archivos adjuntos de los correos electrónicos
     * que contienen los certificados de retención de los clientes
     *
     * @return void
     */
    function clientes_descargar_certificados() {
        $nombre_directorio = '4.CERTIFICADOS DE RETENCION';

        // Obtenemos el token de acceso
        $peticion_token = $this->microsoft_graph->obtener_token();
        $token = $peticion_token['respuesta']['access_token'];
        if(!$token) {
            return json_encode([
                'error' => true,
                'mensaje' => 'No se pudo obtener el token de autenticación'
            ]);
        }
        
        // Buscamos la carpeta a consultar
        $id_directorio = $this->microsoft_graph->buscar_carpeta($token, $nombre_directorio);
        if(!$id_directorio) {
            return json_encode([
                'error' => true,
                'mensaje' => 'No se encontró la carpeta especificada'
            ]);
        }

        // Obtenemos los mensajes del directorio seleccionado
        $peticion_mensajes = $this->microsoft_graph->obtener_mensajes($token, $id_directorio);

        if(empty($peticion_mensajes)) {
            return json_encode([
                'error' => false,
                'mensaje' => 'No se encontraron mensajes con adjuntos en esta carpeta',
            ]);
        }

        // Descarga de archivos
        $mensajes = $peticion_mensajes['respuesta']['value'];
        $archivos_descargados = 0;
        $errores = [];

        foreach($mensajes as $mensaje) {
            $ruta_base = $this->config->item('ruta_archivos_retenciones');

            echo $contenido_formateado = strip_tags($mensaje['body']['content']);
            $nit = $this->extraer_texto_mensaje($contenido_formateado, '/NIT:(?:\s|&nbsp;)*(\d+)/');
            $razon_social = $this->extraer_texto_mensaje(str_replace('&nbsp;', ' ', $contenido_formateado), '/Señores:\s*(.*?)\.\s+NIT:/');

            // Retención en la fuente
            $rete_fuente = $this->extraer_texto_mensaje(str_replace('&nbsp;', ' ', $contenido_formateado), '/RETENCION EN LA FUENTE:\s*(\d+)/');
            if(intval($rete_fuente) > 0) {
                $ruta_archivo = "{$ruta_base}/RETEFUENTE/{$nit}_{$razon_social}_{$rete_fuente}.pdf";
                $this->microsoft_graph->descargar_archivos_adjuntos($token, $mensaje['id'], $ruta_archivo);
            }

            // ReteIVA
            $rete_iva = $this->extraer_texto_mensaje(str_replace('&nbsp;', ' ', $contenido_formateado), '/RETEIVA:\s*(\d+)/');
            if(intval($rete_iva) > 0) {
                $ruta_archivo = "{$ruta_base}/RETEIVA/{$nit}_{$razon_social}_{$rete_iva}.pdf";
                $this->microsoft_graph->descargar_archivos_adjuntos($token, $mensaje['id'], $ruta_archivo);
            }

            // ReteICA
            $rete_ica = $this->extraer_texto_mensaje(str_replace('&nbsp;', ' ', $contenido_formateado), '/RETEICA:\s*(\d+)/');
            if(intval($rete_ica) > 0) {
                $ruta_archivo = "{$ruta_base}/RETEICA/{$nit}_{$razon_social}_{$rete_ica}.pdf";
                $this->microsoft_graph->descargar_archivos_adjuntos($token, $mensaje['id'], $ruta_archivo);
            }
        }
    }

    /**
     * Lee un archivo de Excel con las retenciones de los clientes
     * y crea o actualiza los registros en la base de datos.
     * También procesa los contactos adicionales (celular y email)
     * 
     * @return void
     */
    function importar_clientes_retenciones() {
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

    /**
     * Procesa la carpeta y crea los registros en base de datos
     *
     * @return void
     */
    function contabilidad_validar_comprobantes() {
        $inicio = microtime(true);

        // Obtenemos la primera tarea pendiente
        $resultado = $this->contabilidad_model->obtener('comprobantes_contables_tareas', ['fecha_inicio_ejecucion' => 0]);

        if(empty($resultado[0])) {
            print json_encode([
                'error' => false,
                'resultado' => 'Ninguna tarea encontrada',
            ]);

            $this->configuracion_model->crear('logs', [
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'log_tipo_id' => 107,
                'observacion' => json_encode([
                    'error' => false,
                    'resultado' => 'Ninguna tarea encontrada',
                ])
            ]);

            return;
        }

        // Primera tarea
        $tarea = $resultado[0];

        // Marcamos la fecha de inicio de la tarea
        $this->contabilidad_model->actualizar('comprobantes_contables_tareas', ['id' => $tarea->id], ['fecha_inicio_ejecucion' => DATE('Y-m-d H:i:s')]);

        // Datos necesarios para obtener el formato del nombre del comprobante
        $sede = $this->configuracion_model->obtener('centros_operacion', ['id' => $tarea->centro_operacion_id]); // 100
        $tipo_comprobante = $this->configuracion_model->obtener('comprobantes_contables_tipos', ['id' => $tarea->comprobante_contable_tipo_id]); // FRC
        $periodo = $this->configuracion_model->obtener('periodos', ['mes' => $tarea->mes]); // 01.ENERO

        // archivos/documentos_contables/07.COMPROBANTE DE EGRESOS/2026/1.ITAGUI/1.ENERO
        $carpeta_base = "{$this->config->item('ruta_archivo_digitalizado')}/{$tipo_comprobante->ruta}/{$tarea->anio}/{$sede->ruta}/{$periodo->nombre_comprobante_contable}";

        $consecutivo_inicial = intval($tarea->consecutivo_inicial);
        $consecutivo_final = intval($tarea->consecutivo_final);

        $resultado = [];

        // Recorremos cada consecutivo para validar los datos
        for ($consecutivo = $consecutivo_inicial; $consecutivo <= $consecutivo_final; $consecutivo ++) {
            $soportes = [];
            
            // 00033078
            $consecutivo_formateado = (str_pad($consecutivo, 8, '0', STR_PAD_LEFT));
            
            // 100-FCE-00033078
            $numero_documento = "$sede->codigo-$tipo_comprobante->abreviatura-$consecutivo_formateado";

            // archivos/documentos_contables/01.RECIBOS DE CAJA/2026/1.ITAGUI/1.ENERO/100-FRC-00033151
            $ruta_comprobante = "{$carpeta_base}/$numero_documento";

            // archivos/documentos_contables/01.RECIBOS DE CAJA/2026/1.ITAGUI/1.ENERO/100-FRC-00033151/100-FRC-00033151.pdf
            $ruta_pdf = "$ruta_comprobante/$numero_documento.pdf";
            
            // Se valida si el directorio existe
            $consecutivo_existe = is_dir($ruta_comprobante);

            // Se comprueba si el comprobante existe
            $comprobante_existe = ($consecutivo_existe) ? file_exists($ruta_pdf) : false ;

            // Se verifica que el número del comprobante exista dentro del PDF
            $comprobante_coincide = ($comprobante_existe) ? $this->extraer_texto($ruta_pdf, $tipo_comprobante->abreviatura) : false ;

            // Si existe el directorio del consecutivo, se buscan soportes
            if ($consecutivo_existe) $soportes = $this->buscar_soportes($ruta_comprobante, $numero_documento);

            $datos = [
                'comprobante_contable_tarea_id' => $tarea->id,
                'ruta' => $ruta_comprobante,
                'consecutivo_numero' => $consecutivo,
                'consecutivo_existe' => $consecutivo_existe,
                'comprobante_existe' => $comprobante_existe,
                'comprobante_coincide' => $comprobante_coincide,
                'cantidad_soportes' => count($soportes),
            ];
            print json_encode($datos);

            $this->contabilidad_model->crear('comprobantes_contables_tareas_detalle', $datos);

            $resultado[] = $datos;
        }

        if(empty($resultado)) {
            print json_encode([
                'error' => false,
                'resultado' => false,
                'mensaje' => 'No hay archivos por procesar. Por favor verifica que las carpetas tengan la estructura correcta e intenta nuevamente.',
            ]);
            return false;
        }
                  
        // Marcamos la fecha de finalización de la tarea
        $this->contabilidad_model->actualizar('comprobantes_contables_tareas', ['id' => $tarea->id], ['fecha_fin_ejecucion' => DATE('Y-m-d H:i:s')]);

        $fin = microtime(true);

        $this->configuracion_model->crear('logs', [
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'log_tipo_id' => 107,
            'observacion' => json_encode([
                'registros_procesados' => count($resultado),
                'tiempo_ejecucion' => round($fin - $inicio, 2) . ' segundos'
            ])
        ]);

        print json_encode([
            'error' => false,
            'resultado' => $resultado,
        ]);
    }

    /**
     * Funciones que se encargan de extraer datos del ERP Siesa
     *
     * @param string $tipo
     * @return void
     */
    function erp($tipo) {
        switch ($tipo) {
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

            /**
             * Importa de Siesa V2 las cuentas por pagar
             */
            case 'importar_cuentas_por_pagar':
                $numero_documento = $this->uri->segment(4);
                $tiempo_inicial = microtime(true);
                $total_items = 0;

                try {
                    $codigo = 0;
                    $pagina = 1;
                    $items_almacenados = 0;

                    // Eliminamos todos los ítems asociados al proveedor
                    $this->proveedores_model->eliminar('erp_cuentas_por_pagar', ['f200_id' => $numero_documento]);

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

                            $items_almacenados += $this->proveedores_model->insertar_batch('erp_cuentas_por_pagar', $nuevas_cuentas);
                            
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

            /**
             * Importa de Siesa los movimientos contables
             * de un tercero
             */
            case 'importar_movimientos_contables':
                $numero_documento = $this->uri->segment(4);
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
            break;

            /**
             * Importa de Siesa los productos y su información básica
             */
            case 'importar_productos_detalle':
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
            break;

            /**
             * Importa de Siesa el inventario disponible de cada producto
             */
            case 'importar_productos_inventario':
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
            break;

            /**
             * Importa de Siesa los detalles de pedidos del día anterior
             * o del día seleccionado, con el fin de mostrar en la tienda
             * Los productos destacados y/o más vendidos
             */
            case 'importar_productos_pedidos':
                $fecha = $this->uri->segment(4);
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
            break;

            /**
             * Importa de la API estándar del ERP Siesa 
             * los precios configurados de cada producto 
             */
            case 'importar_productos_precios';
                $tiempo_inicial = microtime(true);
                $total_items = 0;

                try {
                    // Filtro de la lista de precios
                    $filtro_lista_precios = $this->input->get('lista_precio');
                    $lista_precio = ($filtro_lista_precios) ? $filtro_lista_precios : $this->config->item('lista_precio');
                    $resultado = json_decode(obtener_precios_api(['lista_precio' => $lista_precio]));
                    $codigo_resultado = $resultado->codigo;
                    $precios = (isset($resultado->detalle->Table)) ? $resultado->detalle->Table : [] ;
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
            break;

            /**
             * Descarga todos los terceros de Siesa,
             * recorriendo cada página e insertando en
             * la base de datos el resultado por cada página
             */
            case 'importar_terceros':
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
            break;

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
            
            default:
                print json_encode([
                    'exito' => false,
                    'mensaje' => 'Ningún webhook encontrado'
                ]);

                return http_response_code(400);
        }
    }

    /**
     * De un archivo en PDF extrae el texto buscado
     *
     * @param string $pdf_path
     * @param string $tipo
     * @param integer $pagina
     * @return void
     */
    function extraer_texto($pdf_path, $tipo, $pagina = 1) {
        // // Verificar que el archivo existe
        // if (!file_exists($pdf_path)) {
        //     echo "Archivo PDF no encontrado: $pdf_path";
        //     // throw new Exception("Archivo PDF no encontrado: $pdf_path");
        // }
        
        // Cargar PDF Parser si está disponible
        if (class_exists('Smalot\PdfParser\Parser')) {
            $parser = new Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdf_path);
            $paginas = $pdf->getPages();
            
            if ($pagina > count($paginas)) {
                echo "La página $pagina no existe";
                throw new Exception("La página $pagina no existe");
            }
            
            $pagina_actual = $paginas[$pagina - 1];
            
            // Obtener todo el texto de la página
            $texto_completo = trim($pagina_actual->getText());
            // echo "$texto_completo<hr>";

            // En PDF Parser no hay acceso directo a coordenadas del texto
            // Esta es una aproximación básica
            $lineas = explode("\n", $texto_completo);
            $texto_en_area = '';
            
            foreach ($lineas as $linea) {
                $patron = '/\d{3}-'.$tipo.'-\d{8}/'; // Con guión
                // $patron = '/\d{3}'.$tipo.'\d{8}/'; // Sin guuión

                // Buscar el patrón específico que necesitas
                if (preg_match($patron, $linea, $coincidencias)) {
                    $texto_en_area = $coincidencias[0];
                    break;
                }
            }
            
            return $texto_en_area != '';
        } else {
            // throw new Exception("Se requiere PDF Parser: composer require smalot/pdfparser");
        }
    }

    /**
     * Toma el número de documento y lo formatea
     *
     * @param string $codigo
     * @param string $tipo
     * @return void
     */
    function formatear_codigo($codigo, $tipo) {
        // Primero quitar todos los guiones para limpiar
        $codigo_limpio = str_replace('-', '', $codigo);

        $patron = '/^(\d{3})('.$tipo.')(\d+)$/';

        // Patrón: 100FRC52378 -> 100-FRC-52378 (últimos 8 dígitos con ceros a la izquierda)
        if (preg_match($patron, $codigo_limpio, $coincidencias)) {
            // Rellenar con ceros a la izquierda para tener siempre 8 dígitos
            $numeroFormateado = str_pad($coincidencias[3], 8, '0', STR_PAD_LEFT);

            return $coincidencias[1] . '-' . $coincidencias[2] . '-' . $numeroFormateado;
        }

        // Devolver el código original si no coincide el patrón
        return $codigo;
    }
}