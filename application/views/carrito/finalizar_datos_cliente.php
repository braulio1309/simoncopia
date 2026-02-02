<?php
// Consulta de las sucursales del cliente
$resultado_sucursales = json_decode(obtener_clientes_api($datos));
$codigo_resultado_sucursales = $resultado_sucursales->codigo;
$sucursales = ($codigo_resultado_sucursales == 0) ? $resultado_sucursales->detalle->Table : [] ;

// Campo oculto para almacenar la cantidad de sucursales del cliente
$cantidad_sucursales = count($sucursales);

echo "<input type='hidden' value='$cantidad_sucursales' id='cantidad_sucursales'>";

// Consulta de terceros
$resultado_tercero = json_decode(obtener_terceros_api($datos));
$codigo_resultado_tercero = $resultado_tercero->codigo;
$tercero = ($codigo_resultado_tercero == 0) ? $resultado_tercero->detalle->Table[0] : [] ;

// Se almacena un dato para reconocer que el tercero existía previamente, y no crearlo al ir al pago
if(!empty($tercero)) {
    echo "<input type='hidden' id='api_tercero_id' value='$tercero->f200_id' />";
    echo "<input type='hidden' id='api_departamento_codigo' value='$tercero->f015_id_depto' />";
    echo "<input type='hidden' id='api_municipio_codigo' value='$tercero->f015_id_ciudad' />";
}
?>

<input type="hidden" id="total_pedido">

<div class="form-row">
    <div class="form-group col-12">
        <div class="tag-badge tag-badge--new badge_formulario badge_formulario_azul mb-2 mt-2">
            DATOS DE FACTURACIÓN
        </div>
    </div>

    <div class="form-group col-4">
        <label for="checkout_tipo_tercero">Tipo de persona *</label>
        <select id="checkout_tipo_tercero" class="form-control" autofocus>
            <option value="">Seleccione...</option>
            <option value="1">Persona natural</option>
            <option value="2">Persona jurídica</option>
        </select>
    </div>

    <div class="form-group col-4">
        <label for="checkout_tiene_rut">¿Tienes RUT? *</label>
        <select id="checkout_tiene_rut" class="form-control">
            <option value="">Seleccione...</option>
            <option value="1">Sí</option>
            <option value="2">No</option>
        </select>
    </div>

    <div class="form-group col-4">
        <label for="checkout_responsable_iva">¿Responsable de IVA? *</label>
        <select id="checkout_responsable_iva" class="form-control">
            <option value="">Selecciona...</option>
            <option value="0" data-responsable_iva="49" data-causante_iva="ZY">No</option>
            <option value="1" data-responsable_iva="48" data-causante_iva="01">Sí</option>
        </select>
    </div>

    <div class="form-group col-12">
        <label for="checkout_tipo_documento">Tipo de documento *</label>
        <select id="checkout_tipo_documento" class="form-control">
            <option value="">Seleccione...</option>
            <option value="C" data-tipo_tercero="1">Cédula de ciudadanía</option>
            <option value="E" data-tipo_tercero="1">Cédula de extranjería</option>
        </select>
    </div>
</div>

<div class="form-row" id="checkout_datos_persona_natural">
    <div class="form-group col-md-12">
        <label for="checkout_nombres">Nombres</label>
        <input type="text" class="form-control" id="checkout_nombres" value="<?php if(!empty($tercero)) echo $tercero->f200_nombres; ?>">
    </div>
    <div class="form-group col-md-6">
        <label for="checkout_primer_apellido">Primer apellido</label>
        <input type="text" class="form-control" id="checkout_primer_apellido" value="<?php if(!empty($tercero)) echo $tercero->f200_apellido1; ?>">
    </div>
    <div class="form-group col-md-6">
        <label for="checkout_segundo_apellido">Segundo apellido</label>
        <input type="text" class="form-control" id="checkout_segundo_apellido" value="<?php if(!empty($tercero)) echo $tercero->f200_apellido2; ?>">
    </div>
</div>

<div class="form-group">
    <label for="checkout_razon_social">Razón social *<span class="text-muted">(Opcional)</span></label>
    <input type="text" class="form-control" id="checkout_razon_social" value="<?php if(!empty($tercero)) echo $tercero->f200_razon_social; ?>" placeholder="Nombre de la empresa o nombre completo">
</div>

<div class="form-row">
    <div class="form-group col-lg-6 col-sm-12">
        <label for="checkout_departamento_id">Departamento *</label>
        <select id="checkout_departamento_id" class="form-control"></select>
    </div>

    <div class="form-group col-lg-6 col-sm-12">
        <label for="checkout_municipio_id">Municipio *</label>
        <select id="checkout_municipio_id" class="form-control"></select>
    </div>
</div>

<div class="form-group">
    <label for="checkout_direccion">Dirección *</label>
    <input type="text" class="form-control" id="checkout_direccion" value="<?php if(!empty($tercero)) echo $tercero->f015_direccion1; ?>">
</div>

<div class="form-group">
    <label for="checkout_sucursal">Elija la sucursal o placa a la que desea asociar el pedido</label>
    <select id="checkout_sucursal" class="form-control form-control-select2">
        <option value="">Seleccione...</option>
        <?php if(empty($sucursales)) echo "<option value='001'>Principal</option>" ?>
        <?php foreach($sucursales as $sucursal) echo "<option value='$sucursal->f201_id_sucursal' data-tipo_cliente='$sucursal->f201_id_tipo_cli'>$sucursal->f201_descripcion_sucursal</option>"; ?>
    </select>
</div>

<div class="form-group">
    <label for="checkout_vendedor_nit">Elige tu asesor comercial *</label>
    <select id="checkout_vendedor_nit" class="form-control">
        <option value="">Sin asesor comercial asignado</option>
        <?php foreach($this->configuracion_model->obtener('vendedores') as $vendedor) echo "<option value='$vendedor->nit'>$vendedor->nombre</option>"; ?>
    </select>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="checkout_email">Correo electrónico *</label>
        <input type="email" class="form-control" id="checkout_email" value="<?php if(!empty($tercero)) echo $tercero->f015_email; ?>">
    </div>

    <div class="form-group col-md-6">
        <label for="checkout_telefono">Teléfono *</label>
        <input type="text" class="form-control" id="checkout_telefono" value="<?php if(!empty($tercero)) echo $tercero->f015_celular; ?>">
    </div>

    <div class="form-group col-md-12">
        <label for="checkout_comentarios">Observaciones <span class="text-muted">(Opcional)</span></label>
        <textarea id="checkout_comentarios" class="form-control" rows="3"></textarea>
    </div>

    <div class="form-row">
        <div class="form-group col-12">
            <div class="tag-badge tag-badge--new badge_formulario mb-2 mt-2">
                DATOS DE ENVÍO
            </div>
        </div>

        <div class="form-group col-6">
            <label for="checkout_departamento_envio_id">Departamento *</label>
            <select id="checkout_departamento_envio_id" class="form-control"></select>
        </div>

        <div class="form-group col-6">
            <label for="checkout_municipio_envio_id">Municipio *</label>
            <select id="checkout_municipio_envio_id" class="form-control"></select>
        </div>

        <div class="form-group col-12">
            <label for="checkout_direccion_envio">Dirección completa *</label>
            <input type="text" class="form-control" id="checkout_direccion_envio" value="<?php if(!empty($tercero)) echo $tercero->f015_direccion1; ?>">
        </div>

        <div class="form-group col-12">
            <label for="checkout_email_factura_electronica">Email para envío de factura electrónica *</label>
            <input type="text" class="form-control" id="checkout_email_factura_electronica" value="<?php if(!empty($tercero)) echo $tercero->f015_email; ?>">
        </div>
    </div>
</div>

<?php if(isset($tercero)) { ?>
    <script>
        $().ready(() => {
            $('#btn_validar_documento').removeClass('btn-loading').hide()
            $('#checkout_documento_numero').attr('disabled', true)
            $('#btn_pagar').attr('disabled', false)
        })
    </script>
<?php } ?>

<script>
    mostrarTotales = async listaPrecio => {
        // let descuento = 0
        let subtotal = parseFloat('<?php echo $this->cart->total(); ?>')

        $('.checkout__totals-footer').html(`
            <tr>
                <th>Total</th>
                <td>$${formatearNumero(subtotal)}</td>
            </tr>
        `)

        $('#total_pedido').val(subtotal)
    }

    $().ready(async() => {
        // Si tiene sesión iniciada, la lista de precio es la de clientes
        let listaPrecioPorDefecto = '<?php echo $this->config->item('lista_precio'); ?>'
        mostrarTotales(listaPrecioPorDefecto)

        await listarDatos('checkout_departamento_id', {tipo: 'departamentos', pais_id: 169})
        await listarDatos('checkout_departamento_envio_id', {tipo: 'departamentos', pais_id: 169})

        // Si es un tercero existente
        if($('#api_tercero_id').val()) {
            // Pone por defecto el departamento
            $(`#checkout_departamento_id option[data-codigo="${$('#api_departamento_codigo').val()}"]`).attr('selected', true)

            // Carga los datos de municipios
            await listarDatos('checkout_municipio_id', {tipo: 'municipios', departamento_id: $('#checkout_departamento_id').val()})

            // Pone por defecto el municipio
            $(`#checkout_municipio_id option[data-codigo="${$('#api_municipio_codigo').val()}"]`).attr('selected', true)
        }

        // Si es una sola sucursal, se pone por defecto
        if($('#cantidad_sucursales').val() == 1) $('#checkout_sucursal').val('001')

        // Cuando se seleccione el tipo de tercero
        $('#checkout_tipo_tercero').change(() => {
            // Persona natural
            if ($('#checkout_tipo_tercero').val() == 1) {
                $('#checkout_datos_persona_natural').show()
                $('#checkout_razon_social').attr('disabled', true)
            }

            // Persona jurídica
            if ($('#checkout_tipo_tercero').val() == 2) {
                $('#checkout_datos_persona_natural').hide()
                $('#checkout_razon_social').attr('disabled', false)
                $('#checkout_nombres, #checkout_primer_apellido, #checkout_segundo_apellido, #checkout_razon_social').val('')
            }
        })

        // Cuando seleccione si tiene o no RUT
        $('#checkout_tiene_rut').change(function(e) {
            if($(this).val() == 1) {
                // El tipo de documento tiene que ser NIT
                $("#checkout_tipo_documento").append("<option value='N' data-tipo_tercero='2'>NIT</option>").val('N').attr('disabled', true)
            } else {
                // Se elimina la posibilidad de escoger NIT
                $("#checkout_tipo_documento option[value='N']").remove()
                $('#checkout_tipo_documento').attr('disabled', false)
            }
        })
        
        // Cuando se seleccione un departamento
        $('#checkout_departamento_id').change(() => {
            listarDatos('checkout_municipio_id', {tipo: 'municipios', departamento_id: $('#checkout_departamento_id').val()})
        })

        // Cuando se seleccione un departamento de envío
        $('#checkout_departamento_envio_id').change(() => {
            listarDatos('checkout_municipio_envio_id', {tipo: 'municipios', departamento_id: $('#checkout_departamento_envio_id').val()})
        })

        // Cuando se escribe nombres o apellidos
        $('#checkout_datos_persona_natural').keyup(function(e) {
            // Si es persona natural
            if($('#checkout_tipo_tercero').val() == '1') {
                // Se completa la razón social
                $('#checkout_razon_social').val(`${$('#checkout_primer_apellido').val()} ${$('#checkout_segundo_apellido').val()} ${$('#checkout_nombres').val()}`)
            }
        })

        // Si cambia la dirección
        $('#checkout_direccion').keyup(function(e) {
            $('#checkout_direccion_envio').val($('#checkout_direccion').val())
        })
    })
</script>