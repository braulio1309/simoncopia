<?php 
Class Contabilidad_model extends CI_Model {
    function actualizar($tabla, $filtros, $datos){
        return $this->db->where($filtros)->update($tabla, $datos);
        $this->db->close;
    }

    function crear($tipo, $datos) {
        switch ($tipo) {
            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;
        }
    }
    
    function eliminar($tipo, $datos) {
        switch ($tipo) {
            default:
                return $this->db->delete($tipo, $datos);
            break;
        }
    }

    function obtener($tabla, $datos = null) {
		switch ($tabla) {
            case 'comprobantes_contables_tareas':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsqueda
                $busquedas = (isset($datos['busqueda'])) ? $datos['busqueda'] : null ;
                $filtros_having = "HAVING cct.id";
                $filtros_where = "where 1";

                // Si se realiza una búsqueda
                if($busquedas && $busquedas != ""){
                    $palabras = explode(' ', trim($datos['busqueda']));

                    $having = "HAVING";

                    for ($i=0; $i < count($palabras); $i++) {
                        // $having .= " (";
                        // $having .= " cf.Nro_Doc_cruce LIKE '%{$palabras[$i]}%'";
                        // $having .= " OR cf.RazonSocial LIKE '%{$palabras[$i]}%'";

                        // $having .= ") ";
                        // if(($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                // if(isset($datos['id'])) $filtros_where .= " AND cct.id = {$datos['id']}";
                if(isset($datos['fecha_inicio_ejecucion']) && $datos['fecha_inicio_ejecucion'] == 0) $filtros_where .= " AND cct.fecha_inicio_ejecucion IS NULL";
                
                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}" : "ORDER BY cct.id DESC";
                
                $sql =
                "SELECT
                    cct.*,
                    ct.nombre AS tipo_comprobante,
                    s.nombre AS centro_operativo,
                    cct.anio,
                    p.nombre AS mes_nombre,
                    ( SELECT COUNT( cctd.id ) FROM comprobantes_contables_tareas_detalle cctd WHERE cctd.comprobante_contable_tarea_id = cct.id ) cantidad_comprobantes,
                    ( SELECT COUNT( cctd.consecutivo_existe ) FROM comprobantes_contables_tareas_detalle AS cctd WHERE cctd.comprobante_contable_tarea_id = cct.id AND cctd.consecutivo_existe = 1 ) consecutivo_existe,
                    ( SELECT COUNT( cctd.consecutivo_existe ) FROM comprobantes_contables_tareas_detalle AS cctd WHERE cctd.comprobante_contable_tarea_id = cct.id AND cctd.consecutivo_existe = 0 ) consecutivo_no_existe,
                    ( SELECT COUNT( cctd.comprobante_coincide ) FROM comprobantes_contables_tareas_detalle AS cctd WHERE cctd.comprobante_contable_tarea_id = cct.id AND cctd.comprobante_coincide = 1 ) comprobante_coincide,
                    ( SELECT COUNT( cctd.comprobante_coincide ) FROM comprobantes_contables_tareas_detalle AS cctd WHERE cctd.comprobante_contable_tarea_id = cct.id AND cctd.comprobante_coincide = 0 ) comprobante_no_coincide,
                    ( SELECT SUM( cctd.cantidad_soportes ) FROM comprobantes_contables_tareas_detalle AS cctd WHERE cctd.comprobante_contable_tarea_id = cct.id ) cantidad_soportes,
                    TIMESTAMPDIFF(MINUTE, fecha_inicio_ejecucion, fecha_fin_ejecucion) tiempo_ejecucion_minutos
                FROM
                    comprobantes_contables_tareas AS cct
                    INNER JOIN comprobantes_contables_tipos AS ct ON cct.comprobante_contable_tipo_id = ct.id
                    INNER JOIN centros_operacion AS s ON cct.centro_operacion_id = s.id
                    INNER JOIN periodos AS p ON cct.mes = p.mes
                $filtros_where
                $order_by
                $limite";

                // return $sql;
                
                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;

            case 'comprobantes_contables_tareas_detalle':
                unset($datos['tipo']);
                
                return $this->db
                    ->where($datos)
                    ->get($tabla)
                    ->result()
                ;
            break;
        }
    }
}