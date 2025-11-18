<?php
require_once(__DIR__ . '/../config/configuracion.php');

if (!validarAdmin()) {
    header('Location: ' . RUTA_BASE . 'api/auth/logout.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Vivero MyM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-logo">
                <i class="fas fa-leaf"></i>
                <span>Vivero Admin</span>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php" class="nav-item">
                    <i class="fas fa-dashboard"></i> Dashboard
                </a>
                <a href="productos.php" class="nav-item active">
                    <i class="fas fa-box"></i> Productos
                </a>
                <a href="categorias.php" class="nav-item">
                    <i class="fas fa-tags"></i> Categorías
                </a>
                <a href="pedidos.php" class="nav-item">
                    <i class="fas fa-shopping-cart"></i> Pedidos
                </a>
                <a href="usuarios.php" class="nav-item">
                    <i class="fas fa-users"></i> Usuarios
                </a>
            </nav>
            <div class="sidebar-logout">
                <button id="btn-logout" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-topbar">
                <div class="topbar-content">
                    <h1>Gestión de Productos</h1>
                    <button id="btn-nuevo-producto" class="btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Producto
                    </button>
                </div>
            </div>

            <section class="admin-content">
                <div class="search-bar">
                    <input type="text" id="search-input" placeholder="Buscar producto...">
                    <select id="filter-categoria">
                        <option value="">Todas las categorías</option>
                    </select>
                </div>

                <table class="productos-table" id="productos-table">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Vendidos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="productos-tbody">
                        <tr><td colspan="7" class="text-center">Cargando...</td></tr>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <!-- Modal de Producto -->
    <div id="producto-modal" class="modal">
        <div class="modal-content modal-large">
            <button class="modal-close">&times;</button>
            <div class="modal-header">
                <h2 id="modal-title">Nuevo Producto</h2>
            </div>
            <form id="producto-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="form-nombre">Nombre *</label>
                    <input type="text" id="form-nombre" required placeholder="Nombre del producto">
                </div>

                <div class="form-group">
                    <label for="form-descripcion">Descripción *</label>
                    <textarea id="form-descripcion" required placeholder="Descripción del producto"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="form-precio">Precio *</label>
                        <input type="number" id="form-precio" required step="0.01" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="form-stock">Stock *</label>
                        <input type="number" id="form-stock" required min="0" placeholder="0">
                    </div>
                </div>

                <div class="form-group">
                    <label for="form-categoria">Categoría *</label>
                    <select id="form-categoria" required></select>
                </div>

                <div class="form-group">
                    <label for="form-imagen">Imagen del Producto</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="form-imagen" accept="image/*">
                        <div id="image-preview"></div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Guardar Producto</button>
                    <button type="button" class="btn-secondary" onclick="cerrarModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/admin-config.js"></script>
    <script src="../assets/js/admin-productos.js"></script>
</body>
</html>
