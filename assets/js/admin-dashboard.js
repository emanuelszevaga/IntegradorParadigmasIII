/**
 * Script para el dashboard del panel administrativo
 */

document.addEventListener('DOMContentLoaded', async () => {
    await cargarEstadisticas();
    setInterval(cargarEstadisticas, 10000);
});

/**
 * Cargar estadísticas del dashboard
 */
async function cargarEstadisticas() {
    try {
        // Obtener productos
        const productosRes = await apiRequest('/productos/obtener.php?t=' + Date.now());
        if (productosRes.estado === 'exitoso') {
            const productos = productosRes.productos || [];
            document.getElementById('total-productos').textContent = productos.length;

            // Calcular total de ventas (cantidad_vendida)
            const totalVentas = productos.reduce((sum, p) => sum + (parseInt(p.cantidad_vendida) || 0), 0);
            document.getElementById('total-ventas').textContent = totalVentas;

            // Calcular ingresos (precio * cantidad_vendida)
            const totalIngresos = productos.reduce((sum, p) => {
                return sum + ((parseFloat(p.precio) || 0) * (parseInt(p.cantidad_vendida) || 0));
            }, 0);
            document.getElementById('total-ingresos').textContent = `$${totalIngresos.toLocaleString('es-AR')}`;

            // Guardar productos para usar en el modal
            window.productosData = productos;
        }

        // Obtener usuarios únicos desde la API
        const usuariosRes = await apiRequest('/usuarios/obtener.php').catch(() => ({ estado: 'error' }));
        if (usuariosRes.estado === 'exitoso') {
            const usuarios = usuariosRes.usuarios || [];
            document.getElementById('total-usuarios').textContent = usuarios.length;
            window.usuariosData = usuarios;
        } else {
            // Fallback: si no existe endpoint, mostrar 0
            document.getElementById('total-usuarios').textContent = '0';
        }

    } catch (error) {
        console.error('[v0] Error al cargar estadísticas:', error);
    }
}

/**
 * Abre modal de productos
 */
function abrirModalProductos() {
    const modal = document.getElementById('modal-productos');
    modal.style.display = 'flex';
    
    if (window.productosData) {
        mostrarProductosEnModal();
    }
}

/**
 * Abre modal de ventas
 */
function abrirModalVentas() {
    const modal = document.getElementById('modal-ventas');
    modal.style.display = 'flex';
    mostrarVentasEnModal();
}

/**
 * Abre modal de ingresos
 */
function abrirModalIngresos() {
    const modal = document.getElementById('modal-ingresos');
    modal.style.display = 'flex';
    mostrarIngresosEnModal();
}

/**
 * Abre modal de usuarios
 */
function abrirModalUsuarios() {
    const modal = document.getElementById('modal-usuarios');
    modal.style.display = 'flex';
    mostrarUsuariosEnModal();
}

/**
 * Cierra modal
 */
function cerrarModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

/**
 * Muestra productos en el modal
 */
function mostrarProductosEnModal() {
    const tbody = document.getElementById('tbody-productos');
    const productos = (window.productosData || []).sort((a, b) => 
        (parseInt(b.cantidad_vendida) || 0) - (parseInt(a.cantidad_vendida) || 0)
    );
    
    if (productos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay productos</td></tr>';
        return;
    }

    tbody.innerHTML = productos.map((p, idx) => `
        <tr ${idx < 5 ? 'style="background-color: #f0f9ff; font-weight: bold;"' : ''}>
            <td>${p.nombre}</td>
            <td>${p.categoria_nombre || 'Sin categoría'}</td>
            <td>$${parseFloat(p.precio).toLocaleString('es-AR')}</td>
            <td>${p.stock}</td>
            <td>${p.cantidad_vendida || 0}</td>
        </tr>
    `).join('');
}

/**
 * Muestra ventas en el modal
 */
function mostrarVentasEnModal() {
    const tbody = document.getElementById('tbody-ventas');
    const productos = window.productosData || [];

    // Simular ventas desde productos que tengan cantidad_vendida > 0
    const ventas = [];
    productos.forEach(p => {
        if (p.cantidad_vendida > 0) {
            ventas.push({
                id: p.id,
                nombre: p.nombre,
                cantidad: p.cantidad_vendida,
                precio_unitario: p.precio,
                total: parseFloat(p.precio) * parseInt(p.cantidad_vendida)
            });
        }
    });

    if (ventas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay ventas registradas</td></tr>';
        return;
    }

    tbody.innerHTML = ventas.map((v, idx) => `
        <tr>
            <td>#${idx + 1}</td>
            <td>${v.nombre}</td>
            <td>${v.cantidad}</td>
            <td>$${parseFloat(v.precio_unitario).toLocaleString('es-AR')}</td>
            <td>$${v.total.toLocaleString('es-AR')}</td>
            <td>${new Date().toLocaleDateString('es-AR')}</td>
        </tr>
    `).join('');
}

/**
 * Muestra ingresos en el modal
 */
function mostrarIngresosEnModal() {
    const tbody = document.getElementById('tbody-ingresos');
    const productos = window.productosData || [];

    // Agrupar ingresos por fecha (simulado: hoy)
    let totalIngresos = 0;
    let totalProductos = 0;

    productos.forEach(p => {
        if (p.cantidad_vendida > 0) {
            totalIngresos += parseFloat(p.precio) * parseInt(p.cantidad_vendida);
            totalProductos += parseInt(p.cantidad_vendida);
        }
    });

    if (totalProductos === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center">No hay ingresos registrados</td></tr>';
        return;
    }

    const hoy = new Date().toLocaleDateString('es-AR');
    tbody.innerHTML = `
        <tr>
            <td>${hoy}</td>
            <td>$${totalIngresos.toLocaleString('es-AR')}</td>
            <td>${totalProductos} productos</td>
        </tr>
    `;
}

/**
 * Muestra usuarios en el modal
 */
function mostrarUsuariosEnModal() {
    const tbody = document.getElementById('tbody-usuarios');
    const usuarios = window.usuariosData || [];

    if (usuarios.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay usuarios registrados</td></tr>';
        return;
    }

    tbody.innerHTML = usuarios.map(u => `
        <tr>
            <td>${u.id}</td>
            <td>${u.nombre}</td>
            <td>${u.email}</td>
            <td>${new Date(u.fecha_registro).toLocaleDateString('es-AR')}</td>
            <td>$${u.total_compras ? parseFloat(u.total_compras).toLocaleString('es-AR') : '$0'}</td>
        </tr>
    `).join('');
}

/**
 * Filtra productos en el modal
 */
function filtrarProductos() {
    const searchInput = document.getElementById('search-productos');
    const tbody = document.getElementById('tbody-productos');
    const productos = window.productosData || [];
    
    const termino = searchInput.value.toLowerCase();
    const productosFiltrados = productos.filter(p => 
        p.nombre.toLowerCase().includes(termino) ||
        (p.categoria_nombre || '').toLowerCase().includes(termino)
    );

    if (productosFiltrados.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No se encontraron productos</td></tr>';
        return;
    }

    tbody.innerHTML = productosFiltrados.map(p => `
        <tr>
            <td>${p.nombre}</td>
            <td>${p.categoria_nombre || 'Sin categoría'}</td>
            <td>$${parseFloat(p.precio).toLocaleString('es-AR')}</td>
            <td>${p.stock}</td>
            <td>${p.cantidad_vendida || 0}</td>
        </tr>
    `).join('');
}
