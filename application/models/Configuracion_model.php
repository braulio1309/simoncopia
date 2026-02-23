<?php 
Class Configuracion_model extends CI_Model {
    function actualizar($tabla, $id, $datos){
        return $this->db->where('id', $id)->update($tabla, $datos);
    }

    /**
     * Asignar solicitud de crédito a un usuario disponible
     *
     * @return void
     */
    public function asignar_solicitud_credito() {
        // Obtener usuarios disponibles
        $usuarios_disponibles = $this->obtener('usuarios_disponibles');
        
        if (empty($usuarios_disponibles)) return false; // No hay usuarios disponibles
        
        // Estrategia: asignar al usuario con menos solicitudes
        $usuario_seleccionado = $usuarios_disponibles[0];
        
        $datos['usuario_asignado_id'] = $usuario_seleccionado->id;
        $datos['estado'] = 'asignada';        
        
        // Marcar usuario como no disponible temporalmente
        $this->marcar_usuario_no_disponible($usuario_seleccionado->id);
        
        // Se incrementa el contador de solicitudes
        $this->db
            ->set('solicitudes_atendidas', 'solicitudes_atendidas + 1', FALSE)
            ->where('id', $usuario_seleccionado->id)
            ->update('usuarios');
        
        return $usuario_seleccionado->id;
    }

    function crear($tipo, $datos) {
        switch ($tipo) {
            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;

            case 'erp_bodegas_batch':
                return $this->db->insert_batch('erp_bodegas', $datos);
            break;

            case 'erp_listas_precios_batch':
                return $this->db->insert_batch('erp_listas_precios', $datos);
            break;

            case 'erp_compras_ordenes_batch':
                return $this->db->insert_batch('erp_compras_ordenes', $datos);
            break;

            case 'erp_ventas_pedidos_batch':
                return $this->db->insert_batch('erp_ventas_pedidos', $datos);
            break;

            case 'clientes':
                return $this->db->insert_batch('clientes', $datos);
            break;

            case 'recibos_detalle_batch':
                return $this->db->insert_batch('recibos_detalle', $datos);
            break;

            case 'terceros':
                $this->db->insert('usuarios', $datos);
                return $this->db->insert_id();
            break;

            case 'terceros_api':
                return $this->db->insert_batch('terceros', $datos);
            break;
            
            case 'tercero_contacto':
                // return $datos;
                return $this->db->insert('terceros_contactos', $datos);
            break;

            case 'terceros_contactos':
                // return $datos;
                return $this->db->insert_batch('terceros_contactos', $datos);
            break;
        }

        $this->db->close;
    }

    function eliminar($tipo, $datos) {
        switch ($tipo) {
            case 'comprobante':
                $datos['recibo_tipo_id'] = 3;
    
                return $this->db->delete('recibos', $datos);
            default:
                return $this->db->delete($tipo, $datos);
            break;
        }

        $this->db->close;
    }

    /**
     * Liberar usuario (cuando completa una solicitud)
     *
     * @param int $usuario_id
     * @return void
     */
    public function liberar_usuario($id_usuario) {
        return $this->marcar_usuario_disponible($id_usuario);
    }

    /**
     * Verifica actividad de usuarios (para limpiar inactivos)
     *
     * @return void
     */
    public function limpiar_usuarios_inactivos() {
        return $this->db
            ->where('fecha_ultima_actividad <', date('Y-m-d H:i:s', strtotime('-10 minutes')))
            ->where('esta_disponible', 1)
            ->update('usuarios', ['esta_disponible' => 0])
        ;
    }

    /**
     * Marcar usuario como disponible
     *
     * @param int $usuario_id
     * @return void
     */
    public function marcar_usuario_disponible($usuario_id) {
        $datos = [
            'esta_disponible' => 1,
            'fecha_ultima_actividad' => date('Y-m-d H:i:s'),
        ];
        $this->db->where('id', $usuario_id);

        return $this->db->update('usuarios', $datos);
    }

    /**
     * Marcar usuarios como no disponible
     *
     * @param int $usuario_id
     * @return void
     */
    public function marcar_usuario_no_disponible($usuario_id) {
        $datos = [
            'esta_disponible' => 0,
            'fecha_ultima_actividad' => date('Y-m-d H:i:s'),
        ];
        $this->db->where('id', $usuario_id);

        return $this->db->update('usuarios', $datos);
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
            case 'erp_bodegas':
                return $this->db
                    ->select([
                        '*',
                        'f150_rowid id',
                        'LPAD(f150_id, 5, 0) codigo',
                        "CONCAT_WS(' ', f150_id, f150_descripcion) nombre"
                    ])
                    ->where($datos)
                    ->order_by('f150_id')
                    ->get($tabla)
                    ->result()
                ;
            break;
            
            case 'erp_listas_precios':
                if($datos) $this->db->where($datos);

                return $this->db
                    ->select([
                        '*',
                        "f112_id id",
                        "CONCAT_WS(' ', f112_id, f112_descripcion) nombre"
                    ])
                    ->order_by('f112_id')
                    ->get($tabla)
                    ->result()
                ;
            break;
            
            case 'erp_ventas_pedidos':
                return $this->db
                    ->where($datos)
                    ->get($tabla)
                    ->row()
                ;
            break;

            case 'erp_compras_ordenes':
                return $this->db
                    ->where($datos)
                    ->get($tabla)
                    ->row()
                ;
            break;

            case 'centros_operacion':
                if(!empty($datos)) $this->db->where($datos);

                $this->db
                    ->order_by('nombre')
                    ->from($tabla)
                ;

                if(isset($datos['id']) || isset($datos['codigo'])) return $this->db->get()->row();
                return $this->db->get()->result();
            break;

            case 'cliente_factura_movimiento':
                $sql =
                "SELECT
                    SUM(cfm.f351_valor_cr ) f351_valor_cr 
                FROM
                    clientes_facturas_movimientos AS cfm 
                WHERE
                    cfm.f253_id LIKE '4135%' 
                    AND cfm.f200_nit = '{$datos['f200_nit']}' 
                    AND cfm.f350_consec_docto = '{$datos['f350_consec_docto']}'";

                return $this->db->query($sql)->row();
            break;

            case 'comprobantes_contables_tipos':
                if(!empty($datos)) $this->db->where($datos);

                $this->db
                    ->order_by('nombre')
                    ->from($tabla)
                ;

                if(isset($datos['id'])) return $this->db->get()->row();
                return $this->db->get()->result();
            break;

            case 'contactos':
                // Filtro contador
				$contador = (isset($datos['contador'])) ? "LIMIT {$datos['contador']}, {$this->config->item('cantidad_datos')}" : "" ;
                $having = "HAVING id";
                $where = "WHERE tc.id";

                if (isset($datos['busqueda'])) {
                    $palabras = explode(' ', trim($datos['busqueda']));
        
                    for ($i=0; $i < count($palabras); $i++) {
                        $having .= " AND (";
                        $having .= " nombre LIKE '%{$palabras[$i]}%'";
                        $having .= " OR tc.numero LIKE '%{$palabras[$i]}%'";
                        $having .= " OR tc.nit LIKE '%{$palabras[$i]}%'";
                        $having .= " OR tc.email LIKE '%{$palabras[$i]}%'";
                        $having .= " OR modulo LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";
        
                        if(($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                if(isset($datos['id'])) $where .= " AND tc.id = {$datos['id']} ";
                if(isset($datos['numero'])) $where .= " AND tc.numero = '{$datos['numero']}' ";
                if(isset($datos['email'])) $where .= " AND tc.email = '{$datos['email']}' ";
                if(isset($datos['nit'])) $where .= " AND tc.nit = '{$datos['nit']}' ";
                if(isset($datos['modulo_id'])) $where .= " AND tc.modulo_id = '{$datos['modulo_id']}' ";

                $sql =
                "SELECT
                    tc.id,
                    tc.nit,
                    tc.numero,
                    tc.email,
                    t.f200_razon_social nombre,
                    m.descripcion AS modulo
                FROM
                    terceros_contactos AS tc
                    LEFT JOIN terceros AS t ON tc.nit = t.f200_nit
                    LEFT JOIN modulos AS m ON tc.modulo_id = m.id
                $where
                $having
                ORDER BY
                    tc.fecha_creacion DESC
                $contador";

                if(isset($datos['id']) || isset($datos['token']) || isset($datos['documento_numero'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

            case 'codigo_temporal':
                return $this->db
                    ->select([
                        'ct.*',
                        'u.nombres',
                        'u.email',
                    ])
                    ->from("usuarios_codigos_temporales ct")
                    ->join("usuarios u", "ct.usuario_id = u.id")
                    ->where($datos)
                    ->get()
                    ->row()
                ;
            break;

            case 'codigo_temporal_valido':
                return $this->db
                    ->where([
                        'usuario_id' => $datos['usuario_id'],
                        'codigo' => $datos['codigo'],
                        'fecha_vencimiento >' => date('Y-m-d H:i:s'),
                    ])
                    ->get('usuarios_codigos_temporales')
                    ->row()
                ;
            break;

            case 'cuenta_bancaria':
                return $this->db
                    ->where($datos)
                    ->get('cuentas_bancarias')
                    ->row()
                ;
            break;

            case 'cuentas_bancarias':
                return $this->db
                    ->order_by('nombre')
                    ->get('cuentas_bancarias')
                    ->result()
                ;
            break;

            case 'importaciones_estados':
                return $this->db
                    ->where($datos)
                    ->get($tabla)
                    ->result()
                ;
            break;

            case 'periodos':
                if(!empty($datos)) $this->db->where($datos);

                $this->db
                    ->order_by('codigo')
                    ->from($tabla)
                ;

                if(isset($datos['mes'])) return $this->db->get()->row();
                return $this->db->get()->result();
            break;

            case 'recibo_tipo':
                return $this->db
                    ->where($datos)
                    ->get('recibos_tipos')
                    ->row()
                ;
            break;

            case 'recibos':
                $limite = "";
				$limite = (isset($datos['cantidad'])) ? "LIMIT {$datos['cantidad']}, {$this->config->item('cantidad_datos')}" : "" ;
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";
                $where = "WHERE r.recibo_estado_id";
                $having = "";
                $url_archivo = base_url().'archivos/recibos';

                if (isset($datos['busqueda'])) {
                    $palabras = explode(' ', trim($datos['busqueda']));
        
                    $having = "HAVING";
        
                    for ($i=0; $i < count($palabras); $i++) {
                        $having .= " (";
                        $having .= " r.documento_numero LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.razon_social LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.direccion LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.direccion_envio LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.email LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.telefono LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.token LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.wompi_transaccion_id LIKE '%{$palabras[$i]}%'";
                        $having .= " OR estado LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.numero_siesa LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.wompi_datos LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";
        
                        if(($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }
                
                if(isset($datos['id']) && $datos['id']) $where .= " AND r.id = {$datos['id']} ";
                if(isset($datos['finalizado']) && $datos['finalizado']) $where .= " AND r.wompi_status IS NOT NULL ";
                if(isset($datos['id_tipo_recibo'])) $where .= " AND r.recibo_tipo_id = {$datos['id_tipo_recibo']} ";
                if(isset($datos['actualizado_bot']) && trim($datos['actualizado_bot']) !== '') $having .= " HAVING actualizado_bot = {$datos['actualizado_bot']} ";
                if(isset($datos['token']) && $datos['token']) $where .= " AND r.token = '{$datos['token']}' ";
                if(isset($datos['documento_numero']) && $datos['documento_numero']) $where .= " AND r.documento_numero = '{$datos['documento_numero']}'";
                if(isset($datos['recibo_estado_id']) && $datos['recibo_estado_id']) $where .= " AND r.recibo_estado_id = '{$datos['recibo_estado_id']}'";

                // Filtros personalizados
                if (isset($datos['filtro_fecha_creacion']) && $datos['filtro_fecha_creacion']) $where .= " AND DATE(r.fecha_creacion) = '{$datos['filtro_fecha_creacion']}' ";
                if (isset($datos['filtro_numero_documento']) && $datos['filtro_numero_documento']) $where .= " AND r.documento_numero LIKE '%{$datos['filtro_numero_documento']}%' ";
                if (isset($datos['filtro_nombre']) && $datos['filtro_nombre']) $where .= " AND r.razon_social LIKE '%{$datos['filtro_nombre']}%' ";
                if (isset($datos['filtro_forma_pago']) && $datos['filtro_forma_pago']) $where .= " AND r.wompi_datos LIKE '%{$datos['filtro_forma_pago']}%' ";
                if (isset($datos['filtro_recibo_siesa']) && $datos['filtro_recibo_siesa']) $where .= " AND r.numero_siesa LIKE '%{$datos['filtro_recibo_siesa']}%' ";
                if (isset($datos['filtro_estado']) && $datos['filtro_estado']) $where .= " AND re.nombre LIKE '%{$datos['filtro_estado']}%' ";
                if (isset($datos['filtro_valor']) && $datos['filtro_valor']) $where .= " AND r.valor LIKE '%{$datos['filtro_valor']}%' ";
                if (isset($datos['filtro_usuario_creador']) && $datos['filtro_usuario_creador']) $where .= " AND (uc.nombres LIKE '%{$datos['filtro_usuario_creador']}%') AND uc.primer_apellido LIKE '%{$datos['filtro_usuario_creador']}%' ";
                if (isset($datos['filtro_comentarios']) && $datos['filtro_comentarios']) $where .= " AND r.comentarios LIKE '%{$datos['filtro_comentarios']}%' ";
                if (isset($datos['filtro_observaciones']) && $datos['filtro_observaciones']) $where .= " AND r.observaciones LIKE '%{$datos['filtro_observaciones']}%' ";
                if (isset($datos['filtro_telefono']) && $datos['filtro_telefono']) $where .= " AND r.telefono LIKE '%{$datos['filtro_telefono']}%' ";
                if (isset($datos['filtro_token']) && $datos['filtro_token']) $where .= " AND r.token LIKE '%{$datos['filtro_token']}%' ";

                $sql =
                "SELECT
                    r.*,
                    DATE(r.fecha_creacion) fecha,
                    TIME(r.fecha_creacion) hora,
                    rt.nombre tipo,
                    re.nombre estado,
                    re.nombre,
	                re.clase estado_clase,
                    CONCAT_WS( ' ', uc.nombres, uc.primer_apellido ) usuario_creacion,
	                CONCAT_WS( ' ', ug.nombres, ug.primer_apellido ) usuario_gestion,
                    IF(r.fecha_actualizacion_bot is NOT NULL, 1, 0) actualizado_bot,
                    cb.codigo AS cuenta_bancaria_codigo,
                    cb.nombre AS cuenta_bancaria_nombre,
                    CONCAT_WS('/', '$url_archivo', r.id, r.archivo_soporte) archivo_soporte,
                    GREATEST(
                        ( SELECT MAX( fecha_consignacion ) FROM recibos WHERE id = r.id ),
                        ( SELECT MAX( documento_cruce_fecha ) FROM recibos_detalle WHERE recibo_id = r.id )
                    ) fecha_recaudo
                FROM
                    recibos AS r
                    LEFT JOIN recibos_tipos AS rt ON r.recibo_tipo_id = rt.id
	                LEFT JOIN recibos_estados AS re ON r.recibo_estado_id = re.id 
                    LEFT JOIN usuarios AS uc ON r.usuario_creacion_id = uc.id 
	                LEFT JOIN usuarios AS ug ON r.usuario_aprobacion_id = ug.id
                    LEFT JOIN cuentas_bancarias AS cb ON r.cuenta_bancaria_id = cb.id
                $where
                $having
                ORDER BY
                    r.id DESC
                $limite";

                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                
                if(isset($datos['id']) || isset($datos['token']) || isset($datos['nombre'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

            case 'recibos_cuentas_bancarias':
                $this->db
                    ->select([
                        'rcb.*',
                        'cb.codigo auxiliar', 
                        'cb.nombre',
                        'cb.numero',
                    ])
                    ->from("recibos_cuentas_bancarias rcb")
                    ->join("cuentas_bancarias cb", "rcb.cuenta_bancaria_id = cb.id", "left")
                    ->where($datos)
                ;

                return $this->db->get()->result();
            break;

            case 'recibos_tipos':
                if(isset($datos)) $this->db->where($datos);
                return $this->db->get($tabla)->result();
            break;

            case 'recibos_detalle':
                $this->db
                    ->select([
                        "rd.*"
                    ])
                    ->from("recibos_detalle rd")
                    ->join("recibos r", "rd.recibo_id = r.id", "left")
                ;

                if (isset($datos["id"]) && $datos["id"]) $this->db->where("rd.id", $datos["id"]);
                if (isset($datos["recibo_id"]) && $datos["recibo_id"]) $this->db->where("rd.recibo_id", $datos["recibo_id"]);

                if (isset($datos["id"])) return $this->db->get()->row();
                return $this->db->get()->result();
            break;

			case 'grupos':
                $where = "WHERE p.id";

                /**
                 * Filtro de marcas activas
                 */
                $marcas = $this->configuracion_model->obtener('marcas');
                if(isset($datos['marcas_activas'])) {
                    $where .= " AND (";
                    for ($i=0; $i < count($marcas); $i++) {
                        $where .= " p.marca = '{$marcas[$i]->nombre}' ";
                        if(($i + 1) < count($marcas)) $where .= " OR ";
                    }
                    $where .= ") ";
                }

                if(isset($datos['marca'])) $where .= " AND p.marca = '{$datos['marca']}' ";
                if(isset($datos['linea'])) $where .= " AND p.linea = '{$datos['linea']}' ";
                $order_by = (isset($datos['marcas_activas'])) ? " ORDER BY RAND() " : " ORDER BY nombre " ;
                $limite = (isset($datos['marcas_activas'])) ? "LIMIT 10" : "" ;

                $sql =
                "SELECT
                    id,
                    p.grupo nombre
                FROM
                    productos AS p 
                $where
                GROUP BY
                    p.grupo
                $order_by
                $limite
                ";

                if (isset($datos['id'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

			case 'lineas':
                $where = "WHERE p.id";
                
                /**
                 * Filtro de marcas activas
                 */
                $marcas = $this->configuracion_model->obtener('marcas');
                if(isset($datos['marcas_activas'])) {
                    $where .= " AND (";
                    for ($i=0; $i < count($marcas); $i++) {
                        $where .= " p.marca = '{$marcas[$i]->nombre}' ";
                        if(($i + 1) < count($marcas)) $where .= " OR ";
                    }
                    $where .= ") ";
                }

                if(isset($datos['marca'])) $where .= " AND p.marca = '{$datos['marca']}' ";
                $order_by = (isset($datos['marcas_activas'])) ? " ORDER BY RAND() " : " ORDER BY nombre " ;
                $limite = (isset($datos['marcas_activas'])) ? "LIMIT 10" : "" ;

                $sql =
                "SELECT
                    id,
                    p.linea nombre
                FROM
                    productos AS p 
                $where
                GROUP BY
                    p.linea
                $order_by
                $limite
                ";

                if (isset($datos['id'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

            case 'marca':
                unset($datos['tipo']);

                return $this->db
                    ->where($datos)
                    ->get('marcas')
                    ->row()
                ;
            break;

            case 'marcas':
                $where = "WHERE p.id IS NOT NULL";

                if(isset($datos['grupo'])) $where .= " AND p.grupo = '{$datos['grupo']}' ";
                if(isset($datos['linea'])) $where .= " AND p.linea = '{$datos['linea']}' ";
                if(isset($datos['activo'])) $where .= " AND m.activo = '{$datos['activo']}' ";
                $order_by = (isset($datos['marcas_activas'])) ? " ORDER BY RAND() " : " ORDER BY nombre " ;

                $sql =
                "SELECT
                    m.id, 
                    TRIM(m.codigo) codigo,
                    p.marca nombre
                FROM
                    productos AS p
                    LEFT JOIN marcas AS m ON p.marca = m.nombre 
                $where
                GROUP BY
                    p.marca
                $order_by
                ";
                
                return $this->db->query($sql)->result();
            break;

            case 'modulos':
                if($datos) $this->db->where($datos);

                return $this->db
                    ->get($tabla)
                    ->result()
                ;
            break;

            case 'motivos_rechazo':
                if(isset($datos['interfaz_id'])) $this->db->where('interfaz_id', $datos['interfaz_id']);

                return $this->db
                    ->order_by('nombre')
                    ->get($tabla)
                    ->result()
                ;
            break;

            case 'perfil_rol':
                return $this->db
                    ->where($datos)
                    ->get('perfiles_roles')
                    ->row()
                ;
            break;

            case 'perfiles':
                $where = "WHERE p.id";
                
                if(isset($datos['nombre'])) $where .= " AND p.nombre = '{$datos['nombre']}'";
                if(isset($datos['token'])) $where .= " AND p.token = '{$datos['token']}'";
                
                $sql =
                "SELECT
                    *,
                    CASE p.activo WHEN 1 THEN 'Activo' ELSE 'Inactivo' END estado_nombre,
                    CASE p.es_administrador WHEN 1 THEN 'Sí' ELSE 'No' END administrador_nombre
                FROM
                    perfiles AS p
                $where
                ORDER BY
                    p.nombre";

                if(isset($datos['id']) || isset($datos['token']) || isset($datos['nombre'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

            case 'permisos':
                $permisos = [];

                $sql = 
                "SELECT
                    r.id,
                    m.nombre AS modulo,
                    r.nombre AS rol 
                FROM
                    roles AS r
                    INNER JOIN modulos AS m ON r.modulo_id = m.id
                    INNER JOIN perfiles_roles ON perfiles_roles.rol_id = r.id
                    INNER JOIN perfiles ON perfiles_roles.perfil_id = perfiles.id
                    INNER JOIN usuarios AS u ON u.perfil_id = perfiles.id 
                WHERE
                    u.id = {$this->session->userdata('usuario_id')}";

                $resultado = $this->db->query($sql)->result();

				// Se recorren los resultados y se agregan al arreglo de permisos
				foreach ($resultado as $permiso) array_push($permisos, [$permiso->modulo => $permiso->rol]);

				return $permisos;
            break;

            case 'productos_solicitudes_garantia_motivos_reclamacion':
                if(!empty($datos)) $this->db->where($datos);

                $this->db
                    ->order_by('nombre')
                    ->from($tabla)
                ;

                if(isset($datos['mes'])) return $this->db->get()->row();
                return $this->db->get()->result();
            break;

            case 'roles':
                return $this->db
                    ->where('modulo_id', $datos)
                    ->get('roles')
                    ->result()
                ;
            break;

            case 'terceros':
                // Filtro contador
                $having = "";
                $where = "WHERE 1";

                if (isset($datos['busqueda'])) {
                    $palabras = explode(' ', trim($datos['busqueda']));
        
                    $having = "HAVING";
        
                    for ($i=0; $i < count($palabras); $i++) {
                        $having .= " (";
                        $having .= " t.f200_nit LIKE '%{$palabras[$i]}%'";
                        $having .= " OR t.f200_razon_social LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";
        
                        if(($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                if(isset($datos['f200_ind_cliente'])) $where .= " AND t.f200_ind_cliente = {$datos['f200_ind_cliente']} ";
                if(isset($datos['nit'])) $where .= " AND t.f200_nit = '{$datos['nit']}' ";
                if(isset($datos['f200_ind_proveedor']) && $datos['f200_ind_proveedor']) $where .= " AND t.f200_ind_proveedor = {$datos['f200_ind_proveedor']} ";

                $sql =
                "SELECT
                    t.*,
                    t.f200_razon_social nombre
                FROM
                    terceros AS t
                $where
                $having
                ORDER BY
	                t.f200_razon_social";

                if(isset($datos['id']) || isset($datos['nit'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

            case 'usuarios':
                // Filtro contador
				$contador = (isset($datos['contador'])) ? "LIMIT {$datos['contador']}, {$this->config->item('cantidad_datos')}" : "" ;
                $having = "";
                $where = "WHERE u.id";

                if (isset($datos['busqueda'])) {
                    $palabras = explode(' ', trim($datos['busqueda']));
        
                    $having = "HAVING";
        
                    for ($i=0; $i < count($palabras); $i++) {
                        $having .= " (";
                        $having .= " u.nombres LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.primer_apellido LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.segundo_apellido LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.razon_social LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.telefono LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.celular LIKE '%{$palabras[$i]}%'";
                        $having .= " OR estado_nombre LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.celular LIKE '%{$palabras[$i]}%'";
                        $having .= " OR documento_numero LIKE '%{$palabras[$i]}%'";
                        $having .= " OR email LIKE '%{$palabras[$i]}%'";
                        $having .= " OR nombre_contacto LIKE '%{$palabras[$i]}%'";
                        $having .= " OR perfil LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";
        
                        if(($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                if(isset($datos['id'])) $where .= " AND u.id = {$datos['id']} ";
                if(isset($datos['token'])) $where .= " AND u.token = '{$datos['token']}'";
                if(isset($datos['documento_numero'])) $where .= " AND u.documento_numero = '{$datos['documento_numero']}'";
                if(isset($datos['login'])) $where .= " AND u.login = '{$datos['login']}'";
                if(isset($datos['email'])) $where .= " AND u.email = '{$datos['email']}'";
                if(isset($datos['perfil_id'])) $where .= " AND u.perfil_id = '{$datos['perfil_id']}'";

                $sql =
                "SELECT
                    u.*,
                    CONCAT_WS( ' ', u.nombres, u.primer_apellido, u.segundo_apellido) nombre_completo,
                    CASE u.estado WHEN 1 THEN 'Activo' ELSE 'Inactivo' END estado_nombre,
                    p.nombre perfil
                FROM
                    usuarios AS u
                INNER JOIN perfiles AS p ON u.perfil_id = p.id
                $where
                $having
                ORDER BY
	                u.razon_social 
                $contador";

                if(isset($datos['id']) || isset($datos['token']) || isset($datos['documento_numero']) || isset($datos['login']) || isset($datos['email'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

            case 'usuarios_disponibles':
                return $this->db
                    ->where([
                        'estado' => 1,
                        'esta_disponible' => 1,
                        'fecha_ultima_actividad >=' => date('Y-m-d H:i:s', strtotime('-5 minutes'))
                    ])
                    ->order_by('solicitudes_atendidas') // Se asigna al que menos tiene
                    ->get('usuarios')
                    ->result()
                ;
            break;

            case 'usuarios_identificacion_tipos':
                return $this->db
					->order_by("nombre")
                    ->get($tabla)
                    ->result()
                ;
            break;

            case 'usuarios_tipos':
                return $this->db
                    ->get($tabla)
                    ->result()
                ;
            break;

            case 'clientes_sucursales':
                return $this->db
                    ->SELECT([
                        '*',
                        'f201_descripcion_sucursal nombre'
                    ])
                    ->where($datos)
                    ->get('clientes_sucursales')
                    ->result()
                ;
            break;

            case 'cliente_sucursal':
                return $this->db
                    ->where($datos)
                    ->get('clientes_sucursales')
                    ->row()
                ;
            break;

            case 'departamentos':
                return $this->db
                    ->where($datos)
                    ->order_by('nombre')
                    ->get('departamentos')
                    ->result()
                ;
            break;

            case 'logs_tipos':
                return $this->db
                    ->order_by('nombre')
                    ->get('logs_tipos')
                    ->result()
                ;
            break;

            case 'logs':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsquedas
                $where  = "WHERE l.id IS NOT NULL";
                $having = "HAVING l.id";

                if(isset($datos['id'])) $where .= " AND l.id = {$datos['id']} ";
                if(isset($datos["fecha_inicial"]) && $datos["fecha_inicial"]) $where .= " AND l.fecha_creacion >= '{$datos['fecha_inicial']} 00:00:00' ";
                if(isset($datos["fecha_final"]) && $datos["fecha_final"]) $where .= " AND l.fecha_creacion <= '{$datos['fecha_final']} 23:59:59' ";

                // Filtros personalizados
                $filtros_personalizados = isset($datos['filtros_personalizados']) ? $datos['filtros_personalizados']: [];

                // Filtros where
                if (isset($filtros_personalizados['fecha']) && $filtros_personalizados['fecha'] != '') $where .= " AND DATE(l.fecha_creacion) = '{$filtros_personalizados['fecha']}' ";
                if (isset($filtros_personalizados['modulo']) && $filtros_personalizados['modulo'] != '') $where .= " AND m.nombre LIKE '%{$filtros_personalizados['modulo']}%' ";
                if (isset($filtros_personalizados['log_tipo']) && $filtros_personalizados['log_tipo'] != '') $where .= " AND lt.nombre LIKE '%{$filtros_personalizados['log_tipo']}%' ";
                if (isset($filtros_personalizados['observacion']) && $filtros_personalizados['observacion'] != '') $where .= " AND l.observacion LIKE '%{$filtros_personalizados['observacion']}%' ";

                // Filtros having
                if (isset($filtros_personalizados['usuario']) && $filtros_personalizados['usuario'] != '') {
                    $usuario = $this->db->escape_like_str($filtros_personalizados['usuario']);
                    $having .= " AND usuario LIKE '%{$usuario}%'";
                }


                // Si se realiza una búsqueda
                if (isset($datos['busqueda']) && $datos['busqueda'] != '') {
                    // Se divide por palabras
                    $palabras = explode(' ', trim($datos['busqueda']));

                    // Se recorren las palabras
                    for ($i = 0; $i < count($palabras); $i++) {
                        $having .= " AND (";
                        $having .= " l.id LIKE '%{$palabras[$i]}%'";
                        $having .= " OR lt.nombre LIKE '%{$palabras[$i]}%'";
                        $having .= " OR m.nombre LIKE '%{$palabras[$i]}%'";
                        $having .= " OR l.fecha_creacion LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.nombres LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.primer_apellido LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.segundo_apellido LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";

                        if (($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                // Ordenamiento
                $order_by = isset($datos['ordenar']) ? "ORDER BY {$datos['ordenar']}" : "ORDER BY l.fecha_creacion DESC";

                $sql = " SELECT
                        l.*,
                        lt.nombre log_tipo,
                        m.nombre modulo,
                        CONCAT_WS(' ', u.nombres COLLATE utf8mb4_general_ci, u.primer_apellido COLLATE utf8mb4_general_ci, u.segundo_apellido COLLATE utf8mb4_general_ci) AS usuario
                    FROM logs l
                    LEFT JOIN logs_tipos lt ON l.log_tipo_id = lt.id
                    LEFT JOIN modulos m ON lt.modulo_id = m.id
                    LEFT JOIN usuarios u ON l.usuario_id = u.id
                    $where
                    GROUP BY l.id
                    $having
                    $order_by
                    $limite
                ";

                if (isset($datos['contar']) && $datos['contar'])  return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
                break;

            case 'paises':
                return $this->db
                    ->where($datos)
                    ->order_by('nombre')
                    ->get('paises')
                    ->result()
                ;
            break;

            case 'municipios':
                return $this->db
                    ->where($datos)
                    ->order_by('nombre')
                    ->get('municipios')
                    ->result()
                ;
            break;

            case 'segmentos':
                return $this->db
                    ->order_by('plan')
                    ->order_by('nombre')
                    ->get('segmentos')
                    ->result()
                ;
            break;

            // Une las tablas tercero y tercero_contacto para buscar en ambas
            case 'tercero_contacto':
                $filtros = '';

                $filtro_telefono1 = (isset($datos['numero'])) ? " AND (REPLACE(t.f015_telefono, ' ', '') = {$datos['numero']} OR REPLACE(t.f015_celular, ' ', '') = {$datos['numero']})" : "" ;
                $filtro_telefono2 = (isset($datos['numero'])) ? " AND REPLACE(tc.numero, ' ', '') = {$datos['numero']}" : "" ;
                $filtros = (isset($datos['modulo_id'])) ? " AND tc.modulo_id = {$datos['modulo_id']}" : "" ;

                $sql =
                "SELECT
                    t.f015_telefono telefono,
                    t.f015_celular celular,
                    t.f200_nit nit
                FROM
                    terceros AS t
                WHERE
                    t.f200_nit = '{$datos['nit']}'
                    $filtro_telefono1
                UNION
                SELECT
                    tc.numero telefono,
                    NULL celular,
                    tc.nit
                FROM
                    terceros_contactos AS tc
                WHERE
                    tc.nit = '{$datos['nit']}'
                    $filtro_telefono2
                    $filtros";

                return $this->db->query($sql)->row();
            break;

            case 'vendedores':
                return $this->db
                    ->order_by('nombre')
                    ->get('terceros_vendedores')
                    ->result()
                ;
            break;

            case 'vendedor':
                return $this->db
                    ->where($datos)
                    ->get('terceros_vendedores')
                    ->row()
                ;
            break;
        }

        $this->db->close;
	}
}
/* Fin del archivo Configuracion_model.php */
/* Ubicación: ./application/models/Configuracion_model.php */