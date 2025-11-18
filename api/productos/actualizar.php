<?php
/**
 * API para actualizar un producto (solo administradores)
 * POST /api/productos/actualizar.php
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
    $id = $_POST['id'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $categoria_id = $_POST['categoria_id'] ?? '';
    
    if (!validarNumero($id)) {
        http_response_code(400);
        echo json_encode(['estado' => 'error', 'mensaje' => 'ID de producto inválido']);
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
    
    $producto_anterior = $resultado->fetch_assoc();
    $check_stmt->close();
    
    $imagen = $producto_anterior['imagen'];
    
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $file = $_FILES['imagen'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, EXTENSIONES_PERMITIDAS)) {
            http_response_code(400);
            echo json_encode(['estado' => 'error', 'mensaje' => 'Tipo de archivo no permitido']);
            exit;
        }
        
        if ($file['size'] > TAMANIO_MAXIMO_ARCHIVO) {
            http_response_code(400);
            echo json_encode(['estado' => 'error', 'mensaje' => 'Archivo muy grande']);
            exit;
        }
        
        // Eliminar imagen anterior
        if ($producto_anterior['imagen']) {
            $ruta_antigua = RUTA_IMAGENES_LOCAL . basename($producto_anterior['imagen']);
            if (file_exists($ruta_antigua)) {
                unlink($ruta_antigua);
            }
        }
        
        $nombre_archivo = uniqid() . '.' . $ext;
        $ruta_destino = RUTA_IMAGENES_LOCAL . $nombre_archivo;
        
        if (!is_dir(RUTA_IMAGENES_LOCAL)) {
            mkdir(RUTA_IMAGENES_LOCAL, 0755, true);
        }
        
        if (!move_uploaded_file($file['tmp_name'], $ruta_destino)) {
            http_response_code(500);
            echo json_encode(['estado' => 'error', 'mensaje' => 'Error al guardar imagen']);
            exit;
        }
        
        $imagen = RUTA_IMAGENES . $nombre_archivo;
    }
    
    $update_stmt = $conexion->prepare("
        UPDATE productos 
        SET nombre = ?, descripcion = ?, precio = ?, stock = ?, categoria_id = ?, imagen = ? 
        WHERE id = ?
    ");
    
    $update_stmt->bind_param("ssdiisi", $nombre, $descripcion, $precio, $stock, $categoria_id, $imagen, $id);
    
    if (!$update_stmt->execute()) {
        throw new Exception("Error al actualizar: " . $update_stmt->error);
    }
    
    $update_stmt->close();
    
    echo json_encode([
        'estado' => 'exitoso',
        'mensaje' => 'Producto actualizado correctamente'
    ]);
    
} catch (Exception $e) {
    error_log("Error al actualizar producto: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['estado' => 'error', 'mensaje' => 'Error interno del servidor']);
}
?>
