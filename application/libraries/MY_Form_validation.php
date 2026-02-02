<?php

class MY_Form_validation extends CI_Form_validation{
	public function fecha_completa_valida($fecha) {
		$formato_valido = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
		if ($formato_valido && $formato_valido->format('Y-m-d H:i:s') === $fecha) {
			return true;
		}

		$this->set_message('fecha_completa_valida', 'El campo {field} debe contener una fecha válida en el formato YYYY-MM-DD HH:II:SS.');
		return false;
	}
}