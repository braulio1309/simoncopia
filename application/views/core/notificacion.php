<?php
$datos['omitir_bodega'] = true;
$datos['omitir_lista_precio'] = true;
if(isset($datos['id'])) $producto = $this->productos_model->obtener('productos', $datos);

$descripcion = '';
$titulo = 'Notificación';
$imagen = base_url().'images/logo.png';

if(isset($datos['id'])) $imagen = url_fotos($producto->marca, $producto->referencia);
if(isset($datos['titulo'])) $titulo = $datos['titulo'];
if(isset($datos['id'])) $descripcion = $producto->notas;
?>

<div class="notification">
    <div class="notification-header">
        <h3 class="notification-title"><?php echo $titulo; ?></h3>
        <!-- <i class="fa fa-times notification-close"></i> -->
    </div>
    <div class="notification-container">
        <div class="notification-media">
            <img src="<?php echo $imagen; ?>" class="notification-user-avatar">
            <!-- <i class="fa fa-thumbs-up notification-reaction"></i> -->
        </div>
        <div class="notification-content">
            <p class="notification-text">
                <?php echo $descripcion; ?>
            </p>
            <span class="notification-timer">Ahora</span>
        </div>
        <!-- <span class="notification-status"></span> -->
    </div>
</div>