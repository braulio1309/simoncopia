<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your CodeIgniter root. Typically this will be your base URL,
| WITH a trailing slash:
|
|	http://example.com/
|
| WARNING: You MUST set this value!
|
| If it is not set, then CodeIgniter will try to guess the protocol and
| path to your installation, but due to security concerns the hostname will
| be set to $_SERVER['SERVER_ADDR'] if available, or localhost otherwise.
| The auto-detection mechanism exists only for convenience during
| development and MUST NOT be used in production!
|
| If you need to allow multiple domains, remember that this file is still
| a PHP script and you can easily do that on your own.
|
*/
$config['base_url'] = 'http://localhost/ecommerce/';
$config['base_url_blog'] = 'https://repuestossimonbolivar.com/web/blog-de-repuestos-simon-bolivar/';
$config['url_fotos'] = 'http://localhost/ecommerce/archivos/fotos/';
$config['url_wms'] = '';
$config['url_wordpress'] = 'https://repuestossimonbolivar.com/web';
$config['index_page'] = '';
$config['uri_protocol']	= 'REQUEST_URI';
$config['uri_protocol'] = 'PATH_INFO';
$config['enable_query_strings'] = FALSE;
// Datos de la API Siesa
$config['base_url_stage'] = '';
$config['base_url_qa'] = '';
$config['base_url_produccion'] = '';

// Datos de la API de Wompi
$config['base_url_wompi'] = '';

$config['api_siesa'] = [
    'base_url' => $config['base_url_qa'],
    'idCompania' => '',
    'idInterface' => '',
    'idDocumento' => '',
    'idDocumentoImportacionPedido' => '',
    'idDocumentoImportacionDocumentoContable' => '',
    'idDocumentoImportacionDocumentoContableV2' => '',
    'conniKey' => '',
    'conniToken' => '',
];

// Sandbox
$config['api_wompi'] = [
    'url' => 'https://sandbox.wompi.co/v1',
    'llave_publica' => '',
    'llave_privada' => '',
    'secret_eventos' => '',
    'secret_integridad' => '',
];

// Sandbox
$config['api_tcc'] = [
    'url' => '',
    'access_token' => '',
];


$config['datos_email'] = [
    'protocol' => 'smtp',
    'smtp_host' => 'ssl://smtp.googlemail.com',
    'smtp_user' => '',
    'smtp_pass' => '',
    'smtp_port' => 465,
    'mailtype' => 'html',
    'charset' => 'utf-8',
    'newline' => "\r\n",
];

// Bodegas
$config['bodega_principal'] = '00555';

// Listas de precio
$config['lista_precio'] = '002';

// WMS
$config['wms_url'] = ''; // 190.145.127.246 (Conexión remota en producción)
$config['wms_base_datos'] = '';
$config['wms_usuario'] = '';
$config['wms_clave'] = '';
$config['wms_puerto'] = 1433;

// Siesa
$config['siesa_url'] = ''; // 190.145.127.246\BDMYFSB (Conexión remota en producción)
$config['siesa_base_datos'] = '';
$config['siesa_usuario'] = '';
$config['siesa_clave'] = '';
$config['siesa_puerto'] = 1433;
$config['subclass_prefix'] = 'MY_';
$config['log_threshold'] = 0; // O el número que tengas, pero que exista la línea
$config['log_path'] = '';