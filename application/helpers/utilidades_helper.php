<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Verifica que un producto se encuentre
 * en el carrito de compras
 */
function buscar_item_carrito($id) {
    $CI =& get_instance();
    $encontrado = [];

    // Se recorre cada ítem
    foreach ($CI->cart->contents() as $item) {
        // Si se encuentra, se retorna el ítem
        if($item['id'] == $id) $encontrado = $item;
    }

    return $encontrado;
}

function convertir_numero_a_texto($numero) {
    $unidad = [
        '', 'uno', 'dos', 'tres', 'cuatro', 'cinco',
        'seis', 'siete', 'ocho', 'nueve', 'diez',
        'once', 'doce', 'trece', 'catorce', 'quince',
        'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve',
        'veinte'
    ];

    $decenas = [
        '', '', 'veinte', 'treinta', 'cuarenta', 'cincuenta',
        'sesenta', 'setenta', 'ochenta', 'noventa'
    ];
    
    $centenas = [
        '', 'cien', 'doscientos', 'trescientos', 'cuatrocientos',
        'quinientos', 'seiscientos', 'setecientos', 'ochocientos',
        'novecientos'
    ];

    if ($numero == 0) return 'cero';

    if ($numero < 21) {
        return $unidad[$numero];
    } elseif ($numero < 100) {
        $d = intval($numero / 10);
        $u = $numero % 10;
        if ($u == 0) return $decenas[$d];
        if ($d == 2) return 'veinti' . $unidad[$u];
        return $decenas[$d] . ' y ' . $unidad[$u];
    } elseif ($numero < 1000) {
        $c = intval($numero / 100);
        $r = $numero % 100;
        if ($numero == 100) return 'cien';
        return $centenas[$c] . ($r > 0 ? ' ' . convertir_numero_a_texto($r) : '');
    } elseif ($numero < 1000000) {
        $miles = intval($numero / 1000);
        $r = $numero % 1000;
        $txtMiles = ($miles == 1 ? 'mil' : convertir_numero_a_texto($miles) . ' mil');
        return $txtMiles . ($r > 0 ? ' ' . convertir_numero_a_texto($r) : '');
    } else {
        $millones = intval($numero / 1000000);
        $r = $numero % 1000000;
        $txtMillones = ($millones == 1 ? 'un millón' : convertir_numero_a_texto($millones) . ' millones');
        return $txtMillones . ($r > 0 ? ' ' . convertir_numero_a_texto($r) : '');
    }
}

function cubrir_correo($email) {
    // Dividir el correo en dos partes: antes y después de "@"
    list($usuario, $dominio) = explode('@', $email);

    // Calcular cuántos caracteres ocultar en la parte del usuario
    $longitudUsuario = strlen($usuario);
    $ocultarDesde = ceil($longitudUsuario / 3);

    // Reemplazar la parte seleccionada del usuario con asteriscos
    $usuarioOculto = substr_replace($usuario, str_repeat('*', $longitudUsuario - $ocultarDesde), $ocultarDesde);

    // Reconstruir el correo con la parte cubierta
    return $usuarioOculto . '@' . $dominio;
}

/**
 * Formatear números de teléfono para adicionar el prefijo del país
 */
function formatear_numero_telefono($numero) {
    // Eliminar espacios, guiones, paréntesis
    $numero = preg_replace('/[^0-9]/', '', $numero);
    
    // Si no tiene código de país, se agrega
    if (strlen($numero) == 10) $numero = "57$numero" ; // Colombia por defecto
    
    return $numero;
}

function formatear_tamanio($bytes) {
    if ($bytes == 0) return "0 B";
    
    $unidades = ['B', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes, 1024));
    
    return round($bytes / pow(1024, $i), 2) . ' ' . $unidades[$i];
}

function formato_precio($valor) {
    return "$".number_format($valor, 0, ',', '.');
}

function generar_codigo_OTP($longitud = 6) {
    // Definir los caracteres que pueden aparecer en el código OTP
    $caracteres = '1234567890'; // Solo números para un OTP de dígitos
    $codigoOTP = '';
    
    // Generar un código aleatorio de la longitud deseada
    for ($i = 0; $i < $longitud; $i++) $codigoOTP .= $caracteres[random_int(0, strlen($caracteres) - 1)];
    
    return $codigoOTP;
}

/**
 * Para la API del ERP, genera un rango de fechas para
 * los filtros de parámetros
 *
 * @param string $campo
 * @param integer $minutos
 * @return string
 */
function generar_filtro_ultimos_minutos($campo, $minutos = 5) {
    // Inicialización de fechas
    $fecha_inicio = new DateTime();
    $fecha_final = new DateTime();
    
    // Se restan 5 minutos a la fecha de inicio
    $fecha_inicio->modify("-{$minutos} minutes");

    // La hora de inicio se configura en el segundo 00
    $fecha_inicio->setTime(
        intval($fecha_inicio->format('H')),
        intval($fecha_inicio->format('i')),
        0
    );

    // Establecer el segundo 59 para la fecha final
    $fecha_final->setTime(
        intval($fecha_final->format('H')),
        intval($fecha_final->format('i')),
        59
    );
    
    // Se establece el formato de las fechas
    $fecha_inicio_convertido = $fecha_inicio->format('Y-m-d\TH:i:s');
    $fecha_fin_convertido = $fecha_final->format('Y-m-d\TH:i:s');
    
    // Se retorna el string
    return "$campo>=''{$fecha_inicio_convertido}'' and $campo<=''{$fecha_fin_convertido}''";
}

function generar_llave_integridad($datos) {
    return hash("sha256", "{$datos[0]}{$datos[1]}{$datos[2]}{$datos[3]}");
}

function generar_radicado() {
    return date('Ymd').str_pad(rand(1, 9999), 3, '0', STR_PAD_LEFT);
}

function generar_token() {
    return substr(bin2hex(random_bytes(25)), 0, 10);
}

function mostrar_mensajes_estados_wompi($estado) {
    $pedido_completo = false;
    
    switch ($estado) {
        case 'APPROVED':
            $pedido_completo = true;
            $asunto = 'Pedido completado';
            $asunto_factura = 'Pago completado';
            $titulo = 'El pedido fue recibido exitosamente';
            $subtitulo = '¡Muchas gracias por comprar en la tienda de Repuestos Simón Bolívar! Acabamos de recibir tu pago';
        break;
    
        case 'DECLINED':
            $asunto = 'Pedido no completado';
            $asunto_factura = 'Pago no completado';
            $titulo = 'No recibimos tu pago';
            $subtitulo = '¡Lo sentimos! La entidad bancaria rechazó tu pago';
        break;
    
        case 'VOIDED':
            $asunto = 'Pedido no completado';
            $asunto_factura = 'Pago no completado';
            $titulo = 'No recibimos tu pago';
            $subtitulo = '¡Lo sentimos! La entidad bancaria rechazó tu pago';
        break;
    
        case 'ERROR':
            $asunto = 'Pedido no completado';
            $asunto_factura = 'Pago no completado';
            $titulo = 'No recibimos tu pago';
            $subtitulo = '¡Lo sentimos! Ocurrió un error al procesar el pago';
        break;
    }

    return [
        'pedido_completo' => $pedido_completo,
        'asunto' => $asunto,
        'asunto_factura' => $asunto_factura,
        'titulo' => $titulo,
        'subtitulo' => $subtitulo,
    ];
}

function obtener_url_foto($ruta = '', $extensiones = ['jpg', 'jpeg', 'png', 'gif', 'webp']) {
    foreach ($extensiones as $extension) {
        if (verificar_existencia_foto("$ruta.$extension")) return "$ruta.$extension";
    }
    return false;
}

function obtener_numero_recibo_caja($recibo) {
    // Cuando es recibo con comprobante
    if($recibo->recibo_tipo_id == 3) {
        $resultado_movimientos = json_decode(obtener_movimientos_contables_api([
            'numero_documento' => $recibo->documento_numero,
            'notas' => "PAGA FACTURAS CONSIGNACION DEL DIA $recibo->dia_consignacion/$recibo->mes_consignacion/$recibo->anio_consignacion",
        ]));
    } else {
        $resultado_movimientos = json_decode(obtener_movimientos_contables_api([
            'numero_documento' => $recibo->documento_numero,
            'fecha' => "{$recibo->anio}-{$recibo->mes}-{$recibo->dia}",
            'notas' => ($recibo->id >= 280) ? "Recibo $recibo->id" : 'Recibo cargado desde la página web por el cliente',
            'estado' => 1,
        ]));
    }

    if($resultado_movimientos->codigo == 0) {
        $movimientos = $resultado_movimientos->detalle->Table;
        $consecutivo = str_pad($movimientos[0]->f350_consec_docto, 8, '0', STR_PAD_LEFT);

        // Si se encontraron movimientos asociados al recibo
        return ($resultado_movimientos->codigo == 0) ? "{$movimientos[0]->f350_id_co}-{$movimientos[0]->f350_id_tipo_docto}-{$consecutivo}" : null ;
    }

    return null;
}

/**
 * Con los datos del recibo, devuelve la carpeta del archivo digital
 * donde debe almacenarse el comprobante y sus soportes, a la hora de
 * crear el documento contable
 *
 * @param object $recibo
 * @return void
 */
function obtener_ruta_documento_contable($recibo) {
    $CI =& get_instance();

    $comprobante_contable = $CI->configuracion_model->obtener('comprobantes_contables_tipos', ['id' => 1]);
    $sede = $CI->configuracion_model->obtener('centros_operacion', ['codigo' => 100]);
    $periodo = $CI->configuracion_model->obtener('periodos', ['mes' => $recibo->mes_consignacion]);
    
    return "{$CI->config->item('ruta_archivo_digitalizado')}/{$comprobante_contable->ruta}/{$recibo->anio_consignacion}/{$sede->ruta}/{$periodo->nombre_comprobante_contable}";
}

function obtener_segmentos_url($url) {
    // Obtiene de la url todos los segmentos menos el principal(dominio)
    $path = parse_url($url, PHP_URL_PATH);
    // Separa los segmentos de la url
    $segmentos = explode('/', trim($path, '/'));

    return $segmentos;
}

function obtener_tipo_archivo($extension) {
    $tipos = [
        'pdf' => 'Documento PDF',
        'doc' => 'Documento Word',
        'docx' => 'Documento Word',
        'xls' => 'Hoja de cálculo Excel',
        'xlsx' => 'Hoja de cálculo Excel',
        'jpg' => 'Imagen JPEG',
        'jpeg' => 'Imagen JPEG',
        'png' => 'Imagen PNG',
        'txt' => 'Archivo de texto',
        'zip' => 'Archivo comprimido',
        'rar' => 'Archivo comprimido'
    ];
    
    return $tipos[$extension] ?? 'Archivo ' . strtoupper($extension);
}

function url_fotos($marca, $referencia) {
    $CI =& get_instance();

    $marca_filtrada = trim($marca);
    $referencia_filtrada = str_replace('/', '_', trim($referencia));

    $url_foto_producto = obtener_url_foto("{$CI->config->item('url_fotos')}$marca_filtrada/$referencia_filtrada");
    $url_foto_generica = "{$CI->config->item('url_fotos')}producto_generico.webp";
   
    return (verificar_existencia_foto($url_foto_producto)) ? $url_foto_producto : $url_foto_generica;
}

/**
 * Valida que la imagen se encuentre
 *
 * @param string $url
 * @return void
 */
function verificar_existencia_foto($url) {
    $directorio = $_SERVER['DOCUMENT_ROOT'] . parse_url($url, PHP_URL_PATH);

    return (file_exists($directorio) && is_file($directorio)) ? $url : false;
}