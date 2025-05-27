@extends('layout.layout')
@section('contenido')
@section('titulo', 'Historial de Ventas y Reportes')

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
    
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        font-weight: 600;
        border-bottom: 2px solid #0d6efd;
    }
    
    .tab-content {
        padding: 20px 0;
    }
    
    .acciones-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>

<div class="container-fluid p-0 bg-white rounded shadow border border-secondary-subtle">
    <div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1100"></div>
    
    <!-- Pestañas -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="ventas-tab" data-bs-toggle="tab" data-bs-target="#ventas" type="button" role="tab" aria-controls="ventas" aria-selected="true">
                Ventas Individuales
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reportes-tab" data-bs-toggle="tab" data-bs-target="#reportes" type="button" role="tab" aria-controls="reportes" aria-selected="false">
                Reporte de Ventas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="inventario-tab" data-bs-toggle="tab" data-bs-target="#inventario" type="button" role="tab" aria-controls="inventario" aria-selected="false">
                Inventario
            </button>
        </li>
    </ul>
    
    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="myTabContent">
        <!-- Pestaña de Ventas Individuales -->
        <div class="tab-pane fade show active" id="ventas" role="tabpanel" aria-labelledby="ventas-tab">
            <div class="p-4">
                <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 mb-4">
                    <div class="position-relative form-control-sm w-100 w-sm-auto">
                        <input type="text" id="buscarVenta" class="form-control form-control-sm w-100 w-sm-auto" placeholder="Buscar Reporte..." aria-label="Buscar Reporte"/>
                    </div>
                    <select class="form-select form-select-sm w-auto" id="itemsPorPagina" aria-label="Rows per page">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
<!-- Reemplaza la sección de la tabla con esto -->
<div class="table-responsive">
    <table class="table table-borderless align-middle text-secondary-subtle">
        <thead class="bg-light border border-secondary-subtle rounded-2">
            <tr>
                <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 25%;">
                    Nombre del Archivo
                </th>
                <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 15%;">
                    Fecha de Modificación
                </th>
                <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 20%;">
                   Acciones
                </th>                  
            </tr>
        </thead>
        <tbody class="border border-secondary-subtle rounded-2 bg-white" id="tablaReportes">
            @forelse($archivos as $archivo)
                @php
                    $rutaCompleta = public_path('Ventas_individual/'.$archivo);
                    $fechaModificacion = date("d/m/Y H:i", filemtime($rutaCompleta));
                @endphp
                <tr>
                    <td class="text-center">{{ $archivo }}</td>
                    <td class="text-center">{{ $fechaModificacion }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ asset('Ventas_individual/'.$archivo) }}" target="_blank" class="btn btn-sm btn-primary acciones-btn">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <button class="btn btn-sm btn-danger acciones-btn btn-eliminar" data-archivo="{{ $archivo }}">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-4">No se encontraron archivos PDF</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small" id="infoPaginacion">
            Mostrando {{ ($paginaActual - 1) * $porPagina + 1 }} a 
            {{ min($paginaActual * $porPagina, $totalArchivos) }} de 
            {{ $totalArchivos }} reportes
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0" id="paginacion">
                @if($totalPaginas > 1)
                    <!-- Botón Anterior -->
                    <li class="page-item {{ $paginaActual == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="?pagina={{ $paginaActual - 1 }}&porPagina={{ $porPagina }}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    
                    <!-- Páginas numeradas -->
                    @for($i = 1; $i <= $totalPaginas; $i++)
                        <li class="page-item {{ $paginaActual == $i ? 'active' : '' }}">
                            <a class="page-link" href="?pagina={{ $i }}&porPagina={{ $porPagina }}">{{ $i }}</a>
                        </li>
                    @endfor
                    
                    <!-- Botón Siguiente -->
                    <li class="page-item {{ $paginaActual == $totalPaginas ? 'disabled' : '' }}">
                        <a class="page-link" href="?pagina={{ $paginaActual + 1 }}&porPagina={{ $porPagina }}" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</div>
            </div>
        </div>
        
        <!-- Pestaña de Reportes -->
        <div class="tab-pane fade" id="reportes" role="tabpanel" aria-labelledby="reportes-tab">
            <div class="p-4">
                <h4>Reporte de Ventas</h4>
                <p>Aquí iría el contenido del reporte de ventas...</p>
            </div>
        </div>
        
        <!-- Pestaña de Inventario -->
        <div class="tab-pane fade" id="inventario" role="tabpanel" aria-labelledby="inventario-tab">
            <div class="p-4">
                <h4>Inventario</h4>
                <p>Aquí iría el contenido del inventario...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-labelledby="confirmarEliminarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarEliminarModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar este reporte? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
    let archivoAEliminar = '';
    let todosLosArchivos = []; // Almacenará todos los registros

    // Función para cargar todos los archivos 
    function cargarTodosLosArchivos(callback) {
        $.get('{{ route("obtener.todos.reportes") }}', function(response) {
            todosLosArchivos = response;
            if (callback) callback();
        }).fail(function() {
            mostrarAlerta('danger', 'Error al cargar los reportes');
        });
    }

    // Cargar todos los archivos al iniciar
    cargarTodosLosArchivos();

    // Búsqueda en tiempo real
    $('#buscarVenta').on('input', function() {
        const valor = $(this).val().toLowerCase();
        const $paginacion = $('#paginacion');
        const $infoPaginacion = $('#infoPaginacion');
        const $tablaBody = $('#tablaReportes');

        if(valor.length === 0) {
            location.reload();
            return;
        }

        // Ocultar paginación durante la búsqueda
        $paginacion.hide();
        $infoPaginacion.text('Buscando en todos los reportes...');

        // Si ya tenemos todos los archivos cargados, usarlos
        if(todosLosArchivos.length > 0) {
            realizarBusqueda(valor);
        } else {
            // Si no, cargarlos primero
            cargarTodosLosArchivos(function() {
                realizarBusqueda(valor);
            });
        }
    });

    // Función para realizar la búsqueda
    function realizarBusqueda(valor) {
        const $tablaBody = $('#tablaReportes');
        $tablaBody.empty();

        // Filtrar todos los registros
        const resultados = todosLosArchivos.filter(item => 
            item.archivo.toLowerCase().includes(valor) || 
            item.fecha.toLowerCase().includes(valor));

        if(resultados.length === 0) {
            $tablaBody.append(
                '<tr class="no-results"><td colspan="3" class="text-center py-4">No se encontraron resultados</td></tr>'
            );
            $('#infoPaginacion').text('0 resultados encontrados');
        } else {
            resultados.forEach(item => {
                $tablaBody.append(`
                    <tr>
                        <td class="text-center">${item.archivo}</td>
                        <td class="text-center">${item.fecha}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ asset('Ventas_individual/') }}/${item.archivo}" target="_blank" class="btn btn-sm btn-primary acciones-btn">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <button class="btn btn-sm btn-danger acciones-btn btn-eliminar" data-archivo="${item.archivo}">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
            });
            $('#infoPaginacion').text(`${resultados.length} resultados encontrados`);
        }
    }
    
    // Inicializar tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Manejar el botón de eliminar (con delegación de eventos)
    $(document).on('click', '.btn-eliminar', function() {
        archivoAEliminar = $(this).data('archivo');
        $('#confirmarEliminarModal').modal('show');
    });
    
    // Confirmar eliminación
    $('#confirmarEliminar').click(function() {
        $.ajax({
            url: '{{ route("eliminar.reporte") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                archivo: archivoAEliminar
            },
            success: function(response) {
                if(response.success) {
                    mostrarAlerta('success', 'Reporte eliminado correctamente');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    mostrarAlerta('danger', 'Error al eliminar el reporte: ' + response.message);
                }
            },
            error: function(xhr) {
                mostrarAlerta('danger', 'Error al eliminar el reporte');
            }
        });
        
        $('#confirmarEliminarModal').modal('hide');
    });
    
    // Función para mostrar alertas
    function mostrarAlerta(tipo, mensaje) {
        $('#alertContainer').empty();
        const alerta = `
            <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alertContainer').append(alerta);
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }
});
</script>
@endsection