<?php
// Se obtiene un arreglo con todos los productos destacados que se van a mostrar al inicio
$productos = $this->productos_model->obtener('productos_destacados');
$productos_promocion = $this->productos_model->obtener('productos_promocion');
$productos_mas_vendidos = $this->productos_model->obtener('productos_mas_vendidos');

$cantidad_productos = count($productos);
$cantidad_productos_por_bloque = intval($cantidad_productos / 8);
$posicion = 0;

// Slider
$this->load->view('inicio/contado/slider');

// Promociones
if(count($productos_promocion) > 0) {
    $this->data['productos'] = $productos_promocion;
    $this->load->view('inicio/contado/promociones', $this->data);
    echo "<div class='block-space block-space--layout--divider-nl'></div>";
}

// Información de envíos, pago seguro y garantía
$this->load->view('inicio/contado/caracteristicas');
echo "<div class='block-space block-space--layout--divider-nl'></div>";

// Buscador de repuestos
$this->load->view('inicio/contado/buscar_repuestos');
echo "<div class='block-space block-space--layout--divider-nl'></div>";

// Productos más vendidos
$this->data['productos'] = $productos_mas_vendidos;
$this->load->view('inicio/contado/productos_mas_vendidos', $this->data);
echo "<div class='block-space block-space--layout--divider-nl'></div>";

// Bloque de marcas y productos destacados
echo "
<div class='block block-zone'>
    <div class='container'>
        <div class='block-zone__body'>";
            $this->data['desde'] = $posicion;   // Posición del arreglo donde comenzará
            $posicion += $cantidad_productos_por_bloque;
            $this->data['hasta'] = $posicion;  // Posición del arreglo donde terminará
            $this->data['productos'] = $productos;
            $this->data['tipo'] = 'marca';
            $this->load->view('inicio/contado/bloques/marcas');
            $this->load->view('inicio/contado/bloques/productos_destacados', $this->data);
        echo "
        </div>
    </div>
</div>";
echo "<div class='block-space block-space--layout--divider-nl'></div>";

// Productos nuevos
$this->data['desde'] = $posicion;   // Posición del arreglo donde comenzará
$posicion += $cantidad_productos_por_bloque;
$this->data['hasta'] = $posicion;  // Posición del arreglo donde terminará
$this->data['productos'] = $productos;
$this->load->view('inicio/contado/nuevos_productos', $this->data);
echo "<div class='block-space block-space--layout--divider-nl'></div>";

// Bloque de grupos y productos destacados
echo "
<div class='block block-zone'>
    <div class='container'>
        <div class='block-zone__body'>";
            $this->data['desde'] = $posicion;   // Posición del arreglo donde comenzará
            $posicion += $cantidad_productos_por_bloque;
            $this->data['hasta'] = $posicion;  // Posición del arreglo donde terminará
            $this->data['productos'] = $productos;
            $this->data['tipo'] = 'grupo';
            $this->load->view('inicio/contado/bloques/grupos');
            $this->load->view('inicio/contado/bloques/productos_destacados', $this->data);
            echo "
        </div>
    </div>
</div>";
echo "<div class='block-space block-space--layout--divider-nl'></div>";

// Marcas
$this->load->view('inicio/contado/marcas');
echo "<div class='block-space block-space--layout--divider-nl'></div>";

// Bloque de líneas y productos destacados
echo "
<div class='block block-zone'>
    <div class='container'>
        <div class='block-zone__body'>";
            $this->data['desde'] = $posicion;   // Posición del arreglo donde comenzará
            $posicion += $cantidad_productos_por_bloque;
            $this->data['hasta'] = $posicion;  // Posición del arreglo donde terminará
            $this->data['productos'] = $productos;
            $this->data['tipo'] = 'linea';
            $this->load->view('inicio/contado/bloques/lineas');
            $this->load->view('inicio/contado/bloques/productos_destacados', $this->data);
        echo "
        </div>
    </div>
</div>";
echo "<div class='block-space block-space--layout--divider-nl'></div>";

/****************************************************************************************
 *************************** Banners de outlet y promociones ****************************
 ****************************************************************************************/
// $this->load->view('inicio/contado/bloques_banners');
// echo "<div class='block-space block-space--layout--divider-nl'></div>";

/****************************************************************************************
 *********************************** Últimas noticias ***********************************
 ****************************************************************************************/
// $this->load->view('inicio/contado/ultimas_noticias');
// echo "<div class='block-space block-space--layout--divider-nl'></div>";

/****************************************************************************************
 ********************************* Resumen de productos *********************************
 ****************************************************************************************/
echo 
"<div class='block block-products-columns'>
    <div class='container'>
        <div class='row'>";
            // Columna 1
            $this->data['desde'] = $posicion;   // Posición del arreglo donde comenzará
            $posicion += $cantidad_productos_por_bloque;
            $this->data['hasta'] = $posicion;  // Posición del arreglo donde terminará
            $this->data['productos'] = $productos;
            $this->data['titulo'] = 'Para tí';
            $this->load->view('inicio/contado/footer_productos_destacados', $this->data);

            // Columna 2
            $this->data['desde'] = $posicion;   // Posición del arreglo donde comenzará
            $posicion += $cantidad_productos_por_bloque;
            $this->data['hasta'] = $posicion;  // Posición del arreglo donde terminará
            $this->data['productos'] = $productos;
            $this->data['titulo'] = 'Destacados';
            $this->load->view('inicio/contado/footer_productos_destacados', $this->data);
            
            // Columna 3
            $this->data['desde'] = $posicion;   // Posición del arreglo donde comenzará
            $posicion += $cantidad_productos_por_bloque;
            $this->data['hasta'] = $posicion;  // Posición del arreglo donde terminará
            $this->data['productos'] = $productos;
            $this->data['titulo'] = 'Ofertas';
            $this->load->view('inicio/contado/footer_productos_destacados', $this->data);
        echo "
        </div>
    </div>
</div>";
echo "<div class='block-space block-space--layout--divider-nl'></div>";
?>

<script>
    // Se elimina el valor que haya en la búsqueda, para que no interfiera en posteriores búsquedas
    localStorage.removeItem('simonBolivar_buscarProducto')
</script>