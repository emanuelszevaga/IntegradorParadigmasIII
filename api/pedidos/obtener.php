<?php
/**
 * API para obtener pedidos
 * GET /api/pedidos/obtener.php
 * Parámetros: id (opcional, un pedido específico)
 */

require_once(__DIR__ . '/../../config/configuracion.php');

header('Content-Type: application/json; charset=utf-8');

if (!validarSesion()) {
    http_response_code(401);
    echo json_encode(['estado' => 'error', 'mensaje' => 'No autenticado']);
    exit;
}

try {
    $conexion = obtenerConexion();
    if (!$conexion) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    $id = $_GET['id'] ?? null;
    $usuario_id = $_SESSION['usuario_id'];
    $es_admin = validarAdmin();
    
    if ($id && validarNumero($id)) {
        // Obtener un pedido específico
        $query = "SELECT * FROM pedidos WHERE id = ?";
        if (!$es_admin) {
            $query .= " AND usuario_id = ?";
        }
        
        $stmt = $conexion->prepare($query);
        if (!$stmt) {
            throw new Exception("Error: " . $conexion->error);
        }
        
        if ($es_admin) {
            $stmt->bind_param("i", $id);
        } else {
            $stmt->bind_param("ii", $id, $usuario_id);
        }
        
        $stmt->execute();
        $resultado = $stmt->get_result();
        $pedido = $resultado->fetch_assoc();
        $stmt->close();
        
        if (!$pedido) {
            http_response_code(404);
            echo json_encode(['estado' => 'error', 'mensaje' => 'Pedido no encontrado']);
            exit;
        }
        
        // Obtener detalles
        $detalles_stmt = $conexion->prepare("
            SELECT pd.*, p.nombre, p.imagen 
            FROM pedidos_detalles pd 
            JOIN productos p ON pd.producto_id = p.id 
            WHERE pd.pedido_id = ?
        ");
        $detalles_stmt->bind_param("i", $id);
        $detalles_stmt->execute();
        $detalles_resultado = $detalles_stmt->get_result();
        $detalles = $detalles_resultado->fetch_all(MYSQLI_ASSOC);
        $detalles_stmt->close();
        
        $pedido['detalles'] = $detalles;
        
        echo json_encode(['estado' => 'exitoso', 'pedido' => $pedido]);
        exit;
    }
    
    if ($es_admin) {
        $stmt = $conexion->prepare("SELECT * FROM pedidos ORDER BY fecha_pedido DESC");
    } else {
        $stmt = $conexion->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha_pedido DESC");
        $stmt->bind_param("i", $usuario_id);
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    $pedidos = $resultado->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    echo json_encode([
        'estado' => 'exitoso',
        'total' => count($pedidos),
        'pedidos' => $pedidos
    ]);
    
} catch (Exception $e) {
    error_log("Error al obtener pedidos: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['estado' => 'error', 'mensaje' => 'Error al obtener pedidos']);
}

