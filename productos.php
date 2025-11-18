<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Vivero MyM</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="shortcut icon" href="assets\imagenes\favicon1.png" type="image/png">
        <link rel="stylesheet" href="assets\css\style.css">
    </head>
    
    <body>
        <!-- franja superior -->
        <div class="top-bar">
            <div class="top-bar-content container">
                <div class="content-info">
                    <p><i class="fa-brands fa-whatsapp"></i>+54 3754-402158</p>
                    <p><i class="fa-regular fa-envelope"></i>viveromym@gmail.com</p>
                    <p><i class="fa-solid fa-location-dot"></i>Calle San Martín y Andresito</p>
                </div>
                <div class="social-links">
                    <a href="#" class="btn-social-link"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="btn-social-link"><i class="fa-brands fa-facebook"></i></a>
                </div>
            </div>
        </div>

        <!-- header en forma de burbuja -->
        <header>
            <div class="header-bubble">
                <div class="section-logo container">
                    <div class="logo-left">
                        <a href="index.php">
                            <img src="assets\imagenes\logo.png" alt="Vivero MyM logo">
                        </a>
                    </div>
                    <button class="menu-toggle" id="menu-toggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="nav-center" id="nav-center">
                        <nav>
                            <a href="index.php">Inicio</a>
                            <div class="menu-productos">
                                <a href="productos.php">Productos</a>
                                <ul class="submenu">
                                    <li><a href="#" data-filter="todos">Ver todo</a></li>
                                    <li><a href="#" data-filter="citricos">Cítricos</a></li>
                                    <li><a href="#" data-filter="carozo">Frutales de carozo</a></li>
                                    <li><a href="#" data-filter="tropicales">Frutas tropicales</a></li>
                                </ul>
                            </div>
                            <a href="#">Quienes somos</a>
                            <a href="#">Más</a>
                        </nav>
                    </div>
                    <div class="container-actions">
                        <button id="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                        <button id="login-btn"><i class="fa-solid fa-user"></i></button>
                        <button id="cart-btn"><i class="fa-solid fa-cart-shopping"></i></button>
                    </div>
                </div>
            </div>
        </header>

        <!-- sección de productos -->
        <section class="productos">
            <h2>Productos</h2>
            <div class="container productos__grid">
                </div>
        </section>

        <!-- modal -->
        <div id="product-modal" class="modal">
            <div class="modal-content">
                <button class="modal-close">&times;</button>
                <div class="modal-body">
                    <div class="modal-image">
                        <img id="modal-img" src="/placeholder.svg" alt="">
                    </div>
                    <div class="modal-info">
                        <h2 id="modal-nombre"></h2>
                        <p class="modal-precio" id="modal-precio"></p>
                        <p class="modal-cuotas">3 cuotas sin interés</p>
                        <p class="modal-descripcion" id="modal-descripcion"></p>
                        <div class="modal-stock" id="modal-stock"></div>
                        <div class="quantity-selector">
                            <label for="cantidad">Cantidad:</label>
                            <div class="quantity-controls">
                                <button id="btn-decrease">-</button>
                                <input type="number" id="cantidad" value="1" min="1" max="10">
                                <button id="btn-increase">+</button>
                            </div>
                        </div>
                        <button class="btn-add-to-cart" id="btn-add-to-cart">Agregar al carrito</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- modal buscador -->
        <div id="search-modal" class="modal">
            <div class="modal-content search-modal-content">
                <button class="modal-close" id="search-close">&times;</button>
                <div class="search-modal-body">
                    <h2>Buscar productos</h2>
                    <div class="search-input-container">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="search-input" placeholder="Buscar por nombre...">
                    </div>
                    <div id="search-results" class="search-results"></div>
                </div>
            </div>
        </div>

        <!-- modal login -->
        <div id="login-modal" class="modal">
            <div class="modal-content login-modal-content">
                <button class="modal-close" id="login-close">&times;</button>
                <div class="login-modal-body">
                    <h2>Iniciar sesión</h2>
                    <form id="login-form">
                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            <input type="email" id="email" required placeholder="tu@email.com">
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" id="password" required placeholder="••••••••">
                        </div>
                        <button type="submit" class="btn-login">Iniciar sesión</button>
                    </form>
                    <p class="register-link">¿No tienes cuenta? <a href="#">Registrarse</a></p>
                </div>
            </div>
        </div>

        <!-- sidebar carrito -->
        <div id="cart-sidebar" class="cart-sidebar">
            <div class="cart-header">
                <h2>Carrito de compras</h2>
                <button class="cart-close" id="cart-close">&times;</button>
            </div>
            <div class="cart-body" id="cart-body">
                <p class="empty-cart">Tu carrito está vacío</p>
            </div>
            <div class="cart-footer" id="cart-footer">
                <div class="cart-total">
                    <span>Total:</span>
                    <span id="cart-total-price">$0</span>
                </div>
                <button class="btn-checkout">Finalizar compra</button>
            </div>
        </div>
        <div id="cart-overlay" class="cart-overlay"></div>

        <!-- footer -->
        <footer>
            <div class="section-footer container">
                <div class="footer-section-logo">
                    <img src="assets\imagenes\logo.png" alt="logo">
                    <p>Vivero especializado en citricos, frutales de carozo y frutas tropicales</p>
                    <div class="social-links">
                        <a href="#" class="btn-social-link"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="btn-social-link"><i class="fa-brands fa-facebook"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <div class="footer-column">
                        <h4>Compañía</h4>
                        <ul>
                            <li>
                                <a href="#">Sobre nosotros</a>
                            </li>
                            <li>
                                <a href="#">Contacto</a>
                            </li>
                            <li>
                                <a href="#">Política de privacidad</a>
                            </li>
                            <li>
                                <a href="#">Información</a>
                            </li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h4>Servicio al cliente</h4>
                        <ul>
                            <li>
                                <a href="#">Preguntas frecuentes</a>
                            </li>
                            <li>
                                <a href="#">Términos de uso</a>
                            </li>
                            <li>
                                <a href="#">Condiciones de pedido</a>
                            </li>
                            <li>
                                <a href="#">Métodos de pago</a>
                            </li>
                        </ul>
                    </div>
                    <div class="footer-column newsletter">
                        <h4>Suscribite al newsletter</h4>
                        <p>Descubrí todas las novedades</p>
                        <form class="newsletter-form">
                            <input type="email" placeholder="Tu correo">
                            <button type="submit"><i class="fa-solid fa-bell"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </footer>
        <script src="assets\js\main.js"></script>
    </body>
</html>