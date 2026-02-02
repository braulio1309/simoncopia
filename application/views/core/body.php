<!DOCTYPE html>
<html lang="es-CO" dir="ltr">
    <head>
        <?php $this->load->view('core/header'); ?>
        <?php $this->load->view('core/schema'); ?>
    </head>

    <body>
        <!-- Google TAG Manager para poder crear eventos y rastrear elementos -->
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NK3HNKKP" height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>

        <div id="contenedor_notificacion"></div>

        <input type="hidden" id="site_url" value="<?php echo site_url(); ?>">
        <input type="hidden" id="base_url" value="<?php echo site_url(); ?>">
        <input type="hidden" id="cantidad_datos" value="<?php echo $this->config->item('cantidad_datos'); ?>">
        <input type="hidden" id="sesion_usuario_id" value="<?php echo $this->session->userdata('usuario_id'); ?>">
        <input type="hidden" id="sesion_documento_numero" value="<?php echo $this->session->userdata('documento_numero'); ?>">
        <input type="hidden" id="codigo_vendedor" value="<?php echo $this->session->userdata('codigo_vendedor'); ?>">
        
        <div class="site">
            <!-- Menú superior -->
            <?php $this->load->view('core/menu_superior/index'); ?>

            <!-- Menú móvil -->
            <?php $this->load->view('core/menu_movil/index'); ?>

            <!-- Contenido principal de cada sitio -->
            <div class="site__body mt-3">
                <?php $this->load->view($contenido_principal); ?>
            </div>

            <!-- Footer -->
            <?php $this->load->view('core/footer'); ?>
        </div>
        
        <!-- Modals -->
        <?php $this->load->view('core/modals'); ?>
        
        <!-- scripts -->
        <script src="<?php echo base_url(); ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="<?php echo base_url(); ?>vendor/owl-carousel/owl.carousel.min.js"></script>
        <script src="<?php echo base_url(); ?>vendor/nouislider/nouislider.min.js"></script>
        <script src="<?php echo base_url(); ?>vendor/photoswipe/photoswipe.min.js"></script>
        <script src="<?php echo base_url(); ?>vendor/photoswipe/photoswipe-ui-default.min.js"></script>
        <script src="<?php echo base_url(); ?>vendor/select2/js/select2.min.js"></script>
        <script src="<?php echo base_url(); ?>js/number.js"></script>
        <script src="<?php echo base_url(); ?>js/main.js?<?php echo date('Ymdhis'); ?>"></script>

        <!-- Botón de Whatsapp -->
        <div class="whatsapp-container">
            <a
                id="btn_whatsapp"
                href="https://wa.me/573114914780" 
                target="_blank" 
                class="btn_whatsapp" 
                title="Contáctanos en WhatsApp"
            >
                <i class="fab fa-whatsapp"></i>
                <span class="txt_whatsapp">Cotiza aquí</span>
            </a>
        </div>

        <script>
            (function(w,d,u){
                var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
                var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
            })(window,document,'https://cdn.bitrix24.es/b24455241/crm/tag/call.tracker.js');

            $().ready(() => {
                $('#btn_whatsapp').click(() => agregarLog(55))

                actualizarCarrito()
            })
        </script>
    </body>
</html>