<?php $recibo = $this->productos_model->obtener('recibo', ['id' => $datos['id_recibo']]); ?>

<label class="vehicles-list__item">
    <div class="form-group col-4">
        <label for="cuenta_<?php echo $datos['id']; ?>">Cuenta</label>
        <select id="cuenta_<?php echo $datos['id']; ?>" class="form-control">
            <option value="">Seleccione...</option>
            <?php foreach($this->configuracion_model->obtener('cuentas_bancarias') as $cuenta) echo "<option value='$cuenta->id' data-codigo='$cuenta->codigo'>$cuenta->codigo - $cuenta->nombre</option>"; ?>
        </select>
    </div>

    <div class="form-group col-4">
        <label for="fecha_pago_<?php echo $datos['id']; ?>">Fecha de documento banco</label>
        <input type="date" class="form-control" id="fecha_pago_<?php echo $datos['id']; ?>" value="<?php if(isset($datos['cuenta']['fecha_documento_banco'])) echo $datos['cuenta']['fecha_documento_banco']; ?>">
    </div>
    
    <div class="form-group col-4">
        <label for="fecha_pago">Valor</label>
        <input 
            type="text"
            id="valor_<?php echo $datos['id']; ?>"
            class="form-control valor_cuenta_recibo"
            style="text-align: right"
            value="<?php if(isset($datos['cuenta']['valor'])) echo $datos['cuenta']['valor']; ?>"
            data-id="<?php echo $datos['id']; ?>"
            data-auxiliar=""
        >
    </div>
</label>

<?php if(isset($datos['cuenta']['cuenta_bancaria_id'])) { ?>
    <script>
        $('#cuenta_<?php echo $datos['id']; ?>').val(<?php echo $datos['cuenta']['cuenta_bancaria_id']; ?>)
        calcularTotalAmortizacion()
    </script>
<?php } ?>

<script>
    $().ready(() => {
        // Si el número cambia
        $('.valor_cuenta_recibo').on('keyup', function() {
            // Se formatea el campo
            $(this).val(formatearNumero($(this).val()))

            calcularTotalAmortizacion()
        })
    })
</script>