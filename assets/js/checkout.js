/**
 * Script para manejar el proceso de checkout
 */

const API_BASE = '/integrador3.0/api'

document.addEventListener('DOMContentLoaded', () => {
    const checkoutBtn = document.querySelector('.btn-checkout')
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', procesarCheckout)
    }
    
    // Verificar sesión al iniciar
    verificarSesionUsuario()
})

/**
 * Verificar si el usuario tiene sesión activa
 */
async function verificarSesionUsuario() {
    try {
        const response = await fetch(API_BASE + '/auth/verificar-sesion.php')
        const data = await response.json()
        
        console.log("[v0] Estado de sesión:", data)
        
        if (data.estado === 'autenticado') {
            console.log("[v0] Usuario autenticado:", data.usuario.nombre)
            window.usuarioAutenticado = data.usuario
        } else {
            console.log("[v0] Usuario no autenticado")
            window.usuarioAutenticado = null
        }
    } catch (error) {
        console.error("[v0] Error al verificar sesión:", error)
    }
}

/**
 * Procesar el checkout
 */
async function procesarCheckout() {
    console.log("[v0] Iniciando proceso de checkout")
    
    // Verificar si el usuario está autenticado
    if (!window.usuarioAutenticado) {
        alert('Debes iniciar sesión para realizar la compra')
        // Abrir modal de login
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
    
    // La API espera: id, cantidad, precio
    // El localStorage tiene: nombre, precio, imagen, cantidad
    
    // Por ahora haremos una simulación convertir datos
    const carritoFormato = carrito.map(item => ({
        nombre: item.nombre,
        cantidad: item.cantidad,
        precio: item.precio
    }))
    
    console.log("[v0] Carrito a procesar:", carritoFormato)
    
    const confirmacion = confirm(
        `¿Confirmar compra de ${carrito.length} producto(s)?\n` +
        `Total: $${carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0).toLocaleString('es-AR')}`
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
                carrito: carritoFormato
            })
        })
        
        const resultado = await response.json()
        console.log("[v0] Respuesta del servidor:", resultado)
        
        if (resultado.estado === 'exitoso') {
            alert(`¡Pedido creado exitosamente!\nNúmero de pedido: ${resultado.pedido_id}\nTotal: $${resultado.total.toLocaleString('es-AR')}`)
            
            localStorage.removeItem('viveroCart')
            
            // Actualizar interfaz del carrito
            cart = []
            updateCartUI()
            
            // Cerrar modal de carrito
            const cartSidebar = document.getElementById('cart-sidebar')
            if (cartSidebar) {
                cartSidebar.classList.remove('open')
                document.getElementById('cart-overlay').classList.remove('show')
                document.body.style.overflow = 'auto'
            }
        } else {
            alert(`Error: ${resultado.mensaje}`)
        }
    } catch (error) {
        console.error("[v0] Error al procesar pedido:", error)
        alert('Error al procesar el pedido. Intenta nuevamente.')
    }
}
