<?php
$archivo = \PhpOffice\PhpSpreadsheet\IOFactory::load('application/views/reportes/plantillas/consecutivos_detalle.xlsx');

$hoja = $archivo->getActiveSheet();
$fila = 6;
$fecha_actual = date('Y-m-d');

$consecutivos = $this->contabilidad_model->obtener('comprobantes_contables_tareas_detalle', ['comprobante_contable_tarea_id' => $comprobante_contable_tarea_id]);

$hoja->setCellValue("D3", PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($fecha_actual));

foreach($consecutivos as $consecutivo) {
    $hoja->setCellValue("A$fila", $consecutivo->consecutivo_numero);
    $hoja->setCellValue("B$fila", ($consecutivo->consecutivo_existe) ? 'Sí' : 'No');
    $hoja->setCellValue("C$fila", ($consecutivo->comprobante_existe) ? 'Sí' : 'No' );
    $hoja->setCellValue("D$fila", ($consecutivo->comprobante_coincide) ? 'Sí' : 'No');
    $hoja->setCellValue("E$fila", $consecutivo->cantidad_soportes);

    $fila++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Consecutivos_detalle_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($archivo, 'Xlsx');
$writer->save('php://output');
exit;