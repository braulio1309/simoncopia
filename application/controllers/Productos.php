<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		10 de enero de 2023
 * Programa:  	E-Commerce | Módulo de Productos
 *            	Gestión de productos del sistema
 * Email: 		johnarleycano@hotmail.com
 */
class Productos extends MY_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();

        $this->load->model('productos_model');
    }

    function index() {
        $this->data['contenido_principal'] = 'productos/index';
        $this->load->view('core/body', $this->data);
    }

    function obtener() {
        $datos = json_decode($this->input->post('datos'), true);
        $tipo = $datos['tipo'];
        unset($datos['tipo']);

        switch ($tipo) {
            case 'detalle':
                $resultado = $this->productos_model->obtener($tipo, $datos);
            break;
        }

        print json_encode($resultado);
    }

    function ver() {
        $segmento = $this->uri->segment(3);
        if (!$segmento) redirect(site_url(''));

        // Se valida si el tercer segmento de la url es un número
        // o de lo contrario un string
        if (intval($segmento)) {
            $datos["producto_id"] = $segmento;
            $this->data['id'] = $segmento;
        } else {
            $datos["slug"] = $segmento;
        }

        // Se consultan los metadatos del producto
        $metadatos = $this->productos_model->obtener("productos_metadatos", $datos);

        if (!empty($metadatos)) {
            // Se cargan los metadatos en la data
            $this->data["metadatos"] = [
                "titulo" => $metadatos->titulo,
                "descripcion" => $metadatos->descripcion,
                "palabras_clave" => $metadatos->palabras_clave
            ];

            $this->data['id'] = $metadatos->producto_id;
        }

        $this->data['contenido_principal'] = 'productos/detalle';
        $this->load->view('core/body', $this->data);
    }

    function obtener_datos_tabla() {
        if (!$this->input->is_ajax_request()) redirect('inicio');

        $tipo = $this->input->get("tipo");
        // $busqueda = $this->input->get("search")["value"];
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
            case 'productos':
                // Se definen los filtros
                $datos = [
                    'contar' => true,
                    // 'busqueda' => $busqueda,
                ];

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->productos_model->obtener('productos', $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->productos_model->obtener('productos', $datos);

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
/* Fin del archivo Productos.php */
/* Ubicación: ./application/controllers/Productos.php */