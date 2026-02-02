<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author:     John Arley Cano Salinas
 * Fecha:       7 de junio de 2023
 * Programa:    E-Commerce | MY Controller
 *              Gestión de funciones generales
 *              de la aplicación
 * Email:       johnarleycano@hotmail.com
 */
class MY_Controller extends CI_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();

        // Se obtienen los permisos
        if($this->session->userdata('usuario_id')) $this->data['permisos'] = $this->verificar_permisos();
        
        $this->load->model('productos_model');
    }
    
    function verificar_permisos()
    {
        return $this->configuracion_model->obtener('permisos');
    }
}
/* Fin del archivo MY_Controller.php */
/* Ubicación: ./application/controllers/MY_Controller.php */