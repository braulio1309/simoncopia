const agregarLog = async (tipoId, observacion = null) => {
    let datos = {
        tipo: 'logs',
        log_tipo_id: tipoId,
    }

    if(observacion) datos.observacion = observacion

    await consulta('crear', datos, false)
}

cargarFiltros = async(tipo, elemento, datos) => {
    switch(tipo) {
        case 'grupos':
            $(`#${elemento}`).html('')

            marca = await consulta('obtener', {
                tipo: 'marca',
                nombre: datos.marcaNombre
            })

            grupos = await consulta('obtener', {
                tipo: 'grupos',
                marca: datos.marcaNombre
            })

            $.each(grupos, function(key, grupo){
                $(`#${elemento}`).append(`
                    <li class='megamenu-links__item'>
                        <a class='megamenu-links__item-link' href='${$('#site_url').val()}productos?marca=${marca.nombre}&grupo=${grupo.nombre}'>${grupo.nombre}</a>
                    </li>
                `)
            })
        break;

        case 'lineas':
            $(`#${elemento}`).html('')

            marca = await consulta('obtener', {
                tipo: 'marca',
                nombre: datos.marcaNombre
            })

            lineas = await consulta('obtener', {
                tipo: 'lineas',
                marca: datos.marcaNombre
            })

            $.each(lineas, function(key, linea){
                $(`#${elemento}`).append(`
                    <li class='megamenu-links__item'>
                        <a class='megamenu-links__item-link' href='${$('#site_url').val()}productos?marca=${marca.nombre}&linea=${linea.nombre}'>${linea.nombre}</a>
                    </li>
                `)
            })
        break;
    }
}

cargarInterfaz = async(vista = 'index', contenedor = 'contenedor_principal', datos = null, tipo = null) => {
    // Se muestra la carga
    $('#cargando').show()

    $(`#${contenedor}`).html('')

    // Carga de la interfaz
    $(`#${contenedor}`).load(`${$("#site_url").val()}interfaces`, {tipo: tipo, vista: vista, datos: datos}, (respuesta, estado, xhr) => {
        // Si hay error
        if(estado == 'error') console.error(xhr)

        // Si fue exitoso, se oculta la carga
        if(estado == 'success') $("#cargando").hide()
    })
}

cargarMasDatos = tipo => {
    // Se aumenta el contador
    localStorage.simonBolivar_contador = (localStorage.simonBolivar_contador)
    ? parseInt(localStorage.simonBolivar_contador) + parseInt($('#cantidad_datos').val())
    : 0

    var datos = {
        tipo: tipo,
        contador: parseInt(localStorage.simonBolivar_contador),
        busqueda: $("#buscar").val(),
    }

    if($('#filtro_marca')) datos.marca = $('#filtro_marca').val()
    if($('#filtro_grupo')) datos.grupo = $('#filtro_grupo').val()
    if($('#filtro_linea')) datos.linea = $('#filtro_linea').val()
    if($('#recibo_id_tipo')) datos.id_tipo_recibo = $('#recibo_id_tipo').val()

    $.ajax({
        url: `${$('#site_url').val()}interfaces/cargar_mas_datos`,
        data: {datos: datos},
        type: 'POST',
        // beforeSend: () => $('#cargando').show()
    })
    .done(data => {
        $("#datos").append(data)

        // $('#cargando').hide()
    })
    .fail((jqXHR, ajaxOptions, thrownError) => console.error('El servidor no responde.'))
}

confirmar = (tipo, mensaje) => {
    let respuesta = Swal.fire({
        html: mensaje,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#218838',
        cancelButtonColor: '#d33',
        confirmButtonText: `Si, ${tipo}`,
        cancelButtonText: 'No, cancelar'

    }).then((resultado) => {
        return resultado.isConfirmed
    })

    return respuesta
}

consulta = (tipo, datos, notificacion = true, mensaje = '') => {
    let respuesta = obtenerPromesa(`${$('#site_url').val()}interfaces/${tipo}`, datos)
        .then(resultado => {
            switch (tipo) {
                case "actualizar":
                    if (notificacion) mostrarAviso('exito', 'Se actualizaron los datos')
                    return resultado;
                break;

                case "crear":
                    if (notificacion) mostrarAviso('exito', 'Se almacenaron los datos')
                    return resultado;
                break;

                case "eliminar":
                    if (notificacion) mostrarAviso('exito', 'Se eliminaron los datos')
                    return resultado;
                break;

                case "obtener":
                    return resultado;
                break;

                default:
                    return resultado;
                break;
            }

        }).catch(error => console.error(error))

    return respuesta
}

/**
 * Toma un texto de enlace y lo copia al portapapeles
 */
const copiar_enlace = enlace => {
    navigator.clipboard.writeText(enlace)
    .then(() => {
        mostrarAviso('exito', '¡Enlace copiado al portapapeles!')
    })
    .catch(error => {
        mostrarAviso('error', `Error al copiar: ${error}`)
    });
}

/**
 * Crea en Siesa el tercero cliente
 */
 crearTerceroCliente = async(datos) => {
    // Swal.fire({
    //     title: 'Estamos cargando la información...',
    //     text: 'Por favor, espera.',
    //     imageUrl: `${$('#base_url').val()}images/cargando.webp`,
    //     showConfirmButton: false,
    //     allowOutsideClick: false
    // })

    datos.tipo = 'tercero_cliente'

    // Se cargan los movimientos de la factura
    return consulta('crear', datos, false)
    .then(consulta => {
        // Swal.close()
        
        return consulta.resultado
    })
}

/**
 * Obtiene un número y le agrega
 * separadores de mil
 */
formatearNumero = numero => {
    let signo = (numero < 0) ? '-' : ''

    // numero = Math.floor(numero)
    numero = String(numero).replace(/\D/g, "")
    return numero === '' ? signo + numero : signo + Number(numero).toLocaleString('es-ES')
}

/**
 * Genera reportes en diferentes formatos
 */
generarReporte = (tipo, datos) => {
    // Modal de espera al generar los reportes
    Swal.fire({
        title: 'Procesando...',
        text: 'Por favor espera mientras se cargan los datos.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    })

    switch (tipo) {
        case 'excel/consecutivos_detalle':
            agregarLog(110, datos.comprobante_contable_tarea_id)
            url = `${$('#base_url').val()}reportes/${tipo}/${datos.comprobante_contable_tarea_id}`
        break;

        case 'excel/facturas':
            agregarLog(42, datos.numero_documento)
            location.assign(`${$('#base_url').val()}reportes/${tipo}/${datos.numero_documento}`)
        break;

        case 'excel/proveedores_cotizaciones_matriz':
            agregarLog(61, datos.id)
            location.assign(`${$('#base_url').val()}reportes/${tipo}/${datos.id}`)
        break;

        case 'excel/proveedores_orden_compra':
            agregarLog(70, datos.id)
            url = `${$('#base_url').val()}reportes/${tipo}/${datos.id}`
        break;

        case 'excel/proveedores_maestro_marcas':
            url = `${$('#base_url').val()}reportes/${tipo}`
            agregarLog(106)
        break;

        case 'pdf/proveedores_certificado_retenciones':
            agregarLog(90, JSON.stringify(datos))
            url = `${$('#base_url').val()}reportes/${tipo}/${datos.documento_numero}/${datos.anio}`
            location.assign(url)
        break;

        case 'pdf/proveedores_comprobante_egreso':
            agregarLog(84, datos.id)
            location.assign(`${$('#site_url').val()}reportes/${tipo}/${datos.id}`)
        break;

        case 'pdf/solicitud_credito':
            location.assign(`${$('#base_url').val()}reportes/${tipo}/${datos.solicitud_id}`)
        break;
    }

    fetch(url)
    .then(respuesta => {
        if (!respuesta.ok) throw new Error("Error descargando archivo")

        // Obtener nombre desde el servidor
        let nombreArchivo = "";
        const disposition = respuesta.headers.get("Content-Disposition")

        if (disposition && disposition.indexOf("filename=") !== -1) {
            const matches = disposition.match(/filename="(.+)"/)

            if (matches && matches.length > 1) nombreArchivo = matches[1]
        }

        return respuesta.blob().then(blob => ({ blob, nombreArchivo }))
    })
    .then(({ blob, nombreArchivo }) => {
        // Crea URL del blob
        const urlBlob = window.URL.createObjectURL(blob)
        const a = document.createElement("a")
        a.href = urlBlob
        a.download = nombreArchivo || "Archivo.xlsx" // fallback si no viene el nombre
        a.click()
        window.URL.revokeObjectURL(urlBlob)

        Swal.close()
        mostrarAviso('exito', 'Se generó el reporte')
    })
    .catch(error => {
        Swal.close()
        mostrarAviso('error', 'Hubo un problema generando el reporte')
        console.error(error)
    })
}

/**
 * Genera un string aleatorio (letras + números)
 */
const generarToken = () => Math.random().toString(36).substring(2, 10)

/**
 * Obtiene las sucursales del API de Siesa y las almacena en la base de datos
 */
gestionarSucursales = async(numeroDocumento) => {
    // Mensaje mientras se consultan los datos
    $('#contenedor_mensaje_carga').html(`<button class='btn btn-muted btn-loading btn-xs btn-icon'></button> Consultando las sucursales del cliente...`)

    // Se eliminan todas las sucursales del tercero
    await consulta('eliminar', {tipo: 'clientes_sucursales', f200_nit: numeroDocumento}, false)

    var paginas = 100
    var exito = true

    // Se recorren las páginas
    for (let pagina = 1; pagina <= paginas; pagina++) {
        // Se obtienen los registros en esa página
        await consulta('obtener', {tipo: 'clientes_sucursales', numero_documento: numeroDocumento, pagina: pagina}, false)
        .then(sucursales => {
            // Si se obtuvieron registros, se insertan en la base de datos
            if(sucursales.codigo == 0) consulta('crear', {tipo: 'clientes_sucursales', valores: sucursales.detalle.Table}, false)

            // Si no hay más registros, se cambia la variable
            if(sucursales.codigo == 1) exito = false
            
            return exito
        })
        
        // Se detiene el ciclo si no hay más registros
        if(!exito) break
    }
}

const iniciarSesion = async(evento, url = null, nombreElementoLogin, nombreElementoClave) => {
    evento.preventDefault()

    let nombreUsuario = $(`#${nombreElementoLogin}`)
    let clave = $(`#${nombreElementoClave}`)

    let campos = [
        nombreUsuario,
        clave,
    ]

    // Validación de campos obligatorios
    if (!validarCamposObligatorios(campos)) {
        mostrarAviso('alerta', 'Hay campos obligatorios por diligenciar')
        return false
    }

    let datos = {
        tipo: 'usuario',
        login: $.trim(nombreUsuario.val()),
        clave: $.trim(clave.val()),
    }

    let usuario = await obtenerPromesa(`${$('#site_url').val()}sesion/obtener_datos`, datos)
    
    // Si no se encontró el usuario
    if(!usuario) {
        mostrarAviso('alerta', 'El usuario y clave que ha digitado no existen en la base de datos. Por favor verifique nuevamente.')
        return false
    }

    // Si el usuario está desactivado
    if(usuario.estado == 0) {
        mostrarAviso('error', `El usuario ${nombreUsuario.val()} se encuentra desactivado.`)
        return false
    }

    // Se genera el inicio de sesión
    let sesion = await obtenerPromesa(`${$('#site_url').val()}sesion/iniciar`, {id: usuario.id})

    // Si tuvo éxito, se redirecciona
    if(sesion) {
        if(url) {
            location.href = url
        } else {
            location.href = `${$('#site_url').val()}inicio`
        }
    }
}

/**
 * Toma una cadena de texto y le elimina valores alfabéticos
 */
const limpiarCadena = (valor, permitirEspacios = false) => {
    if(permitirEspacios) {
        return valor.replace(/[^a-zA-Z0-9\s]/g, '')
    } else {
        return valor.replace(/[\a-z\&\/\\#,+()$~%.'":*?<>{}/ /_|¿?\-\°!=¡]/g, '')
    }
} 

listarDatos = async(elemento, datos = null) => {
    $(`#${elemento}`).html('').append("<option value=''>Seleccione...</option>")
                                    
    let resultado = await consulta('obtener', datos)
    var campoId

    registros = (datos.tipo == 'terceros_local' || datos.tipo == 'clientes_sucursales_local') ? resultado.resultado : resultado

    $.each(registros, (index, registro) => {
        // Dependiendo del tipo de solicitud, el id será distinto
        switch (datos.tipo) {
            case 'departamentos':
            case 'municipios':
                campoId = registro.codigo
            break;

            case 'clientes_sucursales_local':
                campoId = registro.f200_id_sucursal
            break;

            case 'terceros_local':
                campoId = registro.f200_nit
            break;
        
            default:
                campoId = registro.id
            break;
        }
            
        $(`#${elemento}`).append(`<option value="${campoId}" data-codigo="${registro.codigo}">${registro.nombre}</option>`)
    })
}

const mostrarAviso = (tipo, mensaje, tiempo = 2000, callback = null) => {
    switch (tipo) {
        case 'exito':
            titulo = 'Éxito'
            icono = 'success'
        break;

        case 'error':
            titulo = 'Error'
            icono = 'error'
        break;

        case 'alerta':
            titulo = 'Alerta'
            icono = 'warning'
        break;

        case 'info':
            titulo = 'Información'
            icono = 'info'
        break;

        case 'pregunta':
            titulo = 'Pregunta'
            icono = 'question'
        break;
    }

    Swal.fire({
        customClass: { confirmButton: "btn btn-primary w-xs mb-2" },
        confirmButtonText: 'Aceptar',
        icon: icono,
        html: mensaje,
        timer: tiempo,
        title: titulo,
        timerProgressBar: true,
        didClose: () => {
            if (callback) callback()
        }
    })
}

const mostrarNotificacion = (datos) => {
    if(datos.tipo == 'carrito_nuevo_producto') {
        datos.titulo = 'Nuevo producto agregado al carrito'
    }

    if(datos.tipo == 'carrito_producto_eliminado') {
        datos.titulo = 'Producto eliminado del carrito'
    }

    cargarInterfaz('core/notificacion', 'contenedor_notificacion', datos)

    setTimeout(() => $('.notification').addClass('ocultar'), 3000);
}

const obtenerPromesa = (url, opciones) => {
    return new Promise((resolve, reject) => {
        // Datos a enviar                
        var datos = new FormData()
        datos.append('datos', JSON.stringify(opciones))

        // Creación de solicitud http
        const xhttp = new XMLHttpRequest()
        xhttp.open(`POST`, url, true)

        // Cuando cambie el estado
        xhttp.onreadystatechange = (() => {
            if(xhttp.readyState === 4) {
                // Si el estado es exitoso
                (xhttp.status === 200)
                    ? resolve(JSON.parse(xhttp.responseText)) // Envía el string del peso
                    : reject(new Error('Error', url)) // Envía el error
            }
        })

        // Se envía la solicitud
        xhttp.send(datos)
    }) 
}

const paginar = (cantidadItems, numeroPagina, itemsPorPagina) => {
    // Si no se define el número de la página, inicia en 1
    if(isNaN(numeroPagina)) numeroPagina = 1

    // Se calcula cuántas páginas creará
    let cantidadPaginas = Math.ceil(cantidadItems / itemsPorPagina)
    
    // Si la página que se solicita es mayor a la que pueda existir, se trae la última
    if(numeroPagina > cantidadPaginas) numeroPagina = cantidadPaginas

    numeroPagina -= 1
    let desde = numeroPagina * itemsPorPagina

    // Lógica para la página siguiente
    // Al llegar a la última página, volverá a la primera;
    // Sino, pasará a la siguiente
    let paginaSiguiente = (numeroPagina >= cantidadPaginas - 1) ? 1 : numeroPagina + 2 ;

    // Lógica para la página anterior
    // Al llegar a la primera página, volverá a la primera;
    // Sino, pasará a la anterior
    let paginaAnterior = (numeroPagina < 1) ? cantidadPaginas : numeroPagina ;

    let respuesta = {
        cantidad_items: cantidadItems,
        cantidad_paginas: cantidadPaginas,
        desde: desde,
        items_por_pagina: itemsPorPagina,
        pagina_actual: (numeroPagina + 1),
        pagina_siguiente: paginaSiguiente,
        pagina_anterior: paginaAnterior
    }

    return respuesta
}

const validarCamposObligatorios = (campos, mensaje = null) => {
    let validacionesExitosas = campos.length
    let exito = true

    //Recorrido para validar cada campo
    for (var i = 0; i < campos.length; i++){
        // Se remueve la validación a todos los campos
        $(`.invalid-feedback`).remove()
        campos[i].removeClass(`is-invalid`)
        
        // Si el campo está vacío
        if($.trim(campos[i].val()) == "") {
            // Se resta el campo al total de validaciones exitosas
            validacionesExitosas--

            // Se marcan los campos en rojo con un mensaje
            campos[i].addClass(`is-invalid`)
            // campos[i].after(`<div class="invalid-feedback">Este campo no puede estar vacío</div>`)
        }
    }

    // Si los exitosos son todos
    if(validacionesExitosas != campos.length) {
        mostrarAviso('alerta', mensaje || 'Hay campos obligatorios por diligenciar')

        // No es exitoso
        exito = false
    }
    
    return exito
}

validarCamposTipoRadio = (campos) => {
    let validacionesExitosas = campos.length
    let exito = true

    for (var i = 0; i < campos.length; i++){
        let elementos = $(`input[name^='${campos[i]}']`)
        elementos.removeClass(`is-invalid`)

        if (!elementos.is(':checked')) {
            // Se resta el campo al total de validaciones exitosas
            validacionesExitosas--

            // Se marcan los campos en rojo con un mensaje
            elementos.addClass(`is-invalid`)
        }
    }

    // Si los exitosos son todos
    if (validacionesExitosas != campos.length) {
        mostrarAviso('alerta', 'Hay campos obligatorios por diligenciar')

        // No es exitoso
        exito = false
    }

    return exito
}