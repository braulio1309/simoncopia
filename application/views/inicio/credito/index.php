<div class="block mt-4">
    <div class="container container--max--xl">
        <div class="row">
            <!-- Estado del cliente -->
            <div class="col-lg-8 order-md-2 order-lg-1">
                <div class="row">
                    <div class="col-2">
                        <img src="" id="imagen_cupo_restante"  height="100px">
                    </div>
                    <div class="col-10">
                        <div class="block-reviews__title text-left" id="titulo">Consultando el estado de tu cartera...</div>
                        <div class="block-reviews__subtitle text-left" id="subtitulo">
                            Espera...
                        </div>
                    </div>
                </div>
            </div><!-- Estado del cliente -->

            <!-- Cupo disponible -->
            <div class="col-lg-4 order-md-3 order-lg-2">
                <div class="block-reviews__title text-right">Cupo disponible</div>
                <div class="block-reviews__subtitle text-right">
                    <b>$ <span id="cupo_disponible">-</span></b>
                </div>
            </div><!-- Cupo disponible -->

            <!-- Banner -->
            <div class="col-lg-8 order-md-1 order-lg-3">
                <img src="<?php echo base_url(); ?>archivos/banners/inicio/dashboard_credito.webp" width="100%">
            </div><!-- Banner -->

            <!-- Pedidos -->
            <div class="col-lg-4 text-left order-md-4 order-lg-4">
                <div class="block-reviews__subtitle text-left mb-2">Mis pedidos:</div>
                <div class="wishlist" id="contenedor_pedidos"></div>
            </div><!-- Pedidos -->
        </div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    $().ready(async () => {
        let nit = '<?php echo $this->session->userdata('documento_numero'); ?>'

        cupo.calcularValorCupoRestante()
            .then(resultado => {
                $('#cupo_disponible').text(formatearNumero(resultado.valorCupoRestante))
                $('#imagen_cupo_restante').attr('src', `${$('#base_url').val()}/images/icons/${resultado.imagenCupoRestante}`)
                $('#titulo').text(resultado.estadoTitulo)
                $('#subtitulo').text(resultado.estadoSubtitulo)
                
            }).catch((error) => {
                console.log(error)  
            })

        cargarInterfaz('inicio/credito/pedidos', 'contenedor_pedidos', {nit: nit})
    })
</script>