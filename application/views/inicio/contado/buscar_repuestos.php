<div class="block-finder block">
    <div class="decor block-finder__decor decor--type--bottom">
        <div class="decor__body">
            <div class="decor__start"></div>
            <div class="decor__end"></div>
            <div class="decor__center"></div>
        </div>
    </div>
    <div class="block-finder__image" style="background-image: url('<?php echo base_url(); ?>images/buscar_respuestos.jpg?');"></div>
    <div class="block-finder__body container container--max--xl">
        <div class="block-finder__title">Encuentra el repuesto perfecto para tu camión</div>
        <div class="block-finder__subtitle">Navega a través de nuestras opciones seleccionando la marca, grupo y línea correspondiente. ¡Encuentra lo que buscas con facilidad y rapidez!</div>
        <form class="block-finder__form">
            <!-- Línea -->
            <div class="block-finder__form-control block-finder__form-control--select">
                <select id="repuesto_linea" aria-label="Línea" >
                    <option value="">Escoja una línea</option>
                    <?php foreach($this->configuracion_model->obtener('lineas') as $linea) echo "<option value='$linea->id'>$linea->nombre</option>"; ?>
                </select>
            </div>
            
            <!-- Grupo -->
            <div class="block-finder__form-control block-finder__form-control--select">
                <select id="repuesto_grupo" aria-label="Grupo" disabled>
                    <option value="">Escoja un grupo</option>
                </select>
            </div>

            <!-- Marca -->
            <div class="block-finder__form-control block-finder__form-control--select">
                <select id="repuesto_marca" aria-label="Marca" disabled>
                    <option value="">Escoja una marca</option>
                </select>
            </div>

            <button class="block-finder__form-control block-finder__form-control--button" type="submit">Buscar</button>
        </form>
    </div>
</div>

<script>
    $().ready(() => {
        $('form').submit(evento => {
            evento.preventDefault()
            
            let filtros = ''

            if($('#repuesto_linea').val()) filtros += `?linea=${$('#repuesto_linea option:selected').text()}`
            if($('#repuesto_grupo').val()) filtros += `&grupo=${$('#repuesto_grupo option:selected').text()}`
            if($('#repuesto_marca').val()) filtros += `&marca=${$('#repuesto_marca option:selected').text()}`

            location.href = `${$('#site_url').val()}productos${filtros}`
        })

        $('#repuesto_linea').change(async() => {
            $(`#repuesto_grupo`).html('')
            
            let grupos = await consulta('obtener', {
                tipo: 'grupos',
                linea: $('#repuesto_linea option:selected').text()
            })
            
            $(`#repuesto_grupo`).append(`<option value=''>Seleccione un grupo</option>`)

            $.each(grupos, function(key, grupo){
                $(`#repuesto_grupo`).append(`
                    <option value='${grupo.id}'>${grupo.nombre}</option>
                `)
            })

            $('#repuesto_grupo').attr('disabled', false)
        })

        $('#repuesto_grupo').change(async() => {
            $(`#repuesto_marca`).html('')
            
            let marcas = await consulta('obtener', {
                tipo: 'marcas',
                grupo: $('#repuesto_grupo option:selected').text(),
                linea: $('#repuesto_linea option:selected').text()
            })

            if(marcas.length == 0) $(`#repuesto_marca`).append(`<option value=''>No hay resultados. Elige otra línea y/o grupo.</option>`)
            if(marcas.length > 0) $(`#repuesto_marca`).append(`<option value=''>Elige una marca</option>`)

            $.each(marcas, function(key, marca){
                $(`#repuesto_marca`).append(`
                    <option value='${marca.id}'>${marca.nombre}</option>
                `)
            })

            $('#repuesto_marca').attr('disabled', false)
        })
    })
</script>