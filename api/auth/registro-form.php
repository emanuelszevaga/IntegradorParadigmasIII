<?php
/**
 * API para obtener formulario de registro
 * Devuelve un formulario HTML o JSON con estructura
 */

require_once(__DIR__ . '/../../config/configuracion.php');

header('Content-Type: application/json; charset=utf-8');

// Retornar estructura del formulario
echo json_encode([
    'estado' => 'exitoso',
    'campos' => [
        ['nombre' => 'nombre', 'tipo' => 'text', 'placeholder' => 'Tu nombre', 'requerido' => true],
        ['nombre' => 'email', 'tipo' => 'email', 'placeholder' => 'tu@email.com', 'requerido' => true],
        ['nombre' => 'password', 'tipo' => 'password', 'placeholder' => '••••••••', 'requerido' => true],
        ['nombre' => 'password_confirm', 'tipo' => 'password', 'placeholder' => '••••••••', 'requerido' => true]
    ]
]);

