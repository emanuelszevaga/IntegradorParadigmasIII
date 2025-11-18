<?php
/**
 * API de Registro - Crea nueva cuenta de usuario
 * POST /api/auth/registro.php
 * Esperado: nombre, email, password, password_confirm
 */

require_once(__DIR__ . '/../../config/configuracion.php');

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['estado' => 'error', 'mensaje' => 'Método no permitido']);
    exit;
}

try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    
    $nombre = $input['nombre'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $password_confirm = $input['password_confirm'] ?? '';
    
    error_log("[REGISTRO] Intento de registro con email: " . $email);
    
    // Validaciones
    $errores = [];
    
    if (!validarNoVacio($nombre)) {
        $errores[] = "El nombre es requerido";
    }
    
    if (!validarEmail($email)) {
        $errores[] = "Email inválido";
    }
    
    $validacion_password = validarPassword($password);
    if (!$validacion_password['valido']) {
        $errores = array_merge($errores, $validacion_password['errores']);
    }
    
    if ($password !== $password_confirm) {
        $errores[] = "Las contraseñas no coinciden";
    }
    
    if (!empty($errores)) {
        error_log("[REGISTRO] Validación fallida: " . json_encode($errores));
        http_response_code(400);
        echo json_encode(['estado' => 'error', 'mensajes' => $errores]);
        exit;
    }
    
    $conexion = obtenerConexion();
    if (!$conexion) {
        error_log("[REGISTRO] Error: No se pudo conectar a la base de datos");
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    error_log("[REGISTRO] Conexión exitosa. Verificando email existente: " . $email);
    
    $check_stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
    if (!$check_stmt) {
        error_log("[REGISTRO] Error en prepare check: " . $conexion->error);
        throw new Exception("Error en la consulta: " . $conexion->error);
    }
    
    $check_stmt->bind_param("s", $email);
    if (!$check_stmt->execute()) {
        error_log("[REGISTRO] Error en execute check: " . $check_stmt->error);
        throw new Exception("Error al verificar email: " . $check_stmt->error);
    }
    
    if ($check_stmt->get_result()->num_rows > 0) {
        error_log("[REGISTRO] Email ya registrado: " . $email);
        http_response_code(409);
        echo json_encode(['estado' => 'error', 'mensaje' => 'Este email ya está registrado']);
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();
    
    $password_hash = hashPassword($password);
    
    error_log("[REGISTRO] Insertando nuevo usuario: " . $email);
    
    $insert_stmt = $conexion->prepare(
        "INSERT INTO usuarios (nombre, email, password, rol, fecha_registro) VALUES (?, ?, ?, 'usuario', NOW())"
    );
    
    if (!$insert_stmt) {
        error_log("[REGISTRO] Error en prepare insert: " . $conexion->error);
        throw new Exception("Error en la consulta: " . $conexion->error);
    }
    
    $insert_stmt->bind_param("sss", $nombre, $email, $password_hash);
    
    if (!$insert_stmt->execute()) {
        error_log("[REGISTRO] Error en execute insert: " . $insert_stmt->error);
        throw new Exception("Error al registrar: " . $insert_stmt->error);
    }
    
    $usuario_id = $insert_stmt->insert_id;
    $insert_stmt->close();
    
    error_log("[REGISTRO] Registro exitoso para: " . $email . " con ID: " . $usuario_id);
    
    $_SESSION['usuario_id'] = $usuario_id;
    $_SESSION['usuario_nombre'] = $nombre;
    $_SESSION['usuario_email'] = $email;
    $_SESSION['usuario_rol'] = 'usuario';
    $_SESSION['login_time'] = time();
    
    echo json_encode([
        'estado' => 'exitoso',
        'mensaje' => 'Cuenta creada correctamente. Sesión iniciada.',
        'usuario' => [
            'id' => $usuario_id,
            'nombre' => $nombre,
            'email' => $email,
            'rol' => 'usuario'
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error en registro: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['estado' => 'error', 'mensaje' => 'Error interno del servidor']);
}
?>
