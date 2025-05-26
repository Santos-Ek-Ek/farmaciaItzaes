document.addEventListener('DOMContentLoaded', async function() {
    const buscarProducto = document.getElementById('buscarProducto');
    const resultadosBusqueda = document.getElementById('resultadosBusqueda');
    let todosProductos = []; // Almacenará todos los productos
    let productosEnTabla = []; // Almacenará los productos en la tabla
    let paginaActual = 1;
    let itemsPorPagina = parseInt(document.getElementById('itemsPorPagina').value);

    // Función para cargar todos los productos 
    async function cargarProductos() {
        try {
            const response = await fetch('/obtener-productos');
            const data = await response.json();
            todosProductos = data;
        } catch (error) {
            console.error('Error al cargar productos:', error);
        }
    }

    // Función para normalizar texto (búsqueda sin acentos)
    function normalizarTexto(texto) {
        return texto.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
    }

    // Función de búsqueda
    function buscarProductos(termino) {
        if (termino.length < 2) {
            resultadosBusqueda.style.display = 'none';
            return;
        }
        
        const terminoNormalizado = normalizarTexto(termino);
        
        const resultados = todosProductos.filter(producto => 
            normalizarTexto(producto.nombre).includes(terminoNormalizado) || 
            (producto.descripcion && normalizarTexto(producto.descripcion).includes(terminoNormalizado))
        );
        
        mostrarResultados(resultados);
    }

    // Mostrar resultados de búsqueda
    function mostrarResultados(resultados) {
        resultadosBusqueda.innerHTML = '';
        
        if (resultados.length === 0) {
            resultadosBusqueda.innerHTML = '<div class="dropdown-item">No se encontraron productos</div>';
            resultadosBusqueda.style.display = 'block';
            return;
        }
        
        resultados.slice(0, 10).forEach(producto => {
            const item = document.createElement('a');
            item.className = 'dropdown-item d-flex align-items-center gap-2';
            item.href = '#';
            item.innerHTML = `
                <img src="${producto.imagen || 'https://placehold.co/48x48?text=Imagen'}" width="24" height="24" class="rounded">
                <div>
                    <div>${producto.nombre} - ${producto.unidad_medida} (Disponibles: ${producto.cantidad})</div>
                    <small class="text-muted">${producto.descripcion || ''} - ${producto.categoria}</small>
                </div>
                <span class="ms-auto"><b>$${producto.precio}</b></span>
            `;
            
            item.addEventListener('click', (e) => {
                e.preventDefault();
                
                resultadosBusqueda.style.display = 'none';
                agregarProductoATabla(producto);
            });
            
            resultadosBusqueda.appendChild(item);
        });
        
        resultadosBusqueda.style.display = 'block';
    }

    // Guardar productos en localStorage
    function guardarProductosEnStorage() {
        const productosParaGuardar = productosEnTabla.map(producto => ({
            id: producto.id,
            cantidad: producto.cantidad,
            nombre: producto.nombre,
            precio: producto.precio,
            unidad_medida: producto.unidad_medida,
            categoria: producto.categoria,
            imagen: producto.imagen,
            descripcion: producto.descripcion
        }));
        localStorage.setItem('productosVenta', JSON.stringify(productosParaGuardar));
    }

    // Cargar productos desde localStorage
    function cargarProductosDesdeStorage() {
        const productosGuardados = localStorage.getItem('productosVenta');
        return productosGuardados ? JSON.parse(productosGuardados) : [];
    }

    // Reconstruir tabla desde localStorage
    async function reconstruirTablaDesdeStorage() {
        const productosGuardados = cargarProductosDesdeStorage();
        if (productosGuardados.length === 0) return;

        if (todosProductos.length === 0) {
            await cargarProductos();
        }

        productosGuardados.forEach(productoGuardado => {
            const productoCompleto = todosProductos.find(p => p.id == productoGuardado.id) || productoGuardado;
            agregarProductoATabla(productoCompleto, productoGuardado.cantidad);
        });
    }

    // Agregar producto a la tabla (con cantidad opcional)
    function agregarProductoATabla(producto, cantidad = 1) {
        const filaExistente = document.querySelector(`tr[data-id="${producto.id}"]`);
        
        if (filaExistente) {
            const inputCantidad = filaExistente.querySelector('.product-cantidad');
            const nuevaCantidad = parseInt(inputCantidad.value) + (cantidad || 1);
            const maxCantidad = parseInt(inputCantidad.max);
            
            inputCantidad.value = Math.min(nuevaCantidad, maxCantidad);
            actualizarSubtotal(filaExistente);
            
            const index = productosEnTabla.findIndex(p => p.id == producto.id);
            if (index !== -1) {
                productosEnTabla[index].cantidad = inputCantidad.value;
                guardarProductosEnStorage();
            }
            return;
        }

        const tbody = document.getElementById('tablaProductos');
        const nuevaFila = document.createElement('tr');
        nuevaFila.setAttribute('data-id', producto.id);
        
        nuevaFila.innerHTML = `
            <td class="align-middle" style="width: 25%;">
                <div class="d-flex align-items-center gap-3">
                    <img src="${producto.imagen || 'https://placehold.co/48x48?text=Imagen'}" 
                         alt="${producto.nombre}" 
                         class="rounded product-image" 
                         width="48" height="48" 
                         onerror="this.src='https://placehold.co/48x48?text=Imagen'"/>
                    <div>
                        <div class="product-name">${producto.nombre}</div>
                        <div class="product-desc text-muted small">${producto.descripcion || ''}</div>
                    </div>
                </div>
            </td>
            <td class="text-center align-middle product-unidad" style="width: 15%;">
                ${producto.unidad_medida}
            </td>
            <td class="text-center align-middle product-categoria" style="width: 10%;">
                ${producto.categoria}
            </td>
            <td class="text-center align-middle" style="width: 10%;">
                <input type="number" class="form-control form-control-sm product-cantidad" 
                       value="${cantidad || 1}" min="1" max="${producto.cantidad}" 
                       data-precio="${producto.precio}">
            </td>
            <td class="text-center align-middle product-subtotal" style="width: 10%;">
                $${(producto.precio * (cantidad || 1)).toFixed(2)}
            </td>
            <td class="text-center align-middle" style="width: 15%;">
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;

        const inputCantidad = nuevaFila.querySelector('.product-cantidad');
        inputCantidad.addEventListener('change', function() {
            actualizarSubtotal(nuevaFila);
        });

        const btnEliminar = nuevaFila.querySelector('.btn-eliminar');
        btnEliminar.addEventListener('click', function() {
            productosEnTabla = productosEnTabla.filter(p => p.id != producto.id);
            guardarProductosEnStorage();
            nuevaFila.remove();
            actualizarTotales();
            actualizarPaginacion();
        });

        tbody.appendChild(nuevaFila);
        
        productosEnTabla.push({
            id: producto.id,
            cantidad: cantidad || 1,
            nombre: producto.nombre,
            precio: producto.precio,
            unidad_medida: producto.unidad_medida,
            categoria: producto.categoria,
            imagen: producto.imagen,
            descripcion: producto.descripcion
        });
        
        guardarProductosEnStorage();
        actualizarPaginacion();
        actualizarTotales();
    }

    // Actualizar subtotal
    function actualizarSubtotal(fila) {
        const cantidad = fila.querySelector('.product-cantidad').value;
        const precio = fila.querySelector('.product-cantidad').dataset.precio;
        const productId = fila.getAttribute('data-id');
        const subtotal = cantidad * precio;
        
        fila.querySelector('.product-subtotal').textContent = `$${subtotal.toFixed(2)}`;
        
        const index = productosEnTabla.findIndex(p => p.id == productId);
        if (index !== -1) {
            productosEnTabla[index].cantidad = cantidad;
            guardarProductosEnStorage();
        }
        
        actualizarTotales();
    }

    // Actualizar totales
    function actualizarTotales() {
        
        console.log('Actualizando totales...');
            let totalGeneral = 0;
    
    // Calcular sumando todos los subtotales
    document.querySelectorAll('.product-subtotal').forEach(subtotalElement => {
        const subtotalTexto = subtotalElement.textContent.replace('$', '').replace(',', '');
        const subtotal = parseFloat(subtotalTexto) || 0;
        totalGeneral += subtotal;
    });
    
    // Actualizar el elemento 
    document.getElementById('totalGeneral').textContent = `$${totalGeneral.toFixed(2)}`;
    
    
    return totalGeneral;
    }

        // Función para paginación
    function actualizarPaginacion() {
        const filas = Array.from(document.querySelectorAll('#tablaProductos tr'));
        const totalItems = filas.length;
        
        // Mostrar todas las filas primero para calcular correctamente
        filas.forEach(fila => fila.style.display = '');
        
        if (totalItems === 0) {
            document.getElementById('desde').textContent = '0';
            document.getElementById('hasta').textContent = '0';
            document.getElementById('total').textContent = '0';
            document.getElementById('paginacion').innerHTML = '';
            return;
        }
        
        const totalPaginas = Math.ceil(totalItems / itemsPorPagina);
        
        // Ajustar página actual si es necesario
        if (paginaActual > totalPaginas) {
            paginaActual = Math.max(1, totalPaginas);
        }
        
        // Calcular rango de items a mostrar
        const inicio = (paginaActual - 1) * itemsPorPagina;
        const fin = inicio + itemsPorPagina;
        
        // Ocultar/mostrar filas según la página actual
        filas.forEach((fila, index) => {
            fila.style.display = (index >= inicio && index < fin) ? '' : 'none';
        });
        
        // Actualizar información de paginación
        document.getElementById('desde').textContent = inicio + 1;
        document.getElementById('hasta').textContent = Math.min(fin, totalItems);
        document.getElementById('total').textContent = totalItems;
        
        // Generar controles de paginación
        generarControlesPaginacion(totalPaginas);
    }

    // Función para generar controles de paginación
    function generarControlesPaginacion(totalPaginas) {
        const paginacion = document.getElementById('paginacion');
        paginacion.innerHTML = '';
        
        if (totalPaginas <= 1) {
            paginacion.style.display = 'none';
            return;
        }
        
        paginacion.style.display = 'flex'; 
        
        // Botón Anterior 
        const liAnterior = document.createElement('li');
        liAnterior.className = `page-item ${paginaActual === 1 ? 'disabled' : ''}`;
        liAnterior.innerHTML = `<a class="page-link" href="#" aria-label="Anterior">&laquo;</a>`;
        liAnterior.addEventListener('click', (e) => {
            e.preventDefault();
            if (paginaActual > 1) {
                paginaActual--;
                actualizarPaginacion();
            }
        });
        paginacion.appendChild(liAnterior);
        
        // Números de página 
        const maxPaginasVisibles = 5; // Máximo de botones de página a mostrar
        let inicioPaginas = Math.max(1, paginaActual - Math.floor(maxPaginasVisibles / 2));
        let finPaginas = Math.min(totalPaginas, inicioPaginas + maxPaginasVisibles - 1);
        
        // Ajustar si estamos cerca del final
        if (finPaginas - inicioPaginas + 1 < maxPaginasVisibles) {
            inicioPaginas = Math.max(1, finPaginas - maxPaginasVisibles + 1);
        }
        
        // Mostrar primera página si no está visible
        if (inicioPaginas > 1) {
            const liPrimera = document.createElement('li');
            liPrimera.className = 'page-item';
            liPrimera.innerHTML = `<a class="page-link" href="#">1</a>`;
            liPrimera.addEventListener('click', (e) => {
                e.preventDefault();
                paginaActual = 1;
                actualizarPaginacion();
            });
            paginacion.appendChild(liPrimera);
            
            if (inicioPaginas > 2) {
                const liSeparador = document.createElement('li');
                liSeparador.className = 'page-item disabled';
                liSeparador.innerHTML = `<span class="page-link">...</span>`;
                paginacion.appendChild(liSeparador);
            }
        }
        
        // Botones de páginas numeradas
        for (let i = inicioPaginas; i <= finPaginas; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === paginaActual ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.addEventListener('click', (e) => {
                e.preventDefault();
                paginaActual = i;
                actualizarPaginacion();
            });
            paginacion.appendChild(li);
        }
        
        // Mostrar última página si no está visible
        if (finPaginas < totalPaginas) {
            if (finPaginas < totalPaginas - 1) {
                const liSeparador = document.createElement('li');
                liSeparador.className = 'page-item disabled';
                liSeparador.innerHTML = `<span class="page-link">...</span>`;
                paginacion.appendChild(liSeparador);
            }
            
            const liUltima = document.createElement('li');
            liUltima.className = 'page-item';
            liUltima.innerHTML = `<a class="page-link" href="#">${totalPaginas}</a>`;
            liUltima.addEventListener('click', (e) => {
                e.preventDefault();
                paginaActual = totalPaginas;
                actualizarPaginacion();
            });
            paginacion.appendChild(liUltima);
        }
        
        // Botón Siguiente 
        const liSiguiente = document.createElement('li');
        liSiguiente.className = `page-item ${paginaActual === totalPaginas ? 'disabled' : ''}`;
        liSiguiente.innerHTML = `<a class="page-link" href="#" aria-label="Siguiente">&raquo;</a>`;
        liSiguiente.addEventListener('click', (e) => {
            e.preventDefault();
            if (paginaActual < totalPaginas) {
                paginaActual++;
                actualizarPaginacion();
            }
        });
        paginacion.appendChild(liSiguiente);
    }

 
    document.getElementById('itemsPorPagina').addEventListener('change', function() {
        itemsPorPagina = parseInt(this.value);
        paginaActual = 1; // Resetear siempre a la primera página
        actualizarPaginacion();
    });
    

    // Eventos
    buscarProducto.addEventListener('input', (e) => {
        buscarProductos(e.target.value);
    });
    
    buscarProducto.addEventListener('focus', () => {
        if (resultadosBusqueda.innerHTML !== '') {
            resultadosBusqueda.style.display = 'block';
        }
    });
    
    document.addEventListener('click', (e) => {
        if (!buscarProducto.contains(e.target) && !resultadosBusqueda.contains(e.target)) {
            resultadosBusqueda.style.display = 'none';
        }
    });

document.querySelector('.btn-success').addEventListener('click', function() {
    if (productosEnTabla.length === 0) {
        alert('No hay productos en la venta');
        return;
    }
    
    const modalCobro = new bootstrap.Modal(document.getElementById('modalCobro'));
    mostrarModalCobro();
    modalCobro.show();
});
function mostrarModalCobro() {
    const detallesContainer = document.getElementById('detallesCobro');
    detallesContainer.innerHTML = '';
    let totalVenta = 0;
    
    // Llenar tabla de detalles
    productosEnTabla.forEach(producto => {
        const subtotal = producto.precio * producto.cantidad;
        totalVenta += subtotal;
        
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>${producto.nombre} - ${producto.unidad_medida}</td>
            <td class="text-end">${producto.cantidad}</td>
            <td class="text-end">$${producto.precio.toFixed(2)}</td>
            <td class="text-end">$${subtotal.toFixed(2)}</td>
        `;
        detallesContainer.appendChild(fila);
    });
    
    // Actualizar total en el modal
    document.getElementById('totalModal').textContent = `$${totalVenta.toFixed(2)}`;
    
    // Configurar eventos para calcular cambio
    configurarCalculoCambio(totalVenta);
}
// Función para configurar el cálculo del cambio
function configurarCalculoCambio(totalVenta) {
    const metodoPago = document.getElementById('metodoPago');
    const montoRecibido = document.getElementById('montoRecibido');
    const cambioCalculado = document.getElementById('cambioCalculado');
    const efectivoContainer = document.getElementById('efectivoContainer');
    const tarjetaContainer = document.getElementById('tarjetaContainer');
    
    // Mostrar/ocultar campos según método de pago
    metodoPago.addEventListener('change', function() {
        if (this.value === 'efectivo') {
            efectivoContainer.classList.remove('d-none');
            tarjetaContainer.classList.add('d-none');
            montoRecibido.value = '';
            cambioCalculado.textContent = '$0.00';
        } else {
            efectivoContainer.classList.add('d-none');
            tarjetaContainer.classList.remove('d-none');
            cambioCalculado.textContent = '$0.00';
        }
    });
    
    // Calcular cambio en tiempo real
    montoRecibido.addEventListener('input', function() {
        const monto = parseFloat(this.value) || 0;
        const cambio = monto - totalVenta;
        
        if (cambio >= 0) {
            cambioCalculado.textContent = `$${cambio.toFixed(2)}`;
            this.classList.remove('monto-insuficiente');
            // Remover mensaje de error si existe
            const errorMsg = this.nextElementSibling;
            if (errorMsg && errorMsg.classList.contains('monto-insuficiente-texto')) {
                errorMsg.remove();
            }
        } else {
            cambioCalculado.textContent = `-$${Math.abs(cambio).toFixed(2)}`;
            this.classList.add('monto-insuficiente');
            
            // Agregar mensaje de error si no existe
            if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('monto-insuficiente-texto')) {
                const errorMsg = document.createElement('div');
                errorMsg.className = 'monto-insuficiente-texto';
                errorMsg.textContent = 'El monto recibido es insuficiente';
                this.parentNode.insertBefore(errorMsg, this.nextSibling);
            }
        }
    });
    
    // Configurar evento para confirmar cobro
    document.getElementById('confirmarCobro').addEventListener('click', function() {
        const metodo = metodoPago.value;
        
        if (metodo === 'efectivo') {
            const monto = parseFloat(montoRecibido.value) || 0;
            if (monto < totalVenta) {
                alert('El monto recibido es insuficiente');
                return;
            }
        }
        
        // Aquí iría la lógica para procesar el cobro
        procesarCobro(totalVenta, metodo);
    });
}
// Función para procesar el cobro (versión completa)
async function procesarCobro(total, metodo) {
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalCobro'));
    const btnConfirmar = document.getElementById('confirmarCobro');
    
    // Deshabilitar botón para evitar múltiples clics
    btnConfirmar.disabled = true;
    btnConfirmar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
    
    try {
        // Preparar datos para enviar
        const datosVenta = {
            productos: productosEnTabla.map(producto => ({
                id: producto.id,
                unidad_medida:producto.unidad_medida,
                cantidad: producto.cantidad,
                precio: producto.precio,
                subtotal: producto.cantidad * producto.precio
            })),
            total: total,
            metodo_pago: metodo,
            monto_recibido: metodo === 'efectivo' ? parseFloat(document.getElementById('montoRecibido').value) : null
        };
        
        // Enviar datos al servidor
        const response = await fetch('/procesar-venta', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(datosVenta)
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.error || 'Error al procesar la venta');
        }
        
        mostrarMensajeExito(data);

        if (data.pdf_ticket) {
            const blob = base64ToBlob(data.pdf_ticket, 'application/pdf');
            const url = URL.createObjectURL(blob);
            
            // Abrir en nueva pestaña sin opción de descarga
            const newWindow = window.open(url, '_blank');
            
            // Configurar para que cuando se cierre la pestaña, libere memoria
            newWindow.onbeforeunload = function() {
                URL.revokeObjectURL(url);
            };
            
            // Liberar memoria después de 10 minutos por si no se cierra la pestaña
            setTimeout(() => URL.revokeObjectURL(url), 600000);
        }
        
        // Cerrar el modal
        modal.hide();
        
        // Recargar la página después de un breve retraso para que se vea el mensaje
        setTimeout(() => {
            limpiarVenta();
            window.location.reload();
        }, 1500);
        
    } catch (error) {
        console.error('Error:', error);
        alert(`Error al procesar la venta: ${error.message}`);
    } finally {
        // Restaurar botón
        btnConfirmar.disabled = false;
        btnConfirmar.textContent = 'Confirmar Cobro';
    }
}

// Función para mostrar PDF en nueva pestaña
function mostrarPDFEnNuevaPestaña(base64Data) {
    const blob = base64ToBlob(base64Data, 'application/pdf');
    const url = URL.createObjectURL(blob);
    
    // Abrir en nueva pestaña
    window.open(url, '_blank');
    
    // Liberar memoria después de un tiempo
    setTimeout(() => URL.revokeObjectURL(url), 10000);
}

// Función para convertir base64 a Blob (se mantiene igual)
function base64ToBlob(base64, contentType = '', sliceSize = 512) {
    const byteCharacters = atob(base64);
    const byteArrays = [];

    for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
        const slice = byteCharacters.slice(offset, offset + sliceSize);

        const byteNumbers = new Array(slice.length);
        for (let i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
        }

        const byteArray = new Uint8Array(byteNumbers);
        byteArrays.push(byteArray);
    }

    return new Blob(byteArrays, { type: contentType });
}

// Función para mostrar mensaje de éxito
function mostrarMensajeExito(data) {
    const mensaje = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">Venta registrada exitosamente!</h4>
            <p><strong>Número de venta:</strong> ${data.numero_venta}</p>
            <p><strong>Total:</strong> $${data.total_venta.toFixed(2)}</p>
            <p><strong>Fecha:</strong> ${data.fecha_venta}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Mostrar alerta donde sea apropiado en tu layout
    document.getElementById('alertContainer').innerHTML = mensaje;
}


// Función para limpiar la venta (opcional)
function limpiarVenta() {
    document.getElementById('tablaProductos').innerHTML = '';
    productosEnTabla = [];
    localStorage.removeItem('productosVenta');
    actualizarTotales();
    actualizarPaginacion();
}



    // Inicialización
    await cargarProductos();
    await reconstruirTablaDesdeStorage();
    actualizarPaginacion();

});