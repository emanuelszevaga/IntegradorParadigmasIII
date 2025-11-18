<?php
/**
 * Configuración global de la aplicación
 */

// Iniciar sesiones
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de zona horaria
date_default_timezone_set('America/Argentina/Misiones');

// Configuración de errores (desactivar en producción)
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Incluir archivos de configuración
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/seguridad.php';

// Constantes de la aplicación
define('NOMBRE_APP', 'Vivero MyM');
define('RUTA_BASE', '/integrador3.0/');
define('RUTA_IMAGENES', RUTA_BASE . 'assets/imagenes/productos/');
define('RUTA_IMAGENES_LOCAL', __DIR__ . '/../assets/imagenes/productos/');
define('TAMANIO_MAXIMO_ARCHIVO', 5 * 1024 * 1024); // 5MB
define('EXTENSIONES_PERMITIDAS', ['jpg', 'jpeg', 'png', 'gif']);

// Headers de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Content-Type: text/html; charset=utf-8');
?>
