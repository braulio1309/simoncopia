<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('No direct script access allowed');

ini_set('MAX_EXECUTION_TIME', '-1');
ini_set('memory_limit', '-1');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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
        $this->load->model(['contabilidad_model']);

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
}