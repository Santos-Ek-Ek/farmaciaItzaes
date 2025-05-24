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



    // Inicialización
    await cargarProductos();
    await reconstruirTablaDesdeStorage();
    actualizarPaginacion();
});