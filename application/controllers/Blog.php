<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		29 de junio de 2023
 * Programa:  	E-Commerce | Módulo Blog
 * Email: 		johnarleycano@hotmail.com
 */
class Blog extends MY_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();
    }

    function index() {
        redirect($this->config->item('base_url_blog'));
    }

    function contacto() {
        $this->data['contenido_principal'] = 'blog/contacto';
        $this->load->view('core/body', $this->data);
    }

    function denuncias() {
        $this->data['contenido_principal'] = 'blog/denuncias';
        $this->load->view('core/body', $this->data);
    }

    function credito() {
        $this->data['contenido_principal'] = 'blog/credito';
        $this->load->view('core/body', $this->data);
    }

    function distribuidores() {
        $this->data['contenido_principal'] = 'blog/distribuidores';
        $this->load->view('core/body', $this->data);
    }

    function nosotros() {
        $this->data['contenido_principal'] = 'blog/nosotros';
        $this->load->view('core/body', $this->data);
    }

    function politica_envios() {
        $this->data['contenido_principal'] = 'blog/politica_envios';
        $this->load->view('core/body', $this->data);
    }

    function taller_aliado() {
        $this->data['contenido_principal'] = 'blog/taller_aliado';
        $this->load->view('core/body', $this->data);
    }

    function tratamiento_datos() {
        $this->data['contenido_principal'] = 'blog/tratamiento_datos';
        $this->load->view('core/body', $this->data);
    }

    function tratamiento_cookies() {
        $this->data['contenido_principal'] = 'blog/tratamiento_cookies';
        $this->load->view('core/body', $this->data);
    }
}
/* Fin del archivo Contacto.php */
/* Ubicación: ./application/controllers/Contacto.php */