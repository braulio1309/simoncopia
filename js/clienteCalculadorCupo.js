/**
 * clase se se encarga de calcular el cupo de un cliente,
 * basado en los parámetros indicados como regla de negocio
 */
class ClienteCalculadorCupo {
    constructor(numeroDocumento) {
        this.numeroDocumento = numeroDocumento      // NIT
        this.cupoTotal = 0                          // Cupo asignado al cliente
        this.valorCarteraRegular = 0                // Facturas del módulo pagos con cuota = 0
        this.valorCarteraCuotas = 0                 // Facturas que correspondan a una cuota y cuotas donde el mes sea menor o igual al mes actual
        this.ValorPedidosNoFacturados = 0           // Pedidos no facturados del cliente
        this.valorFacturasLlantas = 0               // Facturas en llantas (Good Year y en el nombre contiene llantas) hasta por 10 millones
        this.valorLimiteLlantas = 10000000          // Valor límite para la cartera de llantas
        this.valorCupoRestante = 0                  // Valor del cupo faltante
        this.facturasPendientes = []                // Almacena las facturas pendientes de pago
        this.porcentajeCupoRestante = 0             // Porcentaje del cupo restante
        this.imagenCupoRestante = ''                // Ícono para mostrar indicador de estado del cupo
        this.estadoTitulo = ''
        this.estadosubtitulo = ''
        this.fechaActual = new Date()
    }

    /**
     * Método principal para calcular el cupo restante
     * @returns {Promise<Object>} Resultado del cálculo
     */
    async calcularValorCupoRestante() {
        try {
            // 1. Obtener cupo total desde API Clientes
            await this.obtenerCupoTotal()
            
            // 2. Calcular cartera regular
            await this.calcularValorCarteraRegular()
            
            // 3. Calcular cartera a cuotas
            await this.calcularvalorCarteraCuotas()
            
            // 6. Calcular cupo restante
            this.calcularCupoFinal()

            // 7. Calcular estado de la cartera
            await this.calcularEstadoCartera()
            
            return this.obtenerResultado()
        } catch (error) {
            console.error('Error calculando cupo:', error)
            throw error
        }
    }

    /**
     * Devuelve el valor del cupo asignado al cliente en la sucursal 001
     */
    async obtenerCupoTotal() {
        try {
            const resultado = await consulta('obtener', {tipo: 'clientes_sucursales', numero_documento: this.numeroDocumento})

            // Si no existe el cliente
            if(resultado.codigo != 0) this.cupoTotal = false
            
            // Asignación del cupo
            let cliente = resultado.detalle.Table[0]
            this.cupoTotal = parseFloat(cliente.f201_cupo_credito)

            // console.log(`Cupo total obtenido: $${this.cupoTotal.toLocaleString()}`)
        } catch (error) {
            return {
                exito: false,
                mensaje: '⚠️ Ocurrió un error al conectarse a la API del ERP para obtener el cupo del cliente'
            }
        }
    }

    /**
     * 2. Calcula cartera regular: facturas que no tienen número de cuota
     */
    async calcularValorCarteraRegular() {
        try {
            // Consulta de las facturas del cliente
            let resultadoEstadoCuenta = await consulta('obtener', {tipo: 'estado_cuenta_cliente', numero_documento: this.numeroDocumento}, false)

            // Las facturas son creadas en la base de datos, si existen
            if(resultadoEstadoCuenta.codigo == 0) await consulta('crear', {tipo: 'clientes_facturas', valores: resultadoEstadoCuenta.detalle.Table}, false)

            this.facturasPendientes = await consulta('obtener', {
                tipo: 'clientes_facturas',
                numero_documento: this.numeroDocumento,
                pendientes: true,
            }, false)

            // Toma todas las facturas que llegaron, suma el valor filtrando por cuota = 0
            this.valorCarteraRegular = this.facturasPendientes
                .filter(factura => factura.Nro_cuota == 0)
                .reduce((total, factura) => total + parseFloat(factura.totalCop), 0)
                
            // console.log(`Cartera regular calculada: $${this.valorCarteraRegular.toLocaleString()}`)
        } catch (error) {
            return {
                exito: false,
                mensaje: '⚠️ Ocurrió un error al conectarse a la API del ERP para calcular la cartera regular'
            }
        }
    }

    /**
     * 3. Calcula cartera a cuotas: facturas con cuota mayor a 0
     * y que su mes de vencimiento sea menor o igual al mes actual
     */
    async calcularvalorCarteraCuotas() {
        try {
            const mesActual = this.fechaActual.getMonth() + 1 // getMonth() devuelve 0-11

            // Toma todas las facturas que llegaron, suma el valor filtrando los que tengan un número de cuota mayor a cero
            this.valorCarteraCuotas = this.facturasPendientes
                .filter(factura => parseInt(factura.Nro_cuota) > 0 && parseInt(factura.mes_vencimiento) <= mesActual)
                .reduce((total, factura) => total + parseFloat(factura.totalCop), 0)
                
            // console.log(`Cartera a cuotas calculada: $${this.valorCarteraCuotas.toLocaleString()}`)
        } catch (error) {
            return {
                exito: false,
                mensaje: '⚠️ Ocurrió un error al conectarse a la API del ERP para calcular la cartera a cuotas'
            }
        }
    }

    /**
     * 6. Calcular cupo restante final
     * Fórmula: Cupo total - cartera normal - cartera cuotas - pedidos no facturados + facturas llantas
     */
    calcularCupoFinal() {
        this.valorCupoRestante = this.cupoTotal 
            - this.valorCarteraRegular 
            - this.valorCarteraCuotas 
            - this.ValorPedidosNoFacturados 
            + this.valorFacturasLlantas
        
        this.valorCupoRestante = (this.valorCupoRestante > 0) ? this.valorCupoRestante : 0

        // console.log(`Cupo restante calculado: $${this.valorCupoRestante.toLocaleString()}`)
    }

    calcularEstadoCartera = async () => {
        // Cálculo del porcentaje de cupo restante
        this.porcentajeCupoRestante = parseFloat(((this.valorCupoRestante / this.cupoTotal) * 100).toFixed(2))
        
        // Cartera al día
        if(this.porcentajeCupoRestante < 100) {
            this.imagenCupoRestante = 'cartera_al_dia.svg'
            this.estadoTitulo = 'Tu cartera se encuentra al día'
            this.estadosubtitulo = 'Puedes comprar y usar tu cupo con normalidad'
        }

        // Cartera en riesgo
        if(this.porcentajeCupoRestante <= 60) {
            this.imagenCupoRestante = 'cartera_riesgo.svg'
            this.estadoTitulo = 'Tu cartera se encuentra en seguimiento'
            this.estadosubtitulo = 'Te recomendamos regularizar tus pagos para evitar restricciones'
        }

        // Cartera con restricciones
        if(this.porcentajeCupoRestante <= 30) {
            this.imagenCupoRestante = 'cartera_negativa.svg'
            this.estadoTitulo = 'Tu cartera se encuentra con restricciones'
            this.estadosubtitulo = 'Para continuar con pedidos a crédito, es necesario regularizar tu cartera'
        }
    }

    /**
     * Obtener resultado completo en formato estructurado
     * @returns {Object} Resultado del cálculo
     */
    obtenerResultado() {
        return {
            numeroDocumento: this.numeroDocumento,
            fechaCalculo: this.fechaActual.toISOString(),
            cupoTotal: this.cupoTotal,
            valorCarteraRegular: this.valorCarteraRegular,
            valorCarteraCuotas: this.valorCarteraCuotas,
            valorCupoRestante: this.valorCupoRestante,
            porcentajeCupoRestante: this.porcentajeCupoRestante,
            imagenCupoRestante: this.imagenCupoRestante,
            estadoTitulo: this.estadoTitulo,
            estadoSubtitulo: this.estadosubtitulo,
            formula: `Cupo restante = ${this.cupoTotal} - ${this.valorCarteraRegular} - ${this.valorCarteraCuotas} - ${this.ValorPedidosNoFacturados} + ${this.valorFacturasLlantas}`
        }
    }
}