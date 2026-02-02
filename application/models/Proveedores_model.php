<?php 
Class Proveedores_model extends CI_Model{
    function actualizar($tabla, $condiciones, $datos){
        return $this->db->where($condiciones)->update($tabla, $datos);
        $this->db->close;
    }

    function actualizar_batch($tabla, $datos, $campo) {
        return $this->db->update_batch($tabla, $datos, $campo);
    }

    function insertar_batch($tabla, $datos) {
        return $this->db->insert_batch($tabla, $datos);
    }

    function crear($tipo, $datos) {
        switch ($tipo) {
            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;
        }

        $this->db->close;
    }

    function eliminar($tipo, $datos = []) {
        switch ($tipo) {
            case 'api_cuentas_por_pagar':
                return $this->db->delete('api_cuentas_por_pagar', $datos);
            break;

            case 'proveedores_marcas':
                return $this->db->delete('proveedores_marcas', $datos);
            break;

            case 'proveedores_cotizaciones_solicitudes':
                return $this->db->delete('proveedores_cotizaciones_solicitudes', $datos);
            break;

            case 'proveedores_cotizaciones_solicitudes_detalle':
                return $this->db->delete('proveedores_cotizaciones_solicitudes_detalle', $datos);
            break;

            case 'proveedores_cotizaciones_detalle':
                return $this->db->delete('proveedores_cotizaciones_detalle', $datos);
            break;
        }

        $this->db->close;
    }

    public function obtener($tipo, $datos = null) {
        switch ($tipo) {
            case 'api_cuentas_por_pagar':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsqueda
                $busquedas = (isset($datos['busqueda'])) ? $datos['busqueda'] : null ;
                $filtros_having = "HAVING id";
                $filtros_where = "";

                // Si se realiza una búsqueda
                if($busquedas && $busquedas != ""){
                    // Se divide por palabras
                    $palabras = explode(" ", trim($busquedas));

                    // Se recorren las palabras
                    for ($i=0; $i < count($palabras); $i++) { 
                        $filtros_having .= " AND (";
                        $filtros_having .= " id LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR f353_rowid LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR f353_consec_docto_cruce LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= ") ";
                        
                        if(($i + 1) < count($palabras)) $filtros_having .= " AND ";
                    }
                }

                // Se aplican los filtros
                if(isset($datos['nit'])) $filtros_where .= " AND acpp.f200_id = '{$datos['nit']}' ";
                if(isset($datos['id'])) $filtros_where .= " AND acpp.id = '{$datos['id']}' ";

                // Filtros personalizados
                $filtros_personalizados = isset($datos['filtros_personalizados']) ? $datos['filtros_personalizados'] : [];
                if (isset($filtros_personalizados['fecha_documento']) && $filtros_personalizados['fecha_documento'] != '') $filtros_having .= " AND fecha = '{$filtros_personalizados['fecha_documento']}' ";
                if (isset($filtros_personalizados['id']) && $filtros_personalizados['id'] != '') $filtros_having .= " AND row_id LIKE '%{$filtros_personalizados['id']}%' ";
                if (isset($filtros_personalizados['sede']) && $filtros_personalizados['sede'] != '') $filtros_having .= " AND sede LIKE '%{$filtros_personalizados['sede']}%' ";
                if (isset($filtros_personalizados['numero_documento_cruce']) && $filtros_personalizados['numero_documento_cruce'] != '') $filtros_having .= " AND documento_cruce LIKE '%{$filtros_personalizados['numero_documento_cruce']}%' ";
                if (isset($filtros_personalizados['valor_documento']) && $filtros_personalizados['valor_documento'] != '') $filtros_having .= " AND valor_documento LIKE '%{$filtros_personalizados['valor_documento']}%' ";
                if (isset($filtros_personalizados['valor_abonos']) && $filtros_personalizados['valor_abonos'] != '') $filtros_having .= " AND valor_abonos LIKE '%{$filtros_personalizados['valor_abonos']}%' ";
                if (isset($filtros_personalizados['valor_saldo']) && $filtros_personalizados['valor_saldo'] != '') $filtros_having .= " AND valor_saldo LIKE '%{$filtros_personalizados['valor_saldo']}%' ";
                if (isset($filtros_personalizados['notas']) && $filtros_personalizados['notas'] != '') $filtros_having .= " AND notas LIKE '%{$filtros_personalizados['notas']}%' ";

                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}": "ORDER BY valor_saldo, fecha DESC";

                $sql =
                "SELECT
                    acpp.id,
                    acpp.f353_id_co_cruce sede_codigo,
                    acpp.f353_id_un_cruce unidad_cruce,
                    acpp.f353_rowid row_id,
                    acpp.f353_consec_docto_cruce documento_cruce,
                    acpp.f353_fecha fecha,
                    acpp.f353_total_cr valor_documento,
                    acpp.f353_total_db valor_abonos,
                    (acpp.f353_total_cr - acpp.f353_total_db) valor_saldo,
                    acpp.f353_notas notas,
                    s.nombre AS sede,
                    t.f200_razon_social provedor_nombre,
                    t.f200_nit provedor_nit,
                    CONCAT_WS('-',acpp.f353_id_co_cruce,'FCE',LPAD(acpp.f353_consec_docto_cruce,8,0)) numero_siesa
                FROM api_cuentas_por_pagar acpp
                LEFT JOIN terceros t ON t.f200_id = acpp.f200_id
                LEFT JOIN centros_operacion AS s ON acpp.f353_id_co_cruce = s.codigo
                WHERE acpp.id is NOT NULL
                $filtros_where
                $filtros_having
                $order_by
                $limite
                ";

                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;

            case 'cotizaciones_mejores_precios':
                $sql = 
                "WITH resultado AS (
                    SELECT
                        producto_id,
                        proveedor_nit,
                        precio_final,
                        observacion,
                        p.referencia,
                        t.f200_razon_social,
                        ROW_NUMBER() OVER ( PARTITION BY producto_id ORDER BY precio_final ASC, proveedor_nit ASC ) AS cantidad_registros 
                    FROM
                        proveedores_cotizaciones_detalle 
                        INNER JOIN productos AS p ON proveedores_cotizaciones_detalle.producto_id = p.id
                        INNER JOIN terceros AS t ON proveedores_cotizaciones_detalle.proveedor_nit = t.f200_nit 
                    WHERE
                        precio_final > 0 
                        AND cotizacion_id = {$datos['id']} 
                    ) SELECT
                    producto_id,
                    proveedor_nit,
                    precio_final,
                    observacion,
                    referencia,
                    f200_razon_social proveedor_nombre
                FROM
                    resultado 
                WHERE
                    cantidad_registros = 1 
                ORDER BY
                    proveedor_nit ASC;";
                
                return $this->db->query($sql)->result();
                break;

            case 'productos':
                unset($datos['tipo']);

                return $this->db
                    ->select([
                        'p.id',
                        'CONCAT_WS(" - ", p.id, p.referencia, p.notas) valor'
                    ])
                    ->from('productos p')
                    ->get()
                    ->result()
                ;
            break;

            case 'proveedores_cotizaciones_detalle':
                $this->db
                    ->select([
                        'pcd.id',
                        'pcd.cotizacion_id',
                        'pcd.producto_id',
                        'IF(pcd.precio > 0, pcd.precio, "") precio',
                        'pcd.descuento_porcentaje',
                        'pcd.descuento_valor',
                        'pcd.precio_final',
                        'pcd.observacion',
                        'pcd.proveedor_nit',
                        'CONCAT_WS(" - ", p.id, p.referencia, p.notas) producto',
                        't.f200_razon_social'
                    ])
                    ->from('proveedores_cotizaciones_detalle pcd')
                    ->join('productos p', 'pcd.producto_id = p.id', 'left')
                    ->join('terceros t', 'pcd.proveedor_nit = t.f200_nit', 'left')
                ;

                if (isset($datos['cotizacion_id'])) $this->db->where('pcd.cotizacion_id', $datos['cotizacion_id']); 
                if (isset($datos['nit'])) $this->db->where('pcd.proveedor_nit', $datos['nit']); 

                return $this->db->get()->result();
            break;

            case 'proveedores_cotizaciones_solicitudes_detalle':
                $this->db
                    ->select([
                        'pcsd.id',
                        'pcsd.cotizacion_id',
                        'pcsd.producto_id',
                        'pcsd.cantidad',
                        'CONCAT_WS(" - ", p.id, p.referencia, p.notas) producto',
                    ])
                    ->from('proveedores_cotizaciones_solicitudes_detalle pcsd')
                    ->join('productos p', 'pcsd.producto_id = p.id', 'left')
                ;

                if (isset($datos['cotizacion_id'])) $this->db->where('pcsd.cotizacion_id', $datos['cotizacion_id']); 
                if (isset($datos['producto_id'])) $this->db->where('pcsd.producto_id', $datos['producto_id']); 
                
                // Si viene el id del producto y el id de la cotización, retornará solamente un registro
                if(isset($datos['cotizacion_id']) && isset($datos['producto_id'])) return $this->db->get()->row();

                return $this->db->get()->result();
            break;

            case 'proveedores_disponibles_por_cotizacion':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsqueda
                $busquedas = isset($datos['busqueda']) ? trim($datos['busqueda']) : null;

                $filtros_where  = "WHERE pcsd.cotizacion_id = {$datos['id']}";
                $filtros_having = "HAVING proveedor IS NOT NULL";

                // Búsqueda general
                if ($busquedas && $busquedas != "") {
                    $palabras = explode(" ", $busquedas);

                    for ($i = 0; $i < count($palabras); $i++) {
                        $filtros_having .= " AND (";
                        $filtros_having .= " proveedor LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR cantidad_productos LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR cantidad_productos_cotizados LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= ")";
                    }
                }

                // Filtros personalizados
                $filtros_personalizados = isset($datos['filtros_personalizados']) ? $datos['filtros_personalizados'] : [];

                if (isset($filtros_personalizados['proveedor']) && $filtros_personalizados['proveedor'] != '') {
                    $proveedor = $this->db->escape_like_str($filtros_personalizados['proveedor']);
                    $filtros_having .= " AND proveedor LIKE '%{$proveedor}%'";
                }

                if (isset($filtros_personalizados['productos_disponibles']) && $filtros_personalizados['productos_disponibles'] != '') $filtros_having .= " AND cantidad_productos = {$filtros_personalizados['productos_disponibles']}";

                if (isset($filtros_personalizados['productos_cotizados']) && $filtros_personalizados['productos_cotizados'] != '') $filtros_having .= " AND cantidad_productos_cotizados = {$filtros_personalizados['productos_cotizados']}";

                // Filtros WHERE
                if (isset($datos['id'])) $filtros_where .= " AND pcsd.cotizacion_id = {$datos['id']} ";

                $order_by = isset($datos['ordenar']) ? "ORDER BY {$datos['ordenar']}" : "ORDER BY proveedor ASC";

                $sql = 
                    "SELECT
                        t.f200_razon_social AS proveedor,
                        COUNT(DISTINCT p.id) AS cantidad_productos,
                        (
                            SELECT COUNT(DISTINCT pcd.producto_id)
                            FROM proveedores_cotizaciones_detalle pcd
                            WHERE pcd.cotizacion_id = {$datos['id']}
                            AND pcd.proveedor_nit = t.f200_nit
                            AND pcd.precio_final IS NOT NULL
                            AND pcd.precio_final > 0
                        ) AS cantidad_productos_cotizados
                    FROM proveedores_cotizaciones_solicitudes_detalle pcsd
                        LEFT JOIN productos AS p ON pcsd.producto_id = p.id
                        LEFT JOIN marcas AS m ON p.marca = m.nombre
                        LEFT JOIN proveedores_marcas AS pm ON m.codigo = pm.marca_codigo
                        INNER JOIN terceros AS t ON pm.proveedor_nit = t.f200_nit
                    $filtros_where
                    GROUP BY proveedor
                    $filtros_having
                    $order_by
                    $limite
                ";

                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                return $this->db->query($sql)->result();
            break;

            case 'proveedores_maestro_solicitudes_detalle':
                return $this->db
                    ->select([
                        'pcsd.id',
                        'pcsd.cotizacion_id',
                        'pcsd.producto_id',
                        'p.referencia producto_referencia',
                        'p.notas producto_notas',
                        'p.marca producto_marca',
                        'pcsd.cantidad',
                        'CONCAT_WS(" - ", p.id, p.referencia, p.notas) producto',
                    ])
                    ->from('proveedores_marcas pm')
                    ->join('marcas m', 'pm.marca_codigo = m.codigo', 'left')
                    ->join('productos p', 'm.nombre = p.marca', 'left')
                    ->join('proveedores_cotizaciones_solicitudes_detalle pcsd', 'pcsd.producto_id = p.id', 'left')
                    ->where([
                        'pcsd.cotizacion_id' => $datos['cotizacion_id'],
                        'pm.proveedor_nit' => $datos['nit']
                    ])
                    ->order_by('producto_notas, producto_referencia')
                    ->get()
                    ->result()
                ;
            break;

            case 'proveedores_marcas':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsqueda
                $busquedas = (isset($datos['busqueda'])) ? $datos['busqueda'] : null ;
                $filtros_having = "";
                $filtros_where = "";

                // Si se realiza una búsqueda
                if($busquedas && $busquedas != ""){
                    // Se divide por palabras
                    $palabras = explode(" ", trim($busquedas));

                    $filtros_having = "HAVING";

                    // Se recorren las palabras
                    for ($i=0; $i < count($palabras); $i++) { 
                        $filtros_having .= " (";
                        $filtros_having .= " proveedor_nombre LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR proveedor_nit LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR marca_nombre LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR marca_codigo LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= ") ";
                        
                        if(($i + 1) < count($palabras)) $filtros_having .= " AND ";
                    }
                }

                // Se aplican los filtros
                if (isset($datos['id']) && $datos['id']) $filtros_where .= " AND pm.id = {$datos['id']} ";

                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}": "ORDER BY pm.fecha_creacion DESC";

                $sql =
                "SELECT
                    pm.id,
                    pm.fecha_creacion,
                    p.f200_nit AS proveedor_nit,
                    p.f200_razon_social AS proveedor_nombre,
                    m.codigo AS marca_codigo,
                    m.nombre AS marca_nombre,
                    pm.usuario_id 
                FROM
                    proveedores_marcas AS pm
                    LEFT JOIN terceros AS p ON pm.proveedor_nit = p.f200_nit
                    LEFT JOIN marcas AS m ON pm.marca_codigo = m.codigo 
                WHERE
                    pm.id IS NOT NULL
                $filtros_where
                $filtros_having
                $order_by
                $limite
                ";

                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;

            case 'provedores_movimientos_contables':
                $filtros = '';
                if (isset($datos['numero_documento'])) $filtros .= "AND tm.f200_nit = '{$datos['numero_documento']}'";
                if (isset($datos['filtro_retenciones'])) $filtros .= "AND tm.f253_id LIKE '%2365%'";

                $sql =
                "SELECT
                    tm.f253_descripcion descripcion,
                    SUM(tm.f351_base_gravable) valor_base,
                    SUM(tm.f351_valor_cr) valor_retenido 
                FROM
                    clientes_facturas_movimientos AS tm
                WHERE
                    1 
                    $filtros
                GROUP BY
                    tm.f253_descripcion";

                return $this->db->query($sql)->result();
            break;

            case 'proveedores_cotizaciones_solicitudes':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsqueda
                $busquedas = (isset($datos['busqueda'])) ? $datos['busqueda'] : null ;
                $filtros_having = "";
                $filtros_where = "";

                // Si se realiza una búsqueda
                if($busquedas && $busquedas != ""){
                    // Se divide por palabras
                    $palabras = explode(" ", trim($busquedas));

                    $filtros_having = "HAVING";

                    // Se recorren las palabras
                    for ($i=0; $i < count($palabras); $i++) { 
                        $filtros_having .= " (";
                        $filtros_having .= " fecha_inicio LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR fecha_fin LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR id LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= ") ";
                        
                        if(($i + 1) < count($palabras)) $filtros_having .= " AND ";
                    }
                }

                // Se aplican los filtros
                if (isset($datos['id']) && $datos['id']) $filtros_where .= " AND pcs.id = {$datos['id']} ";

                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}": "ORDER BY pcs.fecha_inicio DESC";

                $sql =
                "SELECT
                    pcs.id,
                    pcs.fecha_inicio,
                    pcs.fecha_fin,
                    pcs.fecha_creacion,
                    pcs.usuario_id,
                    IF(TIMESTAMPDIFF(SECOND, NOW(), pcs.fecha_fin) > 0, 1, 0) activa,
                    (SELECT
                        COUNT( DISTINCT pcd.proveedor_nit ) 
                    FROM
                        proveedores_cotizaciones_detalle AS pcd 
                    WHERE
                        pcd.cotizacion_id = pcs.id) cantidad_cotizaciones
                FROM
                    proveedores_cotizaciones_solicitudes AS pcs
                WHERE
                    pcs.id IS NOT NULL
                $filtros_where
                $filtros_having
                $order_by
                $limite
                ";
        
                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;

            case 'solicitudes_disponibles':
                return $this->db
                    ->select([
                        'pcs.*',
                        'ROUND(TIMESTAMPDIFF(SECOND, NOW(), pcs.fecha_fin) / 3600) AS horas_restantes',
                    ])
                    ->order_by('pcs.fecha_fin')
                    ->where('pcs.fecha_inicio <=', date('Y-m-d H:i:s'))
                    ->where('pcs.fecha_fin >=', date('Y-m-d H:i:s'))
                    ->get('proveedores_cotizaciones_solicitudes pcs')
                    ->result()
                ;
            break;
        }
    }
}
/* Fin del archivo Proveedores_model.php */
/* Ubicación: ./application/models/Proveedores_model.php */