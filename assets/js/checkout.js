/* Script para manejar el proceso de checkout */

const API_BASE = '/integrador3.0/api'

document.addEventListener('DOMContentLoaded', () => {
    const checkoutBtn = document.querySelector('.btn-checkout')
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', procesarCheckout)
    }
})

/* Procesar el checkout */
async function procesarCheckout() {
    console.log("[v0] Iniciando proceso de checkout")
    
    // Verificar si el usuario está autenticado
    if (!window.usuarioAutenticado) {
        alert('Debes iniciar sesión para realizar la compra')
        const loginBtn = document.querySelector('#login-btn')
        if (loginBtn) loginBtn.click()
        return
    }
    
    // Obtener carrito del localStorage
    const carrito = JSON.parse(localStorage.getItem('viveroCart') || '[]')
    
    if (carrito.length === 0) {
        alert('Tu carrito está vacío')
        return
    }
    
    const carritoFormato = await Promise.all(carrito.map(async (item) => {
        try {
            const response = await fetch(`${API_BASE}/productos/obtener.php`)
            const data = await response.json()
            
            if (data.estado === 'exitoso') {
                const producto = data.productos.find(p => p.nombre === item.nombre)
                if (producto) {
                    return {
                        id: producto.id,
                        cantidad: item.cantidad,
                        precio: item.precio
                    }
                }
            }
        } catch (e) {
            console.error("[v0] Error fetching product ID:", e)
        }
        
        return null
    }))
    
    const carritoValido = carritoFormato.filter(item => item !== null)
    
    if (carritoValido.length === 0) {
        alert('Error: No se pudieron obtener los IDs de los productos')
        return
    }
    
    console.log("[v0] Carrito a procesar:", carritoValido)
    
    const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0)
    const totalPrecio = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0)
    
    const confirmacion = confirm(
        `¿Confirmar compra de ${totalItems} producto(s)?\n` +
        `Total: $${totalPrecio.toLocaleString('es-AR')}`
    )
    
    if (!confirmacion) return
    
    try {
        console.log("[v0] Enviando pedido a servidor...")
        
        const response = await fetch(API_BASE + '/pedidos/crear.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                carrito: carritoValido
            })
        })
        
        const resultado = await response.json()
        console.log("[v0] Respuesta del servidor:", resultado)
        
        if (resultado.estado === 'exitoso') {
            alert(`¡Pedido creado exitosamente!\nNúmero de pedido: ${resultado.pedido_id}\nTotal: $${resultado.total.toLocaleString('es-AR')}`)
            
            localStorage.removeItem('viveroCart')
            
            // Cerrar modal de carrito
            const cartSidebar = document.getElementById('cart-sidebar')
            if (cartSidebar) {
                cartSidebar.classList.remove('open')
                document.getElementById('cart-overlay').classList.remove('show')
                document.body.style.overflow = 'auto'
            }
            
            location.reload()
        } else {
            alert(`Error: ${resultado.mensaje}`)
        }
    } catch (error) {
        console.error("[v0] Error al procesar pedido:", error)
        alert('Error al procesar el pedido. Intenta nuevamente.')
    }
}
