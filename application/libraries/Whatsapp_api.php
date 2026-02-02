<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_api {
    
    private $token;
    private $identificador_numero_telefonico;
    private $identificador_cuenta_whatsapp_business;
    private $version_api = 'v22.0'; // Actualiza según tu versión
    private $ci;
    
    public function __construct() {
        $this->ci =& get_instance();
        
        // Carga las credenciales desde config
        $this->ci->config->load('whatsapp', TRUE);
        $this->token = $this->ci->config->item('whatsapp_token', 'whatsapp');
        $this->identificador_numero_telefonico = $this->ci->config->item('identificador_numero_telefonico', 'whatsapp');
        $this->identificador_cuenta_whatsapp_business = $this->ci->config->item('identificador_cuenta_whatsapp_business', 'whatsapp');
    }
    
    /**
     * Envía un mensaje de texto
     */
    public function enviar_mensaje($numero_telefonico, $mensaje) {
        $url = "https://graph.facebook.com/{$this->version_api}/{$this->identificador_numero_telefonico}/messages";
        
        $data = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => formatear_numero_telefono($numero_telefonico),
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $mensaje
            ]
        ];
        
        return $this->enviar_peticion($url, $data);
    }
    
    /**
     * Envía un mensaje con plantilla (para mensajes pre-aprobados)
     */
    public function enviar_mensaje_con_plantilla($numero_telefonico, $nombre_plantilla, $lenguaje = 'es', $componentes = []) {
        $url = "https://graph.facebook.com/{$this->version_api}/{$this->identificador_numero_telefonico}/messages";
        
        $datos = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => formatear_numero_telefono($numero_telefonico),
            'type' => 'template',
            'template' => [
                'name' => $nombre_plantilla,
                'language' => [ 'code' => $lenguaje ],
                'components' => $componentes
            ]
        ];
        
        return $this->enviar_peticion($url, $datos);
    }
    
    /**
     * Envía un mensaje con imagen
     */
    public function enviar_mensaje_con_imagen($numero_telefonico, $nombre_plantilla, $lenguaje = 'es', $url_imagen) {
        $url = "https://graph.facebook.com/{$this->version_api}/{$this->identificador_numero_telefonico}/messages";

        $componentes = [];

        // HEADER con imagen
        $componentes[] = [
            'type' => 'header',
            'parameters' => [
                [
                    'type' => 'image',
                    'image' => [
                        'link' => $url_imagen
                    ]
                ]
            ]
        ];

        // BODY con parámetros dinámicos
        if (!empty($parametros)) {
            $componentes[] = [
                'type' => 'body',
                'parameters' => array_map(function ($valor) {
                    return [
                        'type' => 'text',
                        'text' => $valor
                    ];
                }, $parametros)
            ];
        }
        
        $datos = [
            'messaging_product' => 'whatsapp',
            'to' => formatear_numero_telefono($numero_telefonico),
            'type' => 'template',
            'template' => [
                'name' => $nombre_plantilla,
                'language' => [
                    'code' => $lenguaje
                ],
                'components' => $componentes
            ]
        ];
        
        return $this->enviar_peticion($url, $datos);
    }
    
    /**
     * Método principal para enviar solicitudes
     */
    private function enviar_peticion($url, $datos = null, $tipo = 'POST') {
        $peticion = curl_init();
        
        curl_setopt($peticion, CURLOPT_URL, $url);

        if($tipo == 'POST') curl_setopt($peticion, CURLOPT_POST, true);
        
        if($datos) curl_setopt($peticion, CURLOPT_POSTFIELDS, json_encode($datos));
        curl_setopt($peticion, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($peticion, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($peticion);
        $codigo_respuesta = curl_getinfo($peticion, CURLINFO_HTTP_CODE);
        $error = curl_error($peticion);
        
        curl_close($peticion);
        
        return [
            'status' => ($codigo_respuesta >= 200 && $codigo_respuesta < 300),
            'http_code' => $codigo_respuesta,
            'response' => json_decode($response, true),
            'error' => $error
        ];
    }

    /**
     * Obtiene las plantillas creadas
     *
     * @return void
     */
    public function obtener_plantillas() {
        $url = "https://graph.facebook.com/{$this->version_api}/{$this->identificador_cuenta_whatsapp_business}/message_templates";
        
        return $this->enviar_peticion($url, null, 'GET');
    }
}