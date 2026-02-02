<?php
$id_importacion = $id;
// Se asume que en el controlador ya cargaste los datos o los buscas aquí
$importacion = $this->importaciones_model->obtener('importaciones', ['id' => $id_importacion]);

if(empty($importacion)) redirect(site_url('importaciones'));
?>

<div class="block-header block-header--has-breadcrumb">
    <div class="container">
        <div class="block-header__body">
            <nav class="breadcrumb block-header__breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb__list">
                    <li class="breadcrumb__spaceship-safe-area" role="presentation"></li>
                    <li class="breadcrumb__item breadcrumb__item--parent breadcrumb__item--first">
                        <a href="<?php echo site_url('inicio'); ?>" class="breadcrumb__item-link">Inicio</a>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--parent">
                        <a href="<?php echo site_url('importaciones'); ?>" class="breadcrumb__item-link">Importaciones</a>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--current breadcrumb__item--last" aria-current="page">
                        <span class="breadcrumb__item-link">Orden #<?php echo $importacion->numero_orden_compra; ?></span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="block-split">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-body card-body--padding--2">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="h3 mb-0">Detalle de Importación</h1>
                            <span class="status-badge <?php echo $importacion->estado_clase; ?> p-2">
                                <?php echo $importacion->estado; ?>
                            </span>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5>Información del Proveedor</h5>
                                <p>
                                    <strong>Razón Social:</strong> <?php echo $importacion->razon_social; ?><br>
                                    <strong>Contacto:</strong> <?php echo $importacion->contacto_principal; ?><br>
                                    <strong>Email:</strong> <?php echo $importacion->email_contacto; ?><br>
                                    <strong>Teléfono:</strong> <?php echo $importacion->telefono_contacto; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h5>Logística</h5>
                                <p>
                                    <strong>País de Origen:</strong> <?php echo $importacion->pais_origen; ?><br>
                                    <strong>BL / AWB:</strong> <?php echo $importacion->bl_awb; ?><br>
                                    <strong>F. Estimada Llegada:</strong> <?php echo ($importacion->fecha_estimada_llegada) ? date('d/m/Y', strtotime($importacion->fecha_estimada_llegada)) : 'Pendiente'; ?><br>
                                    <strong>F. Ingreso SIESA:</strong> <?php echo ($importacion->fecha_ingreso_siesa && $importacion->fecha_ingreso_siesa != '0000-00-00') ? date('d/m/Y', strtotime($importacion->fecha_ingreso_siesa)) : '-'; ?>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <h5>Resumen Financiero</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Concepto</th>
                                        <th>Valor (<?php echo $importacion->moneda_preferida; ?>)</th>
                                        <th>Valor Aprox. (COP)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Valor Total Mercancía</td>
                                        <td><?php echo number_format($importacion->valor_total, 2); ?></td>
                                        <td>$<?php echo number_format($importacion->valor_total_cop); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Impuestos DIAN</td>
                                        <td>-</td>
                                        <td>$<?php echo number_format($importacion->impuestos_dian); ?></td>
                                    </tr>
                                    <?php if($importacion->porcentaje_anticipo > 0): ?>
                                    <tr class="table-info">
                                        <td><strong>Anticipo (<?php echo $importacion->porcentaje_anticipo; ?>%)</strong></td>
                                        <td><?php echo number_format($importacion->valor_total * ($importacion->porcentaje_anticipo / 100), 2); ?></td>
                                        <td>Calculado</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Observaciones y Condiciones</h5>
                    <div class="card p-3 bg-light">
                        <p><strong>Condiciones de Pago:</strong><br><?php echo nl2br($importacion->condiciones_pago); ?></p>
                        <p><strong>Notas Internas:</strong><br><?php echo nl2br($importacion->notas_internas); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Acciones</h5>
                        <a href="<?php echo site_url("importaciones/editar/$id_importacion"); ?>" class="btn btn-primary btn-block mb-2">
                            Editar Importación
                        </a>
                        <button class="btn btn-secondary btn-block" onclick="window.print()">
                            Imprimir Ficha
                        </button>
                        
                        <hr>
                        
                        <div class="product__meta">
                            <table class="w-100">
                                <tr>
                                    <th class="py-1">Orden de Compra:</th>
                                    <td><?php echo $importacion->numero_orden_compra; ?></td>
                                </tr>
                                <tr>
                                    <th class="py-1">TRM Aplicada:</th>
                                    <td>$<?php echo number_format($importacion->valor_trm, 2); ?></td>
                                </tr>
                                <tr>
                                    <th class="py-1">Creado por:</th>
                                    <td>ID: <?php echo $importacion->usuario_id; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4 shadow-sm p-3 bg-white border-left border-primary" style="border-left-width: 5px !important;">
                    <h6>¿Necesitas ayuda?</h6>
                    <p class="small mb-0">Si tienes dudas sobre el estado de esta importación, contacta al departamento de compras o logística.</p>
                </div>
            </div>
        </div>

        <!-- Sección de Bitácoras -->
        <div class="row mt-4" id="bitacoras">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Bitácora de Importación</h5>
                            <button class="btn btn-primary btn-sm" onclick="cargarBitacoraDetalle()" aria-label="Agregar nuevo registro de bitácora">
                                <i class="fas fa-plus"></i> Agregar Registro
                            </button>
                        </div>
                        <div id="contenedor_importaciones_bitacora"></div>
                        <div id="contenedor_importaciones_bitacora_detalle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="importacion_id" value="<?php echo $id_importacion; ?>">

<div class="block-space block-space--layout--before-footer"></div>

<script>
    cargarBitacoraDetalle = (id = null) => {
        cargarInterfaz('importaciones/bitacora/detalle', 'contenedor_importaciones_bitacora_detalle', {id: id})
    }

    listarImportacionesBitacora = () => {
        cargarInterfaz('importaciones/bitacora/lista', 'contenedor_importaciones_bitacora')
    }

    $().ready(() => {
        listarImportacionesBitacora()
    })
</script>