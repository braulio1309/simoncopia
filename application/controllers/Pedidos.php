<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		22 de mayo de 2023
 * Programa:  	E-Commerce | Módulo de Pedidos
 *            	Gestión de pedidos del sistema
 * Email: 		johnarleycano@hotmail.com
 */
class Pedidos extends MY_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();

        // $this->load->model('productos_model');
    }

    function index() {
        print_r(json_decode(obtener_pedidos_api(''))->detalle->Table);
    }

    function obtener() {
        $datos = json_decode($this->input->post('datos'), true);
        $tipo = $datos['tipo'];
        unset($datos['tipo']);

        switch ($tipo) {
            case 'pedidos_api':
                print json_encode(json_decode(obtener_pedidos_api(''))->detalle->Table);
            break;
        }
    }
}
/* Fin del archivo Pedidos.php */
/* Ubicación: ./application/controllers/Pedidos.php */