<?php
/**
 * API para eliminar un producto (solo administradores)
 * POST /api/productos/eliminar.php
 */

require_once(__DIR__ . '/../../config/configuracion.php');

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['estado' => 'error', 'mensaje' => 'Método no permitido']);
    exit;
}

if (!validarAdmin()) {
    http_response_code(403);
    echo json_encode(['estado' => 'error', 'mensaje' => 'No tienes permiso']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $id = $input['id'] ?? '';
    
    if (!validarNumero($id)) {
        http_response_code(400);
        echo json_encode(['estado' => 'error', 'mensaje' => 'ID inválido']);
        exit;
    }
    
    $conexion = obtenerConexion();
    if (!$conexion) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    $check_stmt = $conexion->prepare("SELECT imagen FROM productos WHERE id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $resultado = $check_stmt->get_result();
    
    if ($resultado->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['estado' => 'error', 'mensaje' => 'Producto no encontrado']);
        $check_stmt->close();
        exit;
    }
    
    $producto = $resultado->fetch_assoc();
    $check_stmt->close();
    
    $delete_stmt = $conexion->prepare("UPDATE productos SET estado = 0 WHERE id = ?");
    $delete_stmt->bind_param("i", $id);
    
    if (!$delete_stmt->execute()) {
        throw new Exception("Error al eliminar: " . $delete_stmt->error);
    }
    
    $delete_stmt->close();
    
    if ($producto['imagen']) {
        $ruta_imagen = RUTA_IMAGENES_LOCAL . basename($producto['imagen']);
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }
    }
    
    echo json_encode([
        'estado' => 'exitoso',
        'mensaje' => 'Producto eliminado correctamente'
    ]);
    
} catch (Exception $e) {
    error_log("Error al eliminar producto: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['estado' => 'error', 'mensaje' => 'Error interno del servidor']);
}
?>
