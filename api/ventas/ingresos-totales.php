<?php
/**
 * API para obtener el total de ingresos (suma de subtotal de todas las ventas)
 * GET /api/ventas/ingresos-totales.php
 */
require_once(__DIR__ . '/../../config/configuracion.php');

header('Content-Type: application/json; charset=utf-8');

if (!validarAdmin()) {
    http_response_code(403);
    echo json_encode(['estado' => 'error', 'mensaje' => 'No autorizado']);
    exit;
}

try {
    $conexion = obtenerConexion();
    
    // Suma el campo 'subtotal' de la tabla 'ventas'
    $sql = "SELECT COALESCE(SUM(subtotal), 0) as total_ingresos FROM ventas";
    
    $resultado = $conexion->query($sql);
    
    if ($resultado) {
        $fila = $resultado->fetch_assoc();
        echo json_encode(['estado' => 'exitoso', 'total_ingresos' => $fila['total_ingresos']]);
    } else {
        http_response_code(500);
        echo json_encode(['estado' => 'error', 'mensaje' => 'Error en la consulta']);
    }
} catch (Exception $e) {
    http_response_code(500);
    error_log("Error al obtener ingresos: " . $e->getMessage());
    echo json_encode(['estado' => 'error', 'mensaje' => $e->getMessage()]);
}
?>