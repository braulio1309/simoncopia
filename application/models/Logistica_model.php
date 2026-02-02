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