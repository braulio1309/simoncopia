<?php 

Class Marketing_model extends CI_Model {
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
            case 'marketing_campanias':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsquedas
                $where  = "WHERE mc.id IS NOT NULL";
                $having = "HAVING mc.id";

                if(isset($datos['id'])) $where .= " AND mc.id = {$datos['id']} ";

                // Filtros personalizados
                $filtros_personalizados = isset($datos['filtros_personalizados']) ? $datos['filtros_personalizados']: [];

                // Filtros where
                if (isset($filtros_personalizados['id']) && $filtros_personalizados['id'] != '') $where .= " AND mc.id LIKE '%{$filtros_personalizados['id']}%' ";
                if (isset($filtros_personalizados['fecha_inicio']) && $filtros_personalizados['fecha_inicio'] != '') $where .= " AND DATE(mc.fecha_inicio) = '{$filtros_personalizados['fecha_inicio']}' ";
                if (isset($filtros_personalizados['nombre']) && $filtros_personalizados['nombre'] != '') $where .= " AND mc.nombre LIKE '%{$filtros_personalizados['nombre']}%' ";

                // Filtros having
                if (isset($filtros_personalizados['cantidad_contactos']) && $filtros_personalizados['cantidad_contactos'] != '') $having .= " AND cantidad_contactos = {$filtros_personalizados['cantidad_contactos']} ";
                if (isset($filtros_personalizados['cantidad_envios']) && $filtros_personalizados['cantidad_envios'] != '') $having .= " AND cantidad_envios = {$filtros_personalizados['cantidad_envios']} ";

                // Si se realiza una búsqueda
                if (isset($datos['busqueda']) && $datos['busqueda'] != '') {
                    // Se divide por palabras
                    $palabras = explode(' ', trim($datos['busqueda']));

                    // Se recorren las palabras
                    for ($i = 0; $i < count($palabras); $i++) {
                        $having .= " AND (";
                        $having .= " mc.id LIKE '%{$palabras[$i]}%'";
                        $having .= " OR mc.fecha_inicio LIKE '%{$palabras[$i]}%'";
                        $having .= " OR cantidad_contactos LIKE '%{$palabras[$i]}%'";
                        $having .= " OR cantidad_envios LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";

                        if (($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                // Ordenamiento
                $order_by = isset($datos['ordenar']) ? "ORDER BY {$datos['ordenar']}" : "ORDER BY mc.id DESC";

                $sql = " SELECT
                        mc.*,
                        COUNT(DISTINCT mcc.id) AS cantidad_contactos,
                        (
                            SELECT COUNT(*)
                            FROM marketing_campanias_contactos mcc2
                            WHERE mcc2.campania_id = mc.id
                            AND mcc2.fecha_envio IS NOT NULL
                        ) AS cantidad_envios
                    FROM marketing_campanias mc
                    LEFT JOIN marketing_campanias_contactos mcc
                        ON mcc.campania_id = mc.id
                    $where
                    GROUP BY mc.id
                    $having
                    $order_by
                    $limite
                ";

                if (isset($datos['contar']) && $datos['contar'])  return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
                break;
            
            case 'marketing_campanias_contactos':
                $where  = "WHERE mcc.id IS NOT NULL";

                if (isset($datos['campania_id'])) $where .= " AND mcc.campania_id = {$datos['campania_id']} ";

                $sql = 
                    "SELECT mcc.*
                        FROM marketing_campanias_contactos mcc
                    $where";

                return $this->db->query($sql)->result();

            case 'marketing_banners':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsquedas
                $where  = "WHERE mb.id IS NOT NULL";
                $having = "HAVING mb.id";

                if(isset($datos['id'])) $where .= " AND mb.id = {$datos['id']} ";

                // Filtros personalizados
                $filtros_personalizados = isset($datos['filtros_personalizados']) ? $datos['filtros_personalizados']: [];

                // Filtros where
                if (isset($filtros_personalizados['fecha_creacion']) && $filtros_personalizados['fecha_creacion'] != '') $where .= " AND DATE(mb.fecha_creacion) = '{$filtros_personalizados['fecha_creacion']}' ";
                if (isset($filtros_personalizados['nombre']) && $filtros_personalizados['nombre'] != '') $where .= " AND mbt.nombre LIKE '%{$filtros_personalizados['nombre']}%' ";

                // Si se realiza una búsqueda
                if (isset($datos['busqueda']) && $datos['busqueda'] != '') {
                    // Se divide por palabras
                    $palabras = explode(' ', trim($datos['busqueda']));

                    // Se recorren las palabras
                    for ($i = 0; $i < count($palabras); $i++) {
                        $having .= " AND (";
                        $having .= " mb.id LIKE '%{$palabras[$i]}%'";
                        $having .= " OR mb.fecha_creacion LIKE '%{$palabras[$i]}%'";
                        $having .= " OR banner_tipo_nombre LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";

                        if (($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                // Ordenamiento
                $order_by = isset($datos['ordenar']) ? "ORDER BY {$datos['ordenar']}" : "ORDER BY mbt.nombre ASC";

                $sql = " SELECT
                        mb.*,
                        mbt.nombre AS banner_tipo_nombre,
                        mbt.id AS banner_tipo_id
                    FROM marketing_banners mb
                    LEFT JOIN marketing_banners_tipos mbt ON mb.banner_tipo_id = mbt.id
                    $where
                    GROUP BY mb.id
                    $having
                    $order_by
                    $limite
                ";

                if (isset($datos['contar']) && $datos['contar'])  return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
                break;
                
            case 'marketing_banners_tipos':
                unset($datos['tipo']);
                
                return $this->db
                    ->get($tabla)
                    ->result()
                ;
                break;

             case 'marketing_beneficios':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsquedas
                $where  = "WHERE mb.id IS NOT NULL";
                $having = "HAVING mb.id";

                if(isset($datos['id'])) $where .= " AND mb.id = {$datos['id']} ";

                // Filtros personalizados
                $filtros_personalizados = isset($datos['filtros_personalizados']) ? $datos['filtros_personalizados'] : [];

                // Filtros where
                if (isset($filtros_personalizados['nombre']) && $filtros_personalizados['nombre'] != '') {
                    $nombre_escaped = $this->db->escape_like_str($filtros_personalizados['nombre']);
                    $where .= " AND mb.nombre LIKE '%{$nombre_escaped}%' ";
                }
                
                // CORRECCIÓN: Se agregaron comillas simples '{}' alrededor de los valores
                if (isset($filtros_personalizados['beneficio_tipo']) && $filtros_personalizados['beneficio_tipo'] != '') {
                    $tipo_escaped = $this->db->escape_str($filtros_personalizados['beneficio_tipo']);
                    $where .= " AND mb.beneficio_tipo = '{$tipo_escaped}' ";
                }
                
                if (isset($filtros_personalizados['tipo_venta']) && $filtros_personalizados['tipo_venta'] != '') {
                    $tipo_venta_escaped = $this->db->escape_str($filtros_personalizados['tipo_venta']);
                    $where .= " AND mb.tipo_venta = '{$tipo_venta_escaped}' ";
                }
                
                if (isset($filtros_personalizados['fecha_inicio']) && $filtros_personalizados['fecha_inicio'] != '') {
                    $fecha_inicio_escaped = $this->db->escape_str($filtros_personalizados['fecha_inicio']);
                    $where .= " AND DATE(mb.fecha_inicio) = '{$fecha_inicio_escaped}' ";
                }
                
                if (isset($filtros_personalizados['fecha_final']) && $filtros_personalizados['fecha_final'] != '') {
                    $fecha_final_escaped = $this->db->escape_str($filtros_personalizados['fecha_final']);
                    $where .= " AND DATE(mb.fecha_final) = '{$fecha_final_escaped}' ";
                }

                // Si se realiza una búsqueda
                if (isset($datos['busqueda']) && $datos['busqueda'] != '') {
                    $palabras = explode(' ', trim($datos['busqueda']));
                    $palabras_count = count($palabras);

                    for ($i = 0; $i < $palabras_count; $i++) {
                        $palabra_escaped = $this->db->escape_like_str($palabras[$i]);
                        $having .= " AND (";
                        $having .= " mb.id LIKE '%{$palabra_escaped}%'";
                        $having .= " OR mb.nombre LIKE '%{$palabra_escaped}%'";
                        $having .= " OR mb.beneficio_tipo LIKE '%{$palabra_escaped}%'";
                        $having .= " OR mb.codigo_descuento LIKE '%{$palabra_escaped}%'";
                        $having .= " OR mb.tipo_venta LIKE '%{$palabra_escaped}%'";
                        $having .= ") ";

                        // El loop ya maneja el AND entre palabras
                    }
                }

                // Ordenamiento
                $order_by = isset($datos['ordenar']) ? "ORDER BY {$datos['ordenar']}" : "ORDER BY mb.id DESC";

                $sql = " SELECT
                            mb.*
                        FROM marketing_beneficios mb
                        $where
                        GROUP BY mb.id
                        $having
                        $order_by
                        $limite
                    ";

                if (isset($datos['contar']) && $datos['contar'])  return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
        return $this->db->query($sql)->result();
                    break;
            }
        }
}