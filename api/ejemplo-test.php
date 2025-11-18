<?php
/**
 * Archivo de prueba para verificar que todas las APIs funcionan
 * Acceder desde: http://localhost/integrador30/api/ejemplo-test.php
 */
require_once(__DIR__ . '/../config/configuracion.php');

header('Content-Type: application/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test de APIs - Vivero MyM</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .test { margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; }
        .error { background: #f8d7da; }
        button { padding: 5px 10px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Test de APIs - Vivero MyM</h1>
    
    <div class="test">
        <h3>1. Probar conexión a BD</h3>
        <button onclick="testConexion()">Probar</button>
        <pre id="result-conexion"></pre>
    </div>
    
    <div class="test">
        <h3>2. Obtener productos</h3>
        <button onclick="testProductos()">Probar</button>
        <pre id="result-productos"></pre>
    </div>
    
    <div class="test">
        <h3>3. Obtener categorías</h3>
        <button onclick="testCategorias()">Probar</button>
        <pre id="result-categorias"></pre>
    </div>
    
    <div class="test">
        <h3>4. Verificar sesión</h3>
        <button onclick="testSesion()">Probar</button>
        <pre id="result-sesion"></pre>
    </div>

    <script>
    const API_BASE = '/integrador30/api'
    
    async function testConexion() {
        try {
            const response = await fetch(API_BASE + '/conexion-test.php')
            const data = await response.json()
            document.getElementById('result-conexion').textContent = JSON.stringify(data, null, 2)
            document.getElementById('result-conexion').parentElement.className = 'test ' + (data.estado === 'exitoso' ? 'success' : 'error')
        } catch (e) {
            document.getElementById('result-conexion').textContent = 'Error: ' + e.message
            document.getElementById('result-conexion').parentElement.className = 'test error'
        }
    }
    
    async function testProductos() {
        try {
            const response = await fetch(API_BASE + '/productos/obtener.php')
            const data = await response.json()
            document.getElementById('result-productos').textContent = JSON.stringify(data, null, 2)
            document.getElementById('result-productos').parentElement.className = 'test ' + (data.estado === 'exitoso' ? 'success' : 'error')
        } catch (e) {
            document.getElementById('result-productos').textContent = 'Error: ' + e.message
            document.getElementById('result-productos').parentElement.className = 'test error'
        }
    }
    
    async function testCategorias() {
        try {
            const response = await fetch(API_BASE + '/categorias/obtener.php')
            const data = await response.json()
            document.getElementById('result-categorias').textContent = JSON.stringify(data, null, 2)
            document.getElementById('result-categorias').parentElement.className = 'test ' + (data.estado === 'exitoso' ? 'success' : 'error')
        } catch (e) {
            document.getElementById('result-categorias').textContent = 'Error: ' + e.message
            document.getElementById('result-categorias').parentElement.className = 'test error'
        }
    }
    
    async function testSesion() {
        try {
            const response = await fetch(API_BASE + '/auth/verificar-sesion.php')
            const data = await response.json()
            document.getElementById('result-sesion').textContent = JSON.stringify(data, null, 2)
            document.getElementById('result-sesion').parentElement.className = 'test ' + (data.estado === 'autenticado' ? 'success' : 'error')
        } catch (e) {
            document.getElementById('result-sesion').textContent = 'Error: ' + e.message
            document.getElementById('result-sesion').parentElement.className = 'test error'
        }
    }
    </script>
</body>
</html>
