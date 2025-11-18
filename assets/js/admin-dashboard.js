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

            // Guardar productos para usar en el modal
            window.productosData = productos;
        }

        // === LÓGICA DE INGRESOS (CORREGIDA) ===
        const ingresosRes = await apiRequest('/ventas/ingresos-totales.php').catch(() => ({ estado: 'error', total_ingresos: 0 }));
        if (ingresosRes.estado === 'exitoso') {
            const totalIngresos = parseFloat(ingresosRes.total_ingresos) || 0;
            document.getElementById('total-ingresos').textContent = `$${totalIngresos.toLocaleString('es-AR')}`;
            window.totalIngresosGlobal = totalIngresos;
        } else {
            document.getElementById('total-ingresos').textContent = '$0';
        }
        // ===========================================

        // === LÓGICA PARA VENTAS REALES (CORREGIDA) ===
        const ventasRes = await apiRequest('/ventas/obtener.php').catch(() => ({ estado: 'error' }));
        if (ventasRes.estado === 'exitoso') {
            window.ventasData = ventasRes.ventas || [];
        } else {
            window.ventasData = [];
        }
        // =================================================

        // Obtener usuarios únicos desde la API
        const usuariosRes = await apiRequest('/usuario/obtener.php').catch(() => ({ estado: 'error' }));
        if (usuariosRes.estado === 'exitoso') {
            const usuarios = usuariosRes.usuarios || [];
            document.getElementById('total-usuarios').textContent = usuarios.length;
            window.usuariosData = usuarios;
        } else {
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
 * Muestra ventas en el modal (USANDO DATOS REALES DE VENTAS)
 */
function mostrarVentasEnModal() {
    const tbody = document.getElementById('tbody-ventas');
    const ventas = window.ventasData || []; // Usa la data real de ventas

    if (ventas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay ventas registradas</td></tr>';
        return;
    }

    tbody.innerHTML = ventas.map((v) => {
        const fecha = new Date(v.fecha_venta).toLocaleDateString('es-AR') + ' ' + new Date(v.fecha_venta).toLocaleTimeString('es-AR');
        
        return `
            <tr>
                <td>#${v.id}</td>
                <td>${v.producto_nombre || 'Producto eliminado'}</td>
                <td>${v.cantidad}</td>
                <td>$${parseFloat(v.precio_unitario).toLocaleString('es-AR')}</td>
                <td>$${parseFloat(v.subtotal).toLocaleString('es-AR')}</td>
                <td>${fecha}</td>
            </tr>
        `;
    }).join('');
}

/**
 * Muestra ingresos en el modal (USANDO DATOS REALES DE VENTAS AGRUPADOS POR DÍA)
 */
function mostrarIngresosEnModal() {
    const tbody = document.getElementById('tbody-ingresos');
    const ventas = window.ventasData || [];

    if (ventas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center">No hay ingresos registrados</td></tr>';
        return;
    }

    // Agrupar ingresos por fecha de venta
    const ingresosPorDia = ventas.reduce((acc, v) => {
        // Formatear la fecha a YYYY-MM-DD para agrupar
        const fechaVenta = v.fecha_venta.split(' ')[0]; 
        const fechaDisplay = new Date(fechaVenta + 'T00:00:00').toLocaleDateString('es-AR'); // Usa T00 para evitar problemas de zona horaria
        const subtotal = parseFloat(v.subtotal);
        const cantidad = parseInt(v.cantidad);
        
        if (!acc[fechaDisplay]) {
            acc[fechaDisplay] = { monto: 0, cantidad: 0, fecha: fechaVenta };
        }
        acc[fechaDisplay].monto += subtotal;
        acc[fechaDisplay].cantidad += cantidad;
        return acc;
    }, {});

    // Ordenar por fecha (más reciente primero)
    const filasIngresos = Object.values(ingresosPorDia)
        .sort((a, b) => new Date(b.fecha) - new Date(a.fecha))
        .map(data => {
            const fechaDisplay = new Date(data.fecha + 'T00:00:00').toLocaleDateString('es-AR');
            return `
                <tr>
                    <td>${fechaDisplay}</td>
                    <td>$${data.monto.toLocaleString('es-AR')}</td>
                    <td>${data.cantidad} productos</td>
                </tr>
            `;
        }).join('');
    
    tbody.innerHTML = filasIngresos;
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

    tbody.innerHTML = usuarios.map(u => {
        const totalComprado = parseFloat(u.monto_total_comprado) || 0; 
        
        return `
            <tr>
                <td>${u.id}</td>
                <td>${u.nombre}</td>
                <td>${u.email}</td>
                <td>${new Date(u.fecha_registro).toLocaleDateString('es-AR')}</td>
                <td>$${totalComprado.toLocaleString('es-AR')}</td>
            </tr>
        `;
    }).join('');
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