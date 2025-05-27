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
        <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 mb-4">
            <div class="position-relative form-control-sm w-100 w-sm-auto">
                <input type="text" id="buscarInventario" class="form-control form-control-sm w-100 w-sm-auto" placeholder="Buscar en Inventario..." aria-label="Buscar en Inventario"/>
            </div>
            <select class="form-select form-select-sm w-auto" id="itemsPorPaginaInventario" aria-label="Rows per page">
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
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
                <tbody class="border border-secondary-subtle rounded-2 bg-white" id="tablaInventario">
                    @forelse($archivosInventario as $archivo)
                        @php
                            $rutaCompleta = public_path('Reporte_ventas/'.$archivo);
                            $fechaModificacion = date("d/m/Y H:i", filemtime($rutaCompleta));
                        @endphp
                        <tr>
                            <td class="text-center">{{ $archivo }}</td>
                            <td class="text-center">{{ $fechaModificacion }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ asset('Reporte_ventas/'.$archivo) }}" target="_blank" class="btn btn-sm btn-primary acciones-btn">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <button class="btn btn-sm btn-danger acciones-btn btn-eliminar-inventario" data-archivo="{{ $archivo }}" data-tipo="inventario">
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
                <div class="text-muted small" id="infoPaginacionInventario">
                    Mostrando {{ ($paginaActualInventario - 1) * $porPaginaInventario + 1 }} a 
                    {{ min($paginaActualInventario * $porPaginaInventario, $totalArchivosInventario) }} de 
                    {{ $totalArchivosInventario }} reportes
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0" id="paginacionInventario">
                        @if($totalPaginasInventario > 1)
                            <!-- Botón Anterior -->
                            <li class="page-item {{ $paginaActualInventario == 1 ? 'disabled' : '' }}">
                                <a class="page-link" href="?paginaInventario={{ $paginaActualInventario - 1 }}&porPaginaInventario={{ $porPaginaInventario }}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            
                            <!-- Páginas numeradas -->
                            @for($i = 1; $i <= $totalPaginasInventario; $i++)
                                <li class="page-item {{ $paginaActualInventario == $i ? 'active' : '' }}">
                                    <a class="page-link" href="?paginaInventario={{ $i }}&porPaginaInventario={{ $porPaginaInventario }}">{{ $i }}</a>
                                </li>
                            @endfor
                            
                            <!-- Botón Siguiente -->
                            <li class="page-item {{ $paginaActualInventario == $totalPaginasInventario ? 'disabled' : '' }}">
                                <a class="page-link" href="?paginaInventario={{ $paginaActualInventario + 1 }}&porPaginaInventario={{ $porPaginaInventario }}" aria-label="Next">
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
    let tipoAEliminar = '';
    let todosLosArchivos = [];
    let archivosFiltrados = {
        ventas: [],
        inventario: []
    };
    let configPaginacion = {
        ventas: {
            paginaActual: 1,
            porPagina: 10,
            totalPaginas: 1
        },
        inventario: {
            paginaActual: 1,
            porPagina: 10,
            totalPaginas: 1
        }
    };

    // Función para cargar todos los archivos
    function cargarTodosLosArchivos(callback) {
        $.get('{{ route("obtener.todos.reportes") }}', function(response) {
            todosLosArchivos = response;
            
            // Inicializar archivos filtrados con todos los archivos correspondientes
            archivosFiltrados.ventas = todosLosArchivos.filter(item => item.tipo === 'ventas');
            archivosFiltrados.inventario = todosLosArchivos.filter(item => item.tipo === 'inventario');
            
            // Calcular paginación inicial
            calcularPaginacion('ventas');
            calcularPaginacion('inventario');
            
            if (callback) callback();
        }).fail(function() {
            mostrarAlerta('danger', 'Error al cargar los reportes');
        });
    }

    // Función para calcular la paginación
    function calcularPaginacion(tipo) {
        const config = configPaginacion[tipo];
        const totalArchivos = archivosFiltrados[tipo].length;
        config.totalPaginas = Math.ceil(totalArchivos / config.porPagina);
        config.paginaActual = Math.min(config.paginaActual, config.totalPaginas || 1);
    }

    // Cargar todos los archivos al iniciar y mostrar inicialmente
    cargarTodosLosArchivos(function() {
        actualizarTabla('ventas');
        actualizarTabla('inventario');
    });

    // Búsqueda en tiempo real para Ventas
    $('#buscarVenta').on('input', function() {
        const valor = $(this).val().toLowerCase();
        buscarArchivos(valor, 'ventas');
    });

    // Búsqueda en tiempo real para Inventario
    $('#buscarInventario').on('input', function() {
        const valor = $(this).val().toLowerCase();
        buscarArchivos(valor, 'inventario');
    });

    // Cambiar cantidad de items por página para Ventas
    $('#itemsPorPagina').change(function() {
        configPaginacion.ventas.porPagina = parseInt($(this).val());
        configPaginacion.ventas.paginaActual = 1;
        calcularPaginacion('ventas');
        actualizarTabla('ventas');
    });

    // Cambiar cantidad de items por página para Inventario
    $('#itemsPorPaginaInventario').change(function() {
        configPaginacion.inventario.porPagina = parseInt($(this).val());
        configPaginacion.inventario.paginaActual = 1;
        calcularPaginacion('inventario');
        actualizarTabla('inventario');
    });

    // Función para buscar archivos
    function buscarArchivos(valor, tipo) {
        if (valor.length === 0) {
            // Cuando el input está vacío, mostramos todos los archivos del tipo
            archivosFiltrados[tipo] = todosLosArchivos.filter(item => item.tipo === tipo);
        } else {
            // Filtrar por tipo y valor de búsqueda
            archivosFiltrados[tipo] = todosLosArchivos.filter(item => 
                item.tipo === tipo && (
                    item.archivo.toLowerCase().includes(valor) || 
                    item.fecha.toLowerCase().includes(valor)
                )
            );
        }
        
        // Actualizar paginación
        calcularPaginacion(tipo);
        actualizarTabla(tipo);
    }

    // Función para actualizar la tabla y controles
    function actualizarTabla(tipo) {
        const config = configPaginacion[tipo];
        const archivosTipo = archivosFiltrados[tipo];
        const selectorTabla = tipo === 'ventas' ? '#tablaReportes' : '#tablaInventario';
        const selectorInfo = tipo === 'ventas' ? '#infoPaginacion' : '#infoPaginacionInventario';
        const selectorPaginacion = tipo === 'ventas' ? '#paginacion' : '#paginacionInventario';
        
        // Calcular archivos a mostrar
        const inicio = (config.paginaActual - 1) * config.porPagina;
        const fin = inicio + config.porPagina;
        const archivosAMostrar = archivosTipo.slice(inicio, fin);
        
        // Actualizar tabla
        const $tablaBody = $(selectorTabla);
        $tablaBody.empty();
        
        if (archivosAMostrar.length === 0) {
            $tablaBody.append(
                '<tr class="no-results"><td colspan="3" class="text-center py-4">No se encontraron archivos</td></tr>'
            );
        } else {
            archivosAMostrar.forEach(item => {
                const rutaBase = item.tipo === 'inventario' ? 'Reporte_ventas' : 'Ventas_individual';
                $tablaBody.append(`
                    <tr>
                        <td class="text-center">${item.archivo}</td>
                        <td class="text-center">${item.fecha}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="/${rutaBase}/${item.archivo}" target="_blank" class="btn btn-sm btn-primary acciones-btn">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <button class="btn btn-sm btn-danger acciones-btn btn-eliminar" data-archivo="${item.archivo}" data-tipo="${item.tipo}">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
            });
        }
        
        // Actualizar información de paginación
        const totalArchivos = archivosTipo.length;
        const mostrandoDesde = totalArchivos > 0 ? inicio + 1 : 0;
        const mostrandoHasta = Math.min(fin, totalArchivos);
        
        $(selectorInfo).text(`Mostrando ${mostrandoDesde} a ${mostrandoHasta} de ${totalArchivos} archivos`);
        
        // Actualizar controles de paginación
        const $paginacion = $(selectorPaginacion);
        $paginacion.empty();
        
        if (config.totalPaginas > 1) {
            // Botón Anterior
            $paginacion.append(`
                <li class="page-item ${config.paginaActual === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-pagina="${config.paginaActual - 1}" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            `);
            
            // Páginas
            for (let i = 1; i <= config.totalPaginas; i++) {
                $paginacion.append(`
                    <li class="page-item ${config.paginaActual === i ? 'active' : ''}">
                        <a class="page-link" href="#" data-pagina="${i}">${i}</a>
                    </li>
                `);
            }
            
            // Botón Siguiente
            $paginacion.append(`
                <li class="page-item ${config.paginaActual === config.totalPaginas ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-pagina="${config.paginaActual + 1}" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            `);
        }
    }

    // Manejar cambio de página
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const pagina = parseInt($(this).data('pagina'));
        const tipo = $(this).closest('.tab-pane').is('#ventas') ? 'ventas' : 'inventario';
        
        if (!isNaN(pagina)) {
            configPaginacion[tipo].paginaActual = pagina;
            actualizarTabla(tipo);
        }
    });

    // Manejar el botón de eliminar
    $(document).on('click', '.btn-eliminar', function() {
        archivoAEliminar = $(this).data('archivo');
        tipoAEliminar = $(this).data('tipo');
        $('#confirmarEliminarModal').modal('show');
    });

    // Confirmar eliminación
    $('#confirmarEliminar').click(function() {
        $.ajax({
            url: '{{ route("eliminar.reporte") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                archivo: archivoAEliminar,
                tipo: tipoAEliminar
            },
            success: function(response) {
                if(response.success) {
                    mostrarAlerta('success', 'Reporte eliminado correctamente');
                    // Volver a cargar los datos después de eliminar
                    cargarTodosLosArchivos(function() {
                        buscarArchivos($('#buscar' + (tipoAEliminar === 'ventas' ? 'Venta' : 'Inventario')).val(), tipoAEliminar);
                    });
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