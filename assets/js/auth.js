/**
 * Script para manejar autenticación (login y registro)
 */

const API_BASE = '/integrador3.0/api'

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form')
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin)
    }
})

/**
 * Manejar login
 */
async function handleLogin(e) {
    e.preventDefault()
    
    const email = document.getElementById('email').value
    const password = document.getElementById('password').value
    
    console.log("[v0] Intentando login con:", email)
    
    try {
        const response = await fetch(API_BASE + '/auth/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        })
        
        const resultado = await response.json()
        console.log("[v0] Respuesta login:", resultado)
        
        if (resultado.estado === 'exitoso') {
            alert(`¡Bienvenido ${resultado.usuario.nombre}!`)
            
            // Guardar datos del usuario
            window.usuarioAutenticado = resultado.usuario
            localStorage.setItem('usuarioAutenticado', JSON.stringify(resultado.usuario))
            
            // Cerrar modal
            const loginModal = document.getElementById('login-modal')
            if (loginModal) {
                loginModal.style.display = 'none'
                document.body.style.overflow = 'auto'
            }
            
            // Limpiar formulario
            document.getElementById('login-form').reset()
            
            // Actualizar interfaz si es necesario
            actualizarUIPostLogin()
        } else {
            alert(`Error: ${resultado.mensaje}`)
        }
    } catch (error) {
        console.error("[v0] Error en login:", error)
        alert('Error al iniciar sesión')
    }
}

/**
 * Actualizar interfaz después del login
 */
function actualizarUIPostLogin() {
    const loginBtn = document.getElementById('login-btn')
    if (loginBtn && window.usuarioAutenticado) {
        loginBtn.innerHTML = `<i class="fa-solid fa-user-check"></i>`
        loginBtn.title = `Conectado como ${window.usuarioAutenticado.nombre}`
    }
}

// Cargar estado de usuario al iniciar
window.addEventListener('load', () => {
    const usuarioGuardado = localStorage.getItem('usuarioAutenticado')
    if (usuarioGuardado) {
        window.usuarioAutenticado = JSON.parse(usuarioGuardado)
        actualizarUIPostLogin()
    }
})
