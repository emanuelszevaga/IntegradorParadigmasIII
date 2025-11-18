<?php
/*Panel de Administración - Página principal*/
require_once(__DIR__ . '/../config/configuracion.php');

// Redirigir si no es admin
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
    <title>Panel de Administración - Vivero MyM</title>
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
                <!-- Dashboard y Productos -->
                <a href="index.php" class="nav-item active">
                    <i class="fas fa-dashboard"></i> Dashboard
                </a>
                <a href="productos.php" class="nav-item">
                    <i class="fas fa-box"></i> Productos
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
            <!-- Top Bar -->
            <div class="admin-topbar">
                <div class="topbar-content">
                    <h1>Panel de Administración</h1>
                    <div class="user-info">
                        <span id="user-name"><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <section class="admin-content">
                <div class="dashboard-grid">
                    <!-- Productos, Ventas, Ingresos y Usuarios -->
                    <div class="dashboard-card" onclick="abrirModalProductos()">
                        <div class="card-icon"><i class="fas fa-box"></i></div>
                        <div class="card-content">
                            <h3>Productos</h3>
                            <p class="card-value" id="total-productos">0</p>
                        </div>
                        <a href="#" class="card-link">Ver más</a>
                    </div>

                    <div class="dashboard-card" onclick="abrirModalVentas()">
                        <div class="card-icon"><i class="fas fa-chart-bar"></i></div>
                        <div class="card-content">
                            <h3>Ventas</h3>
                            <p class="card-value" id="total-ventas">0</p>
                        </div>
                        <a href="#" class="card-link">Ver más</a>
                    </div>

                    <div class="dashboard-card" onclick="abrirModalIngresos()">
                        <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
                        <div class="card-content">
                            <h3>Ingresos</h3>
                            <p class="card-value" id="total-ingresos">$0</p>
                        </div>
                        <a href="#" class="card-link">Ver más</a>
                    </div>

                    <div class="dashboard-card" onclick="abrirModalUsuarios()">
                        <div class="card-icon"><i class="fas fa-users"></i></div>
                        <div class="card-content">
                            <h3>Usuarios</h3>
                            <p class="card-value" id="total-usuarios">0</p>
                        </div>
                        <a href="#" class="card-link">Ver más</a>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal de Productos -->
    <div id="modal-productos" class="modal">
        <div class="modal-content modal-large">
            <button class="modal-close" onclick="cerrarModal('modal-productos')">&times;</button>
            <div class="modal-header">
                <h2>Detalles de Productos</h2>
            </div>
            <div class="modal-body">
                <div class="search-bar" style="margin-bottom: 20px;">
                    <input type="text" id="search-productos" placeholder="Buscar producto..." onkeyup="filtrarProductos()">
                </div>
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Vendidos</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-productos">
                        <tr><td colspan="5" class="text-center">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Ventas -->
    <div id="modal-ventas" class="modal">
        <div class="modal-content modal-large">
            <button class="modal-close" onclick="cerrarModal('modal-ventas')">&times;</button>
            <div class="modal-header">
                <h2>Detalle de Ventas</h2>
            </div>
            <div class="modal-body">
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Total</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-ventas">
                        <tr><td colspan="6" class="text-center">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Ingresos -->
    <div id="modal-ingresos" class="modal">
        <div class="modal-content modal-large">
            <button class="modal-close" onclick="cerrarModal('modal-ingresos')">&times;</button>
            <div class="modal-header">
                <h2>Detalle de Ingresos</h2>
            </div>
            <div class="modal-body">
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Productos Vendidos</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-ingresos">
                        <tr><td colspan="3" class="text-center">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Usuarios -->
    <div id="modal-usuarios" class="modal">
        <div class="modal-content modal-large">
            <button class="modal-close" onclick="cerrarModal('modal-usuarios')">&times;</button>
            <div class="modal-header">
                <h2>Detalle de Usuarios</h2>
            </div>
            <div class="modal-body">
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Fecha Registro</th>
                            <th>Total Compras</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-usuarios">
                        <tr><td colspan="5" class="text-center">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin-config.js"></script>
    <script src="../assets/js/admin-dashboard.js"></script>
</body>
</html>
