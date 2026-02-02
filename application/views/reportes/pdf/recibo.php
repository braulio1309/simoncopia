<?php
use Fpdf\Fpdf;

$recibo = $this->productos_model->obtener('recibo', ['token' => $token]);
$recibo_cuentas_bancarias = $this->configuracion_model->obtener('recibos_cuentas_bancarias', ['recibo_id' => $recibo->id]);
$numero_recibo_caja = '';
$usuario_creacion = '';
$usuario_aprobacion = '';
$notas = $recibo->comentarios;

// Temporalemte, se actualiza el recibo con el número de recibo de caja de Siesa
$this->productos_model->actualizar('recibos', ['id' => $recibo->id], ['numero_siesa' => obtener_numero_recibo_caja($recibo)]);

$resultado_movimientos = json_decode(obtener_movimientos_contables_api([
    'numero_documento' => $recibo->documento_numero,
    // 'fecha' => "{$recibo->anio}-{$recibo->mes}-{$recibo->dia}",
    'notas' => ($recibo->id >= 280) ? "Recibo $recibo->id" : 'Recibo cargado desde la página web por el cliente',
    'estado' => 1,
]));

// Si se encontraron movimientos asociados al recibo
if($resultado_movimientos->codigo == 0) {
    // Se capturan los datos
    $movimientos = $resultado_movimientos->detalle->Table;
    $consecutivo = str_pad($movimientos[0]->f350_consec_docto, 8, '0', STR_PAD_LEFT);
    $numero_recibo_caja = "{$movimientos[0]->f350_id_tipo_docto}-{$consecutivo}";
    $usuario_creacion = $movimientos[0]->f350_usuario_creacion;
    $usuario_aprobacion = $movimientos[0]->f350_usuario_aprobacion;
    $notas = $movimientos[0]->f350_notas;
}

// Usuario creación
if($recibo->usuario_creacion_id) {
    $usuario_creacion_sistema = $this->configuracion_model->obtener('usuarios', ['id' => $recibo->usuario_creacion_id]);
    $usuario_creacion = $usuario_creacion_sistema->nombre_completo;
}

// Usuario aprobación
if($recibo->usuario_aprobacion_id) {
    $usuario_aprobacion_sistema = $this->configuracion_model->obtener('usuarios', ['id' => $recibo->usuario_creacion_id]);
    $usuario_aprobacion = $usuario_aprobacion_sistema->nombre_completo;
}

$pdf = new FPDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();

$gris = array('r' => '211', 'g' => '211', 'b' => '211');

// // Parámetros adicionales
// $titulo = utf8_decode("$equipo->Nombre - Hoja de vida");
// $pdf->SetAuthor('John Arley Cano Salinas - johnarleycano@hotmail.com');
// $pdf->SetTitle($titulo);
// $pdf->SetCreator('John Arley Cano Salinas - johnarleycano@hotmail.com');
// $pdf->SetMargins(0, 0, 5);

// Título
$pdf->setXY(60, 10);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(70, 5, utf8_decode('MUELLES Y FRENOS SIMÓN BOLIVAR S.A.S.'), 0, 0, 'L', 0);
$pdf->setXY(60, $pdf->getY() + 5);
$pdf->SetFont('Arial', '', 8);
$pdf->setXY(60, $pdf->getY());
$pdf->Cell(70, 5, 'NIT: 900296641', 0, 1, 'L', 0);
$pdf->setXY(60, $pdf->getY());
$pdf->Cell(70, 5, utf8_decode('Calle 40 # 30 - 139'), 0, 1, 'L', 0);
$pdf->setXY(60, $pdf->getY());
$pdf->Cell(70, 5, utf8_decode('Teléfono: 310 515 0291'), 0, 1, 'L', 0);
$pdf->setXY(60, $pdf->getY());
$pdf->Cell(70, 5, utf8_decode('Itagüí'), 0, 1, 'L', 0);
$pdf->Ln();

// Recibo de caja
$pdf->setXY(130, 10);
$pdf->SetFont('Arial', 'B', 9);
$pdf->setFillColor($gris['r'], $gris['g'], $gris['b']);
$pdf->Cell(70, 8, 'RECIBO DE CAJA', 1, 0, 'C', 1);
$pdf->setXY(130, $pdf->getY() + 8);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(70, 8, $numero_recibo_caja, 1, 0, 'C', 0);
$pdf->Ln(25);

// Información del tercero
$pdf->setXY(10, $pdf->getY()-6);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 6, 'Nombre:', 'L,T', 0, 'L', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(160, 6, utf8_decode($recibo->razon_social), 'T,R', 1, 'L', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 6, 'Nit/CC:', 'L', 0, 'L', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(160, 6, $recibo->documento_numero, 'R', 1, 'L', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 6, 'Fecha', 'L', 0, 'L', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(160, 6, "$recibo->dia/$recibo->mes/$recibo->anio", 'R', 1, 'L', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 6, 'Notas', 'L,B', 0, 'L', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(160, 6, utf8_decode($notas), 'B,R', 1, 'L', 0);
$pdf->Ln(5);

// Encabezado de los registros
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(15, 6, 'Auxiliar', 'L,T', 0, 'C', 1);
$pdf->Cell(65, 6, 'Concepto:', 'L,T', 0, 'C', 1);
$pdf->Cell(10, 6, 'U.N.', 'L,T', 0, 'C', 1);
$pdf->Cell(20, 6, 'Tercero:', 'L,T', 0, 'C', 1);
$pdf->Cell(20, 6, 'Doc. Cruce', 'L,T', 0, 'C', 1);
$pdf->Cell(20, 6, utf8_decode('C.O.:'), 'L,T,R', 0, 'C', 1);
$pdf->Cell(20, 6, utf8_decode('Débitos:'), 'L,T,R', 0, 'C', 1);
$pdf->Cell(20, 6, utf8_decode('Créditos:'), 'L,T,R', 1, 'C', 1);

$total_debitos = 0;
$total_creditos = 0;

// Cuentas bancarias
$pdf->SetFont('Arial', '', 6);
foreach($recibo_cuentas_bancarias as $cuenta) {
    $pdf->Cell(15, 5, $cuenta->auxiliar, 'B,R,L', 0, 'L', 0);
    $pdf->Cell(65, 5, utf8_decode($cuenta->nombre).' Nro. '.$cuenta->numero, 'B,R', 0, 'L', 0);
    $pdf->Cell(10, 5, '01', 'B,R', 0, 'R', 0);
    $pdf->Cell(20, 5, $recibo->documento_numero, 'B,R', 0, 'L', 0);
    $pdf->Cell(20, 5, '', 'B,R', 0, 'L', 0);
    $pdf->Cell(20, 5, '', 'B,R', 0, 'L', 0);
    $pdf->Cell(20, 5, formato_precio($cuenta->valor), 'B,R', 0, 'R', 0);
    $pdf->Cell(20, 5, formato_precio(0), 'B,R', 1, 'R', 0);

    $total_debitos += $cuenta->valor;
}

// Si no trae cuentas bancarias, se ingresa una por defecto
if(empty($recibo_cuentas_bancarias)) {
    $pdf->Cell(15, 5, '11100504', 'B,R,L', 0, 'L', 0);
    $pdf->Cell(65, 5, utf8_decode('BANCOLOMBIA - CUENTA CORRIENTE Nro. 01952279149'), 'B,R', 0, 'L', 0);
    $pdf->Cell(10, 5, '01', 'B,R', 0, 'R', 0);
    $pdf->Cell(20, 5, '', 'B,R', 0, 'L', 0);
    $pdf->Cell(20, 5, '', 'B,R', 0, 'L', 0);
    $pdf->Cell(20, 5, '', 'B,R', 0, 'L', 0);
    $pdf->Cell(20, 5, formato_precio($recibo->valor), 'B,R', 0, 'R', 0);
    $pdf->Cell(20, 5, formato_precio(0), 'B,R', 1, 'R', 0);

    $total_debitos += $recibo->valor;
}

// Movimientos
if(isset($movimientos)) {
    $pdf->SetFont('Arial', '', 6);
    foreach($movimientos as $movimiento) {
        $recibo_detalle = $this->productos_model->obtener('recibos_detalle', ['rd.recibo_id' => $recibo->id, 'subtotal' => $movimiento->f351_valor_cr]);
        
        $pdf->Cell(15, 5, $movimiento->f253_id, 'B,R,L', 0, 'L', 0); // Auxiliar
        $pdf->Cell(65, 5, $movimiento->f253_descripcion, 'B,R', 0, 'L', 0); // Concepto
        $pdf->Cell(10, 5, $movimiento->f351_id_un, 'B,R', 0, 'R', 0); // UN
        $pdf->Cell(20, 5, $movimiento->f200_nit, 'B,R', 0, 'L', 0); // Tercero
        $pdf->Cell(20, 5, (!empty($recibo_detalle[0])) ? $recibo_detalle[0]->documento_cruce_numero : '', 'B,R', 0, 'R', 0); // Documento cruce
        $pdf->Cell(20, 5, $movimiento->f351_id_co_mov, 'B,R', 0, 'R', 0); // Centro operativo
        $pdf->Cell(20, 5, formato_precio($movimiento->f351_valor_db), 'B,R', 0, 'R', 0); // Débitos
        $pdf->Cell(20, 5, formato_precio($movimiento->f351_valor_cr), 'B,R', 1, 'R', 0); // Créditos

        $total_debitos += $movimiento->f351_valor_db;
        $total_creditos += $movimiento->f351_valor_cr;
    }
}

// Sumas iguales
$pdf->SetFont('Arial', 'B', 6);
$pdf->Cell(150, 3, 'Sumas iguales', 1, 0, 'R', 0);
$pdf->SetFont('Arial', '', 6);
$pdf->Cell(20, 3, formato_precio($total_debitos), 'B,R', 0, 'R', 0);
$pdf->Cell(20, 3, formato_precio($total_creditos), 'B,R', 0, 'R', 0);
$pdf->Ln(15);

$pdf->Cell(60, 3, utf8_decode($usuario_creacion), 'B', 0, 'C', 0);
$pdf->Cell(5, 3, '', 0, 0, 'C', 0);
$pdf->Cell(60, 3, utf8_decode($usuario_aprobacion), 'B', 0, 'C', 0);
$pdf->Cell(5, 3, '', 0, 0, 'C', 0);
$pdf->Cell(60, 3, utf8_decode(''), 'B', 1, 'C', 0);

$pdf->Cell(60, 5, utf8_decode('Elaborado'), 0, 0, 'C', 0);
$pdf->Cell(5, 5, '', 0, 0, 'C', 0);
$pdf->Cell(60, 5, utf8_decode('Aprobado'), 0, 0, 'C', 0);
$pdf->Cell(5, 5, '', 0, 0, 'C', 0);
$pdf->Cell(60, 5, utf8_decode('{Recibido}'), 0, 0, 'C', 0);

$pdf->Output("Recibo.pdf", "I");