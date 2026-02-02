<?php
// Convertimos a objeto por si viene como array

$item = (object) $importacion;

// Lógica de estado basada en la fecha
$fecha_llegada = strtotime($item->fecha_estimada_llegada);
$hoy = time();
$es_futuro = $fecha_llegada >= $hoy;
$clase_estado = $es_futuro ? "success" : "failure"; // Verde si falta, Rojo si ya pasó la fecha
$texto_estado = $es_futuro ? "En tránsito" : "Fecha cumplida";
?>

<div class="product-card__image">
    <div class="image image--type--product">
        <a href="<?php echo site_url("importaciones/editar/$item->id"); ?>" class="image__body">
            <svg class="image__tag" width="80px" height="80px" viewBox="0 0 24 24" fill="none" stroke="#ddd" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
        </a>
    </div>
    
    <div class="status-badge status-badge--style--<?php echo $clase_estado; ?> product-card__fit status-badge--has-icon status-badge--has-text">
        <div class="status-badge__body">
            <div class="status-badge__icon">
                <svg width="13" height="13">
                    <?php if ($es_futuro) { ?>
                        <path d="M6.5,0C2.9,0,0,2.9,0,6.5S2.9,13,6.5,13S13,10.1,13,6.5S10.1,0,6.5,0z M6.5,2c0.9,0,1.7,0.3,2.4,0.7L2.7,8.9 C2.3,8.2,2,7.4,2,6.5C2,4,4,2,6.5,2z" />
                    <?php } else { ?>
                        <path d="M12,4.4L5.5,11L1,6.5l1.4-1.4l3.1,3.1L10.6,3L12,4.4z" />
                    <?php } ?>
                </svg>
            </div>
            <div class="status-badge__text"><?php echo $texto_estado; ?></div>
            <div class="status-badge__tooltip" tabindex="0" data-toggle="tooltip" title="Llegada: <?php echo $item->fecha_estimada_llegada; ?>"></div>
        </div>
    </div>
</div>

<div class="product-card__info">
    <div class="product-card__meta">
        <span class="product-card__meta-title">Orden #:</span> <?php echo $item->numero_orden_compra; ?>
    </div>
    
    <div class="product-card__name">
        <div>
            <div class="product-card__badges">
                <?php if($item->requiere_anticipo) echo "<div class='tag-badge tag-badge--hot'>Requiere Anticipo</div>"; ?>
            </div>
            <a href="<?php echo site_url("importaciones/editar/$item->id"); ?>" title="<?php echo $item->razon_social; ?>">
                <?php echo $item->razon_social; ?>
            </a>
        </div>
    </div>
    
    <div class="product-card__features">
        <ul>
            <li>País: <?php echo $item->pais_origen; ?></li>
            <li>Moneda: <?php echo $item->moneda_preferida; ?></li>
            <li>BL/AWB: <?php echo $item->bl_awb ? $item->bl_awb : 'N/A'; ?></li>
            <li><?php echo substr($item->notas_internas, 0, 50) . '...'; ?></li>
        </ul>
    </div>
</div>

<div class="product-card__footer">
    <div class="product-card__prices">
        <div class="product-card__price product-card__price--current">
            <?php echo $item->moneda_preferida . ' ' . number_format($item->valor_total, 2); ?>
        </div>
    </div>

    <a href="<?php echo site_url("importaciones/editar/$item->id"); ?>" class="btn btn-secondary btn-sm" style="margin-left: auto;">
        Gestionar
    </a>
</div>