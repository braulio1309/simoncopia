-- ========================================
-- CONSULTA SQL - TABLA MARKETING_BENEFICIOS
-- Muestra: Nombre, Tipo, Código de descuento, Valor presupuesto, Valor usado, Fecha de inicio, Fecha final
-- ========================================

-- CONSULTA PRINCIPAL
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

-- ========================================
-- CONSULTA ALTERNATIVA (sin formato de moneda)
-- ========================================
SELECT 
    nombre AS 'Nombre',
    beneficio_tipo AS 'Tipo',
    codigo_descuento AS 'Código de descuento',
    presupuesto AS 'Valor presupuesto',
    valor_usado AS 'Valor usado',
    fecha_inicio AS 'Fecha de inicio',
    fecha_final AS 'Fecha final'
FROM 
    marketing_beneficios
ORDER BY 
    fecha_creacion DESC;

-- ========================================
-- CONSULTA CON INFORMACIÓN ADICIONAL
-- ========================================
SELECT 
    id AS 'ID',
    nombre AS 'Nombre',
    beneficio_tipo AS 'Tipo',
    codigo_descuento AS 'Código de descuento',
    presupuesto AS 'Valor presupuesto',
    valor_usado AS 'Valor usado',
    (presupuesto - valor_usado) AS 'Disponible',
    fecha_inicio AS 'Fecha de inicio',
    fecha_final AS 'Fecha final',
    tipo_venta AS 'Tipo de venta',
    limite_uso AS 'Límite de uso',
    fecha_creacion AS 'Fecha de creación'
FROM 
    marketing_beneficios
ORDER BY 
    fecha_creacion DESC;
