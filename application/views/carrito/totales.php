<?php if($this->session->userdata('usuario_id')) $permisos = $this->configuracion_model->obtener('permisos'); ?>

<div class="card-body card-body--padding--2">
    <?php if(isset($permisos) && in_array(['pedidos' => 'pedidos_credito_gestionar'], $permisos)) { ?>
        <!-- Cupo disponible -->
        <div class="color_azul_corporativo_primario"><b>Cupo disponible</b></div>
        <div class="block-reviews__subtitle color_azul_corporativo_primario text-right">
            <span id="valor_cupo_disponible">Calculando...</span>
        </div><!-- Cupo disponible -->
    <?php } ?>

    <h3 class="card-title">Resumen</h3>
    <table class="cart__totals-table">
        <thead>
            <tr>
                <th>Subtotal</th>
                <td><?php echo formato_precio($this->cart->total()); ?></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>Envío</th>
                <td>$ 0</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <td><?php echo formato_precio($this->cart->total()); ?></td>
            </tr>
        </tfoot>
    </table>

    <?php
    // Cupo del cliente
    if(isset($permisos) && in_array(['pedidos' => 'pedidos_credito_gestionar'], $permisos)) {
        $this->load->view('carrito/cupo');
    }
    ?>

    <a class="btn btn-primary btn-xl btn-block" href="<?php echo site_url("carrito/finalizar"); ?>">
        Finalizar pedido
    </a>
</div>