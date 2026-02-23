<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Microsoft_graph {
    // Configuración de Microsoft Graph API
    private $client_id;
    private $client_secret;
    private $tenant_id;
    private $scope;
    private $email_usuario;

    public function __construct() {
        $this->ci =& get_instance();
        
        // Carga las credenciales desde config
        $this->ci->config->load('microsoft_graph', TRUE);
        $this->client_id = $this->ci->config->item('client_id', 'microsoft_graph');
        $this->client_secret = $this->ci->config->item('client_secret', 'microsoft_graph');
        $this->tenant_id = $this->ci->config->item('tenant_id', 'microsoft_graph');
        $this->scope = $this->ci->config->item('scope', 'microsoft_graph');
        $this->email_usuario = $this->ci->config->item('email_usuario', 'microsoft_graph');
    }

    /**
     * Obtiene el token de acceso de Microsoft Graph API
     */
    function obtener_token() {
        $url = "https://login.microsoftonline.com/{$this->tenant_id}/oauth2/v2.0/token";
        
        $datos = [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'client_credentials',
            'scope' => $this->scope
        ];

        return $this->enviar_peticion($url, $datos);
    }

    /**
     * Busca una carpeta por nombre
     */
    function buscar_carpeta($token, $nombre_carpeta) {
        // Si es "Inbox", usar directamente ese ID
        if(strtolower($nombre_carpeta) == 'inbox') return 'Inbox';

        $url = "https://graph.microsoft.com/v1.0/users/{$this->email_usuario}/mailFolders";

        $peticion = $this->enviar_peticion($url, null, $token);
        
        if($peticion['http_code'] == 200) {
            // Buscar la carpeta por nombre
            foreach($peticion['respuesta']['value'] as $carpeta) {
                if(strtolower($carpeta['displayName']) == strtolower($nombre_carpeta)) {
                    return $carpeta['id'];
                }
            }
        }
        
        return null;
    }

    private function enviar_peticion($url, $datos = null, $token = null) {
        $peticion = curl_init();
        $peticion = curl_init();

        if($token) {
            curl_setopt($peticion, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $token",
                'Content-Type: application/json'
            ]);
        }
         
        if(!empty($datos)) {
            curl_setopt($peticion, CURLOPT_POST, true);
            curl_setopt($peticion, CURLOPT_POSTFIELDS, http_build_query($datos));
        } 
        
        curl_setopt($peticion, CURLOPT_URL, $url);
        curl_setopt($peticion, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($peticion, CURLOPT_SSL_VERIFYPEER, false);
        
        $respuesta = curl_exec($peticion);
        $codigo_respuesta = curl_getinfo($peticion, CURLINFO_HTTP_CODE);
        $error = curl_error($peticion);
        
        curl_close($peticion);
        
        return [
            'status' => ($codigo_respuesta >= 200 && $codigo_respuesta < 300),
            'http_code' => $codigo_respuesta,
            'respuesta' => json_decode($respuesta, true),
            'error' => $error
        ];
    }

    /**
     * Obtiene mensajes con adjuntos de una carpeta
     */
    function obtener_mensajes($token, $carpeta_id) {
        $url = "https://graph.microsoft.com/v1.0/users/{$this->email_usuario}/mailFolders/{$carpeta_id}/messages";
        $url .= '?$top=500 '; // Cantidad de registros
        $url .= "?\$filter=hasAttachments%20eq%20true&\$select=id,subject,hasAttachments";        

        return $this->enviar_peticion($url, null, $token);
    }

    /**
     * Descarga los adjuntos de un mensaje
     */
    function descargar_archivos_adjuntos($token, $mensaje_id, $ruta_archivo) {
        // Obtener los adjuntos del mensaje
        $url = "https://graph.microsoft.com/v1.0/users/{$this->email_usuario}/messages/{$mensaje_id}/attachments";        
        $peticion = $this->enviar_peticion($url, null, $token);
    
        $adjuntos = $peticion['respuesta']['value'];
        
        $archivos_guardados = 0;
        
        // // Crear directorio si no existe
        // if(!is_dir($ruta_base)) {
        //     mkdir($ruta_base, 0777, true);
        // }
        
        foreach($adjuntos as $adjunto) {
            // Si es un adjunto en PDF
            if($adjunto['@odata.type'] == '#microsoft.graph.fileAttachment' && $adjunto['@odata.mediaContentType'] == 'application/pdf') {
                $nombre_archivo = $adjunto['name'];
                $contenido = base64_decode($adjunto['contentBytes']);
                
        //         // Extraer NIT, Nombre y Monto del nombre del archivo
        //         // Formato esperado: ALGO_QUE_CONTIENE_NIT_Nombre_Monto.ext
        //         $nombre_procesado = $this->procesar_nombre_archivo($nombre_archivo);
                
                // Guardar el archivo
                if(file_put_contents($ruta_archivo, $contenido)) {
                    $archivos_guardados++;
                }
            }
        }
        
        return ['exito' => true, 'cantidad' => $archivos_guardados];
    }
}