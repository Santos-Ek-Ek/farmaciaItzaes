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
                        <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 10%;">
                            Rol
                        </th>                   
                        <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 15%;">
                            Acciones 
                        </th>
                    </tr>
                </thead>
                <tbody class="border border-secondary-subtle rounded-2 bg-white" id="tablaProductos">
                    <!-- Las filas de productos se agregarán aquí dinámicamente -->
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small" id="infoPaginacion">
                    Mostrando <span id="desde">0</span> a <span id="hasta">0</span> de <span id="total">0</span> empleados
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0" id="paginacion">
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
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono">
                        </div>
                        <div class="col-md-6">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="">Seleccionar rol</option>
                                <option value="admin">Administrador</option>
                                <option value="empleado">Empleado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
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

@endsection

@push('scripts')
<script>
    // Aquí puedes agregar el JavaScript para manejar el modal y el formulario
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar el evento click del botón Guardar
        document.getElementById('btnGuardarEmpleado').addEventListener('click', function() {
            // Aquí iría la lógica para guardar el empleado
            console.log('Guardando empleado...');
            
            // Ejemplo: puedes usar fetch para enviar los datos al servidor
            const formData = new FormData(document.getElementById('formEmpleado'));
            
            fetch('/empleados', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Cerrar el modal y mostrar mensaje de éxito
                    var modal = bootstrap.Modal.getInstance(document.getElementById('modalEmpleado'));
                    modal.hide();
                    
                    // Mostrar alerta de éxito
                    mostrarAlerta('Empleado agregado correctamente', 'success');
                    
                    // Recargar la tabla o agregar el nuevo empleado dinámicamente
                    // ...
                } else {
                    // Mostrar errores de validación
                    mostrarAlerta(data.message || 'Error al agregar empleado', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlerta('Error al procesar la solicitud', 'danger');
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
            
            // Eliminar la alerta después de 5 segundos
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }
    });
</script>
@endpush