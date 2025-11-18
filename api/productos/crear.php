<?php
/**
 * API para crear un nuevo producto (solo administradores)
 * POST /api/productos/crear.php
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
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $categoria_id = $_POST['categoria_id'] ?? '';
    
    $errores = [];
    
    if (!validarNoVacio($nombre)) $errores[] = "Nombre requerido";
    if (!validarNoVacio($descripcion)) $errores[] = "Descripción requerida";
    if (!validarNumero($precio)) $errores[] = "Precio debe ser un número válido";
    if (!validarNumero($stock)) $errores[] = "Stock debe ser un número válido";
    if (!validarNumero($categoria_id)) $errores[] = "Categoría requerida";
    
    if (!empty($errores)) {
        http_response_code(400);
        echo json_encode(['estado' => 'error', 'mensajes' => $errores]);
        exit;
    }
    
    $imagen = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $file = $_FILES['imagen'];
        
        // Validar tipo de archivo
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, EXTENSIONES_PERMITIDAS)) {
            http_response_code(400);
            echo json_encode(['estado' => 'error', 'mensaje' => 'Tipo de archivo no permitido']);
            exit;
        }
        
        // Validar tamaño
        if ($file['size'] > TAMANIO_MAXIMO_ARCHIVO) {
            http_response_code(400);
            echo json_encode(['estado' => 'error', 'mensaje' => 'Archivo muy grande']);
            exit;
        }
        
        // Crear nombre único y guardar
        $nombre_archivo = uniqid() . '.' . $ext;
        $ruta_destino = RUTA_IMAGENES_LOCAL . $nombre_archivo;
        
        // Crear directorio si no existe
        if (!is_dir(RUTA_IMAGENES_LOCAL)) {
            mkdir(RUTA_IMAGENES_LOCAL, 0755, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $ruta_destino)) {
            $imagen = RUTA_IMAGENES . $nombre_archivo;
        } else {
            http_response_code(500);
            echo json_encode(['estado' => 'error', 'mensaje' => 'Error al guardar imagen']);
            exit;
        }
    }
    
    $conexion = obtenerConexion();
    if (!$conexion) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    $stmt = $conexion->prepare("
        INSERT INTO productos 
        (nombre, descripcion, precio, stock, categoria_id, imagen, estado, fecha_creacion) 
        VALUES (?, ?, ?, ?, ?, ?, 1, NOW())
    ");
    
    $stmt->bind_param("ssdiis", $nombre, $descripcion, $precio, $stock, $categoria_id, $imagen);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al crear producto: " . $stmt->error);
    }
    
    $producto_id = $stmt->insert_id;
    $stmt->close();
    
    echo json_encode([
        'estado' => 'exitoso',
        'mensaje' => 'Producto creado correctamente',
        'producto_id' => $producto_id
    ]);
    
} catch (Exception $e) {
    error_log("Error al crear producto: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['estado' => 'error', 'mensaje' => 'Error interno del servidor']);
}
?>
