<?php
$archivo = \PhpOffice\PhpSpreadsheet\IOFactory::load('application/views/reportes/plantillas/facturas.xlsx');
$hoja = $archivo->getActiveSheet();
$fila = 6;
$fecha_actual = date('Y-m-d');

// Obtenemos las facturas del cliente pendientes por pagar
$facturas = $this->clientes_model->obtener('clientes_facturas', [
    'numero_documento' => $numero_documento,
    'pendientes' => true,
    'mostrar_estado_cuenta'=> true,
]);

$tercero = $this->clientes_model->obtener('tercero', ['f200_nit' => $numero_documento]);

// Encabezado
$hoja->setCellValue("H1", $tercero->f200_razon_social);
$hoja->setCellValue("H2", "$tercero->f200_nit-$tercero->f200_dv_nit");
$hoja->setCellValue("H3", PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($fecha_actual));

foreach($facturas as $factura) {
    $sucursal = explode(' ', $factura->RazonSocial_Sucursal);
    
    $hoja->setCellValue("A$fila", $factura->centro_operativo);
    $hoja->setCellValue("B$fila", $factura->Nro_Doc_cruce);
    $hoja->setCellValue("C$fila", PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($factura->Fecha_doc_cruce));
    $hoja->setCellValue("D$fila", PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($factura->Fecha_venc));
    $hoja->setCellValue("E$fila", ($factura->dias_vencido > 0) ? $factura->dias_vencido : 0);
    $hoja->setCellValue("F$fila", $factura->ValorAplicado);
    $hoja->setCellValue("G$fila", $factura->valorDoc);
    $hoja->setCellValue("H$fila", $factura->totalCop);
    $hoja->setCellValue("I$fila", substr($sucursal[0], 0, 10));
    $hoja->setCellValue("J$fila", $factura->nombre_homologado);

    $fila++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=$fecha_actual Estado de cuenta.xlsx");
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($archivo, 'Xlsx');
$writer->save('php://output');
exit;