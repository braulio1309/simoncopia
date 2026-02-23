<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		10 de enero de 2023
 * Programa:  	E-Commerce | Módulo de Configuración
 *            	Gestión de configuración del sistema
 * Email: 		johnarleycano@hotmail.com
 */
class Configuracion extends MY_Controller {
    function __construct() {
        parent::__construct();

        $this->load->model(['productos_model']);

        if($this->session->userdata('usuario_id')) $this->data['permisos'] = $this->verificar_permisos();
    }

    /**
     * Marca la disponibilidad de un usuario para recibir asignaciones de solicitudes de crédito
     *
     * @return void
     */
    public function marcar_disponibilidad() {
        // Limpiar usuarios inactivos
        $this->configuracion_model->limpiar_usuarios_inactivos();
        
        $id_usuario = $this->session->userdata('usuario_id');
        
        // Si no se ha iniciado la sesión
        if (!$id_usuario) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('error' => 'Usuario no autenticado')));
            return;
        }
        
        $disponible = $this->input->post('disponible');
        
        // Dependiendo de la disponibilidad
        if ($disponible) {
            $this->configuracion_model->marcar_usuario_disponible($id_usuario);
            $respuesta = ['estado' => 'disponible'];
        } else {
            $this->configuracion_model->marcar_usuario_no_disponible($id_usuario);
            $respuesta = ['estado' => 'no_disponible'];
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($respuesta));
    }
    
    function obtener() {
        $datos = json_decode($this->input->post('datos'), true);
        $tipo = $datos['tipo'];
        unset($datos['tipo']);

        switch ($tipo) {
            default:
                $resultado = $this->configuracion_model->obtener($tipo, $datos);
            break;

            case 'usuarios_disponibles':
                $resultado = $this->configuracion_model->obtener($tipo);
            break;
        }

        print json_encode($resultado);
    }

    function obtener_datos_tabla() {
        if (!$this->input->is_ajax_request()) redirect('inicio');

        $tipo = $this->input->get("tipo");
        $busqueda = $this->input->get("search")["value"];
        $indice = $this->input->get("start");
        $cantidad = $this->input->get("length");
        $columns = $this->input->get("columns");
        $order = $this->input->get("order");
        $ordenar = null;

        // Filtros personalizados de las columnas
        $filtro_fecha_creacion = $this->input->get("filtro_fecha_creacion");
        $filtro_numero_documento = $this->input->get("filtro_numero_documento");
        $filtro_nombre = $this->input->get("filtro_nombre");
        $filtro_forma_pago = $this->input->get("filtro_forma_pago");
        $filtro_recibo_siesa = $this->input->get("filtro_recibo_siesa");
        $filtro_estado = $this->input->get("filtro_estado");
        $filtro_valor = $this->input->get("filtro_valor");
        $filtro_usuario_creador = $this->input->get("filtro_usuario_creador");
        $filtro_comentarios = $this->input->get("filtro_comentarios");
        $filtro_observaciones = $this->input->get("filtro_observaciones");
        $filtro_telefono = $this->input->get("filtro_telefono");
        $filtro_token = $this->input->get("filtro_token");

        // Si en la tabla se aplico un orden se obtiene el campo por el que se ordena
        if ($order) {
            $columna = $order[0]["column"];
            $orden = $order[0]["dir"];
            $campo = $columns[$columna]["data"];
            if ($campo) $ordenar = "$campo $orden";
        }

        switch ($tipo) {
            case "productos_metadatos":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda
                ];

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->productos_model->obtener("productos_metadatos", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->productos_model->obtener("productos_metadatos", $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
            break;

            case "recibos":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda,
                    "id_tipo_recibo" => $this->input->get("id_tipo_recibo")
                ];

                // Filtros personalizados
                if(isset($filtro_fecha_creacion)) $datos['filtro_fecha_creacion'] = $filtro_fecha_creacion;
                if(isset($filtro_numero_documento)) $datos['filtro_numero_documento'] = $filtro_numero_documento;
                if(isset($filtro_nombre)) $datos['filtro_nombre'] = $filtro_nombre;
                if(isset($filtro_forma_pago)) $datos['filtro_forma_pago'] = $filtro_forma_pago;
                if(isset($filtro_recibo_siesa)) $datos['filtro_recibo_siesa'] = $filtro_recibo_siesa;
                if(isset($filtro_estado)) $datos['filtro_estado'] = $filtro_estado;
                if(isset($filtro_valor)) $datos['filtro_valor'] = $filtro_valor;
                if(isset($filtro_usuario_creador)) $datos['filtro_usuario_creador'] = $filtro_usuario_creador;
                if(isset($filtro_comentarios)) $datos['filtro_comentarios'] = $filtro_comentarios;
                if(isset($filtro_observaciones)) $datos['filtro_observaciones'] = $filtro_observaciones;
                if(isset($filtro_telefono)) $datos['filtro_telefono'] = $filtro_telefono;
                if(isset($filtro_token)) $datos['filtro_token'] = $filtro_token;

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->configuracion_model->obtener("recibos", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->configuracion_model->obtener("recibos", $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
            break;

            case "logs":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda,
                    "fecha_inicial" => $this->input->get("fecha_inicial"),
                    "fecha_final" => $this->input->get("fecha_final"),
                    "filtros_personalizados" => $this->input->get("filtros_personalizados"),
                ];

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->configuracion_model->obtener("logs", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->configuracion_model->obtener("logs", $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
                break;
        }
    }

    function comprobantes() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');
        if(!in_array(['configuracion' => 'configuracion_comprobantes_ver'], $this->data['permisos'])) redirect('inicio');

        switch ($this->uri->segment(3)) {
            case 'crear':
                $this->data['contenido_principal'] = 'configuracion/comprobantes/crear';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function contactos() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');
        if(!in_array(['configuracion' => 'configuracion_contactos_ver'], $this->data['permisos'])) redirect('inicio');

        switch ($this->uri->segment(3)) {
            case 'crear':
                $this->data['contenido_principal'] = 'configuracion/contactos/crear';
                $this->load->view('core/body', $this->data);
            break;

            case 'ver':
                $this->data['contenido_principal'] = 'configuracion/contactos/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function logs() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');
        if(!in_array(['configuracion' => 'configuracion_logs_ver'], $this->data['permisos'])) redirect('inicio');

        switch ($this->uri->segment(3)) {
            case 'ver':
                $this->data['contenido_principal'] = 'configuracion/logs/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function recibos() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');
        if(!in_array(['configuracion' => 'configuracion_recibos_ver'], $this->data['permisos'])) redirect('inicio');

        switch ($this->uri->segment(3)) {
            case 'ver':
                $this->data['id_tipo_recibo'] = $this->uri->segment(4);
                $this->data['contenido_principal'] = 'configuracion/recibos/index';
                $this->load->view('core/body', $this->data);
            break;

            case 'id':
                $this->data['token'] = $this->uri->segment(4);
                $this->data['contenido_principal'] = 'configuracion/recibos/detalle/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function perfiles() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');
        if(!in_array(['configuracion' => 'configuracion_perfiles_ver'], $this->data['permisos'])) redirect('inicio');

        switch ($this->uri->segment(3)) {
            case 'lista':
                $this->data['datos'] = $this->input->post('datos');
                $this->load->view('configuracion/perfiles/lista', $this->data);
            break;

            case 'ver':
                $this->data['contenido_principal'] = 'configuracion/perfiles/index';
                $this->load->view('core/body', $this->data);
            break;

            case 'id':
                $this->data['token'] = $this->uri->segment(4);
                $this->data['contenido_principal'] = 'configuracion/perfiles/detalle/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function productos() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');
        if(!in_array(['configuracion' => 'configuracion_productos_ver'], $this->data['permisos'])) redirect('inicio');

        switch ($this->uri->segment(3)) {
            case 'ver':
                $this->data['contenido_principal'] = 'configuracion/productos/metadatos/index';
                $this->load->view('core/body', $this->data);
            break;

            case 'lista':
                $this->load->view('configuracion/productos/metadatos/lista');
            break;

            case 'crear':
                $this->data['contenido_principal'] = 'configuracion/productos/metadatos/detalle';
                $this->load->view('core/body', $this->data);
            break;

            case 'editar':
                $this->data['id'] = $this->uri->segment(4);
                $this->data['contenido_principal'] = 'configuracion/productos/metadatos/detalle';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    private function importar_productos_metadatos($archivo) {
        try {
            $excel  = PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);
            $hoja = $excel->getActiveSheet();
            $registros = $hoja->toArray();

            // Se elimina la primera fila del excel
            unset($registros[0]);

            // Se filtran los datos y se obtienen solo los id
            $productos_id = array_column($registros, 0);
            array_walk($productos_id, function(&$valor, $indice) {
                $valor = obtener_segmentos_url($valor)[2];
            });

            $productos_metadatos = $this->productos_model->obtener("productos_metadatos", ["productos_ids" => implode(",", $productos_id)]);
            $productos_metadatos_registrados = array_column($productos_metadatos, null, "producto_id");

            $datos_insertar = [];
            $datos_actualizar = [];
            $total = 0;

            foreach ($registros as $registro) {
                $producto_id = obtener_segmentos_url($registro[0])[2];

                $datos = [
                    "producto_id" => $producto_id,
                    "palabras_clave" => $registro[1],
                    "titulo" => $registro[2],
                    "descripcion" => $registro[3],
                    "detalles_tecnicos" => $registro[4],
                    "slug" => obtener_segmentos_url($registro[5])[2],
                    "fecha_creacion" => date("Y-m-d H:i:s")
                ];

                if (isset($productos_metadatos_registrados[$producto_id])) {
                    unset($datos["fecha_creacion"]);
                    $datos["id"] = $productos_metadatos_registrados[$producto_id]->id;
                    $datos["fecha_modificacion"] = date("Y-m-d H:i:s");
                    array_push($datos_actualizar, $datos);
                    continue;
                }

                array_push($datos_insertar, $datos);
            }

            if (!empty($datos_insertar)) $this->productos_model->crear("productos_metadatos_batch", $datos_insertar);
            if (!empty($datos_actualizar)) $this->productos_model->actualizar_batch("productos_metadatos", $datos_actualizar, "id");

            return [
                "exito" => true,
                "mensaje" => "Se subió y se importaron correctamente los metadatos de los productos"
            ];
        } catch (Exception $e) {
            log_message('error', 'Error al importar los datos de los productos metadatos: ' . $e->getMessage());

            return [
                "exito" => true,
                "mensaje" => "No se subieron y no se importaron correctamente los metadatos de los productos"
            ];
        }
    }

    function subir() {
        $exito = false;
        $mensaje = "";

        $directorio = "archivos/temporales/";

        if(!is_dir($directorio)) @mkdir($directorio, 0777);

        $archivo = $_FILES['archivo'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombre_archivo = bin2hex(random_bytes(8)).".$extension";

        if (move_uploaded_file($archivo['tmp_name'], $directorio.$nombre_archivo)) {
            $exito = true;
            $mensaje = "El archivo subió correctamente.";
        } else {
            $mensaje = "Ha ocurrido un error subiendo el archivo.";
        }

        if ($exito) {
            $resultado = $this->importar_productos_metadatos($directorio.$nombre_archivo);
            $exito = $resultado["exito"];
            $mensaje = $resultado["mensaje"];
        }

        unlink($directorio.$nombre_archivo);

        $respuesta = [
            "exito" => $exito,
            "mensaje" => $mensaje
        ];

        print json_encode($respuesta);
    }

    function usuarios() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');
        if(!in_array(['configuracion' => 'configuracion_usuarios_ver'], $this->data['permisos'])) redirect('inicio');

        switch ($this->uri->segment(3)) {
            default:
                $this->data['contenido_principal'] = 'configuracion/usuarios/index';
                $this->data['vista'] = $this->uri->segment(3);
                $this->load->view('core/body', $this->data);
            break;

            case 'lista':
                $this->data['datos'] = $this->input->post('datos');
                $this->load->view('configuracion/usuarios/lista', $this->data);
            break;

            case 'ver':
                $this->data['contenido_principal'] = 'configuracion/usuarios/index';
                $this->load->view('core/body', $this->data);
            break;

            case 'id':
                $this->data['token'] = $this->uri->segment(4);
                $this->data['contenido_principal'] = 'configuracion/usuarios/detalle/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }
}
/* Fin del archivo Configuracion.php */
/* Ubicación: ./application/controllers/Configuracion.php */