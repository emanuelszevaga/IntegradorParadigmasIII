<?php
/**
 * API para obtener todas las ventas registradas
 */
require_once(__DIR__ . '/../../config/configuracion.php');

if (!validarAdmin()) {
    echo json_encode(['estado' => 'error', 'mensaje' => 'No autorizado']);
    exit;
}

try {
    $conexion = obtenerConexion();
    
    $sql = "SELECT v.id, v.usuario_id, v.producto_id, p.nombre as producto_nombre, 
            v.cantidad, v.precio_unitario, v.subtotal, v.fecha_venta
            FROM ventas v
            LEFT JOIN productos p ON v.producto_id = p.id
            ORDER BY v.fecha_venta DESC";
    
    $resultado = $conexion->query($sql);
    
    if ($resultado) {
        $ventas = $resultado->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['estado' => 'exitoso', 'ventas' => $ventas]);
    } else {
        echo json_encode(['estado' => 'error', 'mensaje' => 'Error en la consulta']);
    }
} catch (Exception $e) {
    echo json_encode(['estado' => 'error', 'mensaje' => $e->getMessage()]);
}
?>
