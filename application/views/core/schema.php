<?php
$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'AutoPartsStore',
    'name' => 'Repuestos Simón Bolívar',
    'url' => site_url(),
    'logo' => site_url('images/logo.png'),
    'image' => site_url('images/logo.png'),
    'description' => 'Con más de 20 años de experiencia, somos líderes en el mercado de repuestos para vehículos pesados, comprometidos con la calidad y satisfacción de nuestros clientes.',
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => 'Calle 30 # 40 - 10, Itagüí',
        'addressLocality' => 'Itagüí',
        'addressRegion' => 'Antioquia',
        'postalCode' => '000000',
        'addressCountry' => 'CO',
    ],
    'geo' => [
        '@type' => 'GeoCoordinates',
        'latitude' => '6.1800',
        'longitude' => '-75.6200'
    ],
    'contactPoint' => [
        '@type' => 'ContactPoint',
        'telephone' => '+57-318-6325528',
        'contactType' => 'customer service',
        'areaServed' => 'CO',
        'availableLanguage' => 'es',
    ],
    'openingHoursSpecification' => [
        [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => [
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday'
            ],
            'opens' => '08:00',
            'closes' => '18:00'
        ]
    ],
    'sameAs' => [
        'https://www.facebook.com/repuestossimonbolivar',
        'https://www.instagram.com/repuestossimonbolivar',
        'https://www.linkedin.com/company/repuestossimonbolivar'
    ],

    'itemListElement' => [],
];

$productos = $this->productos_model->obtener('productos');

foreach ($productos as $item => $producto) {
    $url = site_url("productos/ver/$producto->slug");

    $schema['itemListElement'][] = [
        '@type' => 'ListItem',
        'position' => $item + 1,
        'url' => $url,
        'item' => [
            '@type' => 'Product',
            'name' => $producto->referencia,
            // 'image' => url_fotos($producto->marca, $producto->referencia),
            'description' => $producto->descripcion_corta,
            'sku' => $producto->referencia,
            'brand' => [
                '@type' => 'Brand',
                'name' => $producto->marca
            ],
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'COP',
                'price' => $producto->precio,
                'availability' => 'https://schema.org/InStock',
                'url' => $url
            ]
        ]
    ];
}

$schema_codificado = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

echo "
<script type='application/ld+json'>
    $schema_codificado
</script>
";