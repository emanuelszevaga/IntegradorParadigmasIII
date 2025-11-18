<?php
/**
 * API de Login - Recibe credenciales y devuelve token de sesión
 * POST /api/auth/login.php
 * Esperado: email, password
 */
require_once(__DIR__ . '/../../config/configuracion.php');

header('Content-Type: application/json; charset=utf-8');

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['estado' => 'error', 'mensaje' => 'Método no permitido']);
    exit;
}

try {
    // Obtener datos del body
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
    error_log("[LOGIN] Intento de login con email: " . $email);
    
    // Validaciones básicas
    if (!validarEmail($email)) {
        http_response_code(400);
        echo json_encode(['estado' => 'error', 'mensaje' => 'Email inválido']);
        exit;
    }
    
    if (!validarNoVacio($password)) {
        http_response_code(400);
        echo json_encode(['estado' => 'error', 'mensaje' => 'Contraseña requerida']);
        exit;
    }
    
    $conexion = obtenerConexion();
    if (!$conexion) {
        error_log("[LOGIN] Error: No se pudo conectar a la base de datos");
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    error_log("[LOGIN] Conexión exitosa. Buscando usuario: " . $email);
    
    $stmt = $conexion->prepare("SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ?");
    if (!$stmt) {
        error_log("[LOGIN] Error en prepare: " . $conexion->error);
        throw new Exception("Error en la consulta: " . $conexion->error);
    }
    
    $stmt->bind_param("s", $email);
    
    if (!$stmt->execute()) {
        error_log("[LOGIN] Error en execute: " . $stmt->error);
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
    
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
    $stmt->close();
    
    // Verificar si el usuario existe y validar contraseña
    if (!$usuario) {
        error_log("[LOGIN] Usuario no encontrado: " . $email);
        http_response_code(401);
        echo json_encode(['estado' => 'error', 'mensaje' => 'Email o contraseña incorrectos']);
        exit;
    }
    
    if (!verificarPassword($password, $usuario['password'])) {
        error_log("[LOGIN] Contraseña incorrecta para: " . $email);
        http_response_code(401);
        echo json_encode(['estado' => 'error', 'mensaje' => 'Email o contraseña incorrectos']);
        exit;
    }
    
    error_log("[LOGIN] Login exitoso para: " . $email . " con rol: " . $usuario['rol']);
    
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_rol'] = $usuario['rol'];
    $_SESSION['login_time'] = time();
    
    // Registrar último acceso
    $update_stmt = $conexion->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
    if ($update_stmt) {
        $update_stmt->bind_param("i", $usuario['id']);
        $update_stmt->execute();
        $update_stmt->close();
    }
    
    echo json_encode([
        'estado' => 'exitoso',
        'mensaje' => 'Sesión iniciada correctamente',
        'usuario' => [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol']
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error en login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['estado' => 'error', 'mensaje' => 'Error interno del servidor']);
}

