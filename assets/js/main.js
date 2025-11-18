document.addEventListener("DOMContentLoaded", async () => {
    const API_BASE = '/integrador3.0/api'

    async function obtenerProductosDelServidor(categoria = null, ordenar = 'nombre') {
        try {
            let url = API_BASE + '/productos/obtener.php?ordenar=' + ordenar
            if (categoria && categoria !== 'todos') {
                url += '&categoria=' + encodeURIComponent(categoria)
            }
            
            const response = await fetch(url)
            const data = await response.json()
            
            console.log("[v0] Productos obtenidos del servidor:", data)
            
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
            const etiquetaStock = disponible ? '' : '<div class="etiqueta-stock">Sin stock</div>'
            const deshabilitado = !disponible ? 'disabled' : ''
            const textoBtnStock = disponible ? 'Agregar al carrito' : 'Agregar al carrito'
            const imagenNormalizada = producto.imagen.replace(/\\/g, '/')
            
            return `
                <div class="producto-card ${producto.categoria_nombre ? producto.categoria_nombre.toLowerCase().replace(/ /g, '-') : ''}" 
                    data-nombre="${producto.nombre}" 
                    data-precio="${producto.precio}" 
                    data-imagen="${imagenNormalizada}"
                    data-descripcion="${producto.descripcion}"
                    data-stock="${claseStock}">
                    <img src="${imagenNormalizada}" alt="${producto.nombre}">
                    ${etiquetaStock}
                    <h3>${producto.nombre}</h3>
                    <p class="precio">$${Number(producto.precio).toLocaleString('es-AR')}</p>
                    <p class="cuotas">3 cuotas sin interés de $${(producto.precio / 3).toLocaleString('es-AR')}</p>
                    <button class="btn-carrito" ${deshabilitado}>${textoBtnStock}</button>
                </div>
            `
        }).join('')
        
        // Reasignar event listeners a las nuevas tarjetas
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
        const productCards = document.querySelectorAll('.producto-card')
        
        productCards.forEach((card) => {
            // Remover listeners antiguos
            card.replaceWith(card.cloneNode(true))
        })
        
        document.querySelectorAll('.producto-card').forEach((card) => {
            card.addEventListener('click', (e) => {
                if (e.target.classList.contains('btn-carrito')) {
                    return
                }
                
                const nombre = card.getAttribute('data-nombre')
                const precio = card.getAttribute('data-precio')
                const imagen = card.getAttribute('data-imagen')
                const descripcion = card.getAttribute('data-descripcion')
                const stock = card.getAttribute('data-stock')
                
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
            
            // Event listener para botón agregar al carrito desde tarjeta
            const btnCarrito = card.querySelector('.btn-carrito')
            if (btnCarrito && !btnCarrito.disabled) {
                btnCarrito.addEventListener('click', (e) => {
                    e.stopPropagation()
                    // Abrir modal
                    card.click()
                })
            }
        })
    }

    // iniciación del carrito 
    let cart = []
    try {
        // recuperar el carrito guardado en el almacenamiento local
        const storedCart = localStorage.getItem("viveroCart")
        cart = storedCart ? JSON.parse(storedCart) : []
        console.log("[v0] Carrito cargado desde localStorage:", cart)
    } catch (error) {
        console.error("[v0] Error al cargar el carrito:", error)
        cart = []
    }

    // menú hamburguesa toggle
    const toggle = document.getElementById("menu-toggle")
    const nav = document.getElementById("nav-center")
    if (toggle && nav) {
        // muestra u oculta el menú al hacer clic en el botón hamburguesa
        toggle.addEventListener("click", () => {
            nav.classList.toggle("show")
        })
    }

    // submenú de producto en móviles
    const menuProductos = document.querySelector(".menu-productos > a")
    if (menuProductos) {
        menuProductos.addEventListener("click", (e) => {
            // solo activa en pantallas pequeñas (menor o igual a 768px)
            if (window.innerWidth <= 768) {
                e.preventDefault()
                const parent = menuProductos.parentElement
                parent.classList.toggle("open")
            }
        })
    }

    // filtrado de productos por categoría
    const params = new URLSearchParams(window.location.search)
    const categoria = params.get("categoria")

    // submenú para filtrar productos
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
                        card.style.display = card.classList.contains(filtro) ? "block" : "none"
                    }
                })

                // cierra el menú móvil si estaba abierto
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
    const productCards = document.querySelectorAll(".producto-card")

    if (modal && modalClose) {
        // cerrar modal
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

        // agregar productos al carrito desde el modal
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

    // buscador, login, carrito lateral
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
                        // Close search modal
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

    const loginBtn = document.querySelector(".container-actions button:nth-child(2)")
    const loginModal = document.getElementById("login-modal")
    const loginClose = document.getElementById("login-close")
    const loginForm = document.getElementById("login-form")

    if (loginBtn && loginModal) {
        loginBtn.addEventListener("click", () => {
            loginModal.style.display = "flex"
            document.body.style.overflow = "hidden"
        })

        loginClose.addEventListener("click", () => {
            loginModal.style.display = "none"
            document.body.style.overflow = "auto"
        })

        loginModal.addEventListener("click", (e) => {
            if (e.target === loginModal) {
                loginModal.style.display = "none"
                document.body.style.overflow = "auto"
            }
        })

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
                        console.error("[v0] Response status:", response.status)
                        throw new Error(`HTTP Error: ${response.status}`)
                    }

                    const texto = await response.text()
                    console.log("[v0] Login response text:", texto)
                    
                    let resultado
                    try {
                        resultado = JSON.parse(texto)
                    } catch (parseError) {
                        console.error("[v0] Error parsing JSON:", parseError)
                        console.error("[v0] Response text was:", texto)
                        alert('Error al iniciar sesión. La respuesta del servidor no es válida.')
                        return
                    }
                    
                    console.log("[v0] Login response:", resultado)

                    if (resultado.estado === 'exitoso') {
                        alert(`¡Bienvenido ${resultado.usuario.nombre}!`)
                        
                        // Store user data
                        window.usuarioAutenticado = resultado.usuario
                        localStorage.setItem('usuarioAutenticado', JSON.stringify(resultado.usuario))
                        
                        // Update UI
                        actualizarUIPostLogin()
                        
                        // Close modal
                        loginModal.style.display = "none"
                        document.body.style.overflow = "auto"
                        loginForm.reset()
                    } else {
                        alert(`Error: ${resultado.mensaje}`)
                    }
                } catch (error) {
                    console.error("[v0] Login error:", error)
                    alert('Error al iniciar sesión: ' + error.message)
                }
            })
        }
    }

    const registerLink = document.querySelector(".register-link a")
    if (registerLink) {
        registerLink.addEventListener("click", (e) => {
            e.preventDefault()
            // Close login modal
            loginModal.style.display = "none"
            // Open register modal
            const registerModal = document.getElementById("register-modal")
            if (registerModal) {
                registerModal.style.display = "flex"
                document.body.style.overflow = "hidden"
            }
        })
    }

    const registerModal = document.getElementById("register-modal")
    const registerClose = document.getElementById("register-close")
    const registerForm = document.getElementById("register-form")

    if (registerClose && registerModal) {
        registerClose.addEventListener("click", () => {
            registerModal.style.display = "none"
            document.body.style.overflow = "auto"
        })

        registerModal.addEventListener("click", (e) => {
            if (e.target === registerModal) {
                registerModal.style.display = "none"
                document.body.style.overflow = "auto"
            }
        })
    }

    if (registerForm) {
        registerForm.addEventListener("submit", async (e) => {
            e.preventDefault()
            
            const nombre = document.getElementById("reg-nombre").value.trim()
            const email = document.getElementById("reg-email").value.trim()
            const password = document.getElementById("reg-password").value
            const password_confirm = document.getElementById("reg-password-confirm").value

            if (!nombre || !email || !password || !password_confirm) {
                alert("Por favor completa todos los campos")
                return
            }

            if (password !== password_confirm) {
                alert("Las contraseñas no coinciden")
                return
            }

            try {
                console.log("[v0] Enviando registro con:", { nombre, email })
                
                const response = await fetch(API_BASE + '/auth/registro.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ nombre, email, password, password_confirm })
                })

                console.log("[v0] Response status:", response.status)
                const resultado = await response.json()
                console.log("[v0] Register response:", resultado)

                if (resultado.estado === 'exitoso') {
                    alert(`¡Cuenta creada! Bienvenido ${resultado.usuario.nombre}`)
                    
                    // Store user data
                    window.usuarioAutenticado = resultado.usuario
                    localStorage.setItem('usuarioAutenticado', JSON.stringify(resultado.usuario))
                    
                    // Update UI
                    actualizarUIPostLogin()
                    
                    // Close modal
                    registerModal.style.display = "none"
                    document.body.style.overflow = "auto"
                    registerForm.reset()
                } else if (resultado.mensajes) {
                    alert("Errores en el registro:\n" + resultado.mensajes.join("\n"))
                } else {
                    alert(`Error: ${resultado.mensaje}`)
                }
            } catch (error) {
                console.error("[v0] Register error:", error)
                alert('Error al registrar. Verifica tu conexión.')
            }
        })
    }

    function actualizarUIPostLogin() {
        const loginBtn = document.getElementById("login-btn")
        if (loginBtn && window.usuarioAutenticado) {
            const rol = window.usuarioAutenticado.rol || 'usuario'
            const icon = rol === 'administrador' ? 'fa-user-shield' : 'fa-user-check'
            loginBtn.innerHTML = `<i class="fa-solid ${icon}"></i>`
            loginBtn.title = `Conectado como ${window.usuarioAutenticado.nombre} (Click para cerrar sesión)`
            
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
            loginBtn.onclick = null
        }
    }

    function cerrarSesion() {
        window.usuarioAutenticado = null
        localStorage.removeItem('usuarioAutenticado')
        cart = []
        localStorage.removeItem('viveroCart')
        
        // Reset UI
        const loginBtn = document.getElementById("login-btn")
        loginBtn.innerHTML = `<i class="fa-solid fa-user"></i>`
        loginBtn.title = 'Iniciar sesión'
        loginBtn.onclick = null
        
        alert('Sesión cerrada correctamente')
    }

    const cartBtn = document.querySelector(".container-actions button:nth-child(3)")
    const cartSidebar = document.getElementById("cart-sidebar")
    const cartOverlay = document.getElementById("cart-overlay")
    const cartClose = document.getElementById("cart-close")
    const cartBody = document.getElementById("cart-body")
    const cartFooter = document.getElementById("cart-footer")

    function updateCartUI() {
        console.log("[v0] Actualizando interfaz del carrito:", cart)

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

                    console.log("[v0] Rendering cart item:", { nombre, imagen, precio, cantidad })

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

            // calcula el total del carrito
            const total = cart.reduce((sum, item) => sum + (Number(item.precio) || 0) * (Number(item.cantidad) || 1), 0)
            document.getElementById("cart-total-price").textContent = `$${total.toLocaleString("es-AR")}`
            cartFooter.style.display = "block"
        }

        updateCartBadge()
    }

    // muestra la cantidad total de productos en el ícono del carrito
    function updateCartBadge() {
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
        console.log("[v0] Agregando producto al carrito:", { nombre, precio, imagen, cantidad })

        if (!nombre || !precio || !imagen) {
            console.error("[v0] Error: Datos del producto inválidos", { nombre, precio, imagen })
            alert("Error: Datos del producto inválidos")
            return
        }

        const existingItem = cart.find((item) => item.nombre === nombre)

        if (existingItem) {
            existingItem.cantidad += cantidad
            console.log("[v0] Updated existing item:", existingItem)
        } else {
            const newItem = {
                nombre: nombre,
                precio: Number(precio),
                imagen: imagen,
                cantidad: Number(cantidad),
            }
            cart.push(newItem)
            console.log("[v0] Nuevo producto añadido:", newItem)
        }

        localStorage.setItem("viveroCart", JSON.stringify(cart))
        console.log("[v0] Carrito guardado en almacenamiento local:", cart)
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

    // abrir y cerrar carrito lateral
    if (cartBtn && cartSidebar) {
        cartBtn.addEventListener("click", () => {
            cartSidebar.classList.add("open")
            cartOverlay.classList.add("show")
            document.body.style.overflow = "hidden"
        })

        cartClose.addEventListener("click", () => {
            cartSidebar.classList.remove("open")
            cartOverlay.classList.remove("show")
            document.body.style.overflow = "auto"
        })

        cartOverlay.addEventListener("click", () => {
            cartSidebar.classList.remove("open")
            cartOverlay.classList.remove("show")
            document.body.style.overflow = "auto"
        })

        // Use real checkout
        const checkoutBtn = document.querySelector(".btn-checkout")
        if (checkoutBtn) {
            checkoutBtn.addEventListener("click", () => {
                if (cart.length > 0) {
                    procesarCheckout()
                }
            })
        }
    }

    // Cargar productos dinámicamente
    const productos = await obtenerProductosDelServidor()
    renderizarProductosDinamicos(productos)
    
    // Cargar productos más vendidos
    await renderizarCarrouselProductosVendidos()
    
    // actualiza la interfaz al cargar
    updateCartUI()
})

function procesarCheckout() {
    // Placeholder function for processing checkout
    alert("Procesando pago...")
}
