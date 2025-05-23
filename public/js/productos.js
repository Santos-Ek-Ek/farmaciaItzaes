    document.addEventListener('DOMContentLoaded', function() {
        // Configuración del modal
// Configuración del modal
const agregarProductoModal = new bootstrap.Modal(document.getElementById('agregarProductoModal'));

document.getElementById('btnGuardarProducto').addEventListener('click', function() {
    const form = document.getElementById('formAgregarProducto');
    const formData = new FormData(form);
    
    // Obtener el token CSRF del meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Usar la URL del atributo action del formulario
    const url = form.getAttribute('action');
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if(data.success) {
            // Mostrar mensaje de éxito con SweetAlert o similar
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: data.message,
                confirmButtonText: 'Aceptar'
            }).then(() => {
                // Cerrar el modal
                agregarProductoModal.hide();
                // Recargar la página
                location.reload();
            });
        } else {
            // Mostrar errores de validación
            if (data.errors) {
                let errors = '';
                for (const error in data.errors) {
                    errors += `${data.errors[error][0]}\n`;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: errors,
                    confirmButtonText: 'Aceptar'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    confirmButtonText: 'Aceptar'
                });
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al guardar el producto',
            confirmButtonText: 'Aceptar'
        });
    });
});
    });


document.addEventListener('DOMContentLoaded', function() {
    // Configurar los botones de edición
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', function() {
            const productoId = this.getAttribute('data-id');
            cargarDatosProducto(productoId);
        });
    });

    // Función para cargar los datos del producto
function cargarDatosProducto(id) {
    fetch(`/productos/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                const producto = data.producto;
                
                // Llenar el formulario con los datos del producto
                document.getElementById('edit_id').value = producto.id;
                document.getElementById('edit_nombre').value = producto.nombre;
                document.getElementById('edit_cantidad').value = producto.cantidad;
                document.getElementById('edit_cantidad_minima').value = producto.cantidad_minima;
                document.getElementById('edit_precio').value = producto.precio;
                document.getElementById('edit_dia_llegada').value = producto.dia_llegada;
                document.getElementById('edit_fecha_caducidad').value = producto.fecha_caducidad;
                document.getElementById('edit_categoria_id').value = producto.categoria_id;
                document.getElementById('edit_unidad_medida').value = producto.unidad_medida;
                document.getElementById('edit_descripcion').value = producto.descripcion;
                
                // Mostrar vista previa de la imagen si existe
                const imagenPreview = document.getElementById('edit_imagen_preview');
                if(producto.imagen) {
                    // Usar URL base del sitio
                    const baseUrl = window.location.origin;
                    imagenPreview.src = baseUrl + '/' + producto.imagen;
                    imagenPreview.style.display = 'block';
                    
                    // Opcional: manejar errores de carga de imagen
                    imagenPreview.onerror = function() {
                        this.src = 'https://placehold.co/100x100?text=Imagen+no+disponible';
                    };
                } else {
                    imagenPreview.style.display = 'none';
                }
                
                // Mostrar el modal
                const editarModal = new bootstrap.Modal(document.getElementById('editarProductoModal'));
                editarModal.show();
            } else {
                alert('Error al cargar los datos del producto');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al cargar el producto');
        });
}

    // Manejar la actualización del producto
    document.getElementById('btnActualizarProducto').addEventListener('click', function() {
        const form = document.getElementById('formEditarProducto');
        const formData = new FormData(form);
        const productoId = document.getElementById('edit_id').value;
        
        fetch(`/productos/${productoId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-HTTP-Method-Override': 'PUT'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                const modal = bootstrap.Modal.getInstance(document.getElementById('editarProductoModal'));
                modal.hide();
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al actualizar el producto');
        });
    });
});


    // Manejar la eliminación del producto
document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', function() {
        const productoId = this.getAttribute('data-id');
        
        Swal.fire({
            title: '¿Eliminar producto?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/productos-eliminar/${productoId}`, {
                    method: 'PUT', // Usar POST pero con _method=DELETE
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        _method: 'PUT'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire(
                            '¡Eliminado!',
                            data.message,
                            'success'
                        ).then(() => location.reload());
                    } else {
                        Swal.fire(
                            'Error',
                            data.message,
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error',
                        'Ocurrió un error al eliminar el producto',
                        'error'
                    );
                });
            }
        });
    });
});


// Variables de paginación
let paginaActual = 1;
let itemsPorPagina = 7;
let productosFiltrados = [];

// Función para aplicar paginación
function aplicarPaginacion() {
    const inicio = (paginaActual - 1) * itemsPorPagina;
    const fin = inicio + itemsPorPagina;
    const filasVisibles = Array.from(document.querySelectorAll('tbody tr[data-visible="true"]'));
    
    // Ocultar todas las filas primero
    document.querySelectorAll('tbody tr').forEach(row => {
        row.style.display = 'none';
    });
    
    // Mostrar solo las filas de la página actual
    filasVisibles.slice(inicio, fin).forEach(row => {
        row.style.display = '';
    });
    
    // Actualizar información de paginación
    const totalVisibles = filasVisibles.length;
    document.getElementById('desde').textContent = totalVisibles > 0 ? inicio + 1 : 0;
    document.getElementById('hasta').textContent = Math.min(fin, totalVisibles);
    document.getElementById('total').textContent = totalVisibles;
    
    // Generar controles de paginación
    generarControlesPaginacion(totalVisibles);
}

// Función para generar los controles de paginación
function generarControlesPaginacion(totalItems) {
    const totalPaginas = Math.ceil(totalItems / itemsPorPagina);
    const paginacion = document.getElementById('paginacion');
    paginacion.innerHTML = '';
    
    if (totalPaginas <= 1) return;
    
    // Botón Anterior
    const liAnterior = document.createElement('li');
    liAnterior.className = `page-item ${paginaActual === 1 ? 'disabled' : ''}`;
    liAnterior.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
    liAnterior.addEventListener('click', (e) => {
        e.preventDefault();
        if (paginaActual > 1) {
            paginaActual--;
            aplicarPaginacion();
        }
    });
    paginacion.appendChild(liAnterior);
    
    // Números de página
    const inicioPagina = Math.max(1, paginaActual - 2);
    const finPagina = Math.min(totalPaginas, paginaActual + 2);
    
    for (let i = inicioPagina; i <= finPagina; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === paginaActual ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        li.addEventListener('click', (e) => {
            e.preventDefault();
            paginaActual = i;
            aplicarPaginacion();
        });
        paginacion.appendChild(li);
    }
    
    // Botón Siguiente
    const liSiguiente = document.createElement('li');
    liSiguiente.className = `page-item ${paginaActual === totalPaginas ? 'disabled' : ''}`;
    liSiguiente.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
    liSiguiente.addEventListener('click', (e) => {
        e.preventDefault();
        if (paginaActual < totalPaginas) {
            paginaActual++;
            aplicarPaginacion();
        }
    });
    paginacion.appendChild(liSiguiente);
}

// Modificar la función aplicarFiltros para incluir paginación
function aplicarFiltros() {
    const searchTerm = document.getElementById('buscarProducto').value.toLowerCase().trim();
    const categoriaId = document.getElementById('filtroCategoria').value;
    const rangoPrecio = document.getElementById('filtroPrecio').value;
    
    document.querySelectorAll('tbody tr').forEach(row => {
        // Datos del producto
        const nombre = row.querySelector('.product-name').textContent.toLowerCase();
        const descripcion = row.querySelector('.product-desc').textContent.toLowerCase();
        const caducidad = row.querySelector('.prod_cad').textContent.toLowerCase();
        const categoria = row.querySelector('td:nth-child(2)').textContent.trim();
        const precio = parseFloat(row.querySelector('td:nth-child(5)').textContent.replace(/[^\d.]/g, ''));
        
        // Filtro de búsqueda
        const coincideBusqueda = !searchTerm || 
                               nombre.includes(searchTerm) || 
                               descripcion.includes(searchTerm) ||
                               caducidad.includes(searchTerm) ||
                               categoria.toLowerCase().includes(searchTerm) ||
                               searchTerm.split(' ').some(term => 
                                   term && (nombre.includes(term) || 
                                           descripcion.includes(term) ||
                                           caducidad.includes(term) ||
                                           categoria.includes(term))
                               );
        
        // Filtro por categoría
        const coincideCategoria = !categoriaId || 
                                (document.querySelector(`#filtroCategoria option[value="${categoriaId}"]`)?.textContent.trim() === categoria);
        
        // Filtro por precio
        let coincidePrecio = true;
        if (rangoPrecio) {
            const [min, max] = rangoPrecio.split('-').map(Number);
            coincidePrecio = precio >= min && (max === 0 || precio <= max);
        }
        
        // Marcar filas visibles
        const visible = coincideBusqueda && coincideCategoria && coincidePrecio;
        row.style.display = 'none'; // Ocultar todas inicialmente
        row.setAttribute('data-visible', visible);
    });
    
    // Resetear a primera página al aplicar nuevos filtros
    paginaActual = 1;
    aplicarPaginacion();
}

// Evento para cambiar items por página
document.getElementById('itemsPorPagina').addEventListener('change', function() {
    itemsPorPagina = parseInt(this.value);
    paginaActual = 1;
    aplicarPaginacion();
});

// Inicializar paginación al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Marcar todas las filas como visibles inicialmente
    document.querySelectorAll('tbody tr').forEach(row => {
        row.setAttribute('data-visible', 'true');
    });
    aplicarPaginacion();
});

// Función para filtrar productos por agotarse
document.getElementById('filtrarPorAgotarse')?.addEventListener('click', function(e) {
    e.preventDefault();
    filtrarProductosPorAgotarse();
    
    // Mostrar el botón de resetear
    document.getElementById('resetearFiltros').style.display = 'inline-block';
});

// Función para resetear todos los filtros
document.getElementById('resetearFiltros')?.addEventListener('click', function(e) {
    e.preventDefault();
    resetearFiltros();
    
    // Ocultar el botón de resetear después de usarlo
    this.style.display = 'none';
});

function filtrarProductosPorAgotarse() {
    const filas = document.querySelectorAll('tbody tr');
    let productosVisibles = 0;

    filas.forEach(fila => {
        const cantidad = parseInt(fila.querySelector('td:nth-child(3)').textContent);
        const cantidadMinima = parseInt(fila.querySelector('td:nth-child(4)').textContent);
        
        if (cantidad <= cantidadMinima) {
            fila.style.display = '';
            fila.setAttribute('data-visible', 'true');
            productosVisibles++;
        } else {
            fila.style.display = 'none';
            fila.setAttribute('data-visible', 'false');
        }
    });

    // Resetear a la primera página
    paginaActual = 1;
    aplicarPaginacion();
    
    if (productosVisibles === 0) {
        mostrarNotificacion('No hay productos por agotarse', 'warning');
    } else {
        mostrarNotificacion(`Mostrando ${productosVisibles} productos por agotarse`, 'success');
    }
    
    // Mostrar el botón de resetear
    document.getElementById('resetearFiltros').style.display = 'inline-block';
}

// Función para resetear todos los filtros
function resetearFiltros() {
    const filas = document.querySelectorAll('tbody tr');
    filas.forEach(fila => {
        fila.style.display = '';
        fila.setAttribute('data-visible', 'true');
    });
    
    document.getElementById('buscarProducto').value = '';
    document.getElementById('filtroCategoria').value = '';
    document.getElementById('filtroPrecio').value = '';
    
    // Resetear a la primera página
    paginaActual = 1;
    aplicarPaginacion();
    
    mostrarNotificacion('Todos los filtros han sido reseteados', 'success');
    
    // Ocultar el botón de resetear
    document.getElementById('resetearFiltros').style.display = 'none';
}



// Función para mostrar una página específica
function mostrarPagina(numeroPagina) {
    const itemsPorPagina = parseInt(document.getElementById('itemsPorPagina').value);
    const inicio = (numeroPagina - 1) * itemsPorPagina;
    const fin = inicio + itemsPorPagina;
    
    const productosVisibles = Array.from(document.querySelectorAll('tbody tr:not([style*="display: none"])'));
    
    productosVisibles.forEach((producto, index) => {
        producto.style.display = (index >= inicio && index < fin) ? '' : 'none';
    });
    
    // Actualizar información de paginación
    document.getElementById('desde').textContent = inicio + 1;
    document.getElementById('hasta').textContent = Math.min(fin, productosVisibles.length);
    
    // Resaltar página activa
    document.querySelectorAll('#paginacion .page-item').forEach((item, index) => {
        if (index === numeroPagina - 1) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'success') {
    // Eliminar notificaciones anteriores
    const alertasAnteriores = document.querySelectorAll('.alert-notificacion');
    alertasAnteriores.forEach(alerta => alerta.remove());
    
    const tipos = {
        success: 'alert-success',
        warning: 'alert-warning',
        danger: 'alert-danger'
    };
    
    const alerta = document.createElement('div');
    alerta.className = `alert ${tipos[tipo]} alert-dismissible fade show alert-notificacion`;
    alerta.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const contenedor = document.querySelector('.container-fluid');
    contenedor.insertBefore(alerta, contenedor.firstChild);
    
    setTimeout(() => {
        alerta.classList.add('fade');
        setTimeout(() => alerta.remove(), 150);
    }, 3000);
}

// Inicializar paginación al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    actualizarPaginacion();
    
    // Evento para cambiar items por página
    document.getElementById('itemsPorPagina').addEventListener('change', function() {
        actualizarPaginacion();
    });
});

// Asignar eventos a todos los filtros
document.getElementById('buscarProducto').addEventListener('input', aplicarFiltros);
document.getElementById('filtroCategoria').addEventListener('change', aplicarFiltros);
document.getElementById('filtroPrecio').addEventListener('change', aplicarFiltros);