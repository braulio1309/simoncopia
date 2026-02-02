<?php
use setasign\Fpdi\Fpdi;

$tercero = $this->configuracion_model->obtener('terceros', ['nit' => $numero_documento]);
$movimientos_contables = $this->proveedores_model->obtener('provedores_movimientos_contables', ['numero_documento' => $numero_documento, 'filtro_retenciones' => true]);
$valor_total_base = 0;
$valor_total_retenido = 0;

// Crear una instancia de FPDI
$pdf = new FPDI('P', 'mm', 'LETTER');

$pdf->AddPage();
$numero_pagina = $pdf->setSourceFile('application/views/reportes/plantillas/certificado_retenciones.pdf');
$plantilla = $pdf->importPage(1);

// Usar la página del archivo como plantilla
$pdf->useTemplate($plantilla, 10, 10, 200);

$pdf->SetFont('Courier', '', 8);
$pdf->SetXY(14, 42);

// Título
$pdf->SetXY(14, 50);
$pdf->Cell(190, 5, utf8_decode("PERÍODO ENERO-$anio A DICIEMBRE-$anio"), 0, 1, 'C', 0);

// Datos del tercero
$pdf->SetXY(16, 68);
$pdf->Cell(190, 4, utf8_decode($tercero->f200_razon_social), 0, 1, 'L', 0);
$pdf->SetXY(24, 72);
$pdf->Cell(100, 5, utf8_decode($tercero->f200_nit), 0, 1, 'L', 0);
$pdf->SetXY(34, 77);
$pdf->Cell(120, 5, utf8_decode($tercero->f015_direccion1), 0, 1, 'L', 0);

$pdf->SetY(115);
$pdf->SetFont('Arial', '', 8);

foreach ($movimientos_contables as $movimiento) {
    $valor_total_base += $movimiento->valor_base;
    $valor_total_retenido += $movimiento->valor_retenido;

    // Detalle
    $pdf->SetX(18);
    $pdf->Cell(115, 5, utf8_decode($movimiento->descripcion), 0, 0, 'L', 0);
    $pdf->Cell(26, 5, formato_precio($movimiento->valor_base), 'B', 0, 'R', 0);
    $pdf->Cell(44, 5, formato_precio($movimiento->valor_retenido), 'B', 0, 'R', 0);
    $pdf->Ln();
}

// Totales
$pdf->SetX(18);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(115, 5, 'TOTAL', 0, 0, 'L', 0);
$pdf->Cell(26, 5, formato_precio($valor_total_base), 0, 0, 'R', 0);
$pdf->Cell(44, 5, formato_precio($valor_total_retenido), 0, 0, 'R', 0);

// Valor en letras
$pdf->SetFont('Courier', '', 8);
$pdf->SetXY(27, 158);
$pdf->Cell(180, 5, strtoupper(convertir_numero_a_texto($valor_total_retenido)." PESO M/CTE ******"), 0, 0, 'L', 0);

// Fecha de generación
$pdf->SetFont('Courier', '', 8);
$pdf->SetXY(49, 187);
$pdf->Cell(150, 5, date('d/m/Y'), 0, 0, 'L', 0);

$pdf->Output("D", "Certificado de retenciones $tercero->f200_razon_social.pdf");