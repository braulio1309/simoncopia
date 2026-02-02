<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('No direct script access allowed');

class Perfil extends MY_Controller {
    function __construct() {
        parent::__construct();

        $this->load->model(['sesion_model']);
    }

    function index() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');

        $this->data['contenido_principal'] = 'perfil/index';
        $this->data['vista'] = $this->uri->segment(3);
        $this->load->view('core/body', $this->data);
    }
}
/* Fin del archivo Perfil.php */
/* Ubicación: ./application/controllers/Perfil.php */