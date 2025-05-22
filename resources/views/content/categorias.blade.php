@extends('layout.layout')
@section('contenido')
@section('titulo', 'Categorías')

<link rel="stylesheet" href="{{ asset('css/productos.css') }}">

<div class="container-fluid p-0 bg-white rounded shadow border border-secondary-subtle">
    <div class="p-4">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 mb-4">
            <input type="text" class="form-control form-control-sm w-100 w-sm-auto" placeholder="Buscar Categoría"
                aria-label="Buscar Categoría" />
<select class="form-select form-select-sm w-auto" id="rowsPerPage" aria-label="Rows per page">
    <option value="7" {{ (request('per_page', 7) == 7) ? 'selected' : '' }}>7</option>
    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
    <option value="0" {{ request('per_page') == 0 ? 'selected' : '' }}>TODOS</option>
</select>
            <button type="button" class="btn btn-add btn-sm ms-auto ms-sm-0" data-bs-toggle="modal"
                data-bs-target="#categoriaModal">+ Agregar Categoría</button>
        </div>
        <div class="table-responsive">
            <table class="table table-borderless align-middle text-secondary-subtle">
                <thead class="bg-light border border-secondary-subtle rounded-2">
                    <tr>
                        <th class="text-uppercase fw-semibold" style="font-size: 0.625rem; cursor:pointer;">
                            Nombre <i class="fas fa-sort-up"></i>
                        </th>
                        <th class="text-uppercase fw-semibold" style="font-size: 0.625rem; cursor:pointer;">
                            Acciones <i class="fas fa-sort-up"></i>
                        </th>
                    </tr>
                </thead>
                <tbody class="border border-secondary-subtle rounded-2 bg-white">
                    @foreach ($categorias as $categoria)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <div class="product-name">{{$categoria->nombre}}</div>
                                    <div class="product-desc">{{$categoria->descripcion}}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-export btn-sm d-flex align-items-center gap-1 edit-btn"
                                    data-id="{{ $categoria->id }}" data-bs-toggle="modal" data-bs-target="#editCategoriaModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('categorias.destroy', $categoria->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button style="margin-top: 12px;" type="submit" class="btn btn-export btn-sm d-flex align-items-center gap-1"
                                        onclick="return confirm('¿Estás seguro de querer desactivar esta categoría?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
                <!-- Paginación -->
@if(isset($categorias) && method_exists($categorias, 'links'))
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
    <div class="text-muted small mb-2 mb-md-0">
        Mostrando {{ $categorias->firstItem() }} a {{ $categorias->lastItem() }} de {{ $categorias->total() }} resultados
    </div>
    
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm mb-0">
            {{-- flechas de paginacion --}}
            @if ($categorias->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">&laquo; Anterior</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $categorias->previousPageUrl() }}" rel="prev">&laquo; Anterior</a>
                </li>
            @endif

            {{-- numeracion --}}
            @foreach ($categorias->getUrlRange(1, $categorias->lastPage()) as $page => $url)
                @if ($page == $categorias->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- siguiente flecha --}}
            @if ($categorias->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $categorias->nextPageUrl() }}" rel="next">Siguiente &raquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Siguiente &raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
</div>
@endif
    </div>
</div>

<!-- Modal para agregar categoría -->
<div class="modal fade" id="categoriaModal" tabindex="-1" aria-labelledby="categoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoriaModalLabel">Agregar Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('categorias.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar categoría -->
<div class="modal fade" id="editCategoriaModal" tabindex="-1" aria-labelledby="editCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoriaModalLabel">Editar Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoriaForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[placeholder="Buscar Categoría"]');
    const rows = document.querySelectorAll('tbody tr');

    // Función mejorada para filtrar categorías
    const filterCategories = () => {
        // Eliminar espacios al inicio/final y normalizar espacios múltiples
        const searchTerm = searchInput.value.toLowerCase().trim().replace(/\s+/g, ' ');

        rows.forEach(row => {
            const nombre = (row.querySelector('.product-name')?.textContent || '').toLowerCase();
            const descripcion = (row.querySelector('.product-desc')?.textContent || '')
            .toLowerCase();

            // Dividir el término de búsqueda en palabras individuales
            const searchWords = searchTerm.split(' ');

            // Verificar si TODAS las palabras aparecen en nombre O descripción
            const matchesSearch = searchWords.every(word =>
                nombre.includes(word) || descripcion.includes(word));

            row.style.display = matchesSearch ? '' : 'none';
        });
    };

    // Búsqueda en tiempo real
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(filterCategories, 300);
    });

    // Filtrar al cargar si hay valor
    if (searchInput.value.trim()) {
        filterCategories();
    }

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const categoriaId = this.getAttribute('data-id');
            
            // Hacer una petición AJAX para obtener los datos de la categoría
            fetch(`/categorias/${categoriaId}/edit`)
                .then(response => response.json())
                .then(data => {
                    // Llenar el formulario con los datos de la categoría
                    document.getElementById('edit_nombre').value = data.nombre;
                    document.getElementById('edit_descripcion').value = data.descripcion;
                    
                    // Actualizar la acción del formulario
                    document.getElementById('editCategoriaForm').action = `/categorias/${categoriaId}`;
                })
                .catch(error => console.error('Error:', error));
        });
    });


    // Manejar cambio en el select de registros por página
    document.getElementById('rowsPerPage').addEventListener('change', function() {
        const perPage = this.value;
        const url = new URL(window.location.href);
        
        // Resetear a página 1 al cambiar el número de resultados
        url.searchParams.set('page', 1);
        url.searchParams.set('per_page', perPage);
        
        window.location.href = url.toString();
    });
});
</script>

@endsection