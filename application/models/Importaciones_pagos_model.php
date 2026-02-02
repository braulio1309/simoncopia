<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Importaciones_pagos_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Listar pagos (por importación o todos)
    public function obtener($where = []) {
        $this->db->select('*');
        $this->db->from('importaciones_pagos');
        
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        // Ordenar por fecha de creación descendente
        $this->db->order_by('id', 'DESC');
        
        return $this->db->get()->result();
    }

    // Crear nuevo pago
    public function crear($datos) {
        $this->db->insert('importaciones_pagos', $datos);
        return $this->db->insert_id();
    }

    // Actualizar pago (ej: cuando pasa a pagado)
    public function actualizar($id, $datos) {
        $this->db->where('id', $id);
        $this->db->update('importaciones_pagos', $datos);
        return $this->db->affected_rows() >= 0;
    }

    // Eliminar pago
    public function eliminar($id) {
        $this->db->where('id', $id);
        $this->db->delete('importaciones_pagos');
        return $this->db->affected_rows() > 0;
    }

    function obtener_general($tabla, $datos = null) {
        switch ($tabla) {
            case 'importaciones_pagos':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsquedas
                $where  = "WHERE ip.id IS NOT NULL";
                $having = "HAVING ip.id IS NOT NULL"; 

                if (isset($datos['id'])) $where .= " AND ip.id = {$datos['id']} ";

                // Filtros personalizados
                $filtros_personalizados = isset($datos['filtros_personalizados']) ? $datos['filtros_personalizados'] : [];

                // Filtros where
                if (isset($filtros_personalizados['importacion']) && $filtros_personalizados['importacion'] != '') $where .= " AND i.razon_social LIKE '%{$filtros_personalizados['importacion']}%' ";
                if (isset($filtros_personalizados['fecha']) && $filtros_personalizados['fecha'] != '') $where .= " AND ip.fecha LIKE '%{$filtros_personalizados['fecha']}%' ";
                if (isset($filtros_personalizados['observaciones']) && $filtros_personalizados['observaciones'] != '') $where .= " AND ip.observaciones LIKE '%{$filtros_personalizados['observaciones']}%' ";
                if (isset($filtros_personalizados['factura_numero']) && $filtros_personalizados['factura_numero'] != '') $where .= " AND ip.factura_numero LIKE '%{$filtros_personalizados['factura_numero']}%' ";
                if (isset($filtros_personalizados['valor_moneda_extranjera']) && $filtros_personalizados['valor_moneda_extranjera'] != '') $where .= " AND ip.valor_moneda_extranjera LIKE '%{$filtros_personalizados['valor_moneda_extranjera']}%' ";
                if (isset($filtros_personalizados['valor_trm']) && $filtros_personalizados['valor_trm'] != '') $where .= " AND ip.valor_trm LIKE '%{$filtros_personalizados['valor_trm']}%' ";
                if (isset($filtros_personalizados['valor_cop']) && $filtros_personalizados['valor_cop'] != '') $where .= " AND ip.valor_cop LIKE '%{$filtros_personalizados['valor_cop']}%' ";
                if (isset($filtros_personalizados['origen_recursos']) && $filtros_personalizados['origen_recursos'] != '') $where .= " AND ipor.nombre LIKE '%{$filtros_personalizados['origen_recursos']}%' ";
                if (isset($filtros_personalizados['cuenta_bancaria']) && $filtros_personalizados['cuenta_bancaria'] != '') $where .= " AND cb.nombre LIKE '%{$filtros_personalizados['cuenta_bancaria']}%' ";
                if (isset($filtros_personalizados['fecha_creacion']) && $filtros_personalizados['fecha_creacion'] != '') $where .= " AND ip.fecha_creacion LIKE '%{$filtros_personalizados['fecha_creacion']}%' ";

                // Filtros having
                if (isset($filtros_personalizados['estado']) && $filtros_personalizados['estado'] != '') $having .= " AND estado_texto LIKE '%{$filtros_personalizados['estado']}%' ";

                // Búsqueda general
                if (isset($datos['busqueda']) && $datos['busqueda'] != '') {
                    $palabras = explode(' ', trim($datos['busqueda']));
                    for ($i = 0; $i < count($palabras); $i++) {
                        $having .= " AND (";
                        $having .= " i.razon_social LIKE '%{$palabras[$i]}%'";
                        $having .= " OR estado_texto LIKE '%{$palabras[$i]}%'";
                        $having .= " OR ip.fecha LIKE '%{$palabras[$i]}%'";
                        $having .= " OR ip.observaciones LIKE '%{$palabras[$i]}%'";
                        $having .= " OR ip.factura_numero LIKE '%{$palabras[$i]}%'";
                        $having .= " OR ip.valor_moneda_extranjera LIKE '%{$palabras[$i]}%'";
                        $having .= " OR ip.valor_trm LIKE '%{$palabras[$i]}%'";
                        $having .= " OR ip.valor_cop LIKE '%{$palabras[$i]}%'";
                        $having .= " OR ipor.nombre LIKE '%{$palabras[$i]}%'";
                        $having .= " OR cb.nombre LIKE '%{$palabras[$i]}%'";
                        $having .= " OR ip.fecha_creacion LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";

                        if (($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                // Ordenamiento
                $order_by = isset($datos['ordenar']) ? "ORDER BY {$datos['ordenar']}" : "ORDER BY ip.fecha_creacion DESC";

                // SQL
                $sql = "SELECT
                        ip.*,
                        CASE 
                            WHEN ip.estado_id = 0 THEN 'Pagado'
                            WHEN ip.estado_id = 1 THEN 'Pendiente por pagar'
                            ELSE 'Desconocido'
                        END AS estado_texto,
                        cb.nombre AS cuenta_bancaria,
                        i.razon_social AS importacion,
                        ipor.nombre AS origen_recursos
                    FROM importaciones_pagos ip
                    LEFT JOIN cuentas_bancarias cb ON ip.cuenta_bancaria_id = cb.id
                    LEFT JOIN importaciones i ON ip.importacion_id = i.id
                    LEFT JOIN importaciones_pagos_origen_recursos ipor ON ip.origen_recursos_id = ipor.id
                    $where
                    GROUP BY ip.id
                    $having
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