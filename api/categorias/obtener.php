<?php
/**
 * API para obtener todas las categorías
 * GET /api/categorias/obtener.php
 */

require_once(__DIR__ . '/../../config/configuracion.php');

header('Content-Type: application/json; charset=utf-8');

try {
    $conexion = obtenerConexion();
    if (!$conexion) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    $resultado = $conexion->query("SELECT id, nombre, descripcion FROM categorias WHERE estado = 1 ORDER BY nombre");
    
    if (!$resultado) {
        throw new Exception("Error en la consulta: " . $conexion->error);
    }
    
    $categorias = $resultado->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'estado' => 'exitoso',
        'total' => count($categorias),
        'categorias' => $categorias
    ]);
    
} catch (Exception $e) {
    error_log("Error al obtener categorías: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['estado' => 'error', 'mensaje' => 'Error al obtener categorías']);
}
?>
