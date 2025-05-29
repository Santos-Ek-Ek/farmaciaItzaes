@extends('layout.layout')
@section('contenido')
@section('titulo', 'Empleados / Administradores')

<link rel="stylesheet" href="{{ asset('css/productos.css') }}">
<meta name="logged-in-user-id" content="{{ auth()->id() }}">
<style>
    #resultadosBusqueda {
        max-width: 100%;
        overflow: hidden;
    }

    .dropdown-item {
        white-space: normal;
        word-wrap: break-word;
    }

    .password-mismatch {
        border-color: #dc3545 !important;
    }
</style>

<div class="container-fluid p-0 bg-white rounded shadow border border-secondary-subtle">
    <div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1100"></div>
    <div class="p-4">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 mb-4">
            <div class="position-relative form-control-sm w-100 w-sm-auto">
                <input type="text" id="buscarEmpleado" class="form-control form-control-sm w-100 w-sm-auto" placeholder="Buscar..." aria-label="Buscar"/>
                <div id="resultadosBusqueda" class="dropdown-menu w-100" style="display: none;"></div>
            </div>
            <select class="form-select form-select-sm w-auto" id="itemsPorPagina" aria-label="Rows per page">
                <option value="7" selected>7</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <button type="button" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1" id="btnAbrirModalEmpleado" data-bs-toggle="modal" data-bs-target="#modalEmpleado">
                <i class="fas fa-plus"></i>
                Agregar Empleado
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-borderless align-middle text-secondary-subtle">
                <thead class="bg-light border border-secondary-subtle rounded-2">
                    <tr>
                        <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 25%;">
                            Nombre Completo
                        </th>
                        <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 15%;">
                            Email
                        </th>
                        <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 15%;">
                            Teléfono
                        </th>
                        <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 10%;">
                            Rol
                        </th>                   
                        <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 15%;">
                            Acciones 
                        </th>
                    </tr>
                </thead>
                <tbody class="border border-secondary-subtle rounded-2 bg-white" id="tablaEmpleados">
                    @foreach($usuarios as $usuario)
                    <tr>
                        <td class="text-center">{{ $usuario->nombre }} {{ $usuario->apellidos }}</td>
                        <td class="text-center">{{ $usuario->email }}</td>
                        <td class="text-center">{{ $usuario->telefono }}</td>
                        <td class="text-center">
                            @if($usuario->rol == 'Administrador')
                                <span class="badge bg-primary">Administrador</span>
                            @else
                                <span class="badge bg-secondary">Empleado</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-outline-primary acciones-btn" 
                                        onclick="editarEmpleado({{ $usuario->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
<button class="btn btn-sm btn-outline-danger acciones-btn" 
        onclick="confirmarEliminar({{ $usuario->id }}, '{{ $usuario->nombre }} {{ $usuario->apellidos }}')">
    <i class="fas fa-user-slash"></i>
</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
<div class="d-flex justify-content-between align-items-center mt-3">
    <div id="infoPaginacion" class="text-muted small"></div>
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm" id="paginacion">
            <!-- Los botones de paginación se generarán aquí -->
        </ul>
    </nav>
</div>
        </div>
    </div>
</div>

<!-- Modal para agregar empleado -->
<div class="modal fade" id="modalEmpleado" tabindex="-1" aria-labelledby="modalEmpleadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEmpleadoLabel">Agregar Nuevo Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEmpleado">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre(s)</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellido" class="form-label">Apellidos</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
<div class="col-md-6">
    <label for="telefono" class="form-label">Teléfono</label>
    <input type="text" 
           class="form-control" 
           id="telefono" 
           name="telefono" 
           pattern="\d{10}" 
           maxlength="10"
           title="Debe contener exactamente 10 dígitos"
           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
</div>
                        <div class="col-md-4">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="">Seleccionar rol</option>
                                <option value="Administrador">Administrador</option>
                                <option value="Empleado">Empleado</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="8">
                            <small class="text-muted">Mínimo 8 caracteres</small>
                        </div>
                        <div class="col-md-4">
                            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            <div id="passwordError" class="invalid-feedback"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarEmpleado">Guardar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal para editar empleado -->
<div class="modal fade" id="modalEditarEmpleado" tabindex="-1" aria-labelledby="modalEditarEmpleadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarEmpleadoLabel">Editar Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarEmpleado">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_nombre" class="form-label">Nombre(s)</label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_apellido" class="form-label">Apellidos</label>
                            <input type="text" class="form-control" id="edit_apellido" name="apellidos" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_telefono" class="form-label">Teléfono</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="edit_telefono" 
                                   name="telefono" 
                                   pattern="\d{10}" 
                                   maxlength="10"
                                   title="Debe contener exactamente 10 dígitos"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_rol" class="form-label">Rol</label>
                            <select class="form-select" id="edit_rol" name="rol" required>
                                <option value="">Seleccionar rol</option>
                                <option value="Administrador">Administrador</option>
                                <option value="Empleado">Empleado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4 pt-2">
                                <input class="form-check-input" type="checkbox" id="edit_cambiar_password">
                                <label class="form-check-label" for="edit_cambiar_password">
                                    Cambiar contraseña
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 password-fields" style="display: none;">
                            <label for="edit_password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="edit_password" name="password" minlength="8">
                            <small class="text-muted">Mínimo 8 caracteres</small>
                        </div>
                        <div class="col-md-6 password-fields" style="display: none;">
                            <label for="edit_password_confirmation" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation">
                            <div id="edit_passwordError" class="invalid-feedback"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnActualizarEmpleado">Actualizar</button>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let itemsPerPage = parseInt(document.getElementById('itemsPorPagina').value);
    let totalItems = 0;
    let allEmpleados = [];
    let filteredEmpleados = null; 

    // Cargar datos iniciales
    fetchEmpleados();

    // Evento para cambiar items por página
    document.getElementById('itemsPorPagina').addEventListener('change', function() {
        itemsPerPage = parseInt(this.value);
        currentPage = 1;
        renderTable();
        renderPagination();
    });

    // Función para cargar empleados
    function fetchEmpleados() {
        fetch('/empleados/json')
            .then(response => response.json())
            .then(data => {
                allEmpleados = data;
                totalItems = data.length;
                filteredEmpleados = null; 
                renderTable();
                renderPagination();
            })
            .catch(error => console.error('Error:', error));
    }

    // Función para renderizar la tabla con los datos paginados
    function renderTable() {
    const loggedInUserId = document.querySelector('meta[name="logged-in-user-id"]').content;
    const empleadosToShow = filteredEmpleados || allEmpleados;
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedItems = empleadosToShow.slice(startIndex, endIndex);
    
    const tablaEmpleados = document.getElementById('tablaEmpleados');
    tablaEmpleados.innerHTML = '';
    
    paginatedItems.forEach(empleado => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="text-center">${empleado.nombre} ${empleado.apellidos}</td>
            <td class="text-center">${empleado.email}</td>
            <td class="text-center">${empleado.telefono}</td>
            <td class="text-center">
                ${empleado.rol === 'Administrador' 
                    ? '<span class="badge bg-primary">Administrador</span>' 
                    : '<span class="badge bg-secondary">Empleado</span>'}
            </td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-sm btn-outline-primary acciones-btn" 
                            onclick="editarEmpleado(${empleado.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    ${empleado.id != loggedInUserId ? 
                        `<button class="btn btn-sm btn-outline-danger acciones-btn" 
                                onclick="confirmarEliminar(${empleado.id}, '${empleado.nombre} ${empleado.apellidos}')">
                            <i class="fas fa-user-slash"></i>
                        </button>` : ''}
                </div>
            </td>
        `;
        tablaEmpleados.appendChild(row);
    });
        
        // Actualizar información de paginación
        const totalItemsToShow = empleadosToShow.length;
        document.getElementById('infoPaginacion').textContent = 
            `Mostrando ${startIndex + 1} a ${Math.min(endIndex, totalItemsToShow)} de ${totalItemsToShow} empleados`;
    }

    // Función para renderizar los controles de paginación
    function renderPagination() {
        const empleadosToShow = filteredEmpleados || allEmpleados;
        const totalItemsToShow = empleadosToShow.length;
        const totalPages = Math.ceil(totalItemsToShow / itemsPerPage);
        const pagination = document.getElementById('paginacion');
        pagination.innerHTML = '';
        
        // Botón Anterior
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Previous">&laquo;</a>`;
        prevLi.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                renderTable();
                renderPagination();
            }
        });
        pagination.appendChild(prevLi);
        
        // Números de página
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        if (startPage > 1) {
            const firstLi = document.createElement('li');
            firstLi.className = 'page-item';
            firstLi.innerHTML = `<a class="page-link" href="#">1</a>`;
            firstLi.addEventListener('click', (e) => {
                e.preventDefault();
                currentPage = 1;
                renderTable();
                renderPagination();
            });
            pagination.appendChild(firstLi);
            
            if (startPage > 2) {
                const ellipsisLi = document.createElement('li');
                ellipsisLi.className = 'page-item disabled';
                ellipsisLi.innerHTML = `<span class="page-link">...</span>`;
                pagination.appendChild(ellipsisLi);
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
            pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            pageLi.addEventListener('click', (e) => {
                e.preventDefault();
                currentPage = i;
                renderTable();
                renderPagination();
            });
            pagination.appendChild(pageLi);
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsisLi = document.createElement('li');
                ellipsisLi.className = 'page-item disabled';
                ellipsisLi.innerHTML = `<span class="page-link">...</span>`;
                pagination.appendChild(ellipsisLi);
            }
            
            const lastLi = document.createElement('li');
            lastLi.className = 'page-item';
            lastLi.innerHTML = `<a class="page-link" href="#">${totalPages}</a>`;
            lastLi.addEventListener('click', (e) => {
                e.preventDefault();
                currentPage = totalPages;
                renderTable();
                renderPagination();
            });
            pagination.appendChild(lastLi);
        }
        
        // Botón Siguiente
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Next">&raquo;</a>`;
        nextLi.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
                renderPagination();
            }
        });
        pagination.appendChild(nextLi);
    }
    
  
    document.getElementById('buscarEmpleado').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        if (searchTerm.length > 0) {
            // Filtrar empleados
            filteredEmpleados = allEmpleados.filter(empleado => 
                (empleado.nombre + ' ' + empleado.apellidos).toLowerCase().includes(searchTerm) || 
                empleado.email.toLowerCase().includes(searchTerm) ||
                empleado.telefono.includes(searchTerm) ||
                empleado.rol.toLowerCase().includes(searchTerm)
            );
            
            // Mostrar resultados en el dropdown
            const resultados = document.getElementById('resultadosBusqueda');
            resultados.innerHTML = '';
            
            
            // Actualizar tabla y paginación con resultados filtrados
            currentPage = 1;
            renderTable();
            renderPagination();
        } else {
            // Si no hay término de búsqueda, restaurar todos los empleados
            document.getElementById('resultadosBusqueda').style.display = 'none';
            filteredEmpleados = null;
            currentPage = 1;
            renderTable();
            renderPagination();
        }
    });
    
    // Ocultar resultados de búsqueda al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#buscarEmpleado') && !e.target.closest('#resultadosBusqueda')) {
            document.getElementById('resultadosBusqueda').style.display = 'none';
        }
    });
});
</script>

<script>
    function mostrarAlerta(mensaje, tipo) {
        const alertContainer = document.getElementById('alertContainer');
        const alert = document.createElement('div');
        alert.className = `alert alert-${tipo} alert-dismissible fade show`;
        alert.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertContainer.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }

        // Validación en tiempo real de contraseñas
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        const passwordError = document.getElementById('passwordError');
        
        function validatePassword() {
            if (password.value !== passwordConfirmation.value) {
                passwordConfirmation.classList.add('password-mismatch');
                passwordError.textContent = 'Las contraseñas no coinciden';
                passwordError.style.display = 'block';
                return false;
            } else {
                passwordConfirmation.classList.remove('password-mismatch');
                passwordError.style.display = 'none';
                return true;
            }
        }
        
        passwordConfirmation.addEventListener('input', validatePassword);
        password.addEventListener('input', validatePassword);

document.getElementById('btnGuardarEmpleado').addEventListener('click', function() {
    if (!validatePassword()) {
        mostrarAlerta('Las contraseñas no coinciden', 'danger');
        return;
    }
    
    const formData = new FormData(document.getElementById('formEmpleado'));
    
    // Mostrar los datos que se enviarán
    for (let [key, value] of formData.entries()) {
        console.log(key + ": " + value);
    }
    
    fetch('{{ route("empleados.agregar") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        // Primero verificar el estado de la respuesta
        if (!response.ok) {
            // Si la respuesta no es OK, intentar parsear el error
            return response.json().then(err => {
                // Lanzar el error para que sea capturado
                throw err;
            }).catch(() => {
                // Si no se puede parsear como JSON, lanzar un error genérico
                throw new Error('Error en la solicitud: ' + response.status);
            });
        }
        // Si la respuesta es OK, parsear como JSON
        return response.json();
    })
    .then(data => {
        if(data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalEmpleado'));
            modal.hide();
            mostrarAlerta('Empleado agregado correctamente', 'success');
            document.getElementById('formEmpleado').reset();
            // Recargar la página o actualizar la tabla
            location.reload();
        } else {
            // Mostrar mensaje de error 
            mostrarAlerta(data.message || 'Error al agregar empleado', 'danger');
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        if (error.errors) {
            // Error de validación 
            let errorMessages = Object.values(error.errors).flat().join('<br>');
            mostrarAlerta(errorMessages, 'danger');
        } else {
           
            mostrarAlerta(error.message || 'Error al procesar la solicitud', 'danger');
        }
    });
});

    // Función para editar empleado
    function editarEmpleado(id) {
        fetch(`/empleados/${id}/editar`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('edit_id').value = data.empleado.id;
                document.getElementById('edit_nombre').value = data.empleado.nombre;
                document.getElementById('edit_apellido').value = data.empleado.apellidos; 
                document.getElementById('edit_email').value = data.empleado.email;
                document.getElementById('edit_telefono').value = data.empleado.telefono;
                document.getElementById('edit_rol').value = data.empleado.rol;
                
                const modal = new bootstrap.Modal(document.getElementById('modalEditarEmpleado'));
                modal.show();
            } else {
                mostrarAlerta(data.message || 'Error al cargar datos del empleado', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error al cargar datos del empleado', 'danger');
        });
    }

    // Mostrar/ocultar campos de contraseña en edición
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('edit_cambiar_password').addEventListener('change', function() {
            const passwordFields = document.querySelectorAll('.password-fields');
            passwordFields.forEach(field => {
                field.style.display = this.checked ? 'block' : 'none';
            });
        });

        // Validación de contraseñas en edición
        document.getElementById('edit_password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('edit_password').value;
            const confirm = this.value;
            const errorElement = document.getElementById('edit_passwordError');
            
            if (password !== confirm) {
                this.classList.add('password-mismatch');
                errorElement.textContent = 'Las contraseñas no coinciden';
                errorElement.style.display = 'block';
            } else {
                this.classList.remove('password-mismatch');
                errorElement.style.display = 'none';
            }
        });

// Guardar cambios al editar
document.getElementById('btnActualizarEmpleado').addEventListener('click', function() {
    const form = document.getElementById('formEditarEmpleado');
    const formData = new FormData(form);
    
    // Si no se está cambiando la contraseña, eliminar los campos 
    if (!document.getElementById('edit_cambiar_password').checked) {
        formData.delete('password');
        formData.delete('password_confirmation');
    } else {
        // Validar contraseñas si se están cambiando
        const password = document.getElementById('edit_password').value;
        const confirm = document.getElementById('edit_password_confirmation').value;
        
        if (password !== confirm) {
            mostrarAlerta('Las contraseñas no coinciden', 'danger');
            return;
        }
    }
    
    fetch(`/empleados/${formData.get('id')}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-HTTP-Method-Override': 'PUT'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarEmpleado'));
            modal.hide();
            mostrarAlerta('Empleado actualizado correctamente', 'success');
            location.reload();
        } else {
            mostrarAlerta(data.message || 'Error al actualizar empleado', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (error.errors) {
            let errorMessages = Object.values(error.errors).flat().join('<br>');
            mostrarAlerta(errorMessages, 'danger');
        } else {
            mostrarAlerta(error.message || 'Error al procesar la solicitud', 'danger');
        }
    });
});
    });

// Función para confirmar eliminación 
function confirmarEliminar(id, nombre) {
    Swal.fire({
        title: `¿Desactivar a ${nombre}?`,
        text: "El empleado ya no tendrá acceso al sistema",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarEmpleado(id, nombre);
        }
    });
}

// Función para eliminar (desactivar) empleado
function eliminarEmpleado(id, nombre) {
    // Mostrar loader mientras se procesa
    Swal.fire({
        title: 'Desactivando empleado',
        html: `Por favor espera mientras desactivamos a ${nombre}...`,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`/empleados-eliminar/${id}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if(data.success) {
            Swal.fire({
                title: '¡Desactivado!',
                text: `El empleado ${nombre} ha sido desactivado.`,
                icon: 'success',
                confirmButtonColor: '#3085d6',
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'Error al desactivar empleado',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: error.message || 'Error al procesar la solicitud',
            icon: 'error',
            confirmButtonColor: '#3085d6'
        });
    });
}
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validación en tiempo real de contraseñas
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        const passwordError = document.getElementById('passwordError');
        
        function validatePassword() {
            if (password.value !== passwordConfirmation.value) {
                passwordConfirmation.classList.add('password-mismatch');
                passwordError.textContent = 'Las contraseñas no coinciden';
                passwordError.style.display = 'block';
                return false;
            } else {
                passwordConfirmation.classList.remove('password-mismatch');
                passwordError.style.display = 'none';
                return true;
            }
        }
        
        passwordConfirmation.addEventListener('input', validatePassword);
        password.addEventListener('input', validatePassword);
        
        function mostrarAlerta(mensaje, tipo) {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${tipo} alert-dismissible fade show`;
            alert.innerHTML = `
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            alertContainer.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }
    });
</script>

@endsection


