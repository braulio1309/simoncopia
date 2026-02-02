<div class="dashboard">
    <div class="dashboard__profile card profile-card">
        <div class="card-body profile-card__body">
            <div class="profile-card__avatar">
                <img src="<?php echo base_url(); ?>images/logo.png" alt="">
            </div>
            <div class="profile-card__name"><?php echo "{$this->session->userdata('razon_social')}"; ?></div>
            <div class="profile-card__email"><?php echo $this->session->userdata('email'); ?></div>

            <?php if(ENVIRONMENT == 'development') { ?>
                <div class="profile-card__edit">
                    <a href="<?php echo site_url('perfil/index/editar'); ?>" class="btn btn-secondary btn-sm">Editar perfil</a>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="dashboard__address card address-card address-card--featured">
        <?php if(ENVIRONMENT == 'development') { ?>
            <div class="address-card__badge tag-badge tag-badge--theme">Default</div>
            <div class="address-card__body">
                <div class="address-card__name"><?php echo "{$this->session->userdata('razon_social')}"; ?></div>
                <div class="address-card__row">
                    Random Federation<br>
                    115302, Moscow<br>
                    ul. Varshavskaya, 15-2-178
                </div>
                <div class="address-card__row">
                    <div class="address-card__row-title">Phone Number</div>
                    <div class="address-card__row-content">38 972 588-42-36</div>
                </div>
                <div class="address-card__row">
                    <div class="address-card__row-title">Email Address</div>
                    <div class="address-card__row-content">helena@example.com</div>
                </div>
                <div class="address-card__footer">
                    <a href="">Edit Address</a>
                </div>
            </div>
        </div>
    
        <div class="dashboard__orders card">
            <div class="card-header">
                <h5>Pedidos</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-table">
                <div class="table-responsive-sm">
                    <table>
                        <thead>
                            <tr>
                                <th>Nro.</th>
                                <th>Fecha</th>
                                <th>Productos</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<script>
    listarProductos = async() => {
        let productos = await obtenerPromesa(`${$('#site_url').val()}pedidos/obtener`, {tipo: 'pedidos_api'})
        var pedidoId
        var totalItems = 0
        var precio = 0
        var descuento = 0
        var valor = 0
        
        productos.forEach(producto => {
            totalItems++
            valor = valor + (parseFloat(producto.Valor_Bruto) - parseFloat(producto.Descuento))

            if(pedidoId != producto.Nro_Documento) {
                $('tbody').append(`
                    <tr>
                        <td><a href="account-order-details.html">${producto.Nro_Documento}</a></td>
                        <td>${producto.Fecha_Documento}</td>
                        <td align='right'>${totalItems}</td>
                        <td align='right'>${valor}</td>
                    </tr>
                `)

                totalItems = 0
                valor = 0
            }

            pedidoId = producto.Nro_Documento
        })
    }

    $().ready(() => {
        listarProductos()
    })
</script>