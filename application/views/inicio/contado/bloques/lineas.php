<div class="block-zone__card category-card category-card--layout--overlay" onClick="javascript:location.href='<?php echo site_url("productos"); ?>'">
    <div class="category-card__body">
        <div class="category-card__overlay-image">
            <img srcset="<?php echo base_url(); ?>images/categories/lineas-mobile.jpg 530w, <?php echo base_url(); ?>images/categories/lineas.jpg 305w" src="<?php echo base_url(); ?>images/categories/category-overlay-3.jpg" sizes="(max-width: 575px) 530px, 305px" alt="">
        </div>
        <div class="category-card__overlay-image category-card__overlay-image--blur">
            <img srcset="<?php echo base_url(); ?>images/categories/lineas-mobile.jpg 530w, <?php echo base_url(); ?>images/categories/lineas.jpg 305w" src="<?php echo base_url(); ?>images/categories/category-overlay-3.jpg" sizes="(max-width: 575px) 530px, 305px" alt="">
        </div>
        <div class="category-card__content">
            <div class="category-card__info">
                <div class="category-card__name">
                    <a href="<?php echo site_url('productos'); ?>">Líneas</a>
                </div>
                <ul class="category-card__children">
                    <?php foreach($this->configuracion_model->obtener('lineas', ['marcas_activas' => true]) as $linea) echo "<li><a href='".site_url('productos')."?linea=$linea->nombre'>$linea->nombre</a></li>"; ?>
                </ul>
                <div class="category-card__actions">
                    <a href="<?php echo site_url('productos'); ?>" class="btn btn-primary btn-sm">Ver todas las líneas</a>
                </div>
            </div>
        </div>
    </div>
</div>