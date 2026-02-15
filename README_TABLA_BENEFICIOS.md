# 📊 Tabla SQL marketing_beneficios - Resumen Completo

## ✅ Datos Solicitados

Se solicitó mostrar la tabla SQL con los siguientes campos:
- **Nombre**
- **Tipo**
- **Código de descuento**
- **Valor presupuesto**
- **Valor usado**
- **Fecha de inicio**
- **Fecha final**

---

## 🗄️ Consulta SQL Principal

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

---

## 📋 Estructura de la Tabla

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT | ID único (Primary Key, Auto Increment) |
| `fecha_creacion` | DATETIME | Fecha de creación del registro |
| `usuario_id` | INT | ID del usuario que creó el beneficio |
| `nombre` | VARCHAR(255) | **Nombre del beneficio** |
| `beneficio_tipo` | VARCHAR(50) | **Tipo: 'promoción' o 'código descuento'** |
| `codigo_descuento` | VARCHAR(100) | **Código del descuento (si aplica)** |
| `reglas` | TEXT | Reglas y condiciones del beneficio |
| `fecha_inicio` | DATE | **Fecha de inicio de vigencia** |
| `fecha_final` | DATE | **Fecha final de vigencia** |
| `presupuesto` | DECIMAL(15,2) | **Valor máximo del presupuesto** |
| `valor_usado` | DECIMAL(15,2) | **Valor ya utilizado** (default: 0.00) |
| `limite_uso` | INT | Cantidad máxima de items en promoción |
| `tipo_venta` | VARCHAR(20) | Tipo de venta: 'contado' o 'crédito' |

---

## 📊 Ejemplo de Resultado de la Consulta

| Nombre | Tipo | Código de descuento | Valor presupuesto | Valor usado | Fecha de inicio | Fecha final |
|--------|------|---------------------|-------------------|-------------|-----------------|-------------|
| Black Friday 2026 | código descuento | BF2026 | $5,000,000 | $1,250,000 | 2026-11-25 | 2026-11-30 |
| Descuento Navideño | código descuento | NAVIDAD2026 | $3,000,000 | $750,000 | 2026-12-01 | 2026-12-31 |
| Promoción Nuevo Cliente | promoción | - | $2,000,000 | $450,000 | 2026-01-01 | 2026-12-31 |

---

## 📁 Archivos Disponibles

### 1. **CONSULTAS_SQL_BENEFICIOS.md** (6.9 KB)
Documentación completa que incluye:
- ✅ Estructura de la tabla
- ✅ Consulta principal
- ✅ 7 consultas adicionales útiles:
  - Beneficios activos (vigentes hoy)
  - Beneficios por tipo
  - Beneficios con presupuesto disponible
  - Beneficios próximos a vencer
  - Beneficios por tipo de venta
  - Estadísticas de uso
  - Búsqueda por nombre o código
- ✅ Ejemplos de inserción de datos
- ✅ Consultas con formato de moneda colombiana (COP)

### 2. **consulta_beneficios.sql** (1.7 KB)
Script SQL ejecutable con:
- ✅ Consulta principal (con formato de moneda)
- ✅ Consulta alternativa (sin formato)
- ✅ Consulta con información adicional

---

## 🔍 Mapeo de Campos

| Campo Solicitado | Campo en BD | Tipo de Dato |
|------------------|-------------|--------------|
| Nombre | `nombre` | VARCHAR(255) |
| Tipo | `beneficio_tipo` | VARCHAR(50) |
| Código de descuento | `codigo_descuento` | VARCHAR(100) |
| Valor presupuesto | `presupuesto` | DECIMAL(15,2) |
| Valor usado | `valor_usado` | DECIMAL(15,2) |
| Fecha de inicio | `fecha_inicio` | DATE |
| Fecha final | `fecha_final` | DATE |

---

## 💡 Notas Importantes

1. **Formato de Moneda**: Los valores monetarios se muestran en pesos colombianos (COP)
2. **Código de Descuento**: Solo es obligatorio cuando `beneficio_tipo` = 'código descuento'
3. **Valor Usado**: Se actualiza automáticamente cuando se aplica el beneficio en ventas
4. **Charset**: La tabla usa `utf8mb4` para soportar caracteres especiales y emojis
5. **Índice**: El campo `id` es la clave primaria con auto-incremento

---

## 🚀 Cómo Usar

### Opción 1: Ejecutar desde MySQL Workbench / phpMyAdmin
1. Abrir el archivo `consulta_beneficios.sql`
2. Copiar y pegar la consulta en el editor SQL
3. Ejecutar la consulta

### Opción 2: Ejecutar desde línea de comandos
```bash
mysql -u usuario -p nombre_base_datos < consulta_beneficios.sql
```

### Opción 3: Desde la aplicación web
Los datos ya se visualizan automáticamente en:
- URL: `/marketing/beneficios/ver`
- Vista: `application/views/marketing/beneficios/lista.php`

---

## 📱 Interfaz Web

La aplicación ya incluye una interfaz completa en:
- **Ruta**: `/marketing/beneficios/ver`
- **Vista**: DataTables con filtros personalizados
- **Características**:
  - ✅ Filtrado por nombre
  - ✅ Filtrado por tipo
  - ✅ Filtrado por tipo de venta
  - ✅ Filtrado por fechas
  - ✅ Formato automático de moneda COP
  - ✅ Paginación del lado del servidor
  - ✅ Búsqueda global
  - ✅ Botones de edición

---

## 📞 Soporte

Para más información sobre las consultas SQL disponibles, revisar:
- **CONSULTAS_SQL_BENEFICIOS.md** - Documentación detallada
- **consulta_beneficios.sql** - Scripts ejecutables

---

**Fecha de creación**: 15 de febrero de 2026  
**Módulo**: Marketing - Beneficios  
**Base de datos**: marketing_beneficios
