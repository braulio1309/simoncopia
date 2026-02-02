<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends MY_Controller {
    function __construct() {
        parent::__construct();

        // $this->load->model(['sesion_model']);
    }

    function registro() {
        $this->data['url'] = $this->input->get('url');
        $this->data['contenido_principal'] = 'usuarios/registro/index';
        $this->load->view('core/body', $this->data);
    }
}
/* Fin del archivo Usuarios.php */
/* Ubicación: ./application/controllers/Usuarios.php */