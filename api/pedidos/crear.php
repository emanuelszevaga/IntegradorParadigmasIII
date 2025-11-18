<?php
/**
 * API para crear un nuevo pedido (checkout)
 * POST /api/pedidos/crear.php
 * Esperado: carrito (array de productos)
 */
require_once(__DIR__ . '/../../config/configuracion.php');

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['estado' => 'error', 'mensaje' => 'Método no permitido']);
    exit;
}

if (!validarSesion()) {
    http_response_code(401);
    echo json_encode(['estado' => 'error', 'mensaje' => 'Debes iniciar sesión para realizar un pedido']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $carrito = $input['carrito'] ?? [];
    
    if (empty($carrito)) {
        http_response_code(400);
        echo json_encode(['estado' => 'error', 'mensaje' => 'El carrito está vacío']);
        exit;
    }
    
    $conexion = obtenerConexion();
    if (!$conexion) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    $conexion->begin_transaction();
    
    try {
        $usuario_id = $_SESSION['usuario_id'];
        $total = 0;
        
        // Validar stock y calcular total
        $productos_stock = [];
        foreach ($carrito as $item) {
            if (!isset($item['id'], $item['cantidad'], $item['precio'])) {
                throw new Exception("Datos del carrito inválidos");
            }
            
            $check_stmt = $conexion->prepare("SELECT stock FROM productos WHERE id = ?");
            if (!$check_stmt) {
                throw new Exception("Error en consulta: " . $conexion->error);
            }
            
            $check_stmt->bind_param("i", $item['id']);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception("Producto ID {$item['id']} no existe");
            }
            
            $producto = $result->fetch_assoc();
            $check_stmt->close();
            
            if ($producto['stock'] < $item['cantidad']) {
                throw new Exception("Stock insuficiente para producto ID {$item['id']}");
            }
            
            $productos_stock[$item['id']] = [
                'cantidad' => $item['cantidad'],
                'precio' => $item['precio']
            ];
            
            $total += $item['cantidad'] * $item['precio'];
        }
        
        $insert_pedido = $conexion->prepare(
            "INSERT INTO pedidos (usuario_id, total, estado) VALUES (?, ?, 'pendiente')"
        );
        if (!$insert_pedido) {
            throw new Exception("Error: " . $conexion->error);
        }
        
        $insert_pedido->bind_param("id", $usuario_id, $total);
        if (!$insert_pedido->execute()) {
            throw new Exception("Error al crear pedido: " . $insert_pedido->error);
        }
        
        $pedido_id = $insert_pedido->insert_id;
        $insert_pedido->close();
        
        foreach ($carrito as $item) {
            $id_producto = $item['id'];
            $cantidad = $item['cantidad'];
            $precio = $item['precio'];
            $subtotal = $cantidad * $precio;
            
            // Insertar detalle
            $insert_detalle = $conexion->prepare(
                "INSERT INTO pedidos_detalles (pedido_id, producto_id, cantidad, precio_unitario, subtotal) 
                VALUES (?, ?, ?, ?, ?)"
            );
            if (!$insert_detalle) {
                throw new Exception("Error: " . $conexion->error);
            }
            
            $insert_detalle->bind_param("iiidd", $pedido_id, $id_producto, $cantidad, $precio, $subtotal);
            if (!$insert_detalle->execute()) {
                throw new Exception("Error al insertar detalle: " . $insert_detalle->error);
            }
            $insert_detalle->close();
            
            $update_stock = $conexion->prepare(
                "UPDATE productos SET stock = stock - ?, cantidad_vendida = cantidad_vendida + ? WHERE id = ?"
            );
            if (!$update_stock) {
                throw new Exception("Error: " . $conexion->error);
            }
            
            $update_stock->bind_param("iii", $cantidad, $cantidad, $id_producto);
            if (!$update_stock->execute()) {
                throw new Exception("Error al actualizar stock: " . $update_stock->error);
            }
            $update_stock->close();
        }
        
        $conexion->commit();
        
        echo json_encode([
            'estado' => 'exitoso',
            'mensaje' => 'Pedido creado correctamente',
            'pedido_id' => $pedido_id,
            'total' => $total
        ]);
        
    } catch (Exception $e) {
        $conexion->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Error al crear pedido: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['estado' => 'error', 'mensaje' => $e->getMessage()]);
}

