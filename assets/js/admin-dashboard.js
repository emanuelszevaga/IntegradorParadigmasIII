/**
 * Script para el dashboard del panel administrativo
 */

document.addEventListener('DOMContentLoaded', async () => {
    console.log('[v0] Cargando dashboard...');
    
    await cargarEstadisticas();
});

/**
 * Cargar estadísticas del dashboard
 */
async function cargarEstadisticas() {
    try {
        // Obtener productos
        const productosRes = await apiRequest('/productos/obtener.php');
        if (productosRes.estado === 'exitoso') {
            document.getElementById('total-productos').textContent = productosRes.total || 0;

            // Mostrar top 5 productos más vendidos
            const topProductos = (productosRes.productos || [])
                .sort((a, b) => (b.cantidad_vendida || 0) - (a.cantidad_vendida || 0))
                .slice(0, 5);

            const listProductos = document.getElementById('productos-vendidos');
            listProductos.innerHTML = topProductos.map(p => `
                <div class="producto-item">
                    <strong>${p.nombre}</strong>
                    <span>${p.cantidad_vendida || 0} vendidos</span>
                </div>
            `).join('');
        }

    } catch (error) {
        console.error('[v0] Error al cargar estadísticas:', error);
    }
}
