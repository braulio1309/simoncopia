<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');


class Correos_model extends CI_Model {
    
    // Configuración de Microsoft Graph API
   
    
    /**
     * Obtiene el token de acceso de Microsoft Graph API
     */
    function obtener_token_microsoft() {
        $url = "https://login.microsoftonline.com/{$this->tenant_id}/oauth2/v2.0/token";
        
        $datos = [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'client_credentials',
            'scope' => $this->scope
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datos));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code == 200) {
            $resultado = json_decode($response, true);
            return $resultado['access_token'] ?? null;
        }
        
        return null;
    }

    /**
     * Lista las carpetas de correo disponibles desde Microsoft Graph
     */
    function listar_carpetas($token) {
        $carpetas = [];
        $url = "https://graph.microsoft.com/v1.0/users/{$this->email_usuario}/mailFolders?\$top=100";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$token}",
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($http_code == 200) {
            $resultado = json_decode($response, true);
            foreach($resultado['value'] as $carpeta) {
                $carpetas[] = [
                    'id'   => $carpeta['id'],
                    'nombre' => $carpeta['displayName'],
                    'total' => $carpeta['totalItemCount'] ?? 0
                ];
            }
        }

        return $carpetas;
    }

    /**
     * Busca una carpeta por nombre
     */
    function buscar_carpeta($token, $nombre_carpeta) {
        // Si es "Inbox", usar directamente ese ID
        if(strtolower($nombre_carpeta) == 'inbox') {
            return 'Inbox';
        }

        $url = "https://graph.microsoft.com/v1.0/users/{$this->email_usuario}/mailFolders";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$token}",
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if($http_code == 200) {
            $resultado = json_decode($response, true);
            // Buscar la carpeta por nombre
            foreach($resultado['value'] as $carpeta) {
                if(strtolower($carpeta['displayName']) == strtolower($nombre_carpeta)) {
                    return $carpeta['id'];
                }
            }
        }
        
        return null;
    }

    /**
     * Obtiene mensajes con adjuntos de una carpeta
     */
    function obtener_mensajes_con_adjuntos($token, $carpeta_id, $fecha_inicio = null, $fecha_fin = null) {
        $filtros = ["hasAttachments eq true"];

        if(!empty($fecha_inicio) && ($ts = strtotime($fecha_inicio)) !== false) {
            $filtros[] = "receivedDateTime ge " . date('Y-m-d', $ts) . "T00:00:00Z";
        }
        if(!empty($fecha_fin) && ($ts = strtotime($fecha_fin)) !== false) {
            $filtros[] = "receivedDateTime le " . date('Y-m-d', $ts) . "T23:59:59Z";
        }

        $filter_str = urlencode(implode(' and ', $filtros));
        $url = "https://graph.microsoft.com/v1.0/users/{$this->email_usuario}/mailFolders/{$carpeta_id}/messages";
        $url .= "?\$filter={$filter_str}&\$select=id,subject,hasAttachments&\$top=50";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$token}",
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code == 200) {
            $resultado = json_decode($response, true);
            return $resultado['value'] ?? [];
        }
        
        return [];
    }

    /**
     * Descarga los adjuntos de un mensaje
     */
    function descargar_adjuntos_mensaje($token, $mensaje_id, $carpeta_destino) {
        // Obtener los adjuntos del mensaje
        $url = "https://graph.microsoft.com/v1.0/users/{$this->email_usuario}/messages/{$mensaje_id}/attachments";        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$token}",
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code != 200) {
            return ['exito' => false, 'error' => 'No se pudieron obtener los adjuntos', 'cantidad' => 0];
        }
        
        $resultado = json_decode($response, true);
        $adjuntos = $resultado['value'] ?? [];
        
        $archivos_guardados = 0;
        
        // Crear directorio si no existe
        $ruta_base = './archivos/correos/' . $carpeta_destino . '/';
        if(!is_dir($ruta_base)) {
            mkdir($ruta_base, 0777, true);
        }
        
        foreach($adjuntos as $adjunto) {
            if($adjunto['@odata.type'] == '#microsoft.graph.fileAttachment') {
                $nombre_archivo = $adjunto['name'];
                $contenido = base64_decode($adjunto['contentBytes']);
                
                // Extraer NIT, Nombre y Monto del nombre del archivo
                // Formato esperado: ALGO_QUE_CONTIENE_NIT_Nombre_Monto.ext
                $nombre_procesado = $this->procesar_nombre_archivo($nombre_archivo);
                
                // Guardar el archivo
                $ruta_completa = $ruta_base . $nombre_procesado;
                if(file_put_contents($ruta_completa, $contenido)) {
                    $archivos_guardados++;
                    
                    // Registrar en base de datos
                    /*$this->registrar_descarga([
                        'carpeta' => $carpeta_destino,
                        'nombre_original' => $nombre_archivo,
                        'nombre_procesado' => $nombre_procesado,
                        'ruta' => $ruta_completa,
                        'mensaje_id' => $mensaje_id,
                        'fecha_descarga' => date('Y-m-d H:i:s')
                    ]);*/
                }
            }
        }
        
        return ['exito' => true, 'cantidad' => $archivos_guardados];
    }

    /**
     * Procesa el nombre del archivo para extraer NIT, Nombre y Monto
     * Si no puede extraer los datos, devuelve el nombre original
     */
    private function procesar_nombre_archivo($nombre_original) {
        
        if(preg_match('/(\d+)_(.+?)_(\d+)\./', $nombre_original, $matches)) {
            return $nombre_original; // Ya tiene el formato correcto
        }
        
        // Si no, intentar extraer de metadatos o devolver con timestamp
        $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
        $timestamp = date('YmdHis');
        
        // Formato: TIMESTAMP_NombreOriginal
        return "{$timestamp}_{$nombre_original}";
    }

    /**
     * Registra la descarga en base de datos
     */
    private function registrar_descarga($datos) {
        try {
            //$this->db->insert('correos_descargas', $datos);
        } catch(Exception $e) {
            // Si la tabla no existe, continuar sin registrar
            // Puedes crear la tabla ejecutando el script SQL que se proporciona
        }
    }

    /**
     * Lista los archivos descargados desde el sistema de archivos
     */
    function listar_archivos_descargados($limite = 100) {
        $ruta_base = './archivos/correos/';
        $archivos = [];

        if(!is_dir($ruta_base)) return [];

        foreach(glob($ruta_base . '*/') as $carpeta_path) {
            $carpeta = basename($carpeta_path);
            foreach(glob($carpeta_path . '*.*') as $archivo_path) {
                $archivos[] = [
                    'carpeta'          => $carpeta,
                    'nombre_procesado' => basename($archivo_path),
                    'ruta'             => str_replace('./', '', $archivo_path),
                    'fecha_descarga'   => date('Y-m-d H:i:s', filemtime($archivo_path))
                ];
            }
        }

        usort($archivos, function($a, $b) {
            return strtotime($b['fecha_descarga']) - strtotime($a['fecha_descarga']);
        });

        return array_slice($archivos, 0, $limite);
    }
}
