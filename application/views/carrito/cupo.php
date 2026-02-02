<!-- Cupo después de pedido -->
<div class="color_azul_corporativo_primario"><b>Cupo después de este pedido</b></div>
<div class="block-reviews__subtitle color_azul_corporativo_primario text-right">
    <span id="valor_nuevo_cupo">0</span>
</div><!-- Cupo después de pedido -->

<script>
    $().ready(() => {
        let nit = '<?php echo $this->session->userdata('documento_numero'); ?>'

        const cupo = new ClienteCalculadorCupo(nit)

        cupo.calcularValorCupoRestante()
            .then(resultado => {
                let valorNuevoCupo = (resultado.valorCupoRestante - parseFloat(<?php echo $this->cart->total(); ?>))
                valorNuevoCupo = (valorNuevoCupo > 0 ) ? valorNuevoCupo : 0

                $('#valor_cupo_disponible').text(`$ ${formatearNumero(resultado.valorCupoRestante)}`)
                $('#valor_nuevo_cupo').text(`$ ${formatearNumero(valorNuevoCupo)}`)
            }).catch((error) => {
                console.log(error)  
            })
    })
</script>