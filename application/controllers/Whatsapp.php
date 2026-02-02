<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		13 de enero de 2026
 * Programa:  	E-Commerce | Módulo de gestión de mensajes por Whatsapp
 * Email: 		johnarleycano@hotmail.com
 */
class Whatsapp extends MY_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();

        $this->config->load('whatsapp', TRUE);
    }

    /**
     * Método para la recepción de los mensajes
     */
    public function index() {
        $metodo = $_SERVER['REQUEST_METHOD'];

        if ($metodo == 'GET') {
            // El método GET verifica la conexión del webhook
            $this->verificar_webhook();
        } elseif ($metodo == 'POST') {
            $this->gestionar_webhook();
        } else {
            $this->output->set_status_header(405);
            echo 'Método no permitido';
        }
    }

    /**
     * Verificación del webhook (GET)
     */
    function verificar_webhook() {
        $hub_mode = $this->input->get('hub_mode');
        $hub_verify_token = $this->input->get('hub_verify_token');
        $hub_challenge = $this->input->get('hub_challenge');
        
        if ($hub_mode === 'subscribe' && $hub_verify_token === $this->config->item('whatsapp_token_verificacion', 'whatsapp')) {
            // Token válido, se responde con el challenge
            echo $hub_challenge;
            http_response_code(200);
        } else {
            // Token inválido
            $this->output->set_status_header(403);
            echo 'Error en la verificación del webhook';
            log_message('error', 'WhatsApp Webhook: Token de verificación incorrecto');
        }
    }

    /**
     * Manejo de mensajes entrantes (POST)
     */
    function gestionar_webhook() {
        // Lectura de los datos
        $post = file_get_contents('php://input');
        $datos = json_decode($post, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'WhatsApp Webhook: JSON inválido');
            $this->output->set_status_header(400);
            echo 'Invalid JSON';
            return;
        }
        
        // Se verifica que sea un webhook válido de WhatsApp
        if (isset($datos['object']) && $datos['object'] == 'whatsapp_business_account') {
            // Responder 200 OK inmediatamente
            http_response_code(200);
            echo 'EVENT_RECEIVED';
            
            // Procesar en segundo plano (asíncrono)
            $this->procesar_datos($datos);
        } else {
            $this->output->set_status_header(404);
            echo 'Not a WhatsApp webhook';
        }
    }

    /**
     * Procesar datos del webhook
     */
    function procesar_datos($datos) {
        // se registra el webhook recibido (opcional, para debugging)
        log_message('debug', 'Webhook WhatsApp recibido: ' . json_encode($datos));

        // Guardar en archivo log para depuración
        // $this->save_to_log($datos);
        
        // Se procesa cada entrada
        if (isset($datos['entry'])) {
            foreach ($datos['entry'] as $entrada) {
                if (isset($entrada['changes'])) {
                    foreach ($entrada['changes'] as $cambio) {
                        $this->procesar_cambios($cambio);
                    }
                }
            }
        }
    }

    /**
     * Procesar cambios específicos
     */
    function procesar_cambios($cambio) {
        $valor = $cambio['value'];    
        
        // Mensajes recibidos
        if (isset($valor['messages'])) {
            foreach ($valor['messages'] as $mensaje) {
                $this->procesar_mensaje_recibido($mensaje, $valor['metadata']);
            }
        }
        
        // Estados de mensajes (entregado, leído, etc.)
        if (isset($valor['statuses'])) {
            foreach ($valor['statuses'] as $estado) {
                $this->procesar_estado($estado);
            }
        }
    }

    /**
     * Procesar mensaje recibido
     */
    function procesar_mensaje_recibido($mensaje, $metadata) {
        $identificador_numero_telefonico = $metadata['phone_number_id'];
        $remitente = $mensaje['from']; // Número del remitente
        $id_mensaje = $mensaje['id'];
        
        // Determinar tipo de mensaje
        $tipo = $this->obtener_tipo_mensaje($mensaje);
        
        switch ($tipo) {
            case 'text':
                $texto = $mensaje['text']['body'];
                $this->gestionar_mensaje_texto($remitente, $texto, $id_mensaje, $identificador_numero_telefonico);
                break;
                
            case 'image':
                $id_imagen = $mensaje['image']['id'];
                $texto_imagen = isset($mensaje['image']['caption']) ? $mensaje['image']['caption'] : '';
                // $this->handle_image_message($remitente, $id_imagen, $texto_imagen, $id_mensaje, $identificador_numero_telefonico);
                break;
                
            case 'audio':
                $id_audio = $mensaje['audio']['id'];
                // $this->handle_audio_message($remitente, $id_audio, $id_mensaje, $identificador_numero_telefonico);
                break;
                
            case 'document':
                $id_documento = $mensaje['document']['id'];
                $nombre_archivo = $mensaje['document']['filename'];
                // $this->handle_document_message($remitente, $id_documento, $nombre_archivo, $id_mensaje, $identificador_numero_telefonico);
                break;
                
            case 'interactive':
                // $this->handle_interactive_message($remitente, $mensaje['interactive'], $id_mensaje, $identificador_numero_telefonico);
                break;
                
            default:
                log_message('debug', "Tipo de mensaje no manejado: $tipo");
        }
    }

     /**
     * Determinar tipo de mensaje
     */
    function obtener_tipo_mensaje($mensaje) {
        $tipos = ['text', 'image', 'audio', 'document', 'video', 'sticker', 'location', 'contacts', 'interactive'];
        
        foreach ($tipos as $tipo) {
            if (isset($mensaje[$tipo])) return $tipo;
        }
        
        return 'desconocido';
    }
    
    /**
     * Manejar mensaje de texto
     */
    function gestionar_mensaje_texto($remitente, $texto, $id_mensaje, $identificador_numero_telefonico) {
        log_message('debug', "Mensaje de texto de $remitente: $texto");
        
        // Aquí tu lógica de negocio
        // Ejemplo: responder automáticamente
        // $this->enviar_mensaje_texto($remitente, "Gracias por tu mensaje: $texto");

        // Log en base de datos
        $this->configuracion_model->crear('logs', [
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observacion' => json_encode([
                'remitente' => $remitente,
                'mensaje' => $texto
            ]),
            'log_tipo_id' => 102,
        ]);
        
        // Proceso de comandos
        if (strtolower($texto) == 'hola') {
            $this->whatsapp_api->enviar_mensaje($remitente, "¡Hola! ¿Puedo ayudarte en algo?");
        }
    }
    
    /**
     * Guardar log para depuración
     */
    function save_to_log($data) {
        $log_dir = APPPATH . 'logs/whatsapp_webhooks/';
        
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $filename = $log_dir . 'webhook_' . date('Y-m-d') . '.log';
        $log_entry = date('Y-m-d H:i:s') . " - " . json_encode($data) . PHP_EOL;
        
        file_put_contents($filename, $log_entry, FILE_APPEND);
    }
    
    /**
     * Procesar estados de mensajes
     */
    function procesar_estado($status) {
        $id_mensaje = $status['id'];
        $id_remitente = $status['recipient_id'];
        $tipo_estado = $status['status']; // sent, delivered, read, etc.
        
        log_message('debug', "Estado de mensaje $id_mensaje: $status_type");
    }
}