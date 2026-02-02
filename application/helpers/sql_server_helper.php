<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Conexión segura a SQL Server con PDO
 * Valida disponibilidad del servidor antes de intentar la conexión
 *
 * @param string $servidor   Dirección/IP del servidor SQL
 * @param int    $puerto     Puerto de SQL Server (default: 1433)
 * @param string $bd         Nombre de la base de datos
 * @param string $usuario    Usuario SQL
 * @param string $clave      Contraseña SQL
 * @return PDO|null
 */
if (!function_exists('conectar_sql_server')) {
    function conectar_sql_server($servidor, $puerto = 1433, $bd = '', $usuario = '', $clave = '') {
        // Verificamos conectividad al puerto
        $conexion_prueba = @fsockopen($servidor, $puerto, $errno, $errstr, 2);

        if (!$conexion_prueba) {
            log_message('error', "No se pudo conectar al servidor SQL $servidor:$puerto - $errstr");
            return null;
        }
        fclose($conexion_prueba);

        // Intentar conexión con PDO
        try {
            $dsn = "sqlsrv:Server={$servidor},{$puerto};Database={$bd}";
            $conexion = new PDO($dsn, $usuario, $clave, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            return $conexion;
        } catch (PDOException $e) {
            log_message('error', 'Error al conectar con SQL Server: ' . $e->getMessage());
            return null;
        }
    }
}