<?php
/**
 * Panel de Administración - Página principal
 */
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
                <a href="index.php" class="nav-item active">
                    <i class="fas fa-dashboard"></i> Dashboard
                </a>
                <a href="productos.php" class="nav-item">
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
                    <div class="dashboard-card">
                        <div class="card-icon"><i class="fas fa-box"></i></div>
                        <div class="card-content">
                            <h3>Productos</h3>
                            <p class="card-value" id="total-productos">0</p>
                        </div>
                        <a href="productos.php" class="card-link">Ver más</a>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-icon"><i class="fas fa-shopping-cart"></i></div>
                        <div class="card-content">
                            <h3>Pedidos</h3>
                            <p class="card-value" id="total-pedidos">0</p>
                        </div>
                        <a href="pedidos.php" class="card-link">Ver más</a>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
                        <div class="card-content">
                            <h3>Ingresos</h3>
                            <p class="card-value" id="total-ingresos">$0</p>
                        </div>
                        <a href="pedidos.php" class="card-link">Ver más</a>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-icon"><i class="fas fa-users"></i></div>
                        <div class="card-content">
                            <h3>Usuarios</h3>
                            <p class="card-value" id="total-usuarios">0</p>
                        </div>
                        <a href="usuarios.php" class="card-link">Ver más</a>
                    </div>
                </div>

                <div class="dashboard-section">
                    <h2>Productos más vendidos</h2>
                    <div id="productos-vendidos" class="productos-list"></div>
                </div>

                <div class="dashboard-section">
                    <h2>Últimos pedidos</h2>
                    <div id="ultimos-pedidos" class="pedidos-list"></div>
                </div>
            </section>
        </main>
    </div>

    <script src="../assets/js/admin-config.js"></script>
    <script src="../assets/js/admin-dashboard.js"></script>
</body>
</html>
