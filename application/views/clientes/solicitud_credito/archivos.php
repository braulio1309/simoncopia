<?php
$ruta_archivos = "./archivos/solicitudes_credito/{$datos['id']}/*.*";
$archivos = glob($ruta_archivos, GLOB_BRACE);
?>

<div class="block">
    <div class="container">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Archivos</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($archivos as $indice => $archivo) { 
                    $nombre = pathinfo($archivo, PATHINFO_FILENAME);
                    $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                ?>
                    <tr>
                        <td><?php echo $indice + 1; ?></td>
                        <td><?php echo $nombre; ?></td>
                        <td>
                            <?php if ($extension == 'pdf' || $extension == 'jpg' || $extension == 'png' || $extension == 'jpeg' || $extension == 'webp') { ?>
                                <button class="btn btn-primary" title="Ver archivo" onClick="javascript:window.open('<?php echo base_url($archivo) ?>', this.target, 'width=800,height=600'); return false;">
                                    <i class="fas fa-search"></i>
                                </button>
                            <?php } ?>

                            <a href="<?php echo base_url($archivo); ?>" class="btn btn-primary" title="Descargar archivo" download>
                                <i class="fas fa-download"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>