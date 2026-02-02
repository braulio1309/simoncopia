<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function enviar_email_asignacion_credito($id) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['clientes_model', 'email_model']);

    $solicitud = $CI->clientes_model->obtener("clientes_solicitudes_credito", ["id" => $id]);
    $url = site_url("clientes/credito/ver/$id");

    $datos = [
        'pedido_completo' => '',
        'id' => $solicitud->id,
        'asunto' => 'Solicitud de crédito asignada',
        'cuerpo' => [
            'titulo' => "
                Solicitud asignada
            ",
            'subtitulo' => "
                Te ha llegado una solicitud de crédito a nombre de <b>$solicitud->razon_social</b>. Por favor, revísalo para darle trámite a este lo antes posible.<br><br>
                Haz <a href='$url' style='color: #ffd400; text-decoration: none;'>clic aquí</a> para ingresar a la solicitud
            ",
        ],
        'destinatarios' => $solicitud->email_usuario_asignado,
    ];

    $CI->email_model->enviar($datos);
}

function enviar_email_certificado_retencion($id) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $certificado = $CI->clientes_model->obtener('clientes_retenciones_detalle', ['id' => $id]);
    $contactos = $CI->configuracion_model->obtener('contactos', ['nit' => $certificado->nit]);

    $destinatarios = [];

    foreach ($contactos as $contacto) {
        if (!empty($contacto->email)) {
            $destinatarios[] = $contacto->email;
        }
    }

    $url = site_url('sesion');
    
    $datos = [
        'pedido_completo' => '',
        'id' => $certificado->id,
        'asunto' => "Se ha subido un certificado de retención $certificado->tipo_retencion",
        'cuerpo' => [
            'titulo' => '¡La subida del certificado ha sido exitosa!',
            'subtitulo' => "
                Hola, $certificado->razon_social, se ha subido un certificado correspondiente a la retención $certificado->tipo_retencion.
            ",
        ],
        'destinatarios' => $destinatarios,
    ];

    return $CI->email_model->enviar($datos);
}

function enviar_email_masivo_notificacion_certificados($cliente) {
    $CI = get_instance();
    $CI->load->model(['email_model', 'configuracion_model']);
    $CI->load->helper('url');

    // Validación básica
    if (empty($cliente) || empty($cliente->nit)) return ['exito' => false, 'error' => 'Cliente no válido'];

    // Buscar contactos por NIT
    $contactos = $CI->configuracion_model->obtener('contactos', ['nit' => $cliente->nit]);
    if (!$contactos) return ['exito' => false, 'mensaje' => 'Sin datos de contacto'];

    // Filtrar correos válidos
    $destinatarios = [];
    foreach ($contactos as $contacto) {
        if (!empty($contacto->email) && filter_var($contacto->email, FILTER_VALIDATE_EMAIL)) {
            $destinatarios[] = $contacto->email;
        }
    }

    if (empty($destinatarios)) return ['exito' => false, 'error' => 'Sin emails de contacto'];

    // Fecha en español
    setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');
    $fecha = strftime('%d de %B de %Y');

    // URL del portal
    $url = site_url("clientes/certificados_tributarios/crear/$cliente->nit");

    // Preparar imagen en base64
    $imagen_firma = '';
    $ruta_firma = FCPATH . 'archivos/general/firma_contadora.png';

    // Debug: verificar si el archivo existe
    if (file_exists($ruta_firma)) {
        $contenido_imagen = file_get_contents($ruta_firma);
        if ($contenido_imagen !== false) {
            $imagen_base64 = base64_encode($contenido_imagen);
            $imagen_firma = "<img src='data:image/png;base64,{$imagen_base64}' style='max-width:200px;height:auto;' alt='Firma'><br>";
        }
    }

    // Construcción del correo
    $datos = [
        'pedido_completo' => '',
        'id' => $cliente->nit,

        'asunto' => "Solicitud Certificado de Retención en la fuente, Reteiva y Reteica año $cliente->anio",

        'cuerpo' => [
            // TÍTULO
            'titulo' => "SOLICITUD CERTIFICADOS DE RETENCIÓN $cliente->anio A NOMBRE DE MUELLES Y FRENOS SIMÓN BOLÍVAR NIT 900296641-6",

            // TODO el contenido va en subtitulo
            'subtitulo' => "
                Itagüí, $fecha<br><br>

                Señores: $cliente->razon_social<br>
                NIT: $cliente->nit<br>
                <b>Asunto:</b> Solicitud Certificado de Retención en la fuente, Reteiva y Reteica año $cliente->anio<br><br>

                Apreciados Señores,<br><br>

                De acuerdo con lo establecido en el artículo 381 del estatuto tributario y tratándose del asunto en referencia,
                solicitamos de manera cordial expedir los certificados de retención en la fuente, RETEIVA y RETEICA
                por el año gravable $cliente->anio generados a nombre de
                MUELLES Y FRENOS SIMÓN BOLÍVAR NIT 900296641.<br><br>

                Los valores registrados en nuestra contabilidad son:<br><br>

                <b>RETENCIÓN EN LA FUENTE:</b> " . formato_precio($cliente->valor_retencion_fuente) . "<br>
                <b>RETEIVA:</b> " . formato_precio($cliente->valor_retencion_iva) . "<br>
                <b>RETEICA:</b> " . formato_precio($cliente->valor_retencion_ica) . "<br><br>

                Por favor <a href='$url' style='color: #ffd400; text-decoration: none;'>cargar los certificados en la plataforma</a> o enviarlos a los correos
                cartera3@repuestossimonbolivar.com con copia a aux.contable@muellesyfrenossb.com.
                En caso tal de presentar diferencia en los valores expuestos por nosotros,
                por favor enviar también el auxiliar en Excel para realizar la respectiva conciliación.<br><br>

                Agradecemos su colaboración para suministrarnos dicha información en la mayor brevedad posible.<br><br>

                Recuerde que según el Decreto 2229 de diciembre 22 de 2023, en el 2025 los agentes de retención tendrán plazo
                para expedir certificados de retención del año gravable 2024 hasta el 31 de marzo
                (último día hábil de marzo).<br><br>

                Atentamente;<br><br>

                {$imagen_firma}
                Nora Isabel Castro Vargas<br>
                Contadora<br><br>
                <b>MUELLES Y FRENOS SIMÓN BOLÍVAR</b>
            ",
        ],

        'destinatarios' => $destinatarios,
    ];

    // Envío
    $envio = $CI->email_model->enviar($datos);

    if (!$envio) return ['exito' => false, 'mensaje' => 'Error al enviar el email'];

    return ['exito' => true];
}

function enviar_email_clave_cambiada($id) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $usuario = $CI->configuracion_model->obtener('usuarios', ['id' => $id]);
    $url = site_url('sesion');
    
    $datos = [
        'pedido_completo' => '',
        'id' => $usuario->id,
        'asunto' => 'Clave de acceso actualizada',
        'cuerpo' => [
            'titulo' => '¡Tu clave ha sido cambiada con éxito!',
            'subtitulo' => "
                Hola, $usuario->nombres. A partir de ahora tienes acceso a grandes descuentos en nuestra tienda.<br><br>
                <hr>
                Recuerda que tu usuario de ingreso es <b style='color: #ffd400;'>$usuario->login</b>
                <hr>
                Ahora puedes <a href='$url' style='color: #ffd400; text-decoration: none;'>iniciar sesión haciendo clic aquí</a>
            ",
        ],
        'destinatarios' => $usuario->email,
    ];

    return $CI->email_model->enviar($datos);
}

function enviar_email_codigo_otp($id) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $codigo = $CI->configuracion_model->obtener('codigo_temporal', ['ct.id' => $id]);

    $url = site_url('sesion');
    
    $datos = [
        'pedido_completo' => '',
        'id' => $codigo->id,
        'asunto' => 'Código de verificación',
        'cuerpo' => [
            'titulo' => $codigo->codigo,
            'subtitulo' => "
                Hola, $codigo->nombres. Este es el código de validación que solicitaste.<br>
            ",
        ],
        'destinatarios' => $codigo->email,
    ];

    return $CI->email_model->enviar($datos);
}

function enviar_email_pedido($recibo) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $wompi = json_decode($recibo->wompi_datos, true);

    // Dependiendo del estado de la transacción, trae los mensajes
    $mensajes_estado_wompi = mostrar_mensajes_estados_wompi($wompi['status']);
    
    $datos = [
        'pedido_completo' => $mensajes_estado_wompi['pedido_completo'],
        'id' => $recibo->id,
        'asunto' => $mensajes_estado_wompi['asunto'],
        'cuerpo' => [
            'titulo' => $mensajes_estado_wompi['titulo'],
            'subtitulo' => $mensajes_estado_wompi['subtitulo'],
        ],
        'destinatarios' => $recibo->email,
    ];

    $CI->email_model->enviar($datos);
}

function enviar_email_factura_wompi($recibo) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $wompi = json_decode($recibo->wompi_datos, true);

    $url = site_url('clientes');
    $url_recibo = site_url("reportes/pdf/recibo/$recibo->token");

    // Dependiendo del estado de la transacción, trae los mensajes
    $mensajes_estado_wompi = mostrar_mensajes_estados_wompi($wompi['status']);
    
    $datos = [
        'pedido_completo' => $mensajes_estado_wompi['pedido_completo'],
        'id' => $recibo->id,
        'asunto' => 'Transacción exitosa',
        'cuerpo' => [
            'titulo' => 'Nos complace informarte que tu pago ha sido aprobado con éxito.',
            'subtitulo' => "Si tienes alguna pregunta o necesitas más información, no dudes en contactarnos. Estamos aquí para ayudarte en todo momento. ¡Gracias por confiar en nosotros!<br><br>

            Descarga tu recibo de pago en <a href='$url_recibo' style='color: #ffd400; text-decoration: none;'>este enlace</a>.<br><br>

            Ahora puedes consultar el saldo de tu cartera actualizado en <a href='$url' style='color: #ffd400; text-decoration: none;'>www.repuestossimonbolivar.com</a>.<br><br>
            ",
        ],
        'destinatarios' => [$recibo->email, 'publicidad@repuestossimonbolivar.com'],
    ];

    $CI->email_model->enviar($datos);
}

function enviar_email_factura_wompi_comprobante($recibo) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $url = site_url('clientes');
    $url_recibo = site_url("reportes/pdf/recibo/$recibo->token");

    $datos = [
        'pedido_completo' => true,
        'id' => $recibo->id,
        'asunto' => 'Pago aprobado',
        'cuerpo' => [
            'titulo' => 'El soporte de pago que ingresaste en la página web, ya fue validado, aprobado y abonado en tu cartera.',
            'subtitulo' =>  "Estamos muy contentos de que nos hayas elegido para tus necesidades en repuestos para tu vehículo. Nuestro equipo está trabajando duro para brindarte la mejor experiencia posible.<br><br>

            Descarga tu recibo de pago en <a href='$url_recibo' style='color: #ffd400; text-decoration: none;'>este enlace</a>.<br><br>

            También te queremos recordar que puedes hacer el pago directo a través de nuestra página web <a href='$url' style='color: #ffd400; text-decoration: none;'>www.repuestossimonbolivar.com</a>.
            .",
        ],
        'destinatarios' => $recibo->email,
    ];

    $CI->email_model->enviar($datos);
}

function enviar_email_usuario_nuevo($id) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $usuario = $CI->configuracion_model->obtener('usuarios', ['id' => $id]);
    $url = site_url('sesion');
    
    $datos = [
        'pedido_completo' => '',
        'id' => $usuario->id,
        'asunto' => 'Registro exitoso',
        'cuerpo' => [
            'titulo' => '¡Tu registro ha sido exitoso!',
            'subtitulo' => "
                Hola, $usuario->nombres. A partir de ahora tienes acceso a grandes descuentos en nuestra tienda.<br><br>
                Ahora puedes <a href='$url' style='color: #ffd400; text-decoration: none;'>iniciar sesión haciendo clic aquí</a>
            ",
        ],
        'destinatarios' => $usuario->email,
    ];

    return $CI->email_model->enviar($datos);
}

function enviar_email_solicitud_credito($id) {
    // Se obtiene una referencia del objeto Controlador
    $instancia = get_instance();

    $instancia->load->model(['email_model']);

    $solicitud = $instancia->clientes_model->obtener('clientes_solicitudes_credito', ['id' => $id]);
    $url = site_url('');

    $preferencia_enlace = ($solicitud->preferencia_enlace == 1) ? "Whatsapp" : "correo electrónico";

    $datos = [
        'pedido_completo' => '',
        'id' => $solicitud->id,
        'asunto' => 'Solicitud de crédito recibida',
        'cuerpo' => [
            'titulo' => '¡Hemos recibido tu solicitud de crédito!',
            'subtitulo' => "
                Hola, $solicitud->nombre. Gracias por proporcionar tus datos. A partir de este momento vamos a revisar tu solicitud y te contactaremos a la mayor brevedad posible.<br><br>
                Nota: El cliente desea recibir la firma digital mediante $preferencia_enlace.
            ",
        ],
        'destinatarios' => [$solicitud->email, 'analistacartera@repuestossimonbolivar.com', 'carteramyfsimonbolivar@gmail.com'],
        'adjuntos' => false,
    ];

    return $instancia->email_model->enviar($datos);
}

function enviar_email_solicitud_garantia($id) {
    // Se obtiene una referencia del objeto Controlador
    $instancia = get_instance();

    $instancia->load->model(['email_model']);

    $solicitud = $instancia->logistica_model->obtener('productos_solicitudes_garantia', ['id' => $id]);

    $datos = [
        'pedido_completo' => '',
        'id' => $solicitud->id,
        'asunto' => 'Solicitud de garantía recibida',
        'cuerpo' => [
            'titulo' => 'Hemos recibido tu solicitud de garantía',
            'subtitulo' => "
                $solicitud->cliente_razon_social,<br><br>
                Hemos recibido la solicitud de garantía con radicado $solicitud->radicado. Gracias por proporcionar tus datos. A partir de este momento vamos a revisar tu solicitud y te contactaremos a la mayor brevedad posible.<br><br>
            ",
        ],
        'destinatarios' => [$solicitud->solicitante_email, 'coordinadorlogistico@repuestossimonbolivar.com'],
    ];

    return $instancia->email_model->enviar($datos);
}