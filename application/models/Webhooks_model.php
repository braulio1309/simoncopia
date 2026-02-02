<?php 
Class Webhooks_model extends CI_Model {
    /**
	 * Permite obtener registros de la base de datos del WMS
	 * y se almacenan en la base de datos del eCommerce
	 * 
	 * @param  [string] $tabla Tabla a la que se realizara la consulta
	 * @return [array]  Arreglo de datos con el resultado de la consulta
	 */
	function obtener($tabla, $datos = null) {
		switch ($tabla) {
            case 'wms_pedidos':
                // Carga del helper de conexión de SQL Server
                $this->load->helper('sql_server');

                // Conexión al WMS
                $this->bd_wms = conectar_sql_server(
                    $this->config->item('wms_url'),
                    $this->config->item('wms_puerto'),
                    $this->config->item('wms_base_datos'),
                    $this->config->item('wms_usuario'),
                    $this->config->item('wms_clave')
                );

                $filtros_where = "WHERE p.FechaDocumento IS NOT NULL ";
                if(isset($datos['fecha_documento'])) $filtros_where .= " AND p.FechaDocumento = '{$datos['fecha_documento']}' ";

                // Si no hay conexión, se retorna un arreglo vacío
                if (!$this->bd_wms) return [];

                $sql = 
                "SELECT
                    p.FechaDocumento,
                    p.NumeroDocumento,
                    p.NIT,
                    p.RazonSocial,
                    p.Bodega,
                    p.CodProducto,
                    p.CantPedida,
                    p.CantidadPendiente,
                    p.PorcentajeIva,
                    p.PorcentajeDescuento,
                    p.FechaIngresoDocumento,
                    p.HoraIngresoDocumento,
                    p.IdConsecutivo,
                    c.Nombre NombreConsecutivo
                FROM
                    dbo.VTA_Pedidos AS p
                    LEFT JOIN dbo.MT_Consecutivos AS c ON p.IdConsecutivo = c.IdConsecutivo
                $filtros_where
                ORDER BY p.FechaDocumento DESC";

                $resultado = $this->bd_wms->query($sql);

                return $resultado->fetchAll(PDO::FETCH_ASSOC);
            break;

            case 'wms_pedidos_tracking':
                // Carga del helper de conexión de SQL Server
                $this->load->helper('sql_server');
                
                // Conexión al WMS
                $this->bd_wms = conectar_sql_server(
                    $this->config->item('wms_url'),
                    $this->config->item('wms_puerto'),
                    $this->config->item('wms_base_datos'),
                    $this->config->item('wms_usuario'),
                    $this->config->item('wms_clave')
                );

                $filtros_where = "WHERE tr.Fecha IS NOT NULL ";
                if(isset($datos['fecha'])) $filtros_where .= " AND CAST( tr.Fecha AS DATE ) = '{$datos['fecha']}' ";

                // Si no hay conexión, se retorna un arreglo vacío
                if (!$this->bd_wms) return [];

                $sql = 
                "SELECT
                    tr.Id, 
                    tr.IdConsecutivo, 
                    tr.NroDcto, 
                    tr.OrdenExterna, 
                    et.Nombre Estado, 
                    tr.Observaciones, 
                    tr.Fecha, 
                    tr.IdUsuario
                FROM
                    dbo.tbl_Tracking AS tr
                    LEFT JOIN
                    dbo.MT_EstadosTracking AS et
                    ON 
                        tr.IdEstado = et.IdEstado
                $filtros_where
                ORDER BY
                    tr.Fecha DESC";

                $resultado = $this->bd_wms->query($sql);

                return $resultado->fetchAll(PDO::FETCH_ASSOC);
            break;
        }
	}
}