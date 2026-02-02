<div class="wishlist">
    <table class="wishlist__table">
        <thead class="wishlist__head">
            <tr class="wishlist__row wishlist__row--head">
                <th class="wishlist__column wishlist__column--head wishlist__column--product">Tercero</th>
                <th class="wishlist__column wishlist__column--head wishlist__column--stock">Teléfono</th>
                <th class="wishlist__column wishlist__column--head wishlist__column--stock">Módulo</th>
                <th class="wishlist__column wishlist__column--head wishlist__column--button"></th>
            </tr>
        </thead>
        <tbody class="wishlist__body" id="datos">
            <?php $this->load->view("configuracion/contactos/datos"); ?>
        </tbody>
    </table>
</div>

<button class="btn btn-primary btn-block mt-3" onClick="javascript:cargarMasDatos('configuracion/contactos')" id="btn_mostrar_mas">Mostrar más datos</button>