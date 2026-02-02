<?php
/**
 * Modelo encargado de gestionar los correos electrónicos
 * 
 * @author 		       John Arley Cano Salinas -johnarleycano@hotmail.com
 */
Class Email_model extends CI_Model {
    function __construct() {
        parent::__construct();
     
        // Carga de modelos y librerías
        $this->load->helper('directory');
    }

    function enviar($datos) {
        $configuracion = $this->config->item('datos_email');
        if(ENVIRONMENT != 'production') $datos['destinatarios'] = 'johnarleycano@hotmail.com';

        // Preparando el mensaje
        $this->email->initialize($configuracion);
        $this->email->from($configuracion['smtp_user'], "Tienda - Simón Bolívar");
        $this->email->bcc(array('johnarleycano@hotmail.com'));
        $this->email->to($datos['destinatarios']);
        $this->email->subject($datos['asunto']);
        $this->email->set_newline("\r\n");

        // Se organiza la plantilla
	    $mensaje = file_get_contents("application/views/email/plantilla.php");
        $mensaje = str_replace('{TITULO}', $datos['cuerpo']['titulo'], $mensaje);
        $mensaje = str_replace('{SUBTITULO}', $datos['cuerpo']['subtitulo'], $mensaje);
        
        if($datos['pedido_completo']) {
            $mensaje = str_replace('{DETALLE_PEDIDO}', file_get_contents(site_url("interfaces/carrito/{$datos['id']}")), $mensaje);
        } else {
            $mensaje = str_replace('{DETALLE_PEDIDO}', '', $mensaje);
        }

        $this->email->message($mensaje);

        // Si tiene archivos adjuntos
        if(isset($datos['adjuntos']) && $datos['adjuntos']) {
            $directorio = "./archivos/solicitudes_credito/{$datos['id']}/";

            $archivos = directory_map($directorio);

            // Recorrer y adjuntar los archivos al email
            foreach ($archivos as $archivo) {
                $ruta_completa = $directorio.$archivo;
                $this->email->attach($ruta_completa);
            }
        }
       
        // Envío del mensaje
        $this->email->send();

		return $this->email->print_debugger();
    }
}
/* Fin del archivo Email_model.php */
/* Ubicación: ./application/models/Email_model.php */