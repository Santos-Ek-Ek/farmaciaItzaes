    document.addEventListener('DOMContentLoaded', function() {
        // Configuración del modal
        const agregarProductoModal = new bootstrap.Modal(document.getElementById('agregarProductoModal'));
        
       document.getElementById('btnGuardarProducto').addEventListener('click', function() {
    const form = document.getElementById('formAgregarProducto');
    const formData = new FormData(form);
    
    fetch('{{ route("productos.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Mostrar mensaje de éxito
            alert(data.message);
            
            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('agregarProductoModal'));
            modal.hide();
            
            // Recargar la página o actualizar la tabla
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al guardar el producto');
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
                    document.getElementById('edit_precio').value = producto.precio;
                    document.getElementById('edit_dia_llegada').value = producto.dia_llegada;
                    document.getElementById('edit_fecha_caducidad').value = producto.fecha_caducidad;
                    document.getElementById('edit_categoria_id').value = producto.categoria_id;
                    document.getElementById('edit_unidad_medida').value = producto.unidad_medida;
                    document.getElementById('edit_descripcion').value = producto.descripcion;
                    
                    // Mostrar vista previa de la imagen si existe
                    const imagenPreview = document.getElementById('edit_imagen_preview');
                    if(producto.imagen) {
                        imagenPreview.src = "{{ asset('') }}" + producto.imagen;
                        imagenPreview.style.display = 'block';
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
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                fetch(`/productos/${productoId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
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


function aplicarFiltros() {
    const searchTerm = document.getElementById('buscarProducto').value.toLowerCase().trim();
    const categoriaId = document.getElementById('filtroCategoria').value;
    const rangoPrecio = document.getElementById('filtroPrecio').value;
    
    document.querySelectorAll('tbody tr').forEach(row => {
        // Datos del producto - con trim() para eliminar espacios extras
        const nombre = row.querySelector('.product-name').textContent.toLowerCase().trim();
        const descripcion = row.querySelector('.product-desc').textContent.toLowerCase().trim();
        const caducidad = row.querySelector('.prod_cad').textContent.toLowerCase().trim();
        const categoria = row.querySelector('td:nth-child(2)').textContent.trim().toLowerCase();
        const precio = parseFloat(row.querySelector('td:nth-child(5)').textContent.replace(/[^\d.]/g, ''));
        
        // Filtro de búsqueda mejorado
        const coincideBusqueda = !searchTerm || 
                               nombre.includes(searchTerm) || 
                               descripcion.includes(searchTerm) ||
                               categoria.includes(searchTerm) ||
                               caducidad.includes(searchTerm) ||
                               // Búsqueda por palabras individuales
                               searchTerm.split(' ').some(term => 
                                   term && (nombre.includes(term) || 
                                           descripcion.includes(term) ||
                                           caducidad.includes(term) ||
                                           categoria.includes(term))
                               );
        
        // Filtro por categoría
        const coincideCategoria = !categoriaId || 
                                (document.querySelector(`#filtroCategoria option[value="${categoriaId}"]`)?.textContent.trim().toLowerCase() === categoria.toLowerCase());
        
        // Filtro por precio
        let coincidePrecio = true;
        if (rangoPrecio) {
            const [min, max] = rangoPrecio.split('-').map(Number);
            coincidePrecio = precio >= min && (max === 0 || precio <= max);
        }
        
        // Mostrar u ocultar según los filtros
        row.style.display = (coincideBusqueda && coincideCategoria && coincidePrecio) ? '' : 'none';
    });
}

// Asignar eventos a todos los filtros
document.getElementById('buscarProducto').addEventListener('input', aplicarFiltros);
document.getElementById('filtroCategoria').addEventListener('change', aplicarFiltros);
document.getElementById('filtroPrecio').addEventListener('change', aplicarFiltros);