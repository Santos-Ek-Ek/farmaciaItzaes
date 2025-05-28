@extends('layout.layout')
@section('contenido')
@section('titulo', 'Empleados')

<link rel="stylesheet" href="{{ asset('css/productos.css') }}">
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

                <nav aria-label="Page navigation">

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

        // Configurar el evento click del botón Guardar
        document.getElementById('btnGuardarEmpleado').addEventListener('click', function() {
            if (!validatePassword()) {
                mostrarAlerta('Las contraseñas no coinciden', 'danger');
                return;
            }
            
            const formData = new FormData(document.getElementById('formEmpleado'));
            
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
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEmpleado'));
                    modal.hide();
                    mostrarAlerta('Empleado agregado correctamente', 'success');
                    document.getElementById('formEmpleado').reset();
                } else {
                    mostrarAlerta(data.message || 'Error al agregar empleado', 'danger');
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
    
    // Si no se está cambiando la contraseña, eliminar los campos del FormData
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

        // Configurar el evento click del botón Guardar
        document.getElementById('btnGuardarEmpleado').addEventListener('click', function() {
            if (!validatePassword()) {
                mostrarAlerta('Las contraseñas no coinciden', 'danger');
                return;
            }
            
            // Mostrar los valores que se enviarán 
            const formData = new FormData(document.getElementById('formEmpleado'));
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
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
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEmpleado'));
                    modal.hide();
                    mostrarAlerta('Empleado agregado correctamente', 'success');
                    // Recargar la tabla o limpiar el formulario
                    document.getElementById('formEmpleado').reset();
                } else {
                    mostrarAlerta(data.message || 'Error al agregar empleado', 'danger');
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


