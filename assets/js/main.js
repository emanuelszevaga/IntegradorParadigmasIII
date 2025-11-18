document.addEventListener("DOMContentLoaded", async () => {
    const API_BASE = '/integrador3.0/api'

    // === PERSISTENCIA DE SESIÓN CLIENTE ===
    let usuarioGuardado = localStorage.getItem('usuarioAutenticado');
    if (usuarioGuardado) {
        try {
            window.usuarioAutenticado = JSON.parse(usuarioGuardado);
        } catch(e) {
            console.error("[v0] Error al parsear usuario de localStorage:", e);
            localStorage.removeItem('usuarioAutenticado');
        }
    }
    // =====================================

    // === FUNCIÓN PARA OBTENER ALIAS DE FILTRO ===
    // Asegura que los nombres de categorías de la base de datos (ej: 'Cítricos') 
    // se mapeen a los alias del filtro (ej: 'citricos').
    function getFilterAlias(categoriaNombre) {
        if (!categoriaNombre) return '';
        const lower = categoriaNombre.toLowerCase().trim();
        
        if (lower.includes('cítricos') || lower.includes('citricos')) {
            return 'citricos';
        }
        if (lower.includes('carozo')) { 
            return 'carozo';
        }
        if (lower.includes('tropicales')) {
            return 'tropicales';
        }
        if (lower === 'todos') {
            return 'todos';
        }
        return '';
    }
    // =====================================

    async function obtenerProductosDelServidor(categoria = null, ordenar = 'nombre') {
        try {
            let url = API_BASE + '/productos/obtener.php?ordenar=' + ordenar
            url += '&t=' + Date.now()
            
            const response = await fetch(url, { cache: 'no-store' })
            const data = await response.json()
            
            if (data.estado === 'exitoso') {
                return data.productos || []
            }
            return []
        } catch (error) {
            console.error("[v0] Error al obtener productos:", error)
            return []
        }
    }

    function renderizarProductosDinamicos(productos) {
        const contenedor = document.querySelector('.productos__grid')
        if (!contenedor) return
        
        if (productos.length === 0) {
            contenedor.innerHTML = '<p class="text-center">No hay productos disponibles</p>'
            return
        }
        
        contenedor.innerHTML = productos.map(producto => {
            const disponible = producto.stock > 0
            const claseStock = disponible ? 'disponible' : 'sin-stock'
            const etiquetaStock = ''; 
            const deshabilitado = !disponible ? 'disabled' : ''
            const textoBtnStock = disponible ? 'Agregar al carrito' : 'Sin stock'
            const imagenNormalizada = producto.imagen.replace(/\\/g, '/')
            
            const categoriaClase = getFilterAlias(producto.categoria_nombre) 
            
            const descripcionLimpia = producto.descripcion ? producto.descripcion.replace(/"/g, '&quot;') : '';

            return `
                <div class="producto-card ${categoriaClase}" 
                    data-id="${producto.id}"
                    data-nombre="${producto.nombre}" 
                    data-precio="${producto.precio}" 
                    data-imagen="${imagenNormalizada}"
                    data-descripcion="${descripcionLimpia}"
                    data-stock="${claseStock}">
                    <img src="${imagenNormalizada}" alt="${producto.nombre}">
                    ${etiquetaStock}
                    <h3>${producto.nombre}</h3>
                    <p class="precio">$${Number(producto.precio).toLocaleString('es-AR')}</p>
                    <p class="cuotas">3 cuotas sin interés de $${(producto.precio / 3).toLocaleString('es-AR')}</p>
                    <button class="btn-carrito ${claseStock}" ${deshabilitado}>${textoBtnStock}</button>
                </div>
            `
        }).join('')
        
        asignarEventListenersProductos()
    }

    async function renderizarCarrouselProductosVendidos() {
        const productos = await obtenerProductosDelServidor(null, 'vendidos')
        const topProductos = productos.slice(0, 5)
        
        const contenedor = document.querySelector('.carrusel-productos')
        if (!contenedor) return
        
        if (topProductos.length === 0) {
            contenedor.innerHTML = '<p>No hay productos vendidos</p>'
            return
        }
        
        contenedor.innerHTML = topProductos.map(producto => `
            <div class="producto-slide">
                <img src="${producto.imagen.replace(/\\/g, '/')}" alt="${producto.nombre}">
                <h3>${producto.nombre}</h3>
                <p class="precio">$${Number(producto.precio).toLocaleString('es-AR')}</p>
            </div>
        `).join('')

        // Reinicializar Slick Carousel
        if (jQuery && jQuery('.carrusel-productos').length) {
            jQuery('.carrusel-productos').slick('unslick')
            jQuery('.carrusel-productos').slick({
                dots: true,
                infinite: true,
                speed: 300,
                slidesToShow: 4,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 3000,
                responsive: [
                    { breakpoint: 1024, settings: { slidesToShow: 3 } },
                    { breakpoint: 600, settings: { slidesToShow: 2 } },
                    { breakpoint: 480, settings: { slidesToShow: 1, arrows: false } }
                ]
            })
        }
    }

    function asignarEventListenersProductos() {
        document.querySelectorAll('.producto-card').forEach((card) => {
            card.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-carrito')) {
                    e.stopPropagation()
                    return
                }
                
                const nombre = this.getAttribute('data-nombre')
                const precio = this.getAttribute('data-precio')
                const imagen = this.getAttribute('data-imagen')
                const descripcion = this.getAttribute('data-descripcion')
                const stock = this.getAttribute('data-stock')
                
                const imagenNormalizada = imagen.replace(/\\/g, '/')
                document.getElementById('modal-img').src = imagenNormalizada
                document.getElementById('modal-nombre').textContent = nombre
                document.getElementById('modal-precio').textContent = `$${Number.parseInt(precio).toLocaleString("es-AR")}`
                document.getElementById('modal-descripcion').textContent = descripcion
                
                const stockDiv = document.getElementById('modal-stock')
                const addToCartBtn = document.getElementById('btn-add-to-cart')
                const quantityInput = document.getElementById('cantidad')
                
                if (stock === 'sin-stock') {
                    stockDiv.innerHTML = '<span class="stock-badge sin-stock">Sin stock</span>'
                    addToCartBtn.disabled = true
                    addToCartBtn.textContent = 'No disponible'
                    quantityInput.disabled = true
                } else {
                    stockDiv.innerHTML = '<span class="stock-badge disponible">Disponible</span>'
                    addToCartBtn.disabled = false
                    addToCartBtn.textContent = 'Agregar al carrito'
                    quantityInput.disabled = false
                }
                
                quantityInput.value = 1
                addToCartBtn.dataset.nombre = nombre
                addToCartBtn.dataset.precio = precio
                addToCartBtn.dataset.imagen = imagen
                
                document.getElementById('product-modal').style.display = 'flex'
                document.body.style.overflow = 'hidden'
            })
            
            const btnCarrito = card.querySelector('.btn-carrito')
            if (btnCarrito && !btnCarrito.disabled) {
                btnCarrito.addEventListener('click', (e) => {
                    e.stopPropagation()
                    card.click()
                })
            }
        })
    }

    // iniciación del carrito 
    let cart = []
    try {
        const storedCart = localStorage.getItem("viveroCart")
        cart = storedCart ? JSON.parse(storedCart) : []
    } catch (error) {
        cart = []
    }

    // menú hamburguesa toggle
    const toggle = document.getElementById("menu-toggle")
    const nav = document.getElementById("nav-center")
    if (toggle && nav) {
        toggle.addEventListener("click", () => {
            nav.classList.toggle("show")
        })
    }

    // submenú de producto en móviles
    const menuProductos = document.querySelector(".menu-productos > a")
    if (menuProductos) {
        menuProductos.addEventListener("click", (e) => {
            if (window.innerWidth <= 768) {
                e.preventDefault()
                const parent = menuProductos.parentElement
                parent.classList.toggle("open")
            }
        })
    }

    // submenú para filtrar productos (Filtro en Cliente)
    document.querySelectorAll(".submenu a").forEach((link) => {
        link.addEventListener("click", (e) => {
            const filtro = link.getAttribute("data-filter")

            if (filtro) {
                e.preventDefault()
                const productos = document.querySelectorAll(".producto-card")

                productos.forEach((card) => {
                    if (filtro === "todos") {
                        card.style.display = "block"
                    } else {
                        // Aplica la lógica de visibilidad basada en la clase generada
                        card.style.display = card.classList.contains(filtro) ? "block" : "none"
                    }
                })

                const nav = document.getElementById("nav-center")
                if (nav && nav.classList.contains("show")) {
                    nav.classList.remove("show")
                }
            }
        })
    })

    // modal de detalles de producto
    const modal = document.getElementById("product-modal")
    const modalClose = document.querySelector(".modal-close")

    if (modal && modalClose) {
        modalClose.addEventListener("click", () => {
            modal.style.display = "none"
            document.body.style.overflow = "auto"
        })

        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                modal.style.display = "none"
                document.body.style.overflow = "auto"
            }
        })

        const btnDecrease = document.getElementById("btn-decrease")
        const btnIncrease = document.getElementById("btn-increase")
        const cantidadInput = document.getElementById("cantidad")

        if (btnDecrease && btnIncrease && cantidadInput) {
            btnDecrease.addEventListener("click", () => {
                const currentValue = Number.parseInt(cantidadInput.value)
                if (currentValue > 1) {
                    cantidadInput.value = currentValue - 1
                }
            })

            btnIncrease.addEventListener("click", () => {
                const currentValue = Number.parseInt(cantidadInput.value)
                const maxValue = Number.parseInt(cantidadInput.max)
                if (currentValue < maxValue) {
                    cantidadInput.value = currentValue + 1
                }
            })
        }

        const btnAddToCart = document.getElementById("btn-add-to-cart")
        if (btnAddToCart) {
            btnAddToCart.addEventListener("click", () => {
                const nombre = btnAddToCart.dataset.nombre
                const precio = Number.parseInt(btnAddToCart.dataset.precio)
                const imagen = btnAddToCart.dataset.imagen
                const cantidad = Number.parseInt(document.getElementById("cantidad").value)

                addToCart(nombre, precio, imagen, cantidad)

                modal.style.display = "none"
                document.body.style.overflow = "auto"

                alert(`${cantidad}x ${nombre} agregado al carrito`)
            })
        }
    }

    // buscador
    const searchBtn = document.querySelector(".container-actions button:nth-child(1)")
    const searchModal = document.getElementById("search-modal")
    const searchClose = document.getElementById("search-close")
    const searchInput = document.getElementById("search-input")
    const searchResults = document.getElementById("search-results")

    if (searchBtn && searchModal) {
        searchBtn.addEventListener("click", () => {
            searchModal.style.display = "flex"
            document.body.style.overflow = "hidden"
            searchInput.focus()
        })

        searchClose.addEventListener("click", () => {
            searchModal.style.display = "none"
            document.body.style.overflow = "auto"
            searchInput.value = ""
            searchResults.innerHTML = ""
        })

        searchModal.addEventListener("click", (e) => {
            if (e.target === searchModal) {
                searchModal.style.display = "none"
                document.body.style.overflow = "auto"
                searchInput.value = ""
                searchResults.innerHTML = ""
            }
        })

        searchInput.addEventListener("input", (e) => {
            const query = e.target.value.toLowerCase().trim()

            if (query === "") {
                searchResults.innerHTML = ""
                return
            }

            const allProducts = document.querySelectorAll(".producto-card")
            const results = []

            allProducts.forEach((card) => {
                const nombre = card.getAttribute("data-nombre").toLowerCase()
                if (nombre.includes(query)) {
                    results.push({
                        nombre: card.getAttribute("data-nombre"),
                        precio: card.getAttribute("data-precio"),
                        imagen: card.getAttribute("data-imagen"),
                        stock: card.getAttribute("data-stock"),
                    })
                }
            })

            if (results.length === 0) {
                searchResults.innerHTML = '<div class="no-results">No se encontraron productos</div>'
            } else {
                searchResults.innerHTML = results
                    .map(
                        (product) => `<div class="search-result-item" data-nombre="${product.nombre}" data-precio="${product.precio}" data-imagen="${product.imagen}" data-stock="${product.stock}">
                            <img src="${product.imagen.replace(/\\/g, '/')}" alt="${product.nombre}">
                            <div class="search-result-info">
                                <h4>${product.nombre}</h4>
                                <p>$${Number.parseInt(product.precio).toLocaleString("es-AR")}</p>
                            </div>
                        </div>`
                    )
                    .join("")

                document.querySelectorAll(".search-result-item").forEach((item) => {
                    item.addEventListener("click", () => {
                        searchModal.style.display = "none"
                        document.body.style.overflow = "auto"
                        searchInput.value = ""
                        searchResults.innerHTML = ""

                        const nombre = item.getAttribute("data-nombre")
                        const precio = item.getAttribute("data-precio")
                        const imagen = item.getAttribute("data-imagen")
                        const stock = item.getAttribute("data-stock")

                        const productCard = Array.from(document.querySelectorAll('.producto-card')).find((card) => card.getAttribute("data-nombre") === nombre)
                        const descripcion = productCard ? productCard.getAttribute("data-descripcion") : ""

                        const imagenNormalizada = imagen.replace(/\\/g, '/')
                        document.getElementById("modal-img").src = imagenNormalizada

                        document.getElementById("modal-nombre").textContent = nombre
                        document.getElementById("modal-precio").textContent = `$${Number.parseInt(precio).toLocaleString("es-AR")}`
                        document.getElementById("modal-descripcion").textContent = descripcion

                        const stockDiv = document.getElementById("modal-stock")
                        const addToCartBtn = document.getElementById("btn-add-to-cart")
                        const quantityInput = document.getElementById("cantidad")

                        if (stock === "sin-stock") {
                            stockDiv.innerHTML = '<span class="stock-badge sin-stock">Sin stock</span>'
                            addToCartBtn.disabled = true
                            addToCartBtn.textContent = "No disponible"
                            quantityInput.disabled = true
                        } else {
                            stockDiv.innerHTML = '<span class="stock-badge disponible">Disponible</span>'
                            addToCartBtn.disabled = false
                            addToCartBtn.textContent = "Agregar al carrito"
                            quantityInput.disabled = false
                        }

                        quantityInput.value = 1
                        addToCartBtn.dataset.nombre = nombre
                        addToCartBtn.dataset.precio = precio
                        addToCartBtn.dataset.imagen = imagen

                        modal.style.display = "flex"
                        document.body.style.overflow = "hidden"
                    })
                })
            }
        })
    }

    // === LÓGICA DE LOGIN/LOGOUT CENTRALIZADA (SOLUCIÓN AL PROBLEMA 1) ===
    const loginBtn = document.getElementById("login-btn")
    const loginModal = document.getElementById("login-modal")
    const loginForm = document.getElementById("login-form")

    if (loginForm) {
        loginForm.addEventListener("submit", async (e) => {
            e.preventDefault()
            const email = document.getElementById("email").value
            const password = document.getElementById("password").value

            if (!email || !password) {
                alert("Por favor completa todos los campos")
                return
            }

            try {
                const response = await fetch(API_BASE + '/auth/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                })

                if (!response.ok && response.status !== 401 && response.status !== 400) {
                    throw new Error(`HTTP Error: ${response.status}`)
                }

                const texto = await response.text()
                
                let resultado
                try {
                    resultado = JSON.parse(texto)
                } catch (parseError) {
                    alert('Error al iniciar sesión. La respuesta del servidor no es válida.')
                    return
                }
                
                if (resultado.estado === 'exitoso') {
                    alert(`¡Bienvenido ${resultado.usuario.nombre}!`)
                    
                    window.usuarioAutenticado = resultado.usuario
                    localStorage.setItem('usuarioAutenticado', JSON.stringify(resultado.usuario))
                    
                    if (resultado.usuario.rol === 'administrador') {
                        window.location.href = '/integrador3.0/admin/index.php'
                        return
                    }
                    
                    actualizarUIPostLogin()
                    
                    loginModal.style.display = "none"
                    document.body.style.overflow = "auto"
                    loginForm.reset()
                } else {
                    alert(`Error: ${resultado.mensaje}`)
                }
            } catch (error) {
                alert('Error al iniciar sesión: ' + error.message)
            }
        })
    }

    function actualizarUIPostLogin() {
        const loginBtn = document.getElementById("login-btn")
        const loginModal = document.getElementById("login-modal")

        if (!loginBtn || !loginModal) return 
        
        loginBtn.onclick = null // Se limpia el evento anterior

        if (window.usuarioAutenticado) {
            const rol = window.usuarioAutenticado.rol || 'usuario'
            const icon = rol === 'administrador' ? 'fa-user-shield' : 'fa-user-check'
            loginBtn.innerHTML = `<i class="fa-solid ${icon}"></i>`
            loginBtn.title = `Conectado como ${window.usuarioAutenticado.nombre} (Click para cerrar sesión)`
            
            // Asignar LOGOUT en click
            loginBtn.onclick = (e) => {
                e.preventDefault()
                const confirmar = confirm(`¿Deseas cerrar sesión, ${window.usuarioAutenticado.nombre}?`)
                if (confirmar) {
                    cerrarSesion()
                }
            }
        } else {
            loginBtn.innerHTML = `<i class="fa-solid fa-user"></i>`
            loginBtn.title = 'Iniciar sesión'
            
            // Asignar LOGIN MODAL en click
            loginBtn.onclick = (e) => {
                e.preventDefault()
                loginModal.style.display = "flex"
                document.body.style.overflow = "hidden"
            }
        }
    }

    function cerrarSesion() {
        // Limpia el estado de cliente
        window.usuarioAutenticado = null
        localStorage.removeItem('usuarioAutenticado')
        cart = []
        localStorage.removeItem('viveroCart')
        
        actualizarUIPostLogin()
        
        cargarProductosCompletos()
        
        alert('Sesión cerrada correctamente')
    }
    // === FIN LÓGICA DE LOGIN/LOGOUT CENTRALIZADA ===


    // === FUNCIÓN CLAVE PARA CARGA INICIAL Y FILTRADO (SOLUCIÓN AL PROBLEMA 2) ===
    function aplicarFiltroDesdeURLoCategoria(filtroDeseado = null) {
        let filtro = filtroDeseado;
        
        if (!filtro) {
            const params = new URLSearchParams(window.location.search);
            const categoriaURL = params.get("categoria"); 
            if (categoriaURL) {
                filtro = getFilterAlias(categoriaURL);
            }
        }

        if (!filtro) return;

        const productos = document.querySelectorAll(".producto-card");
        
        if (filtro === "todos") {
            productos.forEach((card) => {
                card.style.display = "block";
            });
        } else {
            productos.forEach((card) => {
                // Si la tarjeta contiene la clase de alias, la muestra
                card.style.display = card.classList.contains(filtro) ? "block" : "none";
            });
        }
    }


    async function cargarProductosCompletos() {
        const productos = await obtenerProductosDelServidor()
        renderizarProductosDinamicos(productos)
        await renderizarCarrouselProductosVendidos()
        
        // Aplica el filtro inmediatamente después de renderizar todos los productos
        aplicarFiltroDesdeURLoCategoria(); 
    }
    // =========================================================================


    // abrir y cerrar carrito lateral
    const cartBtn = document.querySelector(".container-actions button:nth-child(3)")
    const cartSidebar = document.getElementById("cart-sidebar")
    const cartOverlay = document.getElementById("cart-overlay")
    const cartClose = document.getElementById("cart-close")
    const cartBody = document.getElementById("cart-body")
    const cartFooter = document.getElementById("cart-footer")

    if (cartBtn) {
        cartBtn.addEventListener('click', () => {
            cartSidebar.classList.add('open')
            cartOverlay.classList.add('show')
            document.body.style.overflow = 'hidden'
        })
    }

    if (cartClose) {
        cartClose.addEventListener('click', () => {
            cartSidebar.classList.remove('open')
            cartOverlay.classList.remove('show')
            document.body.style.overflow = 'auto'
        })
    }

    if (cartOverlay) {
        cartOverlay.addEventListener('click', () => {
            cartSidebar.classList.remove('open')
            cartOverlay.classList.remove('show')
            document.body.style.overflow = 'auto'
        })
    }

    function updateCartUI() {
        const cartBtn = document.querySelector(".container-actions button:nth-child(3)")
        if (!cartBtn) return;

        if (cart.length === 0) {
            cartBody.innerHTML = '<p class="empty-cart">Tu carrito está vacío</p>'
            cartFooter.style.display = "none"
        } else {
            cartBody.innerHTML = cart
                .map((item, index) => {
                    const nombre = item.nombre || "Producto sin nombre"
                    const imagen = item.imagen || "/placeholder.svg"
                    const precio = Number(item.precio) || 0
                    const cantidad = Number(item.cantidad) || 1

                    return `
                        <div class="cart-item">
                            <img src="${imagen}" alt="${nombre}" class="cart-item-image">
                            <div class="cart-item-info">
                                <div class="cart-item-name">${nombre}</div>
                                <div class="cart-item-price">$${precio.toLocaleString("es-AR")}</div>
                                <div class="cart-item-controls">
                                    <button onclick="decreaseCartItem(${index})">-</button>
                                    <span class="cart-item-quantity">${cantidad}</span>
                                    <button onclick="increaseCartItem(${index})">+</button>
                                </div>
                                <span class="cart-item-remove" onclick="removeCartItem(${index})">Eliminar</span>
                            </div>
                        </div>
                    `
                })
                .join("")

            const total = cart.reduce((sum, item) => sum + (Number(item.precio) || 0) * (Number(item.cantidad) || 1), 0)
            document.getElementById("cart-total-price").textContent = `$${total.toLocaleString("es-AR")}`
            cartFooter.style.display = "block"
        }

        updateCartBadge(cartBtn)
    }
    
    function updateCartBadge(cartBtn) {
        const totalItems = cart.reduce((sum, item) => sum + item.cantidad, 0)
        let badge = cartBtn.querySelector(".cart-badge")

        if (totalItems > 0) {
            if (!badge) {
                badge = document.createElement("span")
                badge.className = "cart-badge"
                cartBtn.style.position = "relative"
                cartBtn.appendChild(badge)
            }
            badge.textContent = totalItems
        } else {
            if (badge) {
                badge.remove()
            }
        }
    }


    // agregar productos al carrito
    function addToCart(nombre, precio, imagen, cantidad) {
        if (!nombre || !precio || !imagen) {
            alert("Error: Datos del producto inválidos")
            return
        }

        const existingItem = cart.find((item) => item.nombre === nombre)

        if (existingItem) {
            existingItem.cantidad += cantidad
        } else {
            const newItem = {
                nombre: nombre,
                precio: Number(precio),
                imagen: imagen,
                cantidad: Number(cantidad),
            }
            cart.push(newItem)
        }

        localStorage.setItem("viveroCart", JSON.stringify(cart))
        updateCartUI()
    }

    // funciones globales para editar el carrito
    window.increaseCartItem = (index) => {
        cart[index].cantidad++
        localStorage.setItem("viveroCart", JSON.stringify(cart))
        updateCartUI()
    }

    window.decreaseCartItem = (index) => {
        if (cart[index].cantidad > 1) {
            cart[index].cantidad--
            localStorage.setItem("viveroCart", JSON.stringify(cart))
            updateCartUI()
        }
    }

    window.removeCartItem = (index) => {
        cart.splice(index, 1)
        localStorage.setItem("viveroCart", JSON.stringify(cart))
        updateCartUI()
    }
    
    // Inicia la carga de productos y la UI
    await cargarProductosCompletos()
    updateCartUI()
    actualizarUIPostLogin()

    // para reflejar cambios realizados por el administrador
    setInterval(async () => {
        const productos = await obtenerProductosDelServidor()
        renderizarProductosDinamicos(productos)
        aplicarFiltroDesdeURLoCategoria();
    }, 30000)
})

function procesarCheckout() {
    alert("Procesando pago...")
}