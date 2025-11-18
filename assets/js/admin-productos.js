/* Script para la gestión de productos en el panel administrativo */

let productosActuales = [];
let productoEnEdicion = null;

document.addEventListener('DOMContentLoaded', async () => {
    console.log('[v0] Inicializando panel de productos');

    // Cargar categorías
    await cargarCategorias();

    // Cargar productos
    await cargarProductos();

    // Event listeners
    document.getElementById('btn-nuevo-producto').addEventListener('click', abrirNuevoProducto);
    document.getElementById('producto-form').addEventListener('submit', guardarProducto);
    document.getElementById('search-input').addEventListener('input', filtrarProductos);
    document.getElementById('filter-categoria').addEventListener('change', filtrarProductos);
    document.querySelector('.modal-close').addEventListener('click', cerrarModal);
    document.getElementById('form-imagen').addEventListener('change', previewImagen);

    // Cerrar modal al hacer clic fuera
    document.getElementById('producto-modal').addEventListener('click', (e) => {
        if (e.target.id === 'producto-modal') {
            cerrarModal();
        }
    });
});

/* Cargar todas las categorías en el select */
async function cargarCategorias() {
    const resultado = await apiRequest('/categorias/obtener.php');

    if (resultado.estado === 'exitoso' && resultado.categorias) {
        const selectCategoria = document.getElementById('form-categoria');
        const filterCategoria = document.getElementById('filter-categoria');

        resultado.categorias.forEach(cat => {
            // Agregar a select de formulario
            const option1 = document.createElement('option');
            option1.value = cat.id;
            option1.textContent = cat.nombre;
            selectCategoria.appendChild(option1);

            // Agregar a filtro
            const option2 = document.createElement('option');
            option2.value = cat.nombre;
            option2.textContent = cat.nombre;
            filterCategoria.appendChild(option2);
        });
    }
}

/* Cargar todos los productos */
async function cargarProductos() {
    console.log('[v0] Cargando productos...');
    const resultado = await apiRequest('/productos/obtener.php');

    if (resultado.estado === 'exitoso') {
        productosActuales = resultado.productos || [];
        console.log('[v0] Productos cargados:', productosActuales.length);
        renderizarProductos(productosActuales);
    } else {
        mostrarNotificacion('Error al cargar productos', 'error');
    }
}

/* Renderizar tabla de productos */
function renderizarProductos(productos) {
    const tbody = document.getElementById('productos-tbody');

    if (productos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay productos</td></tr>';
        return;
    }

    tbody.innerHTML = productos.map(producto => `
        <tr>
            <td>
                ${producto.imagen ? `<img src="${producto.imagen}" alt="${producto.nombre}" class="thumb">` : 'Sin imagen'}
            </td>
            <td>${producto.nombre}</td>
            <td>${producto.categoria_nombre || 'Sin categoría'}</td>
            <td>$${parseFloat(producto.precio).toLocaleString('es-AR')}</td>
            <td>
                <span class="badge ${producto.stock > 0 ? 'badge-success' : 'badge-danger'}">
                    ${producto.stock}
                </span>
            </td>
            <td>${producto.cantidad_vendida || 0}</td>
            <td>
                <button class="btn-icon" onclick="editarProducto(${producto.id})" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-icon btn-danger" onclick="eliminarProducto(${producto.id})" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

/* Filtrar productos por búsqueda y categoría */
function filtrarProductos() {
    const busqueda = document.getElementById('search-input').value.toLowerCase();
    const categoria = document.getElementById('filter-categoria').value;

    const filtrados = productosActuales.filter(p => {
        const cumpleBusqueda = p.nombre.toLowerCase().includes(busqueda) ||
            (p.descripcion && p.descripcion.toLowerCase().includes(busqueda));
        const cumpleCategoria = !categoria || p.categoria_nombre === categoria;
        return cumpleBusqueda && cumpleCategoria;
    });

    renderizarProductos(filtrados);
}

/* Abrir modal para nuevo producto */
function abrirNuevoProducto() {
    console.log('[v0] Abriendo modal nuevo producto');
    productoEnEdicion = null;
    document.getElementById('modal-title').textContent = 'Nuevo Producto';
    document.getElementById('producto-form').reset();
    document.getElementById('image-preview').innerHTML = '';
    abrirModal();
}

/* Editar producto existente */
async function editarProducto(id) {
    console.log('[v0] Editando producto:', id);

    const resultado = await apiRequest(`/productos/obtener.php?id=${id}`);

    if (resultado.estado === 'exitoso' && resultado.producto) {
        const p = resultado.producto;
        productoEnEdicion = p.id;

        document.getElementById('modal-title').textContent = 'Editar Producto';
        document.getElementById('form-nombre').value = p.nombre;
        document.getElementById('form-descripcion').value = p.descripcion;
        document.getElementById('form-precio').value = p.precio;
        document.getElementById('form-stock').value = p.stock;
        document.getElementById('form-categoria').value = p.categoria_id;

        if (p.imagen) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = `<img src="${p.imagen}" alt="${p.nombre}">`;
        }

        abrirModal();
    }
}

/* Preview de imagen */
function previewImagen(e) {
    const archivo = e.target.files[0];
    if (!archivo) return;

    const reader = new FileReader();
    reader.onload = (event) => {
        document.getElementById('image-preview').innerHTML =
            `<img src="${event.target.result}" alt="Preview">`;
    };
    reader.readAsDataURL(archivo);
}

/* Guardar producto (crear o actualizar) */
async function guardarProducto(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('nombre', document.getElementById('form-nombre').value);
    formData.append('descripcion', document.getElementById('form-descripcion').value);
    formData.append('precio', document.getElementById('form-precio').value);
    formData.append('stock', document.getElementById('form-stock').value);
    formData.append('categoria_id', document.getElementById('form-categoria').value);

    if (document.getElementById('form-imagen').files.length > 0) {
        formData.append('imagen', document.getElementById('form-imagen').files[0]);
    }

    const endpoint = productoEnEdicion
        ? '/productos/actualizar.php'
        : '/productos/crear.php';

    if (productoEnEdicion) {
        formData.append('id', productoEnEdicion);
    }

    console.log('[v0] Guardando producto:', productoEnEdicion ? 'actualizar' : 'crear');

    try {
        const response = await fetch(API_BASE + endpoint, {
            method: 'POST',
            body: formData
        });

        const resultado = await response.json();

        if (resultado.estado === 'exitoso') {
            mostrarNotificacion(resultado.mensaje, 'success');
            cerrarModal();
            await cargarProductos();
        } else {
            mostrarNotificacion(resultado.mensaje || 'Error al guardar', 'error');
        }
    } catch (error) {
        console.error('[v0] Error:', error);
        mostrarNotificacion('Error al guardar producto', 'error');
    }
}

/* Eliminar producto */
async function eliminarProducto(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este producto?')) return;

    const resultado = await apiRequest('/productos/eliminar.php', 'POST', { id });

    if (resultado.estado === 'exitoso') {
        mostrarNotificacion(resultado.mensaje, 'success');
        await cargarProductos();
    } else {
        mostrarNotificacion(resultado.mensaje || 'Error al eliminar', 'error');
    }
}
