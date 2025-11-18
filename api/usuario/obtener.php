<?php
/**
 * API para obtener usuarios con total de compras
 */
require_once(__DIR__ . '/../../config/configuracion.php');

if (!validarAdmin()) {
    echo json_encode(['estado' => 'error', 'mensaje' => 'No autorizado']);
    exit;
}

try {
    $conexion = obtenerConexion();
    
    $sql = "SELECT u.id, u.nombre, u.email, u.fecha_registro, 
            COUNT(v.id) as total_compras,
            COALESCE(SUM(v.subtotal), 0) as monto_total_comprado
            FROM usuarios u
            LEFT JOIN ventas v ON u.id = v.usuario_id
            GROUP BY u.id
            ORDER BY u.fecha_registro DESC";
    
    $resultado = $conexion->query($sql);
    
    if ($resultado) {
        $usuarios = $resultado->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['estado' => 'exitoso', 'usuarios' => $usuarios]);
    } else {
        echo json_encode(['estado' => 'error', 'mensaje' => 'Error en la consulta']);
    }
} catch (Exception $e) {
    echo json_encode(['estado' => 'error', 'mensaje' => $e->getMessage()]);
}
?>
