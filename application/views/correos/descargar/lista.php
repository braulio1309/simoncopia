<?php if(empty($archivos)): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> No hay archivos descargados aún
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Carpeta</th>
                    <th>Nombre Archivo</th>
                    <th>Fecha Descarga</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($archivos as $archivo): ?>
                    <tr>
                        <td>
                            <span class="badge badge-info"><?php echo $archivo['carpeta']; ?></span>
                        </td>
                        <td>
                            <small><?php echo $archivo['nombre_procesado']; ?></small>
                        </td>
                        <td>
                            <small><?php echo date('d/m/Y H:i', strtotime($archivo['fecha_descarga'])); ?></small>
                        </td>
                        <td>
                            <a href="<?php echo base_url($archivo['ruta']); ?>" 
                               class="btn btn-sm btn-outline-primary" 
                               download
                               title="Descargar">
                                <i class="fas fa-download"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>