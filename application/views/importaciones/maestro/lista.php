<style>
    /* He mantenido tus estilos, solo ajusté el ID si quisieras usarlo */
    #tabla_anticipos {
        font-size: 0.9em;
        font-family: Futura, sans-serif;
    }

    #tabla_anticipos th {
        background-color: #19287F;
        color: white;
    }
</style>

<div class="table-responsive">
    <table class="table-striped table-bordered" id="tabla_anticipos" style="width:100%">
        <thead>
            <tr>
                <th class="text-center">NIT</th>
                <th class="text-center">Proveedor</th>
                <th class="text-center">Porcentaje Anticipo</th>
                <th class="text-center" style="width: 150px;">Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    $().ready(() => {
        $("#tabla_anticipos").DataTable({
            ajax: {
                url: `${$("#site_url").val()}importaciones/obtener_datos_tabla`,
                data: {
                    // Nombre exacto de la tabla en MySQL
                    tipo: "importaciones_maestro_anticipos", 
                },
            },
            columns: [
                { data: 'nit' },
                { data: 'proveedor' },
                { 
                    data: 'porcentaje',
                    render: function(data, type, row) {
                        // Agregamos el símbolo % visualmente
                        return data ? parseFloat(data).toFixed(2) + '%' : '0%';
                    }
                },
                {
                    data: null, 
                    className: "text-center",
                    render: (data, type, row) => {
                        return `
                            <div class="p-1">
                                <a type="button" class="btn btn-sm btn-primary" 
                                   href="${$("#site_url").val()}importaciones/maestro/editar/${data.id}" 
                                   title="Editar">
                                    <i class="fa fa-pencil"></i>
                                </a>

                                <a type="button" class="btn btn-sm btn-danger" 
                                   href="javascript:eliminarAnticipo(${data.id})" 
                                   title="Eliminar">
                                    <i class="fa fa-close"></i>
                                </a>
                            </div>
                        `
                    }
                }
            ],
            serverSide: true,
            stateSave: true,
            scrollY: '320px',
            searching: true,
            language: {
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            scrollX: false,
            scrollCollapse: true
        })

        $('#contenedor_mensaje_carga').html('')
    })
</script>