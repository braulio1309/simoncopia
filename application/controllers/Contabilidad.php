<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		4 de octubre de 2025
 * Programa:  	Simón Bolívar | Módulo de Contabilidad
 * Email: 		johnarleycano@hotmail.com
 */
class Contabilidad extends MY_Controller {
    function __construct() {
        parent::__construct();
     
        // Carga de modelos y librerías
        $this->load->model(['contabilidad_model', 'productos_model']);

        if($this->session->userdata('usuario_id')) $this->data['permisos'] = $this->verificar_permisos();
    }

    /**
     * Gestión de comprobantes
     *
     * @return void
     */
    function comprobantes($tipo) {
        switch ($tipo) {
            case 'validacion':
                $this->data['contenido_principal'] = 'contabilidad/validacion_comprobantes/index';
                $this->load->view('core/body', $this->data);
            break;
        }
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

    function obtener_datos_tabla() {
        if (!$this->input->is_ajax_request()) redirect('inicio');

        $tipo = $this->input->get("tipo");
        $busqueda = $this->input->get("search")["value"];
        $indice = $this->input->get("start");
        $cantidad = $this->input->get("length");
        $columns = $this->input->get("columns");
        $order = $this->input->get("order");
        $ordenar = null;

        // Filtros personalizados de las columnas
        // $filtro_fecha_creacion = $this->input->get("filtro_fecha_creacion");

        // Si en la tabla se aplico un orden se obtiene el campo por el que se ordena
        if ($order) {
            $columna = $order[0]["column"];
            $orden = $order[0]["dir"];
            $campo = $columns[$columna]["data"];
            if ($campo) $ordenar = "$campo $orden";
        }

        switch ($tipo) {
            case "comprobantes_contables_tareas":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda
                ];

                // Filtros personalizados
                // if(isset($filtro_fecha_creacion)) $datos['filtro_fecha_creacion'] = $filtro_fecha_creacion;

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->contabilidad_model->obtener("comprobantes_contables_tareas", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->contabilidad_model->obtener("comprobantes_contables_tareas", $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
            break;
        }
    }

    /**
     * Procesa la carpeta y crea los registros en base de datos
     *
     * @param int $id_comprobante_tipo
     * @param int $anio
     * @param int $id_sede
     * @param int $mes
     * @return void
     */
    function validar_comprobantes() {
        header('Content-type: application/json');

        // Obtenemos la primera tarea pendiente
        $resultado = $this->contabilidad_model->obtener('comprobantes_contables_tareas', ['fecha_inicio_ejecucion' => 0]);

        if(empty($resultado[0])) {
            print json_encode([
                'error' => false,
                'resultado' => 'Ninguna tarea encontrada',
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

        print json_encode([
            'error' => false,
            'resultado' => $resultado,
        ]);
    }
}