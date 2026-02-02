<?php
$this->data['recibo'] = $this->configuracion_model->obtener('recibos', ['token' => $datos['token']]);
$this->data['recibo_detalle'] = $this->productos_model->obtener('recibos_detalle', ['rd.recibo_id' => $this->data['recibo']->id]);
$this->data['wompi'] = json_decode($this->data['recibo']->wompi_datos, true);

// Si el recibo es de un pedido
if($this->data['recibo']->recibo_tipo_id == 1) {
    $this->load->view('configuracion/recibos/detalle/productos', $this->data);
} else {
    $this->load->view('configuracion/recibos/detalle/comprobantes', $this->data);
}
?>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    $(`.facturas_items`).addClass('account-nav__item--active')
</script>