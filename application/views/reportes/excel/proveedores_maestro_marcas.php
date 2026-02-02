<?php
$archivo = \PhpOffice\PhpSpreadsheet\IOFactory::load('application/views/reportes/plantillas/proveedores_maestro.xlsx');
$hoja = $archivo->getActiveSheet();
$fila = 6;
$fecha_actual = date('Y-m-d');

$proveedores_marcas = $this->proveedores_model->obtener('proveedores_marcas');
$final_borde = $fila + count($proveedores_marcas) - 1;

$estilo_borde = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
];

$fuente_letra = [
    'font' => [
        'name' => 'Arial',
        'size' => 10
    ]
];

$hoja->getStyle("A$fila:D$final_borde")->applyFromArray($estilo_borde);
$hoja->getStyle("A$fila:D$final_borde")->applyFromArray($fuente_letra);
$hoja->getStyle("A$fila:D$final_borde")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

$hoja->setCellValue("D3", PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($fecha_actual));

foreach ($proveedores_marcas as $proveedor_marca) {
    $hoja->setCellValue("A$fila", $proveedor_marca->marca_codigo);
    $hoja->setCellValue("B$fila", $proveedor_marca->marca_nombre);
    $hoja->setCellValue("C$fila", $proveedor_marca->proveedor_nit);
    $hoja->setCellValue("D$fila", $proveedor_marca->proveedor_nombre);
    $fila++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Maestro_de_proveedores.xlsx"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($archivo, 'Xlsx');
$writer->save('php://output');
exit;