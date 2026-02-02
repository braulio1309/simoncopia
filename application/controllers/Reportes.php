<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		9 de noviemnbre de 2023
 * Programa:  	E-Commerce | Módulo de Reportes
 *            	Generación de reportes en diferentes
 *            	formatos, como PDF, Excel y gráficos
 * Email: 		johnarleycano@hotmail.com
 */
class Reportes extends MY_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();

        $this->load->model(['clientes_model', 'proveedores_model']);
        header('Content-type: application/json');
    }

    function procesar() {
        $ruta_archivo = "archivos/pagos.xlsx";
        $fila_inicial = 3;
        $fecha_creacion = date('Y-m-d H:i:s');
        $valor_total = 0;
        $descuento_total = 0;

        try {
            // Cargar el archivo Excel usando IOFactory
            $archivo = IOFactory::load($ruta_archivo);

            // Lectura de datos de la primera hoja
            $hoja = $archivo->getActiveSheet();

            // Identificación de la cantidad de filas usada
            $ultima_fila = $hoja->getHighestRow();
        
            // Arreglo con las claves que representan a cada columna
            $columnas = [
                [ 'nombre' => 'A', 'valor' => 'documento_numero' ],
                [ 'nombre' => 'B', 'valor' => 'cuota' ],
                [ 'nombre' => 'C', 'valor' => 'tercero_nombre' ],
                [ 'nombre' => 'D', 'valor' => 'tercero_nit' ],
                [ 'nombre' => 'E', 'valor' => 'mora_descuento' ],
                [ 'nombre' => 'F', 'valor' => 'saldo_valor' ],
                [ 'nombre' => 'G', 'valor' => 'pago_valor' ],
                [ 'nombre' => 'H', 'valor' => 'diferencia_subtotal' ],
                [ 'nombre' => 'I', 'valor' => 'diferencia_porcentaje' ],
                [ 'nombre' => 'J', 'valor' => 'pago_numero' ],
                [ 'nombre' => 'K', 'valor' => 'pago_fecha' ],
                [ 'nombre' => 'L', 'valor' => 'pago_cuenta_bancaria' ],
                [ 'nombre' => 'M', 'valor' => 'impuesto_2_85_valor' ],  // 2.85%
                [ 'nombre' => 'N', 'valor' => 'impuesto_2_50_valor' ],  // 2.5%
                [ 'nombre' => 'O', 'valor' => 'impuesto_4_00_valor' ],  // 4%
                [ 'nombre' => 'P', 'valor' => 'impuesto_0_70_valor' ],  // 0.7%
                [ 'nombre' => 'Q', 'valor' => 'descuento_valor' ],
                [ 'nombre' => 'R', 'valor' => 'ajuste_retenciones_subtotal' ],
                [ 'nombre' => 'S', 'valor' => 'ajuste_diferencia' ],
                [ 'nombre' => 'T', 'valor' => 'descuento_porcentaje' ],
                [ 'nombre' => 'U', 'valor' => 'descuento_subtotal' ],
                [ 'nombre' => 'V', 'valor' => 'pago_con_descuento_subtotal' ],
                [ 'nombre' => 'W', 'valor' => 'factura_fecha' ],
                [ 'nombre' => 'X', 'valor' => 'valor_bruto_subtotal' ],
                [ 'nombre' => 'Y', 'valor' => 'iva_subtotal' ],
                [ 'nombre' => 'Z', 'valor' => 'retencion_iva' ],
                [ 'nombre' => 'AA', 'valor' => 'retencion_fuente_2_50_valor' ],
                [ 'nombre' => 'AB', 'valor' => 'retencion_fuente_4_00_valor' ],
                [ 'nombre' => 'AC', 'valor' => 'factura_subtotal' ],
                [ 'nombre' => 'AD', 'valor' => 'abonos_subtotal' ],
                [ 'nombre' => 'AE', 'valor' => 'centro_operativo' ],
                [ 'nombre' => 'AF', 'valor' => 'documento_cruce_tipo' ],
                [ 'nombre' => 'AG', 'valor' => 'documento_cruce_numero' ],
                [ 'nombre' => 'AH', 'valor' => 'tercero_sucursal_nombre' ],
                [ 'nombre' => 'AI', 'valor' => 'tercero_sucursal_codigo' ],
                [ 'nombre' => 'AJ', 'valor' => 'c_x_c_subtotal' ],
                [ 'nombre' => 'AK', 'valor' => 'cuenta_numero' ],
                [ 'nombre' => 'AL', 'valor' => 'tercero_fc' ],
                [ 'nombre' => 'AM', 'valor' => 'dias_facturados' ],
                [ 'nombre' => 'AN', 'valor' => 'retencion_fuente_6_00' ],
                [ 'nombre' => 'AO', 'valor' => 'cuenta_nombre' ],
                [ 'nombre' => 'AP', 'valor' => 'fecha_vencimiento' ],
                [ 'nombre' => 'AQ', 'valor' => 'retencion_fuente_1_00' ],
                [ 'nombre' => 'AR', 'valor' => 'retencion_fuente_3_50' ],
            ];

            $ultimo_tercero = '';   // Memoria del tercero
            $recibos = [];  // Arreglo que almacenará los recibos a crear
            $recibos_detalle = [];  // Arreglo que almacenará las facturas del recibo a crear
            
            // Recorrido de as filas
            for ($fila = $fila_inicial; $fila <= $ultima_fila; $fila++) {
                // Recorrido de los datos de cada ítem (columnas)
                foreach ($columnas as $columna) {
                    // Formato de campos
                    $hoja->getStyle("G{$fila}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                    $hoja->getStyle("R{$fila}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                    $hoja->getStyle("Q{$fila}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

                    // Captura de los datos de las celdas
                    $tercero_nit = $hoja->getCell("D{$fila}")->getValue();
                    $tercero_nombre = $hoja->getCell("C{$fila}")->getValue();
                    $pago_valor = (int)$hoja->getCell("G{$fila}")->getValue();
                    $ajuste_retenciones_subtotal = (int)$hoja->getCell("R{$fila}")->getValue();
                    $cuota = $hoja->getCell("B{$fila}")->getValue();
                    $celda_fecha_pago = $hoja->getCell("K{$fila}")->getValue();
                    $fecha_pago = ($celda_fecha_pago != '') ? Date::excelToDateTimeObject($celda_fecha_pago)->format('Y-m-d') : null ;
                    $documento_cruce_tipo = $hoja->getCell("AF{$fila}")->getValue();
                    $documento_cruce_numero = $hoja->getCell("AG{$fila}")->getValue();
                    $centro_operativo = $hoja->getCell("AE{$fila}")->getValue();
                    $descuento_valor = $hoja->getCell("Q{$fila}")->getValue();
                    
                    // Cuando el tercero es uno diferente
                    if($tercero_nit != $ultimo_tercero) {
                        // Se actualiza el último tercero
                        $ultimo_tercero = $tercero_nit;

                        $recibo = [
                            'documento_numero' => $tercero_nit,
                            'razon_social' => $tercero_nombre,
                            'fecha_creacion' => $fecha_creacion,
                            'observaciones' => "CONSIGNACIÓN DEL DIA $fecha_pago",
                            'recibo_tipo_id' => 5, // Importación desde archivo plano
                            'token' => 'ec-'.generar_token($fecha_creacion),
                        ];

                        // Se agrega la fecha de consignación (si la tiene)
                        if($fecha_pago) $recibo['fecha_consignacion'] = $fecha_pago;
                        
                        // Almacenamiento del recibo en el arreglo
                        $recibos[] = $recibo;
                    }
                }

                // Detalle del recibo
                $recibo_detalle = [
                    'precio' => $pago_valor,
                    'subtotal' => $pago_valor + $ajuste_retenciones_subtotal,
                    'cuota_numero' => $cuota,
                    'documento_cruce_numero' => $documento_cruce_numero,
                    'documento_cruce_tipo' =>  $documento_cruce_tipo,
                    'centro_operativo' => $centro_operativo,
                    // valor_saldo_inicial => ,
                    // 'valor_abonos' => ,
                    // 'valor_factura' => ,
                ];
                
                // Se agrega descuento (si la tiene)
                $recibo_detalle['descuento'] = $descuento_valor;

                // Se agrega la fecha de consignación (si la tiene)
                if($fecha_pago) $recibo_detalle['documento_cruce_fecha'] = $fecha_pago;

                // Sumatoria de totales
                $descuento_total += $descuento_valor;
                $valor_total += $pago_valor + $ajuste_retenciones_subtotal;

                // Almacenamiento del detalle del recibo en el arreglo
                $recibos_detalle[] = $recibo_detalle;
            }

            // Se agrega el total de los pagos al arreglo del recibo
            $recibo['valor'] = $valor_total - $descuento_total;

            // Se crea el recibo en la base de datos
            $id_recibo = $this->configuracion_model->crear('recibos', $recibo);

            // A las facturas del recibo se le agrega el id del recibo creado para relacionarlos
            foreach ($recibos_detalle as &$registro) $registro['recibo_id'] = $id_recibo;

            // Se agregan las facturas del recibo
            $this->configuracion_model->crear('recibos_detalle_batch', $recibos_detalle);

            // Se crea el documento contable
            $resultado = crear_documento_contable($id_recibo);

            print_r($resultado);
        } catch (\Throwable $error) {
            log_message('error', 'Error al cargar el archivo Excel: ' . $error->getMessage());
            return false;
        }
    }

    function excel() {
        switch ($this->uri->segment(3)) {
            case 'facturas':
                $this->data["numero_documento"] = $this->uri->segment(4);
                $this->load->view('reportes/excel/facturas', $this->data);
            break;

            case 'proveedores_cotizaciones_matriz':
                $this->data["id"] = $this->uri->segment(4);
                $this->load->view('reportes/excel/proveedores_cotizaciones_matriz', $this->data);
            break;

            case 'proveedores_orden_compra':
                $this->data["id"] = $this->uri->segment(4);
                $this->load->view('reportes/excel/proveedores_orden_compra', $this->data);
            break;

            case 'proveedores_maestro_marcas':
                $this->load->view('reportes/excel/proveedores_maestro_marcas');
            break;
        }
    }

    function pdf() {
        switch ($this->uri->segment(3)) {
            case "proveedores_certificado_retenciones":
                $this->data['numero_documento'] = $this->uri->segment(4);
                $this->data['anio'] = $this->uri->segment(5);
                if ($this->load->view('reportes/pdf/proveedores_certificado_retenciones', $this->data)) print json_encode(true);
            break;

            case "proveedores_comprobante_egreso":
                $this->data['id'] = $this->uri->segment(4);
                if ($this->load->view('reportes/pdf/comprobante_egreso', $this->data)) {
                    print json_encode(true);
                }
            break;

            case "recibo":
                $this->data['token'] = $this->uri->segment(4);
                $this->load->view('reportes/pdf/recibo', $this->data);
            break;

            case "solicitud_credito":
                $this->data['solicitud_id'] = $this->uri->segment(4);
                if ($this->load->view('reportes/pdf/solicitud_credito', $this->data)) {
                    print json_encode(true);
                }
            break;
        }
    }
}
/* Fin del archivo Productos.php */
/* Ubicación: ./application/controllers/Productos.php */