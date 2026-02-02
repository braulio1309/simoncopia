<div class="site__body">
    <div class="block">
        <div class="container container--max--xl">
            <div class="row">
                <div class="col-12 col-lg-6 offset-3 mt-4 mt-lg-0">
                    <div class="card">
                        <div class="card-header">
                            <h4>Cotiza el envío con TCC</h4>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-body card-body--padding--2">
                            <div class="row no-gutters">
                                <div class="form-group col-6">
                                    <label for="envio_departamento_origen">Departamento *</label>
                                    <select id="envio_departamento_origen" class="form-control"></select>
                                </div>

                                <div class="form-group col-6">
                                    <label for="envio_municipio_origen">Municipio *</label>
                                    <select id="envio_municipio_origen" class="form-control"></select>
                                </div>
                            </div>

                            <div class="row no-gutters">
                                <div class="form-group col-6">
                                    <label for="envio_departamento_destino">Departamento *</label>
                                    <select id="envio_departamento_destino" class="form-control"></select>
                                </div>

                                <div class="form-group col-6">
                                    <label for="envio_municipio_destino">Municipio *</label>
                                    <select id="envio_municipio_destino" class="form-control"></select>
                                </div>
                            </div>

                            <div class="row no-gutters">
                                <div class="form-group col-12">
                                    <label for="envio_tipo">Tipo de envío *</label>
                                    <select id="envio_tipo" class="form-control">
                                        <option value="1">Nacional</option>
                                        <option value="2">Urbano</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row no-gutters">
                                <div class="form-group col-4">
                                    <label for="envio_alto">Alto *</label>
                                    <input type="number" class="form-control" id="envio_alto">
                                </div>

                                <div class="form-group col-4">
                                    <label for="envio_ancho">Ancho *</label>
                                    <input type="number" class="form-control" id="envio_ancho">
                                </div>

                                <div class="form-group col-4">
                                    <label for="envio_largo">Largo *</label>
                                    <input type="number" class="form-control" id="envio_largo">
                                </div>

                                <div class="form-group col-12">
                                    <label for="envio_peso">Peso *</label>
                                    <input type="number" class="form-control" id="envio_peso">
                                </div>

                                <div class="form-group col-12">
                                    <label for="envio_valor_declarado">Valor declarado *</label>
                                    <input type="number" class="form-control" id="envio_valor_declarado">
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <button class="btn btn-primary btn-block mt-3" onClick="javascript:cotizarEnvio()">Cotizar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    cotizarEnvio = async () => {
        let codigoMunicipioOrigen = `${$('#envio_departamento_origen option:selected').attr('data-codigo')}${$('#envio_municipio_origen option:selected').attr('data-codigo')}000`
        let codigoMunicipioDestino = `${$('#envio_departamento_destino option:selected').attr('data-codigo')}${$('#envio_municipio_destino option:selected').attr('data-codigo')}000`
        
        let datos = {
            tipo: 'tcc_liquidacion',
            tipoenvio: $('#envio_tipo').val(),                           // 1: Nacional, 2: Urbano
            idciudadorigen: codigoMunicipioOrigen,  // Código DANE de 8 dígitos
            idciudaddestino: codigoMunicipioDestino,// Código DANE de 8 dígitos
            valormercancia: $('#envio_valor_declarado').val(),
            boomerang: 0,                           // Cantidad de documentos tipo boomerag que acompañarán la marcancia
            identificacion: '900296641',            // Número de identificacion de quien realizará el despacho
            cuenta: '5625200',                      // Numero de acuerdo comercial asignado (1485100: Paquetería; 5625200: Mensajería)
            fecharemesa: '2025-09-30',    
            idunidadnegocio: 1,                      // 1: Paqueteria; 2: Mensajeria
            unidades: [
                {
                    numerounidades: 1,
                    pesoreal: 10,               // Si son enviadas las dimensiones, estas priman sobre el valor enviado en volumen, tambien si no se tiene el volumen se puede enviar Cero (0), siempre y cuando se envie el peso real(ancho (metros) * largo (metros) * alto (metros)) * 400 -> (0.4 * 0.4 * 0.3) * 400
                    pesovolumen: $('#envio_peso').val(),
                    alto: $('#envio_alto').val(),           
                    largo: $('#envio_largo').val(),
                    ancho: $('#envio_ancho').val(),
                    tipoempaque: ""
                }
            ]
        }
        // console.log(datos)

        let resultado = await consulta('obtener', datos, false)

        if(resultado.codigoResultado == 0) {
            let mensaje = `
                <b>Id de liquidación:</b> ${resultado.idliquidacion}<br>
                <b>Mensaje:</b> ${resultado.mensajeResultado}<br>
                <b>Días estimados de entrega:</b> ${resultado.total.diasestimadosentrega}<br>
                <b>Costo del envío:</b> $ ${resultado.total.totaldespacho} (${resultado.total.valortarifa})<br>
            `

            mostrarAviso("exito", mensaje, false)
        }
    }

    $().ready(() => {
        listarDatos('envio_departamento_origen', {tipo: 'departamentos', pais_id: 169})
        listarDatos('envio_departamento_destino', {tipo: 'departamentos', pais_id: 169})

        // Cuando se seleccione un departamento
        $('#envio_departamento_origen').change(() => {
            listarDatos('envio_municipio_origen', {tipo: 'municipios', departamento_id: $('#envio_departamento_origen').val()})
        })

        $('#envio_departamento_destino').change(() => {
            listarDatos('envio_municipio_destino', {tipo: 'municipios', departamento_id: $('#envio_departamento_destino').val()})
        })
    })
</script>