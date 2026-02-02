<?php
use setasign\Fpdi\Fpdi;

// Crear una instancia de FPDI
$pdf = new FPDI('P', 'mm', 'LEGAL');

$solicitud = $this->clientes_model->obtener("clientes_solicitudes_credito", ["id" => $solicitud_id]);
$clientes_socios_accionistas = $this->clientes_model->obtener("clientes_solicitudes_credito_detalle", ["solicitud_id" => $solicitud_id, "formulario_tipo" => 1]);
$beneficiarios_clientes_socios_accionistas = $this->clientes_model->obtener("clientes_solicitudes_credito_detalle", ["solicitud_id" => $solicitud_id, "formulario_tipo" => 2]);
$personas_autorizadas = $this->clientes_model->obtener("clientes_solicitudes_credito_detalle", ["solicitud_id" => $solicitud_id, "formulario_tipo" => 3]);

$fecha =  date(" d / m / Y", strtotime($solicitud->fecha_creacion));
$fecha_expedicion =  date("d  /   m   / Y", strtotime($solicitud->fecha_expedicion));

// Cargar el archivo PDF como plantilla
$pdf->AddPage();
$pageCount = $pdf->setSourceFile('application/views/reportes/plantillas/solicitud_credito.pdf');
$tplIdx = $pdf->importPage(1);

// Usar la página del archivo como plantilla
$pdf->useTemplate($tplIdx, 10, 10, 200);

$pdf->SetFont('Arial', '', 7);
$pdf->SetXY(29, 43);
$pdf->Write(0, ($solicitud->nueva) ? "X": "");

$pdf->SetXY(45.2, 43);
$pdf->Write(0, ($solicitud->nueva) ? "": "X");

$pdf->SetXY(74, 43.6);
$pdf->Write(0, $fecha);

// Persona natural
if ($solicitud->persona_tipo_id == 1) {
    $pdf->SetXY(49, 61);
    $pdf->Write(0, substr(utf8_decode($solicitud->primer_apellido), 0, 50));

    $pdf->SetXY(98, 61);
    $pdf->Write(0, substr(utf8_decode($solicitud->segundo_apellido), 0, 50));

    $pdf->SetXY(138, 61);
    $pdf->Write(0, substr(utf8_decode($solicitud->nombre), 0, 50));

    $pdf->SetXY(37, 65.2);
    $pdf->Write(0, utf8_decode($solicitud->documento_numero));

    $pdf->SetXY(120, 65.2);
    $pdf->Write(0, utf8_decode($fecha_expedicion));

    $pdf->SetXY(164, 65.2);
    $pdf->Write(0, utf8_decode($solicitud->telefono));

    $pdf->SetXY(42, 69.6);
    $pdf->Write(0, utf8_decode($solicitud->direccion));

    $pdf->SetXY(123, 69.6);
    $pdf->Write(0, utf8_decode($solicitud->municipio));

    $pdf->SetXY(168, 69.6);
    $pdf->Write(0, utf8_decode($solicitud->departamento));

    $pdf->SetXY(50, 73.6);
    $pdf->Write(0, utf8_decode($solicitud->email));

    $pdf->SetXY(67, 77.8);
    $pdf->Write(0, utf8_decode($solicitud->email_factura_electronica));
}

$pdf->SetXY(170, 77.8);
$pdf->Write(0, utf8_decode($solicitud->cantidad_vehiculos));

// Persona jurídica
if ($solicitud->persona_tipo_id == 2) {
    $pdf->SetXY(45, 96.8);
    $pdf->Write(0, utf8_decode($solicitud->razon_social));

    $pdf->SetXY(34, 100.8);
    $pdf->Write(0, utf8_decode($solicitud->documento_numero));

    $pdf->SetXY(145, 100.8);
    $pdf->Write(0, utf8_decode($solicitud->telefono));

    $pdf->SetXY(52, 105);
    $pdf->Write(0, utf8_decode($solicitud->direccion));

    $pdf->SetXY(112, 105);
    $pdf->Write(0, utf8_decode($solicitud->municipio));

    $pdf->SetXY(168, 105);
    $pdf->Write(0, utf8_decode($solicitud->departamento));

    $pdf->SetXY(67, 109);
    $pdf->Write(0, utf8_decode($solicitud->email_factura_electronica));

    $pdf->SetXY(40, 117.8);
    $pdf->Write(0, utf8_decode($solicitud->representante_legal));

    $pdf->SetXY(104, 117.8);
    $pdf->Write(0, utf8_decode($solicitud->representante_legal_documento_numero));

    $pdf->SetXY(167, 117.8);
    $pdf->Write(0, utf8_decode($fecha_expedicion));

    $pdf->SetXY(40, 121.5);
    $pdf->Write(0, $solicitud->representante_legal_correo);

    $pdf->SetXY(145, 121.5);
    $pdf->Write(0, $solicitud->celular);
}

$pdf->SetXY(52, 142);
$pdf->Write(0, utf8_decode($solicitud->tesoreria_nombre));

$pdf->SetXY(105, 142);
$pdf->Write(0, utf8_decode($solicitud->tesoreria_email));

$pdf->SetXY(155, 142);
$pdf->Write(0, utf8_decode($solicitud->tesoreria_telefono));

$pdf->SetXY(174, 142);
$pdf->Write(0, utf8_decode($solicitud->tesoreria_celular));

$pdf->SetXY(52, 145.5);
$pdf->Write(0, utf8_decode($solicitud->comercial_nombre));

$pdf->SetXY(105, 145.5);
$pdf->Write(0, utf8_decode($solicitud->comercial_email));

$pdf->SetXY(155, 145.5);
$pdf->Write(0, utf8_decode($solicitud->comercial_telefono));

$pdf->SetXY(174, 145.5);
$pdf->Write(0, utf8_decode($solicitud->comercial_celular));

$pdf->SetXY(52, 149);
$pdf->Write(0, utf8_decode($solicitud->contabilidad_nombre));

$pdf->SetXY(105, 149);
$pdf->Write(0, utf8_decode($solicitud->contabilidad_email));

$pdf->SetXY(155, 149);
$pdf->Write(0, utf8_decode($solicitud->contabilidad_telefono));

$pdf->SetXY(174, 149);
$pdf->Write(0, utf8_decode($solicitud->contabilidad_celular));

$pdf->SetXY(40, 167.8);
$pdf->Write(0, utf8_decode($solicitud->referencia_comercial_entidad1));

$pdf->SetXY(98, 167.8);
$pdf->Write(0, $solicitud->referencia_comercial_cel1);

$pdf->SetXY(128, 167.8);
$pdf->Write(0, utf8_decode(substr($solicitud->referencia_comercial_direccion1, 0, 39)));

$pdf->SetXY(40, 171.5);
$pdf->Write(0, utf8_decode($solicitud->referencia_comercial_entidad2));

$pdf->SetXY(98, 171.5);
$pdf->Write(0, $solicitud->referencia_comercial_cel2);

$pdf->SetXY(128, 171.5);
$pdf->Write(0, utf8_decode(substr($solicitud->referencia_comercial_direccion2, 0, 39)));

$coordenada = 196.6;
foreach ($personas_autorizadas as $registro) {
    $pdf->SetXY(106, $coordenada);
    $pdf->Write(0, utf8_decode($registro->nombre));

    $pdf->SetXY(153, $coordenada);
    $pdf->Write(0, utf8_decode($registro->documento_numero));

    $pdf->SetXY(173, $coordenada);
    $pdf->Write(0, utf8_decode($registro->celular));

    $coordenada = $coordenada + 5;
}

$pdf->SetXY(55, 233);
$pdf->Write(0, utf8_decode($solicitud->vendedor_nombre));

$pdf->AddPage();
$tplIdx = $pdf->importPage(2);

// Usar la página del archivo como plantilla
$pdf->useTemplate($tplIdx, 10, 10, 200);

$pdf->SetXY(129.5, 101.7);
$pdf->Write(0, ($solicitud->recursos_publicos) ? "X": "");

$pdf->SetXY(143, 101.7);
$pdf->Write(0, ($solicitud->recursos_publicos) ? "": "X");

$pdf->SetXY(169, 101.7);
$pdf->Write(0, $solicitud->recursos_publicos_cual);

$pdf->SetXY(129.5, 105.2);
$pdf->Write(0, ($solicitud->poder_publico) ? "X": "");

$pdf->SetXY(143, 105.2);
$pdf->Write(0, ($solicitud->poder_publico) ? "": "X");

$pdf->SetXY(169, 105.2);
$pdf->Write(0, $solicitud->poder_publico_cual);

$pdf->SetXY(129.5, 108.5);
$pdf->Write(0, ($solicitud->reconocimiento_publico) ? "X": "");

$pdf->SetXY(143, 108.5);
$pdf->Write(0, ($solicitud->reconocimiento_publico) ? "": "X");

$pdf->SetXY(169, 108.5);
$pdf->Write(0, $solicitud->reconocimiento_publico_cual);

$pdf->SetXY(129.5, 111.8);
$pdf->Write(0, ($solicitud->persona_expuesta) ? "X": "");

$pdf->SetXY(143, 111.8);
$pdf->Write(0, ($solicitud->persona_expuesta) ? "": "X");

$pdf->SetXY(169, 111.8);
$pdf->Write(0, $solicitud->persona_expuesta_cual);

$pdf->SetXY(62, 121.8);
$pdf->Write(0, ($solicitud->ingresos_mensuales) ? '$'.number_format($solicitud->ingresos_mensuales, 0, ',', '.'): "");

$pdf->SetXY(144, 121.8);
$pdf->Write(0, ($solicitud->egresos_mensuales) ? '$'.number_format($solicitud->egresos_mensuales, 0, ',', '.'): "");

$pdf->SetXY(62, 125.5);
$pdf->Write(0, ($solicitud->activos) ? '$'.number_format($solicitud->activos, 0, ',', '.'): "");

$pdf->SetXY(144, 125.5);
$pdf->Write(0, ($solicitud->pasivos) ? '$'.number_format($solicitud->pasivos, 0, ',', '.'): "");

$pdf->SetXY(62, 128.9);
$pdf->Write(0, ($solicitud->otros_ingresos) ? '$'.number_format($solicitud->otros_ingresos, 0, ',', '.'): "");

$pdf->SetXY(144, 128.9);
$pdf->Write(0, ($solicitud->concepto_otros_ingresos) ? '$'.number_format(floatval($solicitud->concepto_otros_ingresos), 0, ',', '.'): "");

$coordenada = 47.4;
foreach ($clientes_socios_accionistas as $registro) {
    $pdf->SetXY(26.5, $coordenada);
    $pdf->Write(0, utf8_decode($registro->nombre));

    $pdf->SetXY(68, $coordenada);
    $pdf->Write(0, utf8_decode($registro->tipo_identificacion));

    $pdf->SetXY(110, $coordenada);
    $pdf->Write(0, utf8_decode($registro->documento_numero));

    $pdf->SetXY(151, $coordenada);
    $pdf->Write(0, utf8_decode($registro->porcentaje_participacion));

    $coordenada = $coordenada + 4;
}

$coordenada = 76.2;
foreach ($beneficiarios_clientes_socios_accionistas as $registro) {
    $pdf->SetXY(26.5, $coordenada);
    $pdf->Write(0, utf8_decode($registro->nombre));

    $pdf->SetXY(68, $coordenada);
    $pdf->Write(0, utf8_decode($registro->tipo_identificacion));

    $pdf->SetXY(110, $coordenada);
    $pdf->Write(0, utf8_decode($registro->documento_numero));

    $pdf->SetXY(151, $coordenada);
    $pdf->Write(0, utf8_decode($registro->porcentaje_participacion));

    $coordenada = $coordenada + 4;
}

$pdf->Output("F", "archivos/solicitudes_credito/$solicitud_id/Solicitud de crédito.pdf");