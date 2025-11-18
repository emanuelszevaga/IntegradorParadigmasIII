<?php
/* Archivo de conexión centralizado a la base de datos MySQL */
/* Usa singleton pattern para evitar múltiples conexiones */

// Variables de configuración
$config = [
    'host' => 'localhost',
    'puerto' => 3307,
    'usuario' => 'root',
    'password' => '', 
    'base_datos' => 'vivero_mym'
];

// Variable para almacenar la conexión (patrónsingleton)
$conexion = null;

/**
 * Obtiene la conexión a la base de datos
 * @return mysqli|false Retorna la conexión o false si falla
 */

function obtenerConexion() {
    global $config, $conexion;
    
    // Si ya existe una conexión, reutilizarla
    if ($conexion !== null) {
        return $conexion;
    }
    
    // Crear nueva conexión
    $conexion = new mysqli(
        $config['host'],
        $config['usuario'],
        $config['password'],
        $config['base_datos'],
        $config['puerto']
    );
    
    // Verificar si la conexión fue exitosa
    if ($conexion->connect_error) {
        error_log("Error de conexión: " . $conexion->connect_error);
        return false;
    }
    
    $conexion->set_charset("utf8mb4");
    
    return $conexion;
}

/* Cierra la conexión a la base de datos */
function cerrarConexion() {
    global $conexion;
    if ($conexion !== null) {
        $conexion->close();
        $conexion = null;
    }
}

// Obtener conexión automáticamente
obtenerConexion();

