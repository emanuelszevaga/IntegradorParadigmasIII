<?php
/**
 * API de Logout - Destruye la sesión del usuario
 * GET /api/auth/logout.php
 */

require_once(__DIR__ . '/../../config/configuracion.php');

header('Content-Type: application/json; charset=utf-8');

try {
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, 
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    
    echo json_encode([
        'estado' => 'exitoso',
        'mensaje' => 'Sesión cerrada correctamente'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['estado' => 'error', 'mensaje' => 'Error al cerrar sesión']);
}
?>
