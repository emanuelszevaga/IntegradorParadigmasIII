<?php
/**
 * API para verificar si el usuario tiene sesión activa
 * GET /api/auth/verificar-sesion.php
 */

require_once(__DIR__ . '/../../config/configuracion.php');

header('Content-Type: application/json; charset=utf-8');

if (validarSesion()) {
    echo json_encode([
        'estado' => 'autenticado',
        'usuario' => [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'],
            'email' => $_SESSION['usuario_email'],
            'rol' => $_SESSION['usuario_rol']
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode(['estado' => 'no_autenticado']);
}
?>
