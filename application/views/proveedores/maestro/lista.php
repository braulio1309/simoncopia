<style>
    #tabla_productos_metadatos {
        font-size: 0.8em;
        font-family: Futura;
    }

    #tabla_productos_metadatos th {
        background-color: #19287F;
        color: white;
    }
</style>

<div class="table-responsive">
    <table class="table-striped table-bordered" id="tabla_marcas">
        <thead>
            <tr>
                <th class="text-center">Código</th>
                <th class="text-center">Marca</th>
                <th class="text-center">NIT</th>
                <th class="text-center">Proveedor</th>
                <th class="text-center" style="width: 200px;">Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    $().ready(() => {
        $("#tabla_marcas").DataTable({
            ajax: {
                url: `${$("#site_url").val()}proveedores/obtener_datos_tabla`,
                data: {
                    tipo: "proveedores_marcas",
                },
            },
            columns: [
                { data: 'marca_codigo' },
                { data: 'marca_nombre' },
                { data: 'proveedor_nit' },
                { data: 'proveedor_nombre' },
                {
                    data: null, 
                    render: (data, type, row) => {
                        return `
                            <div class="p-1">
                                <a type="button" class="btn btn-sm btn-primary" href="${$("#site_url").val()}proveedores/maestro/editar/${data.id}">
                                    <i class="fa fa-pencil"></i>
                                </a>

                                <a type="button" class="btn btn-sm btn-danger" href="javascript:eliminarMarcas(${data.id})">
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