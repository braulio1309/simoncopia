<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		3 de enero de 2023
 * Programa:  	E-Commerce | Módulo de Inicio
 *            	Gestión del inicio del sistema
 * Email: 		johnarleycano@hotmail.com
 */
class Inicio extends MY_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();

        $this->load->model(['configuracion_model']);
        
        if($this->session->userdata('usuario_id')) $this->data['permisos'] = $this->verificar_permisos();
    }

    function index() {
        // Cuando tenga rol de cliente crédito, cargará el dashboard correspondiente
        $contenido = 
        (isset($this->data['permisos']) && in_array(['inicio' => 'inicio_ver_dashboard_credito'], $this->data['permisos'])) 
        ? 'inicio/credito/index'
        : 'inicio/contado/index';

        $this->data['contenido_principal'] = $contenido;
        $this->load->view('core/body', $this->data);
    }
}
/* Fin del archivo Inicio.php */
/* Ubicación: ./application/controllers/Inicio.php */