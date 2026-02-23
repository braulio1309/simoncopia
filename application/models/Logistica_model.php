<?php 
Class Logistica_model extends CI_Model {
    function crear($tipo, $datos){
        switch ($tipo) {
            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;
        }
    }

    /**
	 * Permite obtener registros de la base de datos
	 * los cuales se retornar a las vistas
	 * 
	 * @param  [string] $tabla Tabla a la que se realizara la consulta
	 * @return [array]  Arreglo de datos con el resultado de la consulta
	 */
	function obtener($tabla, $datos = null) {
		switch ($tabla) {
            case 'pedidos':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsqueda
                $busquedas = (isset($datos['busqueda'])) ? $datos['busqueda'] : null ;
                $filtros_having = "HAVING id";
                $filtros_where = "WHERE p.id";

                // Si se realiza una búsqueda
                if($busquedas && $busquedas != ""){
                    // Se divide por palabras
                    $palabras = explode(" ", trim($busquedas));

                    // Se recorren las palabras
                    for ($i=0; $i < count($palabras); $i++) { 
                        $filtros_having .= " AND (";
                        $filtros_having .= " OR p.f430_id_fecha LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR p.f200_nit_pedido_rem LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR p.f200_razon_social_pedido_rem LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR p.f430_num_docto_referencia LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR p.f430_usuario_creacion LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR p.f431_vlr_neto LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR numero LIKE '%{$palabras[$i]}%'";

                        $filtros_having .= ") ";
                        
                        if(($i + 1) < count($palabras)) $filtros_having .= " AND ";
                    }
                }

                // Filtros personalizados
                $filtros_personalizados = isset($datos['filtros_personalizados']) ? $datos['filtros_personalizados'] : [];
                if (isset($filtros_personalizados['id']) && $filtros_personalizados['id'] != '') $filtros_where .= " AND p.id LIKE '%{$filtros_personalizados['id']}%' ";
                if (isset($filtros_personalizados['fecha']) && $filtros_personalizados['fecha'] != '') $filtros_where .= " AND DATE(p.f430_id_fecha) = '{$filtros_personalizados['fecha']}' ";
                if (isset($filtros_personalizados['nit']) && $filtros_personalizados['nit'] != '') $filtros_where .= " AND p.f200_nit_pedido_rem LIKE '%{$filtros_personalizados['nit']}%' ";
                if (isset($filtros_personalizados['razon_social']) && $filtros_personalizados['razon_social'] != '') $filtros_where .= " AND p.f200_razon_social_pedido_rem LIKE '%{$filtros_personalizados['razon_social']}%' ";
                if (isset($filtros_personalizados['orden_compra']) && $filtros_personalizados['orden_compra'] != '') $filtros_where .= " AND p.f430_num_docto_referencia LIKE '%{$filtros_personalizados['orden_compra']}%' ";
                if (isset($filtros_personalizados['creador']) && $filtros_personalizados['creador'] != '') $filtros_where .= " AND p.f430_usuario_creacion LIKE '%{$filtros_personalizados['creador']}%' ";
                if (isset($filtros_personalizados['valor']) && $filtros_personalizados['valor'] != '') $filtros_where .= " AND p.f431_vlr_neto LIKE '%{$filtros_personalizados['valor']}%' ";


                // Filtros having
                if (isset($filtros_personalizados['numero']) && $filtros_personalizados['numero'] != '')$filtros_having .= " AND numero LIKE '%{$filtros_personalizados['numero']}%' ";

                if (isset($filtros_personalizados['items']) && $filtros_personalizados['items'] != '')$filtros_having .= " AND items = {$filtros_personalizados['items']} ";

                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}": "ORDER BY p.f430_id_fecha DESC";
                
                $sql = 
                "SELECT
                    p.id,
                    p.f430_id_fecha fecha,
                    CONCAT_WS('-', p.f430_id_co_fact, p.f430_id_tipo_docto, p.f430_consec_docto) AS numero,
                    p.f200_nit_pedido_rem nit,
                    p.f200_razon_social_pedido_rem razon_social,
                    p.f430_num_docto_referencia orden_compra,
                    p.f430_usuario_creacion creador,
                    COUNT(*) AS items,
                    SUM(p.f431_vlr_neto) AS valor
                FROM erp_ventas_pedidos p
                $filtros_where
                GROUP BY p.f430_rowid
                $filtros_having
                $order_by
                $limite";

                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;

            case 'productos_solicitudes_garantia':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsqueda
                $busquedas = (isset($datos['busqueda'])) ? $datos['busqueda'] : null ;
                $filtros_having = "HAVING id";
                $filtros_where = "WHERE psg.id";

                // Si se realiza una búsqueda
                if($busquedas && $busquedas != ""){
                    // Se divide por palabras
                    $palabras = explode(" ", trim($busquedas));

                    // Se recorren las palabras
                    for ($i=0; $i < count($palabras); $i++) { 
                        $filtros_having .= " AND (";
                        $filtros_having .= " psg.id LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR psg.id LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= ") ";
                        
                        if(($i + 1) < count($palabras)) $filtros_having .= " AND ";
                    }
                }

                // Se aplican los filtros
                if(isset($datos['id'])) $filtros_where .= " AND psg.id = '{$datos['id']}' ";

                // Filtros personalizados
                $filtros_personalizados = isset($datos['filtros_personalizados']) ? $datos['filtros_personalizados'] : [];
                if (isset($filtros_personalizados['id']) && $filtros_personalizados['id'] != '') $filtros_where .= " AND psg.id LIKE '%{$filtros_personalizados['id']}%' ";
                if (isset($filtros_personalizados['fecha_creacion']) && $filtros_personalizados['fecha_creacion'] != '') $filtros_where .= " AND DATE(psg.fecha_creacion) = '{$filtros_personalizados['fecha_creacion']}' ";
                if (isset($filtros_personalizados['numero_documento']) && $filtros_personalizados['numero_documento'] != '') $filtros_where .= " AND psg.documento_numero LIKE '%{$filtros_personalizados['numero_documento']}%' ";
                if (isset($filtros_personalizados['nombre']) && $filtros_personalizados['nombre'] != '') $filtros_where .= " AND psg.solicitante_nombres LIKE '%{$filtros_personalizados['nombre']}%' ";
                if (isset($filtros_personalizados['estado']) && $filtros_personalizados['estado'] != '') $filtros_having .= " AND estado LIKE '%{$filtros_personalizados['estado']}%' ";
                if (isset($filtros_personalizados['vendedor']) && $filtros_personalizados['vendedor'] != '') $filtros_having .= " AND vendedor_nombre LIKE '%{$filtros_personalizados['vendedor']}%' ";
                if (isset($filtros_personalizados['producto']) && $filtros_personalizados['producto'] != '') $filtros_having .= " AND producto LIKE '%{$filtros_personalizados['producto']}%' ";
                if (isset($filtros_personalizados['usuario_asignado']) && $filtros_personalizados['usuario_asignado'] != '') $filtros_having .= " AND nombre_usuario_asignado LIKE '%{$filtros_personalizados['usuario_asignado']}%' ";
                if (isset($filtros_personalizados['radicado']) && $filtros_personalizados['radicado'] != '') $filtros_where .= " AND psg.radicado LIKE '%{$filtros_personalizados['radicado']}%' ";

                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}": "ORDER BY psg.fecha_creacion DESC";
                
                $sql = 
                "SELECT
                    psg.*,
                    YEAR(psg.fecha_creacion) anio_creacion,
                    DATE(psg.fecha_creacion) fecha_creacion,
                    TIME(psg.fecha_creacion) hora_creacion,
                    DATE(psg.fecha_cierre) fecha_cierre,
                    TIME(psg.fecha_cierre) hora_cierre,
                    psge.nombre estado, 
	                psge.clase estado_clase,
                    tv.nombre vendedor_nombre,
                    p.notas AS producto,
                    IF(ua.razon_social is not null, ua.razon_social, '-') nombre_usuario_asignado,
                    (
                        SELECT b.observaciones 
                        FROM productos_solicitudes_garantia_bitacora AS b 
                        WHERE b.solicitud_id = psg.id 
                        ORDER BY b.fecha_creacion DESC LIMIT 1 
                    ) ultimo_comentario,
                    mr.nombre AS motivo_rechazo
                FROM productos_solicitudes_garantia psg
                LEFT JOIN productos_solicitudes_garantia_estados AS psge ON psg.estado_id = psge.id
                LEFT JOIN terceros_vendedores AS tv ON psg.vendedor_nit = tv.nit
                LEFT JOIN productos AS p ON psg.producto_id = p.id
                LEFT JOIN usuarios AS ua ON psg.usuario_asignado_id = ua.id
                LEFT JOIN motivos_rechazo AS mr ON psg.motivo_rechazo_id = mr.id
                $filtros_where
                $filtros_having
                $order_by
                $limite";

                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;

            case 'productos_solicitudes_garantia_bitacora':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsqueda
                $busquedas = (isset($datos['busqueda'])) ? $datos['busqueda'] : null ;
                $filtros_having = "HAVING psgb.id";
                $filtros_where = "";

                // Si se realiza una búsqueda
                if($busquedas && $busquedas != ""){
                    // Se divide por palabras
                    $palabras = explode(" ", trim($busquedas));

                    // Se recorren las palabras
                    for ($i=0; $i < count($palabras); $i++) { 
                        $filtros_having .= " AND (";
                        $filtros_having .= " id LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR observaciones LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= ") ";
                        
                        if(($i + 1) < count($palabras)) $filtros_having .= " AND ";
                    }
                }

                // Se aplican los filtros
                if(isset($datos['id'])) $filtros_where .= " AND psgb.id = {$datos['id']} ";
                if(isset($datos['solicitud_id'])) $filtros_where .= " AND psgb.solicitud_id = {$datos['solicitud_id']} ";

                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}": "ORDER BY psgb.fecha_creacion DESC";

                $sql =
                "SELECT
                    psgb.*,
                    DATE(psgb.fecha_creacion) fecha,
                    TIME(psgb.fecha_creacion) hora,
                    IF(u.razon_social is not null, u.razon_social, '-') nombre_usuario
                FROM productos_solicitudes_garantia_bitacora psgb
                LEFT JOIN usuarios u ON psgb.usuario_id = u.id
                WHERE psgb.id is NOT NULL
                $filtros_where
                $filtros_having
                $order_by
                $limite
                ";

                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;
        }
    }
}