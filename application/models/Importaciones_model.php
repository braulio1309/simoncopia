<?php
defined('BASEPATH') or exit('El acceso directo a este archivo no está permitido');

class Importaciones_model extends CI_Model
{

    // --------------------------------------------------------------------
    // FUNCIONES DE ESCRITURA (UPDATE, INSERT, DELETE)
    // --------------------------------------------------------------------

    // --------------------------------------------------------------------
    // 2. CREAR (Guardar nuevo)
    // --------------------------------------------------------------------
    public function crear($datos, $tabla = 'importaciones')
    {
        // Limpiamos el array de datos por si viene basura del JS
        $this->db->insert($tabla, $datos);


        $resultado = $this->db->affected_rows() > 0;

        if ($resultado) {
            return ['resultado' => $this->db->insert_id()];
        }

        return $resultado;
    }

    // --------------------------------------------------------------------
    // 3. ACTUALIZAR (Editar existente)
    // --------------------------------------------------------------------
    public function actualizar($tabla, $condiciones, $datos)
    {
        // Limpiamos datos
        $datos_limpios = $this->limpiar_datos($datos);

        $this->db->where($condiciones);
        $this->db->update($tabla, $datos_limpios);

        return $this->db->affected_rows() >= 0; // >= 0 porque a veces guardas sin cambios y cuenta como éxito
    }

    // --------------------------------------------------------------------
    // 4. ELIMINAR
    // --------------------------------------------------------------------
    public function eliminar($tabla, $condiciones)
    {
        $this->db->where($condiciones);
        $this->db->delete($tabla);
        return $this->db->affected_rows() > 0;
    }

    // --------------------------------------------------------------------
    // AUXILIAR: Limpieza de datos
    // --------------------------------------------------------------------
    private function limpiar_datos($datos)
    {
        // Si el JS envía 'tipo' o 'contador', lo quitamos porque no son columnas de BD
        if (isset($datos['tipo'])) unset($datos['tipo']);
        if (isset($datos['contador'])) unset($datos['contador']);

        return $datos;
    }

    // --------------------------------------------------------------------
    // FUNCIONES DE LECTURA (SELECT)
    // --------------------------------------------------------------------

    public function obtener($tipo, $datos = null)
    {

        switch ($tipo) {

            // CASO PRINCIPAL: Obtener listado o detalle de importaciones
            case 'importaciones':
                // 1. Preparación de filtros básicos
                $this->db
                    ->select([
                        'i.*',
                        'ie.nombre estado',
                        'ie.clase estado_clase'
                    ])
                    ->from('importaciones i')
                    ->join('importaciones_estados ie', 'i.importacion_estado_id = ie.id', 'LEFT')
                ;

                // 2. Filtro por ID (Para ver detalle único)
                if (isset($datos['id'])) {
                    $this->db->where('i.id', $datos['id']);
                    return $this->db->get()->row(); // Retorna un solo objeto
                }

                // 3. Búsqueda (Texto libre)
                if (isset($datos['busqueda']) && $datos['busqueda'] != '') {
                    $palabras = explode(' ', trim($datos['busqueda']));

                    $this->db->group_start(); // Abrimos paréntesis para el OR
                    foreach ($palabras as $palabra) {
                        $this->db->like('i.numero_orden_compra', $palabra);
                        $this->db->or_like('i.razon_social', $palabra); // Proveedor
                        $this->db->or_like('i.bl_awb', $palabra);       // Documento transporte
                        $this->db->or_like('i.notas_internas', $palabra);
                        $this->db->or_like('i.pais_origen', $palabra);
                    }
                    $this->db->group_end(); // Cerramos paréntesis
                }

                // 4. Filtros Específicos (Dropdowns)
                if (isset($datos['pais_origen']) && $datos['pais_origen'] != '') {
                    $this->db->where('i.pais_origen', $datos['pais_origen']);
                }
                if (isset($datos['moneda_preferida']) && $datos['moneda_preferida'] != '') {
                    $this->db->where('i.moneda_preferida', $datos['moneda_preferida']);
                }
                if (isset($datos['razon_social']) && $datos['razon_social'] != '') {
                    $this->db->where('i.razon_social', $datos['razon_social']);
                }

                // 5. Ordenamiento
                if (isset($datos['ordenar_por'])) {
                    $this->db->order_by($datos['ordenar_por'], 'DESC');
                } else {
                    // Por defecto: Las llegadas más lejanas primero (o cambio a DESC para ver lo más reciente creado)
                    $this->db->order_by('i.fecha_estimada_llegada', 'DESC');
                }

                // 6. Si solo queremos contar (para paginación)
                if (isset($datos['contar']) && $datos['contar'] == true) {
                    return $this->db->count_all_results();
                }

                // 7. Paginación (Limit)
                // Soporta tanto 'contador' (tu estilo antiguo) como 'start/length' (DataTables)
                if (isset($datos['cantidad'])) {
                    $inicio = isset($datos['indice']) ? $datos['indice'] : 0;
                    if (isset($datos['contador'])) $inicio = $datos['contador']; // Compatibilidad

                    $this->db->limit($datos['cantidad'], $inicio);
                }

                return $this->db->get()->result();
                break;

            // HELPER: Obtener lista única de Países para llenar el select de filtros
            case 'importaciones_pagos':
                $this->db->distinct();
                $this->db->select('id');
                $this->db->where('importacion_id', $datos['importacion_id']);
                $this->db->order_by('id', 'DESC');
                $this->db->limit(1);
                return $this->db->get('importaciones_pagos')->result();
                break;

            // HELPER: Obtener lista única de Proveedores para llenar el select de filtros
            case 'importaciones_maestro_anticipos':
                // 1. Definición de la consulta base
                $this->db->select('a.*, t.f200_razon_social as proveedor');
                $this->db->from('importaciones_maestro_anticipos a');
                $this->db->join('terceros t', 't.f200_nit = a.nit', 'left');

                // 2. Si la petición es para UN solo registro (Ej: abrir un modal de edición)
                // En este caso sí se permite el return porque no es para la tabla principal
                if (isset($datos['id']) && !empty($datos['id'])) {
                    $this->db->where('a.id', $datos['id']);
                    return $this->db->get()->row();
                }

                if (isset($datos['contar']) && $datos['contar'] == true) {
                    return $this->db->count_all_results();
                }

                // 3. Filtro de búsqueda (Lógica para DataTables)
                if (isset($datos['busqueda']) && $datos['busqueda'] != '') {
                    $palabras = explode(' ', trim($datos['busqueda']));

                    $this->db->group_start();
                    foreach ($palabras as $palabra) {
                        $this->db->like('a.id', $palabra);
                        $this->db->or_like('a.nit', $palabra);
                        $this->db->or_like('a.porcentaje', $palabra);
                        $this->db->or_like('t.f200_razon_social', $palabra);
                    }
                    $this->db->group_end();
                }

                // 4. Ordenamiento por defecto
                $this->db->order_by('a.id', 'ASC');
                return $this->db->get()->result();
                // IMPORTANTE:
                // Quitamos el $this->db->get()->result() y el return.
                // Solo dejamos el break para que el flujo siga hacia la paginación automática.
                break;

            // HELPER: Obtener lista única de Monedas
            case 'lista_monedas':
                $this->db->distinct();
                $this->db->select('moneda_preferida');
                return $this->db->get('importaciones')->result();
                break;

            case 'importaciones_bitacora':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsqueda
                $busquedas = (isset($datos['busqueda'])) ? $datos['busqueda'] : null;
                $filtros_having = "HAVING psgb.id";
                $filtros_where = "";

                // Si se realiza una búsqueda
                if ($busquedas && $busquedas != "") {
                    // Se divide por palabras
                    $palabras = explode(" ", trim($busquedas));

                    // Se recorren las palabras
                    for ($i = 0; $i < count($palabras); $i++) {
                        $filtros_having .= " AND (";
                        $filtros_having .= " id LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR observaciones LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= ") ";

                        if (($i + 1) < count($palabras)) $filtros_having .= " AND ";
                    }
                }

                // Se aplican los filtros
                if (isset($datos['id'])) $filtros_where .= " AND psgb.id = {$datos['id']} ";
                if (isset($datos['importacion_id'])) $filtros_where .= " AND psgb.importacion_id = {$datos['importacion_id']} ";

                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}" : "ORDER BY psgb.fecha_creacion DESC";

                $sql = "SELECT
            psgb.*,
            DATE(psgb.fecha_creacion) fecha,
            TIME(psgb.fecha_creacion) hora,
            u.nombres as nombre_usuario,
            i.razon_social as proveedor
        FROM importaciones_bitacora psgb
        LEFT JOIN usuarios u ON psgb.usuario_id = u.id
        LEFT JOIN importaciones i ON psgb.importacion_id = i.id
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

            case 'importaciones_pagos_tipos':
                $this->db->where('activo', 1);
                return $this->db->get('importaciones_pagos_tipos')->result();
            break;
        }

        $this->db->close();
    }
}
/* Fin del archivo Importaciones_model.php */
/* Ubicación: ./application/models/Importaciones_model.php */