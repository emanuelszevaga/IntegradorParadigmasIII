<?php
/**
 * Archivo de prueba para verificar que la conexión a BD funciona correctamente
 * Acceder desde: http://localhost/integrador30/api/conexion-test.php
 */
require_once(__DIR__ . '/../config/configuracion.php');

header('Content-Type: application/json; charset=utf-8');

try {
    $conexion = obtenerConexion();
    
    if (!$conexion) {
        throw new Exception("No se pudo obtener la conexión");
    }
    
    // Consulta simple para verificar que funciona
    $resultado = $conexion->query("SELECT 1 as conexion");
    
    if (!$resultado) {
        throw new Exception("Error al ejecutar consulta: " . $conexion->error);
    }
    
    echo json_encode([
        'estado' => 'exitoso',
        'mensaje' => 'Conexión a la base de datos establecida correctamente',
        'servidor' => 'localhost',
        'base_datos' => 'vivero_mym'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'estado' => 'error',
        'mensaje' => $e->getMessage()
    ]);
}
?>
