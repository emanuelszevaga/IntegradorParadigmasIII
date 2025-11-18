<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Vivero MyM</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        
        <!-- archivo base de slick -->
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
        
        <!-- archivo tema de slick -->
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>

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
                                    <li><a href="productos.php?categoria=todos">Ver todo</a></li>
                                    <li><a href="productos.php?categoria=citricos">Cítricos</a></li>
                                    <li><a href="productos.php?categoria=carozo">Frutales de carozo</a></li>
                                    <li><a href="productos.php?categoria=tropicales">Frutas tropicales</a></li>
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

        <!-- sección de carrusel -->
        <section class="hero-slider">
            <div class="slider-container">
                <!-- diapositiva 1 -->
                <div class="slide active">
                <img src="assets\imagenes\slide1.jpg" alt="Vivero MyM">
                <div class="slide-content">
                    <h2>Vivero MyM</h2>
                    <p>Tu rincón verde comienza acá</p>
                    <a href="productos.php" class="hero-btn">Ver más</a>
                </div>
                </div>

                <!-- diapositiva 2 -->
                <div class="slide">
                <img src="assets\imagenes\slide4.jpg" alt="Oferta Clave">
                <div class="slide-content">
                    <h2>3 Cuotas sin interés</h2>
                    <p>Solo Comprando por la Web.</p>
                    <a href="productos.php" class="hero-btn">Ver más</a>
                </div>
                </div>

                <!-- diapositiva 3 -->
                <div class="slide">
                <img src="assets\imagenes\slide3.jpg" alt="Variedades Nuevas">
                <div class="slide-content">
                    <h2>Nuevas variedades</h2>
                    <p>Descrubí las nuevas variedades de citricos</p>
                    <a href="productos.php" class="hero-btn">Ver más</a>
                </div>
                </div>

                <!-- indicadores para cambiar de diapositiva -->
                <div class="slider-dots">
                    <span class="dot active"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
            </div>
        </section>

        <!-- sección de beneficios -->
        <section class="beneficios">
            <div class="container beneficios__grid">
                <div class="beneficio-box">
                    <i class="fa-solid fa-credit-card"></i>
                    <div>
                        <h3>PAGÁ COMO QUIERAS</h3>
                        <p>Tarjetas de crédito, débito o transferencia bancaria.</p>
                    </div>
                </div>
                <div class="beneficio-box">
                    <i class="fa-solid fa-truck"></i>
                    <div>
                        <h3>COMPRÁ ONLINE LAS 24 HS</h3>
                        <p>En la comodidad de tu casa y retirá en el vivero.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- sección productos mas vendidos (plugin)-->
        <section class="productos-mas-vendidos">
            <h2 class="ofertas-title">Productos más vendidos</h2>
            
            <div class="carrusel-productos container">
                <div class="producto-slide">
                    <img src="assets\imagenes\producto1.jpg" alt="Limón 4 estaciones">
                    <h3>Limón 4 estaciones</h3>
                    <p class="precio">$6.000</p>
                </div>
                <div class="producto-slide">
                    <img src="assets\imagenes\producto2.jpg" alt="Naranja ombligo">
                    <h3>Naranja ombligo</h3>
                    <p class="precio">$6.000</p>
                </div>
                <div class="producto-slide">
                    <img src="assets\imagenes\producto4.jpg" alt="Naranja jugo">
                    <h3>Naranja de jugo</h3>
                    <p class="precio">$6.000</p>
                </div>
                <div class="producto-slide">
                    <img src="assets\imagenes\producto6.jpg" alt="Pomelo rosado">
                    <h3>Pomelo rosado</h3>
                    <p class="precio">$6.000</p>
                </div>
                <div class="producto-slide sin-stock" 
                    data-nombre="Durazno" 
                    data-precio="6000" 
                    data-imagen="assets\imagenes\producto8.jpg"
                    data-stock="sin-stock">
                    <img src="assets\imagenes\producto8.jpg" alt="Durazno">
                    <div class="etiqueta-stock">Sin stock</div>
                    <h3>Durazno</h3>
                    <p class="precio">$6.000</p>
                </div>
            </div>
        </section>

        <!-- sección productos -->
        <section class="productos">
            <h2>Productos</h2>
            <div class="container productos__grid">
                <div class="producto-card citricos"
                    data-nombre="Limón 4 estaciones" 
                    data-precio="6000" 
                    data-imagen="assets\imagenes\producto1.jpg"
                    data-descripcion="Limón de excelente calidad que produce frutos durante todo el año. Ideal para climas cálidos y templados."
                    data-stock="disponible">
                    <img src="assets\imagenes\producto1.jpg" alt="Limón 4 estaciones">
                    <h3>Limón 4 estaciones</h3>
                    <p class="precio">$6.000</p>
                    <p class="cuotas">3 cuotas sin interés de $2.000</p>
                    <button class="btn-carrito">Agregar al carrito</button>
                </div>

                <div class="producto-card citricos"
                    data-nombre="Naranja ombligo" 
                    data-precio="6000" 
                    data-imagen="assets\imagenes\producto2.jpg"
                    data-descripcion="Naranja dulce y jugosa, perfecta para consumo fresco. Frutos grandes sin semillas."
                    data-stock="disponible">
                    <img src="assets\imagenes\producto2.jpg" alt="Naranja ombligo">
                    <h3>Naranja ombligo</h3>
                    <p class="precio">$6.000</p>
                    <p class="cuotas">3 cuotas sin interés de $2.000</p>
                    <button class="btn-carrito">Agregar al carrito</button>
                </div>

                <div class="producto-card sin-stock tropicales"
                    data-nombre="Mango" 
                    data-precio="6000" 
                    data-imagen="assets\imagenes\producto3.jpg"
                    data-descripcion="Mango tropical de pulpa dulce y aromática. Excelente para consumo fresco y preparaciones."
                    data-stock="sin-stock">
                    <img src="assets\imagenes\producto3.jpg" alt="Mango">
                    <div class="etiqueta-stock">Sin stock</div>
                    <h3>Mango</h3>
                    <p class="precio">$6.000</p>
                    <p class="cuotas">3 cuotas sin interés de $2.000</p>
                    <button class="btn-carrito">Agregar al carrito</button>
                </div>

                <div class="producto-card citricos"
                    data-nombre="Naranja de jugo" 
                    data-precio="6000" 
                    data-imagen="assets\imagenes\producto4.jpg"
                    data-descripcion="Naranja especialmente seleccionada para jugos. Alto contenido de jugo y sabor intenso."
                    data-stock="disponible">
                    <img src="assets\imagenes\producto4.jpg" alt="Naranja jugo">
                    <h3>Naranja de jugo</h3>
                    <p class="precio">$6.000</p>
                    <p class="cuotas">3 cuotas sin interés de $2.000</p>
                    <button class="btn-carrito">Agregar al carrito</button>
                </div>

                <div class="producto-card sin-stock tropicales"
                    data-nombre="Palta" 
                    data-precio="6000" 
                    data-imagen="assets\imagenes\producto5.jpg"
                    data-descripcion="Palta cremosa y nutritiva. Perfecta para ensaladas y preparaciones saludables."
                    data-stock="sin-stock">
                    <img src="assets\imagenes\producto5.jpg" alt="Palta">
                    <div class="etiqueta-stock">Sin stock</div>
                    <h3>Palta</h3>
                    <p class="precio">$6.000</p>
                    <p class="cuotas">3 cuotas sin interés de $2.000</p>
                    <button class="btn-carrito">Agregar al carrito</button>
                </div>

                <div class="producto-card citricos"
                    data-nombre="Pomelo rosado" 
                    data-precio="6000" 
                    data-imagen="assets\imagenes\producto6.jpg"
                    data-descripcion="Pomelo de pulpa rosada, dulce y refrescante. Rico en vitamina C y antioxidantes."
                    data-stock="disponible">
                    <img src="assets\imagenes\producto6.jpg" alt="Pomelo rosado">
                    <h3>Pomelo rosado</h3>
                    <p class="precio">$6.000</p>
                    <p class="cuotas">3 cuotas sin interés de $2.000</p>
                    <button class="btn-carrito">Agregar al carrito</button>
                </div>

                <div class="producto-card sin-stock citricos"
                    data-nombre="Kinoto" 
                    data-precio="6000" 
                    data-imagen="assets\imagenes\producto7.jpg"
                    data-descripcion="Cítrico pequeño y aromático. Ideal para mermeladas y decoración de platos."
                    data-stock="sin-stock">
                    <img src="assets\imagenes\producto7.jpg" alt="Kinoto">
                    <div class="etiqueta-stock">Sin stock</div>
                    <h3>Kinoto</h3>
                    <p class="precio">$6.000</p>
                    <p class="cuotas">3 cuotas sin interés de $2.000</p>
                    <button class="btn-carrito">Agregar al carrito</button>
                </div>

                <div class="producto-card sin-stock carozo"
                    data-nombre="Durazno" 
                    data-precio="6000" 
                    data-imagen="assets\imagenes\producto8.jpg"
                    data-descripcion="Durazno jugoso y dulce. Excelente para consumo fresco, conservas y postres."
                    data-stock="sin-stock">
                    <img src="assets\imagenes\producto8.jpg" alt="Durazno">
                    <div class="etiqueta-stock">Sin stock</div>
                    <h3>Durazno</h3>
                    <p class="precio">$6.000</p>
                    <p class="cuotas">3 cuotas sin interés de $2.000</p>
                    <button class="btn-carrito">Agregar al carrito</button>
                </div>

                <div class="producto-card sin-stock carozo"
                    data-nombre="Ciruela" 
                    data-precio="6000" 
                    data-imagen="assets\imagenes\producto9.jpg"
                    data-descripcion="Ciruela dulce y jugosa. Perfecta para consumo fresco y preparación de mermeladas."
                    data-stock="sin-stock">
                    <img src="assets\imagenes\producto9.jpg" alt="Ciruela">
                    <div class="etiqueta-stock">Sin stock</div>
                    <h3>Ciruela</h3>
                    <p class="precio">$6.000</p>
                    <p class="cuotas">3 cuotas sin interés de $2.000</p>
                    <button class="btn-carrito">Agregar al carrito</button>
                </div>
            </div>
        </section>

        <!-- product detail modal -->
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

        <!-- modal buscador-->
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
                    <p class="register-link">¿No tienes cuenta? <a href="#" onclick="document.getElementById('login-modal').style.display='none'; document.getElementById('register-modal').style.display='flex'; return false;">Registrarse</a></p>
                </div>
            </div>
        </div>

        <!-- modal registro -->
        <div id="register-modal" class="modal">
            <div class="modal-content login-modal-content">
                <button class="modal-close" id="register-close">&times;</button>
                <div class="login-modal-body">
                    <h2>Crear cuenta</h2>
                    <form id="register-form">
                        <div class="form-group">
                            <label for="reg-nombre">Nombre completo</label>
                            <input type="text" id="reg-nombre" required placeholder="Tu nombre">
                        </div>
                        <div class="form-group">
                            <label for="reg-email">Correo electrónico</label>
                            <input type="email" id="reg-email" required placeholder="tu@email.com">
                        </div>
                        <div class="form-group">
                            <label for="reg-password">Contraseña</label>
                            <input type="password" id="reg-password" required placeholder="••••••••">
                            <small>Mínimo 8 caracteres, incluye mayúscula, minúscula y número</small>
                        </div>
                        <div class="form-group">
                            <label for="reg-password-confirm">Confirmar contraseña</label>
                            <input type="password" id="reg-password-confirm" required placeholder="••••••••">
                        </div>
                        <button type="submit" class="btn-login">Registrarse</button>
                    </form>
                    <p class="register-link">¿Ya tienes cuenta? <a href="#" onclick="document.getElementById('register-modal').style.display='none'; document.getElementById('login-modal').style.display='flex'; return false;">Iniciar sesión</a></p>
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
        <!-- scripts de js -->
        <script src="assets/js/main.js"></script>
        <script src="assets/js/slider.js"></script>
        <script src="assets/js/checkout.js"></script>

        <!--librería de jquery-->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!--archivo JavaScript de Slick Carousel.-->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
        
        <script>
        $(document).ready(function(){ //Asegura que el código solo se ejecute cuando el navegador haya terminado de cargar todo el DOM.
            
            // Inicializa el carrusel en el contenedor: .carrusel-productos
            $('.carrusel-productos').slick({ //selector de clase CSS de jQuery para encontrar el/los elemento/s HTML que tienen la clase carrusel-productos
                dots: true, // Muestra puntos de navegación
                infinite: true, // Repetición infinita del carrusel
                speed: 300, //velocidad de transición 
                slidesToShow: 4, // Muestra 4 ítems en desktop
                slidesToScroll: 1, //Cuántos ítems se mueven cada vez que el usuario hace clic en las flechas
                autoplay: true,
                autoplaySpeed: 3000,

                // Configuración responsive
                responsive: [  
                    {
                        breakpoint: 1024,
                        settings: {
                        slidesToShow: 3,
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                        slidesToShow: 2,
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                        slidesToShow: 1,
                        arrows: false 
                        }
                    }
                ]
            });
        });
        </script>
        
    </body>
</html>
