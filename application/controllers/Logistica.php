<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		30 de septiembre de 2025
 * Programa:  	E-Commerce | Módulo de Logística
 *            	Gestión de información del área logística
 * Email: 		johnarleycano@hotmail.com
 */
class Logistica extends MY_Controller {
    function __construct() {
        parent::__construct();

        $this->load->model(['logistica_model']);
    }

    /**
     * Gestión de envíos
     *
     * @return void
     */
    function envios() {
        switch ($this->uri->segment(3)) {
            case 'cotizacion':
                $this->data['contenido_principal'] = 'logistica/envios/cotizacion/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    /**
     * Gestión de solicitudes de garantía
     *
     * @return void
     */
    function solicitudes_garantia() {
        switch ($this->uri->segment(3)) {
            default:
                $this->data['contenido_principal'] = 'logistica/solicitudes_garantia/detalle';
                $this->load->view('core/body', $this->data);
            break;

            case 'ver':
                if(!$this->session->userdata('usuario_id')) redirect('inicio');

                $id = intval($this->uri->segment(4));

                // Se verifica si es un id válido
                if ($id) {
                    if (gettype($id) === "integer") {
                        $solicitud_garantia = $this->logistica_model->obtener("productos_solicitudes_garantia", ["id" => $id]);
                        
                        if (!empty($solicitud_garantia)) {
                            $this->data['solicitud_garantia'] = $solicitud_garantia;
                            $this->data['tipo'] = $this->uri->segment(5);
                            $this->data['contenido_principal'] = 'logistica/solicitudes_garantia/detalle_general';
                            $this->load->view('core/body', $this->data);
                            return;
                        }

                        redirect('inicio');
                    } else {
                        redirect('inicio');
                    }
                }

                $this->data['contenido_principal'] = 'logistica/solicitudes_garantia/index';
                $this->load->view('core/body', $this->data);
            break;
        }
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

        // Si en la tabla se aplico un orden se obtiene el campo por el que se ordena
        if ($order) {
            $columna = $order[0]["column"];
            $orden = $order[0]["dir"];
            $campo = $columns[$columna]["data"];
            if ($campo) $ordenar = "$campo $orden";
        }

        switch ($tipo) {
            case "solicitudes_garantia":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda,
                    "filtros_personalizados" => $this->input->get('filtros_personalizados'),
                ];

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->logistica_model->obtener("productos_solicitudes_garantia", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->logistica_model->obtener("productos_solicitudes_garantia", $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
            break;

            case "solicitudes_garantia_bitacora":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda
                ];

                $datos['solicitud_id'] = $this->input->get("solicitud_id");

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->logistica_model->obtener("productos_solicitudes_garantia_bitacora", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->logistica_model->obtener("productos_solicitudes_garantia_bitacora", $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
            break;
        }
    }

    function subir() {
        $id_solicitud = $this->uri->segment(3);
        $exito = false;

        // Directorio del año
        $anio = date('Y');
        $directorio_anio = "./archivos/solicitudes_garantia/$anio";

        // Valida que el directorio exista. Si no existe,lo crea con el id obtenido y asigna los permisos correspondientes
        if(!is_dir($directorio_anio)) @mkdir($directorio_anio, 0777);

        // Directorio de la solicitud
        $directorio = "$directorio_anio/$id_solicitud/";

        // Valida que el directorio exista. Si no existe,lo crea con el id obtenido y asigna los permisos correspondientes
        if(!is_dir($directorio)) @mkdir($directorio, 0777);

        $archivo = $_FILES;

        foreach($_FILES as $archivo) {
            $nombre_principal = pathinfo($archivo['name'], PATHINFO_FILENAME);
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombre_archivo = $archivo['name'];

            if (file_exists($directorio . $nombre_archivo)) {
                $nombre_archivo = "{$nombre_principal} (".uniqid().").$extension";
            }

            // Si se guarda el archivo
            if(move_uploaded_file($archivo['tmp_name'], $directorio . $nombre_archivo)) {
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
}