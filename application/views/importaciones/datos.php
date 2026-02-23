<?php
// Usamos el modelo para traer datos de la tabla 'importaciones'
$importaciones = $this->importaciones_model->obtener('importaciones', $datos);


if (empty($importaciones)) { ?>
    <div class="text-center p-5">
        <div class="mb-3">
            <i class="fas fa-box-open fa-3x text-muted"></i>
        </div>
        <h3 class="text-muted">No se encontraron importaciones</h3>
        <p class="text-muted">Intenta con otro término de búsqueda o crea una nueva.</p>
    </div>
<?php } else { ?>

    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="bg-light">
                <tr>
                    <th scope="col" class="font-weight-bold">ID Importación</th>
                    <th scope="col" class="font-weight-bold">Fecha de Creación</th>
                    <th scope="col" class="font-weight-bold">Proveedor</th>
                    <th scope="col" class="font-weight-bold">Número OC</th>
                    <th scope="col" class="font-weight-bold">Valor (USD)</th>
                    <th scope="col" class="font-weight-bold">Valor (COP)</th>
                    <th scope="col" class="font-weight-bold">Fecha Llegada</th>
                    <th scope="col" class="font-weight-bold">Estado</th>
                    <th scope="col" class="text-right font-weight-bold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($importaciones as $item) {
                    // 1. Formato de ID (IMP-001)
                    $id_formateado = 'IMP-' . str_pad($item->id, 3, '0', STR_PAD_LEFT);
                ?>
                    <tr>
                        <td class="font-weight-bold text-primary">
                            <a href="<?php echo site_url('importaciones/ver/' . $item->id); ?>">
                                <?php echo $id_formateado; ?>
                            </a>
                        </td>

                        <td>
                            <?php echo $item->fecha_creacion; ?>
                        </td>

                        <td>
                            <?php echo $item->razon_social; ?>
                        </td>

                        <td>
                            <?php echo $item->numero_orden_compra; ?>
                        </td>

                        <td class="font-weight-bold">
                            <?php echo $item->moneda_preferida . ' ' . number_format($item->valor_total, 0, ',', '.'); ?>
                        </td>

                        <td class="text-muted">
                            $ <?php echo number_format($item->valor_total_cop, 0, ',', '.'); ?>
                        </td>

                        <td>
                            <?php
                            $fecha = $item->fecha_estimada_llegada;

                            if ($fecha == null || $fecha == '0000-00-00' || empty($fecha)) {
                                echo "-";
                            } else {
                                // Si es válida, la formateamos
                                echo date('d/m/Y', strtotime($fecha));
                            }
                            ?>
                        </td>

                        <td>
                            <span class="badge <?php echo $item->estado_clase; ?> p-2" style="font-size: 0.85rem; border-radius: 20px; width: 100%; display: inline-block; text-align: center;">
                                <?php echo $item->estado; ?>
                            </span>
                        </td>

                        <td class="text-right">
                            <div class="btn-group" role="group">
                                 <a href="<?php echo site_url('importaciones/ver/' . $item->id . '#bitacoras'); ?>" 
                                    class="btn btn-sm btn-light text-primary shadow-sm"
                                    title="Ver Detalle de Bitácora">
                                    <i class="fas fa-eye"></i>
                               
                                </a>
                                <a href="<?php echo site_url('importaciones/ver/' . $item->id); ?>" class="btn btn-sm btn-light text-secondary" title="Ver Detalle">
                                    <i class="far fa-eye"></i>
                                </a>
                                <a href="<?php echo site_url('importaciones/editar/' . $item->id); ?>" class="btn btn-sm btn-light text-primary" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-light text-danger" onclick="eliminarImportacion(<?php echo $item->id; ?>)" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3 text-center">
        <small class="text-muted">Mostrando <?php echo count($importaciones); ?> registros</small>
    </div>
<?php } ?>

<script>
    // Script simple para eliminar (puedes moverlo a tu archivo JS global)
    eliminarImportacion = async (id) => {
        let confirmacion = await confirmar('Eliminar', `¿Está seguro de eliminar la importación IMP-${String(id).padStart(3, '0')}?`)
        
        if (confirmacion) {
            let eliminar = await consulta('eliminar', {tipo: 'importaciones', id: id})
            if (eliminar) {
                mostrarAviso('exito', 'Importación eliminada correctamente', 5000)
                listarImportaciones()
            }
        }
    }

    cargarBitacoraDetalle = (id = null) => {
        cargarInterfaz('/bitacora/detalle', 'contenedor_importaciones_bitacora', {
            id: id
        })
    }

    listarImportacionesBitacora = () => {
        cargarInterfaz('importaciones/bitacora/lista', 'contenedor_importaciones_bitacora')
    }

    $().ready(() => {
        listarImportacionesBitacora()
    })
</script>