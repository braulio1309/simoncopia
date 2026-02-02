<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		12 de mayo de 2025
 * Programa:  	E-Commerce | Módulo Sitemap
 *            	Gestión de sitemap con los productos del sistema
 * Email: 		johnarleycano@hotmail.com
 */
class Sitemap extends MY_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();

        $this->load->model('productos_model');
    }

    /**
     * Genera el archivo sitemap.xml
     */
    function generar() {
        $productos = $this->productos_model->obtener('productos');

        // Inicio del XML
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        // Recorrido de los productos
        foreach ($productos as $producto) {
            $url = $xml->addChild('url');
            $url->addChild('loc', site_url("productos/ver/$producto->slug"));
            $url->addChild('lastmod', date('Y-m-d'));
            $url->addChild('changefreq', 'weekly');
            $url->addChild('priority', '0.8');
        }

        // Obtener el XML como texto
        $contenido = $xml->asXML();

        // Ruta a la raíz del proyecto
        $ruta_archivo = FCPATH.'sitemap.xml';

        // Guardar el archivo
        file_put_contents($ruta_archivo, $contenido);

        // Confirmación opcional
        echo "Sitemap generado en: $ruta_archivo";
    }
}
/* Fin del archivo Sitemap.php */
/* Ubicación: ./application/controllers/sitemap.php */