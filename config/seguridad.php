<?php
/**
 * Funciones de seguridad para validación y protección
 */

/**
 * Valida si el email tiene un formato correcto
 * @param string $email Email a validar
 * @return bool True si es válido
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida si la contraseña cumple requisitos mínimos de seguridad
 * Mínimo 8 caracteres, debe incluir mayúscula, minúscula y número
 * @param string $password Contraseña a validar
 * @return array Array con 'valido' (bool) y 'errores' (array)
 */
function validarPassword($password) {
    $errores = [];
    
    if (strlen($password) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errores[] = "La contraseña debe incluir mayúsculas";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errores[] = "La contraseña debe incluir minúsculas";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errores[] = "La contraseña debe incluir números";
    }
    
    return [
        'valido' => empty($errores),
        'errores' => $errores
    ];
}

/**
 * Valida si un valor es un número entero positivo
 * @param mixed $valor Valor a validar
 * @return bool True si es válido
 */
function validarNumero($valor) {
    return is_numeric($valor) && (int)$valor > 0;
}

/**
 * Valida que un valor no esté vacío
 * @param string $valor Valor a validar
 * @return bool True si no está vacío
 */
function validarNoVacio($valor) {
    return !empty(trim($valor));
}

/**
 * Hashea una contraseña usando bcrypt
 * @param string $password Contraseña a hashear
 * @return string Contraseña hasheada
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verifica si una contraseña coincide con su hash
 * @param string $password Contraseña a verificar
 * @param string $hash Hash almacenado
 * @return bool True si coinciden
 */
function verificarPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Escapa caracteres especiales para prevenir inyección SQL
 * NOTA: Siempre usar prepared statements cuando sea posible
 * @param mysqli $conexion Conexión a la base de datos
 * @param string $valor Valor a escapar
 * @return string Valor escapado
 */
function escaparSQL($conexion, $valor) {
    return $conexion->real_escape_string(trim($valor));
}

/**
 * Valida el rol del usuario
 * @param string $rol Rol a validar
 * @return bool True si es un rol válido
 */
function validarRol($rol) {
    $rolesValidos = ['usuario', 'administrador'];
    return in_array($rol, $rolesValidos);
}

/**
 * Valida que el usuario tenga sesión iniciada
 * @return bool True si hay sesión activa
 */
function validarSesion() {
    return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_rol']);
}

/**
 * Valida que el usuario sea administrador
 * @return bool True si es administrador
 */
function validarAdmin() {
    return validarSesion() && $_SESSION['usuario_rol'] === 'administrador';
}

/**
 * Genera un token CSRF para prevenir ataques
 * @return string Token CSRF
 */
function generarTokenCSRF() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica un token CSRF
 * @param string $token Token a verificar
 * @return bool True si el token es válido
 */
function verificarTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
