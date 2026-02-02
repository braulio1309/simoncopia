<?php 
Class Clientes_model extends CI_Model {
    function actualizar($tabla, $filtros, $datos){
        return $this->db->where($filtros)->update($tabla, $datos);
        $this->db->close;
    }

    function crear($tipo, $datos){
        switch ($tipo) {
            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;

            case 'recibos_cuentas_bancarias':
                if($this->db->delete($tipo, ['recibo_id' => $datos[0]['recibo_id']])) return $this->db->insert_batch($tipo, $datos);
            break;

            case 'clientes_facturas':
                if($this->db->delete($tipo, ['Cliente' => $datos[0]['Cliente']])) return $this->db->insert_batch($tipo, $datos);
            break;

            case 'clientes_facturas_detalle':
                if($this->db->delete($tipo, ['f350_consec_docto' => $datos[0]['f350_consec_docto']])) return $this->db->insert_batch($tipo, $datos);
            break;

            case 'clientes_facturas_movimientos':
                if($this->db->delete($tipo, ['f350_consec_docto' => $datos[0]['f350_consec_docto']])) return $this->db->insert_batch($tipo, $datos);
            break;

            case 'clientes_facturas_movimientos_proveedores':
                return $this->db->insert_batch('clientes_facturas_movimientos', $datos);
            break;

            case 'clientes_sucursales':
                return $this->db->insert_batch($tipo, $datos);
            break;

            case 'clientes_solicitudes_credito_detalle':
                return $this->db->insert_batch($tipo, $datos);
            break;

            case 'terceros':
                if($this->db->delete($tipo, ['f200_nit' => $datos[0]['f200_nit']])) return $this->db->insert_batch($tipo, $datos);
            break;

            case 'wms_pedidos':
                return $this->db->insert_batch($tipo, $datos);
            break;

            case 'wms_pedidos_tracking':
                return $this->db->insert_batch($tipo, $datos);
            break;
        }

        $this->db->close;
    }

    function eliminar($tipo, $datos){
        switch ($tipo) {
            case 'clientes_solicitudes_credito_detalle':
                return $this->db->delete($tipo, $datos);
            break;
            
            case 'clientes_sucursales':
                return $this->db->delete($tipo, $datos);
            break;

            case 'wms_pedidos':
                return $this->db->delete($tipo, $datos);
            break;

            case 'wms_pedidos_tracking':
                return $this->db->delete($tipo, $datos);
            break;

            case 'clientes_facturas_movimientos':
                return $this->db->delete($tipo, $datos);
            break;
        }

        $this->db->close;
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
            case 'cliente_factura':
                unset($datos['tipo']);
                
                return $this->db
                    ->where($datos)
                    ->get('clientes_facturas')
                    ->row()
                ;
            break;

            case 'clientes_facturas':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsqueda
                $busquedas = (isset($datos['busqueda'])) ? $datos['busqueda'] : null ;
                $filtros_having = "HAVING cf.id";
                $filtros_where = "WHERE cf.id";

                // Si se realiza una búsqueda
                if($busquedas && $busquedas != ""){
                    // Se divide por palabras
                    $palabras = explode(' ', trim($datos['busqueda']));

                    // Se recorren las palabras
                    for ($i=0; $i < count($palabras); $i++) {
                        $filtros_having .= " AND (";
                        $filtros_having .= " cf.Nro_Doc_cruce LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR cf.RazonSocial LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR cf.RazonSocial_Sucursal LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR centro_operativo LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR a.nombre_homologado LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR Fecha_doc_cruce LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR Fecha_venc LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR cf.ValorAplicado LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR cf.totalCop LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR cf.valorDoc LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= ") ";

                        if(($i + 1) < count($palabras)) $filtros_having .= " AND ";
                    }
                }

                // Filtros personalizados
                $filtros_personalizados = isset($datos['filtros_personalizados']) ? $datos['filtros_personalizados'] : [];
                if (isset($filtros_personalizados['sede']) && $filtros_personalizados['sede'] != '') $filtros_having .= " AND centro_operativo LIKE '%{$filtros_personalizados['sede']}%' ";
                if (isset($filtros_personalizados['documento_cruce']) && $filtros_personalizados['documento_cruce'] != '') $filtros_where .= " AND cf.Nro_Doc_cruce LIKE '%{$filtros_personalizados['documento_cruce']}%' ";
                if (isset($filtros_personalizados['cuota']) && $filtros_personalizados['cuota'] != '') $filtros_where .= " AND cf.Nro_cuota = '{$filtros_personalizados['cuota']}' ";
                if (isset($filtros_personalizados['fecha']) && $filtros_personalizados['fecha'] != '') $filtros_having .= " AND Fecha_doc_cruce = '{$filtros_personalizados['fecha']}' ";
                if (isset($filtros_personalizados['fecha_vencimiento']) && $filtros_personalizados['fecha_vencimiento'] != '') $filtros_having .= " AND Fecha_venc = '{$filtros_personalizados['fecha_vencimiento']}' ";
                if (isset($filtros_personalizados['dias_vencido']) && $filtros_personalizados['dias_vencido'] != '') $filtros_having .= " AND dias_vencido LIKE '%{$filtros_personalizados['dias_vencido']}%' ";
                if (isset($filtros_personalizados['valor_documento']) && $filtros_personalizados['valor_documento'] != '') $filtros_where .= " AND cf.ValorAplicado LIKE '%{$filtros_personalizados['valor_documento']}%' ";
                if (isset($filtros_personalizados['valor_abonos']) && $filtros_personalizados['valor_abonos'] != '') $filtros_where .= " AND cf.valorDoc LIKE '%{$filtros_personalizados['valor_abonos']}%' ";
                if (isset($filtros_personalizados['valor_saldo']) && $filtros_personalizados['valor_saldo'] != '') $filtros_where .= " AND cf.totalCop LIKE '%{$filtros_personalizados['valor_saldo']}%' ";
                if (isset($filtros_personalizados['sucursal']) && $filtros_personalizados['sucursal'] != '') $filtros_where .= " AND cf.RazonSocial_Sucursal LIKE '%{$filtros_personalizados['sucursal']}%' ";
                if (isset($filtros_personalizados['tipo_credito']) && $filtros_personalizados['tipo_credito'] != '') $filtros_where .= " AND a.nombre_homologado LIKE '%{$filtros_personalizados['tipo_credito']}%' ";

                if(isset($datos['numero_documento'])) $filtros_where .= " AND cf.Cliente = '{$datos['numero_documento']}' ";
                if(isset($datos['pendientes'])) $filtros_where .= " AND cf.totalCop <> 0 AND cf.totalCop NOT BETWEEN -1 AND 1 ";
                if(isset($datos['id'])) $filtros_where .= " AND cf.id = {$datos['id']}";
                if(isset($datos['Tipo_Doc_cruce'])) $filtros_where .= " AND cf.Tipo_Doc_cruce = '{$datos['Tipo_Doc_cruce']}'";
                if(isset($datos['Nro_Doc_cruce'])) $filtros_where .= " AND cf.Nro_Doc_cruce = '{$datos['Nro_Doc_cruce']}'";
                if(isset($datos['Cliente'])) $filtros_where .= " AND cf.Cliente = '{$datos['Cliente']}'";
                if(isset($datos['mostrar_estado_cuenta'])) $filtros_where .= " AND a.mostrar_estado_cuenta = 1";
                if(isset($datos['mostrar_alerta'])) $filtros_where .= " AND a.mostrar_alerta = 1";
                
                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}" : "ORDER BY Fecha_venc DESC, Nro_cuota, Nro_Doc_cruce";

                $sql =
                "SELECT
                    cf.id,
                    cf.Fecha_Creacion,
                    cf.Desc_auxiliar,
                    cf.Cliente,
                    cf.Tipo_Doc_cruce,
                    cf.Nro_Doc_cruce,
                    cf.doccruce,
                    cf.RazonSocial,
                    cf.RazonSocial_Sucursal,
                    date(cf.Fecha_doc_cruce) Fecha_doc_cruce,
                    date(cf.Fecha_venc) Fecha_venc,
                    cf.CentroOperaciones,
                    cf.diasvencidos,
                    cf.ValorAplicado,
                    cf.valorDoc,
                    cf.totalCop,
                    cf.Nro_cuota,
                    YEAR(cf.Fecha_venc) anio_vencimiento,
                    MONTH(cf.Fecha_venc) mes_vencimiento,
                    DAY(cf.Fecha_venc) dia_vencimiento,
                    co.nombre centro_operativo,
                    a.nombre_homologado,
                    DATEDIFF (DATE(NOW()), DATE(cf.Fecha_doc_cruce)) AS dias_expedicion,
	                -- IF((SELECT dias_expedicion) > 8 AND (SELECT dias_expedicion) <= 30, 1.5, IF((SELECT dias_expedicion) <= 8, 2.5, 0)) descuento_porcentaje,
                    IF(cf.Tipo_Doc_cruce = 'CFE', IF((SELECT dias_expedicion) > 8 AND (SELECT dias_expedicion) <= 31, 1.5, IF((SELECT dias_expedicion) <= 8, 2.5, 0) ), 0) descuento_porcentaje,
                    DATEDIFF(date(NOW()), date(cf.Fecha_venc)) AS dias_vencido, 
                    ( SELECT cs.f201_id_sucursal FROM clientes_sucursales AS cs WHERE cs.f201_descripcion_sucursal = cf.RazonSocial_Sucursal LIMIT 1 ) sucursal_id,
                    a.codigo codigo_auxiliar,
                    co.codigo centro_operativo_codigo,
                    (
                        SELECT
                            CONCAT_WS('/', r.id, r.archivo_soporte)
                        FROM
                            recibos_detalle AS rd
                            INNER JOIN recibos AS r ON rd.recibo_id = r.id 
                        WHERE
                            r.recibo_estado_id = 3 
                            AND documento_cruce_numero = cf.Nro_Doc_cruce 
                            AND documento_numero = cf.Cliente 
                            LIMIT 1 
                    ) por_aplicar_archivo_pendiente
                FROM
                    clientes_facturas AS cf
                LEFT JOIN centros_operacion AS co ON cf.CentroOperaciones = co.codigo
                LEFT JOIN auxiliares AS a ON cf.Desc_auxiliar = a.nombre
                $filtros_where
                $filtros_having
                $order_by
                $limite";

                // return $sql;
                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id']) || isset($datos['Tipo_Doc_cruce']) || isset($datos['Nro_Doc_cruce'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;

            case 'clientes_facturas_detalle':
                unset($datos['tipo']);
                
                return $this->db
                    ->where($datos)
                    ->get($tabla)
                    ->result()
                ;
            break;
            
            case 'clientes_facturas_movimientos':
                $sql =
                "SELECT
                    cfm.f253_id,
                    cfm.f253_descripcion,
                    r.nombre_homologado,
                    cfm.f351_valor_db
                FROM
                    clientes_facturas_movimientos AS cfm
                    LEFT JOIN retenciones AS r ON cfm.f253_id = r.codigo 
                WHERE
                    r.mostrar_estado_cuenta = 1
                    AND f200_nit = '{$datos['f200_nit']}'
                    AND f350_consec_docto = '{$datos['f350_consec_docto']}' 
                ";

                if (isset($datos['id'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

            case 'clientes_retenciones_detalle':
                // Búsquedas
                $where  = "WHERE crd.id IS NOT NULL";

                if(isset($datos['id'])) $where .= " AND crd.id = {$datos['id']} ";
                if(isset($datos['nit'])) $where .= " AND crd.nit = '{$datos['nit']}' ";

                $sql = " SELECT
                        crd.*,
                        cri.razon_social
                    FROM clientes_retenciones_detalle crd
                    LEFT JOIN clientes_retenciones_informe cri ON cri.nit = crd.nit
                    $where
                ";

                // Si viene id se devuelve solo un registro
                if (isset($datos['id']) || isset($datos['nit'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;

            case 'clientes_retenciones_informe':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsquedas
                $where  = "WHERE cri.id IS NOT NULL";

                if(isset($datos['nit'])) $where .= " AND cri.nit = {$datos['nit']} ";
                if(isset($datos['anio'])) $where .= " AND cri.anio = {$datos['anio']} ";
                if (isset($datos['existe_fecha_envio'])) $where .= " AND (cri.fecha_envio IS NULL OR cri.fecha_envio = '') ";

                // Filtros personalizados
                $filtros_personalizados = isset($datos['filtros_personalizados']) ? $datos['filtros_personalizados']: [];

                // Filtros where
                if (isset($filtros_personalizados['nit']) && $filtros_personalizados['nit'] != '') $where .= " AND cri.nit LIKE '%{$filtros_personalizados['nit']}%' ";
                if (isset($filtros_personalizados['razon_social']) && $filtros_personalizados['razon_social'] != '') $where .= " AND cri.razon_social LIKE '%{$filtros_personalizados['razon_social']}%' ";
                if (isset($filtros_personalizados['vendedor']) && $filtros_personalizados['vendedor'] != '') $where .= " AND cri.vendedor LIKE '%{$filtros_personalizados['vendedor']}%' ";
                if (isset($filtros_personalizados['fuente']) && $filtros_personalizados['fuente'] != '') $where .= " AND cri.valor_retencion_fuente LIKE '%{$filtros_personalizados['fuente']}%' ";
                if (isset($filtros_personalizados['iva']) && $filtros_personalizados['iva'] != '') $where .= " AND cri.valor_retencion_iva LIKE '%{$filtros_personalizados['iva']}%' ";
                if (isset($filtros_personalizados['ica']) && $filtros_personalizados['ica'] != '') $where .= " AND cri.valor_retencion_ica LIKE '%{$filtros_personalizados['ica']}%' ";
                if (isset($filtros_personalizados['fecha_actualizacion']) && $filtros_personalizados['fecha_actualizacion'] != '') $where .= " AND DATE(cri.fecha_actualizacion) = '{$filtros_personalizados['fecha_actualizacion']}' ";

                // Si se realiza búsqueda genral
                if (isset($datos['busqueda']) && $datos['busqueda'] != '') {
                    $palabras = explode(' ', trim($datos['busqueda']));
                    $where .= " AND (";

                    foreach ($palabras as $i => $palabra) {

                        if ($i > 0) $where .= " OR ";

                        $where .= " ( cri.nit LIKE '%{$palabra}%'
                            OR cri.razon_social LIKE '%{$palabra}%'
                            OR cri.vendedor LIKE '%{$palabra}%'
                            OR cri.valor_retencion_fuente LIKE '%{$palabra}%'
                            OR cri.valor_retencion_iva LIKE '%{$palabra}%'
                            OR cri.valor_retencion_ica LIKE '%{$palabra}%'
                        )";
                    }

                    $where .= ")";
                }

                // Ordenamiento
                $order_by = isset($datos['ordenar']) ? "ORDER BY {$datos['ordenar']}" : "ORDER BY cri.razon_social ASC";

                $sql = " SELECT
                        cri.*
                    FROM clientes_retenciones_informe cri
                    $where
                    $order_by
                    $limite
                ";

                if (isset($datos['contar']) && $datos['contar'])  return $this->db->query($sql)->num_rows();

                // Si viene NIT se devuelve solo un registro
                if (isset($datos['nit'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;

            case 'clientes_solicitudes_credito':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsqueda
                $busquedas = (isset($datos['busqueda'])) ? $datos['busqueda'] : null ;
                $filtros_having = "HAVING csc.id";
                $filtros_where = "";

                // Si se realiza una búsqueda
                if($busquedas && $busquedas != ""){
                    // Se divide por palabras
                    $palabras = explode(" ", trim($busquedas));

                    // Se recorren las palabras
                    for ($i=0; $i < count($palabras); $i++) { 
                        $filtros_having .= " AND (";
                        $filtros_having .= " nombre LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR primer_apellido LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR razon_social LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR documento_numero LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= ") ";
                        
                        if(($i + 1) < count($palabras)) $filtros_having .= " AND ";
                    }
                }

                // Se aplican los filtros
                if(isset($datos['id'])) $filtros_where .= " AND csc.id = {$datos['id']} ";
                if(isset($datos['solicitud_credito_estado_id'])) $filtros_where .= " AND csc.solicitud_credito_estado_id = {$datos['solicitud_credito_estado_id']} ";
                if(isset($datos['documentos_validados'])) $filtros_having .= " AND documentos_validados = {$datos['documentos_validados']} ";
                
                // Filtros personalizados
                if (isset($datos['filtro_fecha_creacion']) && $datos['filtro_fecha_creacion']) $filtros_where .= " AND DATE(csc.fecha_creacion) = '{$datos['filtro_fecha_creacion']}' ";
                if (isset($datos['filtro_numero_documento']) && $datos['filtro_numero_documento']) $filtros_where .= " AND csc.documento_numero LIKE '%{$datos['filtro_numero_documento']}%' ";
                if (isset($datos['filtro_nombre']) && $datos['filtro_nombre']) $filtros_where .= " AND IF(csc.razon_social is NULL, CONCAT_WS(' ', csc.nombre, csc.primer_apellido, csc.segundo_apellido), csc.razon_social) LIKE '%{$datos['filtro_nombre']}%' ";
                if (isset($datos['filtro_id']) && $datos['filtro_id']) $filtros_where .= " AND csc.id = {$datos['filtro_id']} ";
                if (isset($datos['filtro_estado']) && $datos['filtro_estado']) $filtros_having .= " AND estado LIKE '%{$datos['filtro_estado']}%' ";
                if (isset($datos['filtro_usuario_asignado']) && $datos['filtro_usuario_asignado']) $filtros_having .= " AND nombre_usuario_asignado LIKE '%{$datos['filtro_usuario_asignado']}%' ";
                if (isset($datos['filtro_vendedor']) && $datos['filtro_vendedor']) $filtros_having .= " AND vendedor_nombre LIKE '%{$datos['filtro_vendedor']}%' ";
                if (isset($datos['filtro_fecha_cierre']) && $datos['filtro_fecha_cierre']) $filtros_where .= " AND DATE(csc.fecha_cierre) = '{$datos['filtro_fecha_cierre']}' ";
                if (isset($datos['filtro_motivo_rechazo']) && $datos['filtro_motivo_rechazo']) $filtros_having .= " AND motivo_rechazo LIKE '%{$datos['filtro_motivo_rechazo']}%' ";
                if (isset($datos['filtro_cupo']) && $datos['filtro_cupo']) $filtros_where .= " AND csc.cupo_asignado = {$datos['filtro_cupo']} ";
                if (isset($datos['filtro_ultimo_comentario']) && $datos['filtro_ultimo_comentario']) $filtros_having .= " AND ultimo_comentario LIKE '%{$datos['filtro_ultimo_comentario']}%' ";
                if (isset($datos['filtro_tipo']) && $datos['filtro_tipo']) $filtros_having .= " AND tipo LIKE '%{$datos['filtro_tipo']}%' ";

                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}": "ORDER BY csc.fecha_creacion DESC";

                $sql =
                "SELECT
                    csc.*,
                    DATE(csc.fecha_creacion) fecha,
                    TIME(csc.fecha_creacion) hora,
                    DATE(csc.fecha_cierre) fecha_cierre,
                    TIME(csc.fecha_cierre) hora_cierre,
                    IF(csc.razon_social is NULL, CONCAT_WS(' ', csc.nombre, csc.primer_apellido, csc.segundo_apellido), csc.razon_social) nombre_solicitante,
                    d.nombre departamento,
                    d.codigo departamento_codigo,
                    m.nombre municipio,
                    tv.nombre vendedor_nombre,
                    csce.nombre estado,
                    csce.clase estado_clase,
                    IF(ua.razon_social is not null, ua.razon_social, '-') nombre_usuario_asignado,
                    ua.email AS email_usuario_asignado,
                    mr.nombre motivo_rechazo,
                    (
                        SELECT b.observaciones 
                        FROM clientes_solicitudes_credito_bitacora AS b 
                        WHERE b.solicitud_id = csc.id 
                        ORDER BY b.fecha_creacion DESC LIMIT 1 
                    ) ultimo_comentario,
                    uit.codigo tipo_identificacion_codigo,
                    v.nombre vendedor_nombre,
	                v.codigo vendedor_codigo,
                    IF(csc.fecha_validacion_documentos is NOT NULL, 1, 0) documentos_validados,
                    m.codigo municipio_codigo,
                    IF(csc.nueva = 1, 'Nueva', 'Actualización') tipo
                FROM clientes_solicitudes_credito csc
                LEFT JOIN municipios m ON csc.ciudad_id = m.codigo AND csc.departamento_id = m.departamento_id
                LEFT JOIN departamentos d ON csc.departamento_id = d.id
                LEFT JOIN terceros_vendedores tv ON csc.tercero_vendedor_id = tv.id
                LEFT JOIN clientes_solicitudes_credito_estados AS csce ON csc.solicitud_credito_estado_id = csce.id
                LEFT JOIN usuarios AS ua ON csc.usuario_asignado_id = ua.id
                LEFT JOIN motivos_rechazo AS mr ON csc.motivo_rechazo_id = mr.id
                LEFT JOIN usuarios_identificacion_tipos AS uit ON csc.identificacion_tipo_id = uit.id
                LEFT JOIN terceros_vendedores AS v ON csc.tercero_vendedor_id = v.id
                WHERE csc.id is NOT NULL
                $filtros_where
                $filtros_having
                $order_by
                $limite
                ";

                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;

            case 'clientes_solicitudes_credito_asignaciones':
                $sql = 
                "SELECT
                    u.razon_social,
                    COUNT( csc.id ) AS total_solicitudes,
                    COUNT( CASE WHEN csc.fecha_creacion >= DATE_FORMAT( CURDATE(), '%Y-%m-01' ) THEN csc.id END ) AS solicitudes_asignadas_este_mes,
                    COUNT( CASE WHEN csc.fecha_creacion >= DATE_SUB( CURDATE(), INTERVAL WEEKDAY( CURDATE()) DAY ) THEN csc.id END ) AS solicitudes_asignadas_esta_semana,
                    COUNT( CASE WHEN DATE( csc.fecha_creacion ) = CURDATE() THEN csc.id END ) AS solicitudes_asignadas_hoy 
                FROM
                    clientes_solicitudes_credito AS csc
                    LEFT JOIN usuarios AS u ON csc.usuario_asignado_id = u.id 
                GROUP BY
                    csc.usuario_asignado_id,
                    u.razon_social 
                HAVING
                    solicitudes_asignadas_este_mes > 0 
                ORDER BY
                    csc.usuario_asignado_id ASC;";

                return $this->db->query($sql)->result();
                break;

            case 'clientes_solicitudes_credito_bitacora':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsqueda
                $busquedas = (isset($datos['busqueda'])) ? $datos['busqueda'] : null ;
                $filtros_having = "HAVING cscb.id";
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
                if(isset($datos['id'])) $filtros_where .= " AND cscb.id = {$datos['id']} ";
                if(isset($datos['solicitud_id'])) $filtros_where .= " AND cscb.solicitud_id = {$datos['solicitud_id']} ";

                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}": "ORDER BY cscb.fecha_creacion DESC";

                $sql =
                "SELECT
                    cscb.id,
                    cscb.solicitud_id,
                    cscb.observaciones,
                    cscb.fecha_creacion,
                    cscb.usuario_id,
                    DATE(cscb.fecha_creacion) fecha,
                    TIME(cscb.fecha_creacion) hora,
                    IF(u.razon_social is not null, u.razon_social, '-') nombre_usuario
                FROM clientes_solicitudes_credito_bitacora cscb
                LEFT JOIN usuarios u ON cscb.usuario_id = u.id
                WHERE cscb.id is NOT NULL
                $filtros_where
                $filtros_having
                $order_by
                $limite
                ";

                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;

            case 'clientes_solicitudes_credito_detalle':
                return $this->db
                    ->select([
                        'cscd.*',
                        'uit.nombre tipo_identificacion',
                        "CASE cscd.formulario_tipo
                            WHEN 1 THEN 'Socio o accionista'
                            WHEN 2 THEN 'Beneficiario finale de socio o accionista igual o superior a 5%'
                            WHEN 3 THEN 'Persona autorizada para brindar información'
                        END tipo_dato",
                    ])
                    ->from('clientes_solicitudes_credito_detalle cscd')
                    ->join('usuarios_identificacion_tipos uit', 'cscd.identificacion_tipo_id = uit.id', 'left')
                    ->where($datos)
                    ->get()
                    ->result()
                ;
            break;

            case 'tercero':
                unset($datos['tipo']);
                
                return $this->db
                    ->where($datos)
                    ->get('terceros')
                    ->row()
                ;
            break;

            case 'wms_pedidos':
				$limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                $filtros_where = "WHERE p.FechaDocumento IS NOT NULL ";
                $filtros_having = "HAVING p.NIT IS NOT NULL";
                
                if(isset($datos['fecha_documento'])) $filtros_where .= " AND p.FechaDocumento = '{$datos['fecha_documento']}' ";

                // Filtros personalizados
                if (isset($datos['filtro_numero_pedido']) && $datos['filtro_numero_pedido']) $filtros_having .= " AND numero_documento LIKE '%{$datos['filtro_numero_pedido']}%' ";
                if (isset($datos['filtro_nombre_consecutivo']) && $datos['filtro_nombre_consecutivo']) $filtros_where .= " AND p.NombreConsecutivo LIKE '%{$datos['filtro_nombre_consecutivo']}%' ";
                if (isset($datos['filtro_fecha_creacion']) && $datos['filtro_fecha_creacion']) $filtros_where .= " AND p.FechaDocumento = '{$datos['filtro_fecha_creacion']}' ";
                if (isset($datos['filtro_numero_documento']) && $datos['filtro_numero_documento']) $filtros_where .= " AND p.NIT LIKE '%{$datos['filtro_numero_documento']}%' ";
                if (isset($datos['filtro_nombre']) && $datos['filtro_nombre']) $filtros_where .= " AND p.RazonSocial LIKE '%{$datos['filtro_nombre']}%' ";
                if (isset($datos['filtro_estado']) && $datos['filtro_estado']) $filtros_having .= " AND ultimo_estado LIKE '%{$datos['filtro_estado']}%' ";

                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}": "ORDER BY p.FechaDocumento DESC";
                
                $sql = 
                "SELECT
                    p.FechaDocumento fecha_documento,
                    concat(p.IdConsecutivo, '-', p.NumeroDocumento + 0) numero_documento,
                    p.NombreConsecutivo consecutivo_nombre,
                    p.NIT nit,
                    p.RazonSocial rzon_social,
                    COUNT(p.CodProducto) cantidad_productos,
                    (
                        SELECT
                            pt.Estado 
                        FROM
                            wms_pedidos_tracking AS pt 
                        WHERE
                            CAST( pt.NroDcto AS UNSIGNED ) = p.NumeroDocumento 
                            AND CAST( pt.IdConsecutivo AS UNSIGNED ) = p.IdConsecutivo 
                        ORDER BY
                            pt.Fecha DESC 
                            LIMIT 1 
                    ) ultimo_estado 
                FROM
                    wms_pedidos AS p
                $filtros_where
                GROUP BY
                    p.NumeroDocumento, 
                    p.NombreConsecutivo
                $filtros_having
                $order_by
                $limite";

                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;
        }

        $this->db->close;
	}
}
/* Fin del archivo Clientes_model.php */
/* Ubicación: ./application/models/Clientes_model.php */