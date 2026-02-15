# Consultas SQL - Tabla marketing_beneficios

## Estructura de la Tabla

La tabla `marketing_beneficios` contiene la información de los beneficios de marketing (promociones y códigos de descuento).

```sql
CREATE TABLE `marketing_beneficios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha_creacion` datetime NULL,
  `usuario_id` int NULL,
  `nombre` varchar(255) NULL,
  `beneficio_tipo` varchar(50) NULL COMMENT 'promoción o código descuento',
  `codigo_descuento` varchar(100) NULL,
  `reglas` text NULL,
  `fecha_inicio` date NULL,
  `fecha_final` date NULL,
  `presupuesto` decimal(15,2) NULL COMMENT 'Valor máximo del presupuesto',
  `valor_usado` decimal(15,2) DEFAULT 0.00 COMMENT 'Valor usado del presupuesto',
  `limite_uso` int NULL COMMENT 'Cantidad de items en promoción',
  `tipo_venta` varchar(20) NULL COMMENT 'contado o crédito',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Consulta Principal - Mostrar Datos Solicitados

Esta consulta muestra los datos requeridos: Nombre, Tipo, Código de descuento, Valor presupuesto, Valor usado, Fecha de inicio, Fecha final.

```sql
SELECT 
    nombre AS 'Nombre',
    beneficio_tipo AS 'Tipo',
    IFNULL(codigo_descuento, '-') AS 'Código de descuento',
    CONCAT('$', FORMAT(presupuesto, 0)) AS 'Valor presupuesto',
    CONCAT('$', FORMAT(valor_usado, 0)) AS 'Valor usado',
    fecha_inicio AS 'Fecha de inicio',
    fecha_final AS 'Fecha final'
FROM 
    marketing_beneficios
ORDER BY 
    fecha_creacion DESC;
```

## Consulta Completa con Todos los Campos

```sql
SELECT 
    id,
    fecha_creacion,
    usuario_id,
    nombre,
    beneficio_tipo,
    codigo_descuento,
    reglas,
    fecha_inicio,
    fecha_final,
    presupuesto,
    valor_usado,
    limite_uso,
    tipo_venta
FROM 
    marketing_beneficios
ORDER BY 
    id DESC;
```

## Consultas Adicionales Útiles

### 1. Beneficios Activos (vigentes hoy)

```sql
SELECT 
    nombre AS 'Nombre',
    beneficio_tipo AS 'Tipo',
    codigo_descuento AS 'Código',
    presupuesto AS 'Presupuesto',
    valor_usado AS 'Usado',
    fecha_inicio AS 'Inicio',
    fecha_final AS 'Final'
FROM 
    marketing_beneficios
WHERE 
    fecha_inicio <= CURDATE() 
    AND fecha_final >= CURDATE()
ORDER BY 
    fecha_inicio DESC;
```

### 2. Beneficios por Tipo

```sql
-- Solo Promociones
SELECT 
    nombre,
    beneficio_tipo,
    presupuesto,
    valor_usado,
    fecha_inicio,
    fecha_final
FROM 
    marketing_beneficios
WHERE 
    beneficio_tipo = 'promoción'
ORDER BY 
    fecha_inicio DESC;

-- Solo Códigos de Descuento
SELECT 
    nombre,
    beneficio_tipo,
    codigo_descuento,
    presupuesto,
    valor_usado,
    fecha_inicio,
    fecha_final
FROM 
    marketing_beneficios
WHERE 
    beneficio_tipo = 'código descuento'
ORDER BY 
    fecha_inicio DESC;
```

### 3. Beneficios con Presupuesto Disponible

```sql
SELECT 
    nombre AS 'Nombre',
    beneficio_tipo AS 'Tipo',
    codigo_descuento AS 'Código',
    presupuesto AS 'Presupuesto Total',
    valor_usado AS 'Valor Usado',
    (presupuesto - valor_usado) AS 'Disponible',
    CONCAT(ROUND((valor_usado / presupuesto) * 100, 2), '%') AS '% Usado'
FROM 
    marketing_beneficios
WHERE 
    presupuesto > valor_usado
ORDER BY 
    (presupuesto - valor_usado) DESC;
```

### 4. Beneficios Próximos a Vencer (próximos 7 días)

```sql
SELECT 
    nombre AS 'Nombre',
    beneficio_tipo AS 'Tipo',
    codigo_descuento AS 'Código',
    fecha_inicio AS 'Inicio',
    fecha_final AS 'Final',
    DATEDIFF(fecha_final, CURDATE()) AS 'Días restantes'
FROM 
    marketing_beneficios
WHERE 
    fecha_final >= CURDATE() 
    AND fecha_final <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
ORDER BY 
    fecha_final ASC;
```

### 5. Beneficios por Tipo de Venta

```sql
-- Beneficios para Contado
SELECT 
    nombre,
    beneficio_tipo,
    codigo_descuento,
    presupuesto,
    valor_usado,
    fecha_inicio,
    fecha_final,
    tipo_venta
FROM 
    marketing_beneficios
WHERE 
    tipo_venta = 'contado'
ORDER BY 
    fecha_inicio DESC;

-- Beneficios para Crédito
SELECT 
    nombre,
    beneficio_tipo,
    codigo_descuento,
    presupuesto,
    valor_usado,
    fecha_inicio,
    fecha_final,
    tipo_venta
FROM 
    marketing_beneficios
WHERE 
    tipo_venta = 'crédito'
ORDER BY 
    fecha_inicio DESC;
```

### 6. Estadísticas de Uso de Beneficios

```sql
SELECT 
    beneficio_tipo AS 'Tipo',
    COUNT(*) AS 'Total Beneficios',
    SUM(presupuesto) AS 'Presupuesto Total',
    SUM(valor_usado) AS 'Total Usado',
    SUM(presupuesto - valor_usado) AS 'Total Disponible',
    CONCAT(ROUND(AVG((valor_usado / presupuesto) * 100), 2), '%') AS '% Promedio Usado'
FROM 
    marketing_beneficios
GROUP BY 
    beneficio_tipo;
```

### 7. Beneficios con Búsqueda por Nombre o Código

```sql
SELECT 
    nombre AS 'Nombre',
    beneficio_tipo AS 'Tipo',
    codigo_descuento AS 'Código',
    presupuesto AS 'Presupuesto',
    valor_usado AS 'Usado',
    fecha_inicio AS 'Inicio',
    fecha_final AS 'Final'
FROM 
    marketing_beneficios
WHERE 
    nombre LIKE '%black%' 
    OR codigo_descuento LIKE '%BF%'
ORDER BY 
    fecha_creacion DESC;
```

## Ejemplo de Datos

```sql
-- Insertar datos de ejemplo
INSERT INTO marketing_beneficios 
(fecha_creacion, usuario_id, nombre, beneficio_tipo, codigo_descuento, reglas, fecha_inicio, fecha_final, presupuesto, valor_usado, limite_uso, tipo_venta) 
VALUES 
(NOW(), 1, 'Black Friday 2026', 'código descuento', 'BF2026', 'Válido para todos los productos', '2026-11-25', '2026-11-30', 5000000.00, 1250000.00, 500, 'contado'),
(NOW(), 1, 'Descuento Navideño', 'código descuento', 'NAVIDAD2026', 'Descuento especial de temporada', '2026-12-01', '2026-12-31', 3000000.00, 750000.00, 300, 'contado'),
(NOW(), 1, 'Promoción Nuevo Cliente', 'promoción', NULL, 'Descuento para primeros compradores', '2026-01-01', '2026-12-31', 2000000.00, 450000.00, 200, 'crédito');
```

## Consulta Formatada con Moneda Colombiana (COP)

```sql
SELECT 
    nombre AS 'Nombre',
    beneficio_tipo AS 'Tipo',
    IFNULL(codigo_descuento, '-') AS 'Código',
    CONCAT('$ ', FORMAT(presupuesto, 0, 'es_CO')) AS 'Presupuesto',
    CONCAT('$ ', FORMAT(valor_usado, 0, 'es_CO')) AS 'Valor Usado',
    CONCAT('$ ', FORMAT(presupuesto - valor_usado, 0, 'es_CO')) AS 'Disponible',
    DATE_FORMAT(fecha_inicio, '%d/%m/%Y') AS 'Fecha Inicio',
    DATE_FORMAT(fecha_final, '%d/%m/%Y') AS 'Fecha Final',
    tipo_venta AS 'Tipo Venta'
FROM 
    marketing_beneficios
ORDER BY 
    fecha_creacion DESC;
```

## Notas Importantes

1. **Campo beneficio_tipo**: Puede ser 'promoción' o 'código descuento'
2. **Campo codigo_descuento**: Solo aplica cuando beneficio_tipo = 'código descuento'
3. **Campo valor_usado**: Se actualiza automáticamente cuando se aplica el beneficio en una venta
4. **Formato de moneda**: Los valores están en pesos colombianos (COP)
5. **Charset**: La tabla usa utf8mb4 para soportar caracteres especiales y emojis
