<?php
use setasign\Fpdi\Fpdi;

// Crear una instancia de FPDI
$pdf = new FPDI('P', 'mm', 'LEGAL');

$cuenta = $this->proveedores_model->obtener("api_cuentas_por_pagar", ["id" => $id]);

// Cargar el archivo PDF como plantilla
$pdf->AddPage();
$numero_pagina = $pdf->setSourceFile('application/views/reportes/plantillas/comprobante_egreso.pdf');
$plantilla = $pdf->importPage(1);

// Usar la página del archivo como plantilla
$pdf->useTemplate($plantilla, 10, 10, 200);

$pdf->SetFont('Courier', '', 7);

$pdf->SetXY(14, 42);
$pdf->Cell(85, 5, utf8_decode($cuenta->provedor_nombre), 0, 1, 'L', 0);

$pdf->SetXY(103, 42);
$pdf->Cell(65, 5, $cuenta->provedor_nit, 0, 1, 'L', 0);

$pdf->SetXY(172, 42);
$pdf->Cell(25, 5, date("d/m/Y", strtotime($cuenta->fecha)), 0, 1, 'L', 0);

$pdf->SetXY(15, 56);
$pdf->MultiCell(185, 4, utf8_decode("Notas: $cuenta->notas"), 0, 'L');

$pdf->SetXY(14, 72);
// $pdf->Write(0, $cuenta->row_id); // Auxiliar

$pdf->SetXY(34, 70);
$pdf->Cell(8, 6, $cuenta->sede_codigo, 0, 1, 'L', 0); // Centro Operativo

$pdf->SetXY(41, 70);
$pdf->Cell(8, 6, $cuenta->unidad_cruce, 0, 1, 'L', 0); // Unidad cruce

$pdf->SetXY(47, 70);
$pdf->Cell(20, 6, $cuenta->provedor_nit, 0, 1, 'L', 0); // NIT

$pdf->SetXY(117, 70);
$pdf->Cell(25, 6, $cuenta->numero_siesa, 0, 1, 'L', 0); // Número Siesa

$pdf->SetXY(142, 70);
$pdf->Cell(30, 4, formato_precio($cuenta->valor_documento), 0, 0, 'R'); // 

$pdf->SetXY(172, 70);
$pdf->Cell(30, 4, formato_precio($cuenta->valor_abonos), 0, 0, 'R'); //

$pdf->SetXY(142, 76);
$pdf->Cell(30, 4, formato_precio($cuenta->valor_documento), 0, 0, 'R'); // 

$pdf->SetXY(172, 76);
$pdf->Cell(30, 4, formato_precio($cuenta->valor_abonos), 0, 0, 'R'); //

$pdf->Output("D", "Comprobante de egreso.pdf");