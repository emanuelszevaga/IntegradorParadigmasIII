/* Configuración global para el panel de administración */

const API_BASE = '/integrador3.0/api';

/* Realiza peticiones AJAX a la API */
async function apiRequest(endpoint, method = 'GET', data = null) {
    const url = API_BASE + endpoint;
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };

    if (data && (method === 'POST' || method === 'PUT')) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(url, options);
        const resultado = await response.json();

        if (!response.ok && resultado.estado === 'error') {
            console.error('[v0] Error API:', resultado.mensaje);
        }

        return { status: response.status, ...resultado };
    } catch (error) {
        console.error('[v0] Error en petición:', error);
        return { estado: 'error', mensaje: error.message };
    }
}

/* Muestra notificación al usuario */
function mostrarNotificacion(mensaje, tipo = 'success') {
    const notif = document.createElement('div');
    notif.className = `notificacion notificacion-${tipo}`;
    notif.textContent = mensaje;
    document.body.appendChild(notif);

    setTimeout(() => notif.remove(), 3000);
}

/* Cierra modal */
function cerrarModal() {
    const modal = document.getElementById('producto-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

/* Abre modal */
function abrirModal() {
    const modal = document.getElementById('producto-modal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

// Configurar logout
document.addEventListener('DOMContentLoaded', () => {
    const btnLogout = document.getElementById('btn-logout');
    if (btnLogout) {
        btnLogout.addEventListener('click', async () => {
            await fetch(API_BASE + '/auth/logout.php');
            window.location.href = '/integrador3.0/';
        });
    }
});
