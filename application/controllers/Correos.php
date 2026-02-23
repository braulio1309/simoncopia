<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');


class Correos extends MY_Controller {
    /**
     * Función constructora de la clase
     */
    function __construct() {
        parent::__construct();
        $this->load->model('correos_model');
    }

    function index()
    {
        $this->data['contenido_principal'] = 'correos/descargar/index';
        $this->load->view('core/body', $this->data);
    }

    /**
     * Vista principal del módulo de descarga de correos
     */
    function descargar() {
        switch ($this->uri->segment(3)) {
            case 'ver':
                if(!$this->session->userdata('usuario_id')) redirect('inicio');
                
                $this->data['contenido_principal'] = 'correos/descargar/index';
                $this->load->view('core/body', $this->data);
                break;
        }
    }

    /**
     * Procesa la descarga de correos desde Microsoft Graph API
     */
    function procesar() {
        if(!$this->session->userdata('usuario_id')) {
            echo json_encode([
                'error' => true,
                'mensaje' => 'Sesión no válida'
            ]);
            return;
        }

        $datos = json_decode($this->input->post('datos'), true);
        
        // Validar que se reciba el nombre de la carpeta
        if(empty($datos['nombre_carpeta'])) {
            echo json_encode([
                'error' => true,
                'mensaje' => 'Debe proporcionar el nombre de la carpeta'
            ]);
            return;
        }

        try {
            // 1. Obtener token de acceso
            $token = $this->correos_model->obtener_token_microsoft();
            if(!$token) {
                echo json_encode([
                    'error' => true,
                    'mensaje' => 'No se pudo obtener el token de autenticación'
                ]);
                return;
            }

            // 2. Buscar la carpeta en Microsoft Graph
            $carpeta_id = $this->correos_model->buscar_carpeta($token, $datos['nombre_carpeta']);
            
            if(!$carpeta_id) {
                echo json_encode([
                    'error' => true,
                    'mensaje' => 'No se encontró la carpeta especificada'
                ]);
                return;
            }

            // 3. Obtener mensajes con adjuntos de esa carpeta
            $mensajes = $this->correos_model->obtener_mensajes_con_adjuntos($token, $carpeta_id);
            if(empty($mensajes)) {
                echo json_encode([
                    'error' => false,
                    'mensaje' => 'No se encontraron mensajes con adjuntos en esta carpeta',
                    'archivos_descargados' => 0
                ]);
                return;
            }

            // 4. Procesar cada mensaje y descargar sus adjuntos
            $archivos_descargados = 0;
            $errores = [];
            
            foreach($mensajes as $mensaje) {
                $resultado = $this->correos_model->descargar_adjuntos_mensaje(
                    $token, 
                    $mensaje['id'],
                    $datos['nombre_carpeta']
                );
                
                if($resultado['exito']) {
                    $archivos_descargados += $resultado['cantidad'];
                } else {
                    $errores[] = $resultado['error'];
                }
            }

            echo json_encode([
                'error' => false,
                'mensaje' => "Se descargaron {$archivos_descargados} archivos correctamente",
                'archivos_descargados' => $archivos_descargados,
                'errores' => $errores
            ]);

        } catch(Exception $e) {
            echo json_encode([
                'error' => true,
                'mensaje' => 'Error al procesar: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Lista los archivos descargados
     */
    function listar() {
        if(!$this->session->userdata('usuario_id')) {
            $this->data['archivos'] = [];
        } else {
            $this->data['archivos'] = $this->correos_model->listar_archivos_descargados();
        }
        
        $this->load->view('correos/descargar/lista', $this->data);
    }
}
/* Fin del archivo Correos.php */
/* Ubicación: ./application/controllers/Correos.php */