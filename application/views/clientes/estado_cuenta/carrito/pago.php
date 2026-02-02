<?php 
$recibo = $this->productos_model->obtener('recibo', ['id' => $datos['id']]);

$llave_integridad = generar_llave_integridad([
    $recibo->token,
    (floatval($recibo->valor)) * 100,
    'COP',
    $this->config->item('api_wompi')['secret_integridad'],
]);
?>

<script>
    var checkout = new WidgetCheckout({
        currency: 'COP',
        amountInCents: parseFloat(<?php echo $recibo->valor; ?>) * 100,
        reference: '<?php echo $recibo->token; ?>',
        publicKey: '<?php echo $this->config->item('api_wompi')['llave_publica']; ?>',
        signature: {integrity : '<?php echo $llave_integridad; ?>'},
        redirectUrl: `${$('#site_url').val()}clientes/respuesta?referencia=<?php echo $recibo->token; ?>`, // Opcional
        // expirationTime: '2023-06-09T20:28:50.000Z', // Opcional
        // taxInCents: { // Opcional
        //     vat: 1900,
        //     consumption: 800
        // }
        // customerData: { // Opcional
        //     email: '<?php // echo $recibo->email; ?>',
        //     fullName: `<?php // echo $recibo->razon_social; ?>`,
        //     phoneNumber: `<?php // echo $recibo->telefono; ?>`,
        //     phoneNumberPrefix: '+57',
        //     // legalId: '123456789',
        //     // legalIdType: 'CC'
        // },
        // shippingAddress: { // Opcional
            // addressLine1: '',
        //     city: "Medellín",
        //     phoneNumber: factura.telefono,
        //     region: "Antioquia",
        //     country: "CO"
        // }
    })

    checkout.open(function (resultado) {
        var transaction = resultado.transaction

        // Se redirecciona a la página de resultados
        location.href = transaction.redirectUrl
        
        // Si se aprobó la transacción, se vacea el carrito
        if(transaction.status == 'APPROVED') vaciarCarritoEstadoCuenta()
    })
</script>