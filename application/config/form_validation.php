<?php 
if( ! defined('BASEPATH') ) exit('No direct script access allowed');

$config = [
    'pedidos_get' => [
		['field' => 'id', 'label' => 'id', 'rules' => 'trim|integer|greater_than[0]'],
    ],

	'productos_get' => [
        ['field' => 'id', 'label' => 'ID del producto', 'rules' => 'trim|integer|greater_than[0]'],
    ],

    'recibos_get' => [
		['field' => 'id', 'label' => 'id', 'rules' => 'trim|integer|greater_than[0]'],
        ['field' => 'actualizado_bot', 'label' => 'actualizado_bot', 'rules' => 'required|trim|in_list[0,1]'],
        ['field' => 'id_tipo_recibo', 'label' => 'recibo_tipo_id', 'rules' => 'required|trim|in_list[1,2,3,4,5]'],
    ],

	'recibos_detalle_get' => [
        ['field' => 'recibo_id', 'label' => 'id del recibo', 'rules' => 'required|trim|integer|greater_than[0]'],
    ],

    'recibo_put' => [
		['field' => 'id', 'label' => 'id', 'rules' => 'required|trim|integer|greater_than[0]', 'errors' => ['required' => 'El campo {field} debe ser enviado como parámetro en la url y es obligatorio.']],
        ['field' => 'fecha_actualizacion_bot', 'label' => 'fecha de actualización bot', 'rules' => 'required|trim|fecha_completa_valida'],
    ],

    'solicitud_credito_put' => [
		['field' => 'id', 'label' => 'id', 'rules' => 'required|trim|integer|greater_than[0]', 'errors' => ['required' => 'El campo {field} debe ser enviado como parámetro en la url y es obligatorio.']],
		['field' => 'solicitud_credito_estado_id', 'label' => 'Id del estado de la solicitud', 'rules' => 'trim|integer|greater_than[0]|in_list[1,2,3]'],
        ['field' => 'fecha_validacion_documentos', 'label' => 'Fecha de validación de los documentos', 'rules' => 'trim|fecha_completa_valida'],
    ],

    'solicitudes_credito_get' => [
		['field' => 'id', 'label' => 'id', 'rules' => 'trim|integer|greater_than[0]'],
		['field' => 'solicitud_credito_estado_id', 'label' => 'Id del estado de la solicitud', 'rules' => 'trim|integer|greater_than[0]|in_list[1,2,3]'],
		['field' => 'documentos_validados', 'label' => 'Documentos validados', 'rules' => 'trim|integer|in_list[0,1]'],
    ],

    'solicitudes_credito_archivos_get' => [
        ['field' => 'solicitud_credito_id', 'label' => 'id de la solicitud de crédito', 'rules' => 'required|trim|integer|greater_than[0]'],
    ],

    'solicitudes_credito_detalle_get' => [
        ['field' => 'solicitud_credito_id', 'label' => 'id de la solicitud de crédito', 'rules' => 'required|trim|integer|greater_than[0]'],
    ],

	'terceros_get' => [
        ['field' => 'nit', 'label' => 'NIT del tercero', 'rules' => 'trim'],
    ],

	'terceros_contactos_get' => [
        ['field' => 'nit', 'label' => 'NIT del tercero', 'rules' => 'required|trim|integer|greater_than[0]'],
        ['field' => 'modulo_id', 'label' => 'Id del módulo', 'rules' => 'trim|integer|greater_than[0]'],
    ],

	'whatsapp_logistica_diligencia_post' => [
        ['field' => 'identificador', 'label' => 'Identificador de la diligencia', 'rules' => 'required|trim'],
        ['field' => 'solicitante', 'label' => 'Solicitante de la diligencia', 'rules' => 'required|trim'],
        ['field' => 'tipo_solicitud', 'label' => 'Tipo de solicitud', 'rules' => 'required|trim'],
        ['field' => 'observaciones', 'label' => 'Id del módulo', 'rules' => 'required|trim'],
    ],

    'whatsapp_proveedores_orden_compra' => [
        ['field' => 'orden_numero', 'label' => 'Numero de orden de compra', 'rules' => 'required|trim'],
        ['field' => 'proveedor', 'label' => 'Nombre del proveedor', 'rules' => 'required|trim'],
        ['field' => 'url', 'label' => 'URL', 'rules' => 'required|trim'],
    ],

    'whatsapp_proveedores_orden_compra_aprobada' => [
        ['field' => 'orden_compra', 'label' => 'Numero de orden de compra', 'rules' => 'required|trim'],
        ['field' => 'proveedor', 'label' => 'Nombre del proveedor', 'rules' => 'required|trim'],
        ['field' => 'url', 'label' => 'URL', 'rules' => 'required|trim'],
    ],

    'whatsapp_proveedores_orden_compra_rechazada' => [
        ['field' => 'orden_compra', 'label' => 'Numero de orden de compra', 'rules' => 'required|trim'],
        ['field' => 'proveedor', 'label' => 'Nombre del proveedor', 'rules' => 'required|trim'],
        ['field' => 'url', 'label' => 'URL', 'rules' => 'required|trim'],
    ],

    'whatsapp_clientes_asignacion_solicitud_credito' => [
        ['field' => 'nombre_cliente', 'label' => 'Nombre del cliente', 'rules' => 'required|trim'],
        ['field' => 'url', 'label' => 'URL', 'rules' => 'required|trim'],
    ]
];