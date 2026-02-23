<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		20 de septiembre de 2023
 * Programa:  	E-Commerce | Módulo de Clientes
 *            	Gestión de información del cliente
 *              desde fuentes externas
 * Email: 		johnarleycano@hotmail.com
 */
class Clientes extends MY_Controller {
    function __construct() {
        parent::__construct();

        $this->load->model(['productos_model', 'clientes_model']);
    }

    function index() {
        $this->consultar();
    }

    function pedidos() {
        // if(!in_array(['configuracion' => 'configuracion_recibos_ver'], $this->data['permisos'])) redirect('inicio');
        
        switch ($this->uri->segment(3)) {
            case 'ver':
                $this->data['contenido_principal'] = 'clientes/pedidos/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function certificados_tributarios() {        
        switch ($this->uri->segment(3)) {
            case 'ver':
                $this->data['contenido_principal'] = 'clientes/certificados_tributarios/index';
                $this->load->view('core/body', $this->data);
                break;
            
            case 'crear':
                $this->data['nit'] = $this->uri->segment(4);
                $this->data['contenido_principal'] = 'clientes/certificados_tributarios/detalle';
                $this->load->view('core/body', $this->data);
                break;
        }
    }

    function credito() {
        switch ($this->uri->segment(3)) {
            case 'ver':
                if(!$this->session->userdata('usuario_id')) redirect('inicio');

                $id = intval($this->uri->segment(4));

                // Se valida si es un id válido
                if ($id) {
                    if (gettype($id) === "integer") {
                        $solicitud = $this->clientes_model->obtener('clientes_solicitudes_credito', ['id' => $id]);

                        if (!empty($solicitud)) {
                            $this->data['id'] = $solicitud->id;
                            $this->data['tipo'] = $this->uri->segment(5);
                            $this->data['contenido_principal'] = 'clientes/solicitud_credito/detalle_general';
                            $this->load->view('core/body', $this->data);
                            return;
                        }

                        redirect('inicio');
                    } else {
                        redirect('inicio');
                    }
                }

                $this->data['contenido_principal'] = 'clientes/solicitud_credito/index';
                $this->load->view('core/body', $this->data);
            break;

            default:
                $this->data['contenido_principal'] = 'clientes/solicitud_credito/detalle';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function consultar() {
        $this->data['contenido_principal'] = 'clientes/estado_cuenta/index';
        $this->load->view('core/body', $this->data);
    }

    function estado_cuenta() {
        $this->data['contenido_principal'] = 'clientes/estado_cuenta/index';
        $this->load->view('core/body', $this->data);
    }

    function respuesta() {
        $this->data['contenido_principal'] = 'clientes/estado_cuenta/carrito/respuesta';
        $this->load->view('core/body', $this->data);
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
        $filtro_id = $this->input->get("filtro_id");
        $filtro_estado = $this->input->get("filtro_estado");
        $filtro_usuario_asignado = $this->input->get("filtro_usuario_asignado");
        $filtro_vendedor = $this->input->get("filtro_vendedor");
        $filtro_fecha_cierre = $this->input->get("filtro_fecha_cierre");
        $filtro_motivo_rechazo = $this->input->get("filtro_motivo_rechazo");
        $filtro_cupo = $this->input->get("filtro_cupo");
        $filtro_ultimo_comentario = $this->input->get("filtro_ultimo_comentario");
        $filtro_id_consecutivo = $this->input->get("filtro_id_consecutivo");
        $filtro_nombre_consecutivo = $this->input->get("filtro_nombre_consecutivo");
        $filtro_numero_pedido = $this->input->get("filtro_numero_pedido");
        $filtro_tipo = $this->input->get("filtro_tipo");

        // Si en la tabla se aplico un orden se obtiene el campo por el que se ordena
        if ($order) {
            $columna = $order[0]["column"];
            $orden = $order[0]["dir"];
            $campo = $columns[$columna]["data"];
            if ($campo) $ordenar = "$campo $orden";
        }

        switch ($tipo) {
            case "clientes_retenciones_informe":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda,
                    "filtros_personalizados" => $this->input->get("filtros_personalizados"),
                ];

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->clientes_model->obtener("clientes_retenciones_informe", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->clientes_model->obtener("clientes_retenciones_informe", $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
            break;
            
            case 'facturas_pendientes':
                // Se definen los filtros
                $datos = [
                    'contar' => true,
                    'busqueda' => $busqueda,
                    'filtros_personalizados' => $this->input->get('filtros_personalizados'),
                    'numero_documento' => $this->input->get('numero_documento'),
                    'pendientes' => $this->input->get('pendientes'),
                    'mostrar_estado_cuenta' => $this->input->get('mostrar_estado_cuenta'),
                ];

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->clientes_model->obtener("clientes_facturas", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->clientes_model->obtener("clientes_facturas", $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
            break;

            case "pedidos":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda
                ];

                // Filtros personalizados
                if(isset($filtro_numero_documento)) $datos['filtro_numero_documento'] = $filtro_numero_documento;
                if(isset($filtro_id_consecutivo)) $datos['filtro_id_consecutivo'] = $filtro_id_consecutivo;
                if(isset($filtro_nombre_consecutivo)) $datos['filtro_nombre_consecutivo'] = $filtro_nombre_consecutivo;
                if(isset($filtro_fecha_creacion)) $datos['filtro_fecha_creacion'] = $filtro_fecha_creacion;
                if(isset($filtro_numero_pedido)) $datos['filtro_numero_pedido'] = $filtro_numero_pedido;
                if(isset($filtro_nombre)) $datos['filtro_nombre'] = $filtro_nombre;
                if(isset($filtro_estado)) $datos['filtro_estado'] = $filtro_estado;

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->clientes_model->obtener("wms_pedidos", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->clientes_model->obtener("wms_pedidos", $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
            break;

            case "solicitudes_credito":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda
                ];

                // Filtros personalizados
                if(isset($filtro_fecha_creacion)) $datos['filtro_fecha_creacion'] = $filtro_fecha_creacion;
                if(isset($filtro_numero_documento)) $datos['filtro_numero_documento'] = $filtro_numero_documento;
                if(isset($filtro_nombre)) $datos['filtro_nombre'] = $filtro_nombre;
                if(isset($filtro_id)) $datos['filtro_id'] = $filtro_id;
                if(isset($filtro_estado)) $datos['filtro_estado'] = $filtro_estado;
                if(isset($filtro_usuario_asignado)) $datos['filtro_usuario_asignado'] = $filtro_usuario_asignado;
                if(isset($filtro_vendedor)) $datos['filtro_vendedor'] = $filtro_vendedor;
                if(isset($filtro_fecha_cierre)) $datos['filtro_fecha_cierre'] = $filtro_fecha_cierre;
                if(isset($filtro_motivo_rechazo)) $datos['filtro_motivo_rechazo'] = $filtro_motivo_rechazo;
                if(isset($filtro_cupo)) $datos['filtro_cupo'] = $filtro_cupo;
                if(isset($filtro_ultimo_comentario)) $datos['filtro_ultimo_comentario'] = $filtro_ultimo_comentario;
                if(isset($filtro_tipo)) $datos['filtro_tipo'] = $filtro_tipo;

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->clientes_model->obtener("clientes_solicitudes_credito", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->clientes_model->obtener("clientes_solicitudes_credito", $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
            break;

            case "solicitudes_credito_bitacora":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda
                ];

                $datos['solicitud_id'] = $this->input->get("solicitud_id");

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->clientes_model->obtener("clientes_solicitudes_credito_bitacora", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->clientes_model->obtener("clientes_solicitudes_credito_bitacora", $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
            break;
        }
    }

    /**
     * subir_certificado
     * 
     * Función encargada de subir el certificado de retención de un cliente, 
     * construyendo automáticamente el nombre del archivo con el NIT, razón social 
     * y monto del certificado. Crea el directorio si no existe y retorna un JSON 
     * con el resultado del proceso.
     *
     * @return void
     */
    function subir_certificado() {
        $id = $this->uri->segment(3);
        $tipo_retencion = $this->uri->segment(4);
        $exito = false;
        $anio = date('Y') - 1; // Año anterior

        // Se consulta información del certificado para construir el nombre del archivo
        $certificado = $this->clientes_model->obtener('clientes_retenciones_detalle', ['id' => $id]);

        if (!$certificado) {
            print json_encode(['resultado' => false, 'mensaje' => 'Certificado no encontrado']);
            return;
        }

        // Se consulta información del cliente información del cliente
        $cliente = $this->clientes_model->obtener('clientes_retenciones_informe', ['nit' => $certificado->nit]);

        if (!$cliente) {
            print json_encode(['resultado' => false, 'mensaje' => 'Cliente no encontrado']);
            return;
        }

        // Crear directorio si no existe
        $directorio = "{$this->config->item('ruta_archivo_digitalizado')}/15. CERTIFICADOS DE RETENCION/$anio/$tipo_retencion";

        if (!is_dir($directorio)) @mkdir($directorio, 0777, true);

        $archivo = $_FILES['archivo'];
        
        // Obtener extensión del archivo
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        
        // Limpiar razón social SIN quitar espacios
        $razon_social = preg_replace('/[^A-Za-z0-9 ]/', '', $cliente->razon_social);
        $razon_social = trim($razon_social);

        // Formatear monto
        $monto = floatval($certificado->monto);

        if ($monto == intval($monto)) {
            $monto_formateado = intval($monto);
        } else {
            $monto_formateado = str_replace('.', '_', number_format($monto, 2, '.', ''));
        }

        // Construir nombre del archivo
        $nombre_archivo = "{$cliente->nit}_{$razon_social}_{$monto_formateado}.{$extension}";

        // Subir archivo
        if (move_uploaded_file($archivo['tmp_name'], "$directorio/$nombre_archivo")) {
            $exito = true;
            $mensaje = "El archivo <b>{$nombre_archivo}</b> se subió correctamente.";
        } else {
            $mensaje = "Ha ocurrido un error subiendo el archivo.";
        }

        print json_encode(['resultado' => [
            "mensaje" => $mensaje,
            "exito" => $exito
        ]]);
    }

    function subir() {
        $id_solicitud = $this->uri->segment(3);
        $directorio = "./archivos/solicitudes_credito/$id_solicitud/";
        $exito = false;

        // Valida que el directorio exista. Si no existe,lo crea con el id obtenido,
        // asigna los permisos correspondientes
        if( ! is_dir($directorio)) @mkdir($directorio, 0777);

        $archivo = $_FILES;

        foreach($_FILES as $archivo) {
            $nombre_principal = pathinfo($archivo['name'], PATHINFO_FILENAME);
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombre_archivo = $archivo['name'];

            if (file_exists($directorio.$nombre_archivo)) {
                $nombre_archivo = "{$nombre_principal} (".uniqid().").$extension";
            }

            // Si se guarda el archivo
            if(move_uploaded_file($archivo['tmp_name'], $directorio.$nombre_archivo)) {
                $exito = true;
                $mensaje = "El archivo <b>{$nombre_archivo}</b> se subió correctamente.";
            } else {
                $mensaje = "Ha ocurrido un error subiendo el archivo.";
            }
        }

        print json_encode(['resultado' => [
            "mensaje" => $mensaje,
            "exito" => $exito
        ]]);
    }

    function ventas() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');
        
        switch ($this->uri->segment(3)) {
            default:
                $this->data['contenido_principal'] = 'clientes/ventas/gestion/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }
}
/* Fin del archivo Clientes.php */
/* Ubicación: ./application/controllers/Clientes.php */