<?php
Class Sesion_model extends CI_Model{
    function obtener($tipo, $datos = null)
	{
        switch($tipo) {
            case 'usuario':
                return $this->db
                    ->where($datos)
                    ->get('usuarios')
                    ->row()
                ;
            break;
        }

        $this->db->close;
    }
}
/* Fin del archivo Sesion_model.php */
/* Ubicación: ./application/models/Sesion_model.php */