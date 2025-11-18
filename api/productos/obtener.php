<?php
/**
 * API para obtener productos
 * GET /api/productos/obtener.php
 */

require_once(__DIR__ . '/../../config/configuracion.php');

header('Content-Type: application/json; charset=utf-8');

try {
    $conexion = obtenerConexion();
    if (!$conexion) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    $id = $_GET['id'] ?? null;
    $categoria = $_GET['categoria'] ?? null;
    $ordenar = $_GET['ordenar'] ?? 'nombre';
    
    if ($id && validarNumero($id)) {
        $stmt = $conexion->prepare("
            SELECT p.*, c.nombre as categoria_nombre 
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $producto = $resultado->fetch_assoc();
        $stmt->close();
        
        if (!$producto) {
            http_response_code(404);
            echo json_encode(['estado' => 'error', 'mensaje' => 'Producto no encontrado']);
            exit;
        }
        
        echo json_encode(['estado' => 'exitoso', 'producto' => $producto]);
        exit;
    }
    
    $query = "
        SELECT p.*, c.nombre as categoria_nombre 
        FROM productos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.estado = 1
    ";
    
    $params = [];
    $tipos = "";
    
    // Filtrar por categoría 
    if ($categoria && validarNoVacio($categoria) && $categoria !== 'todos') {
        $query .= " AND c.nombre = ?";
        $params[] = $categoria;
        $tipos .= "s";
    }
    
    $orden = match($ordenar) {
        'vendidos' => 'p.cantidad_vendida DESC',
        'precio_bajo' => 'p.precio ASC',
        'precio_alto' => 'p.precio DESC',
        'nuevos' => 'p.fecha_creacion DESC',
        default => 'p.nombre ASC'
    };
    
    $query .= " ORDER BY " . $orden;
    
    $stmt = $conexion->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($tipos, ...$params);
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    $productos = $resultado->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    echo json_encode([
        'estado' => 'exitoso',
        'total' => count($productos),
        'productos' => $productos
    ]);
    
} catch (Exception $e) {
    error_log("Error al obtener productos: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['estado' => 'error', 'mensaje' => 'Error al obtener productos']);
}

