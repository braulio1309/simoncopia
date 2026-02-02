<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

use PhpOffice\PhpSpreadsheet\Shared\Date;

class Proveedores extends MY_Controller {
    function __construct() {
        parent::__construct();

        $this->load->model(['proveedores_model']);
    }

    function certificados($tipo) {
        switch ($tipo) {
            default:
                redirect(site_url());
            break;

            case 'index':
                $this->data['contenido_principal'] = 'proveedores/certificados/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function cotizaciones($tipo, $solicitud_id = null, $nit = null, $token_recibido = null) {
        // if(!$this->session->userdata('usuario_id')) redirect('inicio');

        switch ($tipo) {
            default:
                redirect(site_url());
            break;

            case 'index':
                $this->data['contenido_principal'] = 'proveedores/cotizaciones/index';
                $this->load->view('core/body', $this->data);
            break;

            case 'cotizar':
                // Se construye el token
                $token_valido = substr(md5($solicitud_id.$nit), 0, 10);

                // Si los token no coinciden, no se puede acceder
                if($token_valido !== $token_recibido) redirect('inicio');

                $this->data['cotizacion_id'] = $solicitud_id;
                $this->data['nit'] = $nit;
                $this->data['contenido_principal'] = 'proveedores/cotizaciones/realizar';
                $this->load->view('core/body', $this->data);
            break;

            case 'ver':
                if(!$this->session->userdata('usuario_id')) redirect('inicio');
                $this->data['cotizacion_id'] = $solicitud_id;
                $this->data['contenido_principal'] = 'proveedores/cotizaciones/ver';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function facturas($tipo) {
        switch ($tipo) {
            default:
                redirect(site_url());
            break;

            case 'index':
                $this->data['contenido_principal'] = 'proveedores/facturas/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function maestro($opcion) {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');

        switch ($opcion) {
            case 'ver':
                $this->data['contenido_principal'] = 'proveedores/maestro/index';
                $this->load->view('core/body', $this->data);
            break;

            case 'lista':
                $this->load->view('proveedores/maestro/lista');
            break;

            case 'crear':
                $this->data['contenido_principal'] = 'proveedores/maestro/detalle';
                $this->load->view('core/body', $this->data);
            break;

            case 'editar':
                $this->data['id'] = $this->uri->segment(4);
                $this->data['contenido_principal'] = 'proveedores/maestro/detalle';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function obtener() {
        $datos = json_decode($this->input->post('datos'), true);
        $tipo = $datos['tipo'];
        unset($datos['tipo']);

        switch ($tipo) {
            default:
                $resultado = $this->proveedores_model->obtener($tipo, $datos);
                break;
        }

        print json_encode($resultado);
    }

    function solicitudes($opcion) {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');

        switch ($opcion) {
            case 'ver':
                $this->data['contenido_principal'] = 'proveedores/solicitudes/index';
                $this->load->view('core/body', $this->data);
            break;

            case 'lista':
                $this->load->view('proveedores/maestro/lista');
            break;

            case 'crear':
                $this->data['contenido_principal'] = 'proveedores/solicitudes/detalle';
                $this->load->view('core/body', $this->data);
            break;

            case 'editar':
                $this->data['id'] = $this->uri->segment(4);
                $this->data['contenido_principal'] = 'proveedores/solicitudes/detalle';
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
        $filtos_personalizados = [];
        $filtos_personalizados['fecha_documento'] = $this->input->get("filtro_fecha_documento");

        // Si en la tabla se aplico un orden se obtiene el campo por el que se ordena
        if ($order) {
            $columna = $order[0]["column"];
            $orden = $order[0]["dir"];
            $campo = $columns[$columna]["data"];
            if ($campo) $ordenar = "$campo $orden";
        }

        switch ($tipo) {
            case "api_cuentas_por_pagar":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda,
                    "filtros_personalizados" => $this->input->get('filtros_personalizados'),
                ];

                $datos['nit'] = $this->input->get("numero_documento");

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->proveedores_model->obtener("api_cuentas_por_pagar", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->proveedores_model->obtener("api_cuentas_por_pagar", $datos);
            break;

            case "proveedores_marcas":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda
                ];

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->proveedores_model->obtener("proveedores_marcas", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->proveedores_model->obtener("proveedores_marcas", $datos);
            break;

            case "proveedores_cotizaciones_detalle":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda,
                    "id" => $this->input->get('cotizacion_id'),
                    "filtros_personalizados" => $this->input->get('filtros_personalizados'),
                ];
                
                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->proveedores_model->obtener("proveedores_disponibles_por_cotizacion", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->proveedores_model->obtener("proveedores_disponibles_por_cotizacion", $datos);
            break;

            case "proveedores_cotizaciones_solicitudes":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda
                ];

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->proveedores_model->obtener("proveedores_cotizaciones_solicitudes", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->proveedores_model->obtener("proveedores_cotizaciones_solicitudes", $datos);
            break;
        }

        print json_encode([
            "draw" => $this->input->get("draw"),
            "recordsTotal" => $total_resultados,
            "recordsFiltered" => $total_resultados,
            "data" => $resultados
        ]);
    }

    private function importar_productos_solicitud_cotizacion($archivo) {
        try {
            $excel  = PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);
            $hoja = $excel->getActiveSheet();
            $registros = $hoja->toArray();

            // Se obtienen la fechas de inicio y fin
            $fecha_inicio = $hoja->getCell('B1')->getValue();
            $fecha_fin = $hoja->getCell('B2')->getValue();
            $hora_inicio = $hoja->getCell('D1')->getFormattedValue();
            $hora_fin = $hoja->getCell('D2')->getFormattedValue();

            $fecha_inicio = Date::excelToDateTimeObject("$fecha_inicio")->format('Y-m-d');
            $fecha_fin = Date::excelToDateTimeObject("$fecha_fin")->format('Y-m-d');
            
            // Se elimina la primera y segunda fila del excel
            unset($registros[0], $registros[1]);

            // Se organizan los datos que se almacenaran de la solicitud de cotización
            $datos = [
                "fecha_inicio" => "$fecha_inicio $hora_inicio",
                "fecha_fin" => "$fecha_fin $hora_fin",
                "fecha_creacion" => date('Y-m-d H:i:s'),
                "usuario_id" => $this->session->userdata('usuario_id')
            ];

            // Se crea la cotización
            $cotizacion_id = $this->proveedores_model->crear("proveedores_cotizaciones_solicitudes", $datos);
            $detalle = [];

            foreach ($registros as $registro) {
                // Se definen los productos de la solicitud
                $datos_insertar = [
                    "producto_id" => $registro[0],
                    "cotizacion_id" => $cotizacion_id,
                    "cantidad" => $registro[1],
                ];

                array_push($detalle, $datos_insertar);
            }

            // Se almacenan los productos de la solicitud en el detalle de la misma
            if (!empty($detalle)) $this->proveedores_model->insertar_batch("proveedores_cotizaciones_solicitudes_detalle", $detalle);

            return [
                "exito" => true,
                "mensaje" => "Se subió y se importaron correctamente los productos de la solicitud de cotización"
            ];
        } catch (Exception $e) {
            log_message('error', 'Error al importar los datos de los productos de la solicitud de cotización: ' . $e->getMessage());

            return [
                "exito" => true,
                "mensaje" => "No se subieron y no se importaron correctamente los productos de la solicitud de cotización"
            ];
        }
    }

    function importar_solicitud_cotizacion() {
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
            $resultado = $this->importar_productos_solicitud_cotizacion($directorio.$nombre_archivo);
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
}
/* Fin del archivo Proveedores.php */
/* Ubicación: ./application/controllers/Proveedores.php */