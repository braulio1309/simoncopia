<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') or exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	Laura Isabel Flórez Ramírez
 * Fecha: 		24 de enero de 2026
 * Programa:  	E-Commerce | Módulo de Importaciones pagos
 *            	Gestión de importaciones pagos del sistema
 * Email: 		lauraisabelflorezramirez@gmail.com
 */
class Importaciones_pagos extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model(['importaciones_pagos_model']);
    }

    /**
     * Carga la vista principal del módulo de Importaciones Pagos
     * 
     * Esta función define cuál vista se mostrará dentro del layout principal
     * del sistema (core/body), asignando la vista del módulo como contenido principal.
     */
    function ver()
    {
        $this->data['contenido_principal'] = 'importaciones_pagos/index';
        $this->load->view('core/body', $this->data);
    }

    /**
     * Obtiene los datos para la tabla DataTables (ServerSide Processing)
     * 
     * Esta función recibe los parámetros enviados por DataTables vía AJAX,
     * aplica filtros, búsqueda global, ordenamiento y paginación, y retorna
     * los datos en formato JSON requerido por DataTables.
     */
    function obtener_datos_tabla()
    {
        if (!$this->input->is_ajax_request()) redirect('inicio');

        $tipo = $this->input->get("tipo");
        $busqueda = $this->input->get("search")["value"];
        $indice = $this->input->get("start");
        $cantidad = $this->input->get("length");
        $columns = $this->input->get("columns");
        $order = $this->input->get("order");
        $ordenar = null;

        // Si en la tabla se aplico un orden se obtiene el campo por el que se ordena
        if ($order) {
            $columna = $order[0]["column"];
            $orden = $order[0]["dir"];
            $campo = $columns[$columna]["data"];
            if ($campo) $ordenar = "$campo $orden";
        }

        switch ($tipo) {
            case "importaciones_pagos":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda,
                    "filtros_personalizados" => $this->input->get("filtros_personalizados"),
                ];

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->importaciones_pagos_model->obtener_general("importaciones_pagos", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->importaciones_pagos_model->obtener_general("importaciones_pagos", $datos);

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
