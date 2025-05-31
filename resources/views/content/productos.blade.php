@extends('layout.layout')
@section('contenido')
@section('titulo', 'Productos / Stock')

  <link rel="stylesheet" href="{{ asset('css/productos.css') }}">

  <div class="container-fluid p-0 bg-white rounded shadow border border-secondary-subtle">
    @if($productos->filter(fn($p) => $p->estaPorAgotarse())->count() > 0)
<div class="alert alert-warning mb-4">
    <i class="fas fa-exclamation-triangle me-2"></i>
    @php
        $porAgotarse = $productos->filter(fn($p) => $p->estaPorAgotarse());
        $cantidad = $porAgotarse->count();
    @endphp
    Tienes {{ $cantidad }} {{ $cantidad === 1 ? 'producto' : 'productos' }} por agotarse.
    @if($cantidad > 0)
        <a href="#" class="alert-link" id="filtrarPorAgotarse">Filtrar</a>
    @endif
    <button type="button" class="btn btn-sm btn-outline-secondary" id="resetearFiltros" style="display: none;">
        <i class="fas fa-sync-alt"></i> Resetear
    </button>
</div>
@endif

   <div class="p-4 border-bottom border-secondary-subtle">
    <h2 class="fw-semibold text-secondary mb-3" style="font-size: 0.875rem;">Filtros</h2>
    <form class="row g-3">
<div class="col-12 col-sm-4 col-md-3 col-lg-2">
    <select class="form-select" id="filtroCategoria" name="filtroCategoria">
        <option value="" selected>Todas las categorías</option>
        @foreach ($categorias as $categoria)
            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
        @endforeach
    </select>
</div>
     <div class="col-12 col-sm-4 col-md-3 col-lg-2">
 <select class="form-select" aria-label="Rango de precios" id="filtroPrecio" name="filtroPrecio">
        <option selected value="">Todos los precios</option>
        @php
            // Obtener el precio máximo de la base de datos
            $maxPrice = (float)App\Models\Productos::max('precio');
            
            // Si no hay productos o el precio es 0, establecer un valor por defecto
            if($maxPrice <= 0) {
                $maxPrice = 200; // Valor por defecto para mostrar al menos dos rangos
            }
            
            // Definir el incremento base
            $increment = 100;
            $current = 0;
            
            // Calcular los rangos 
            while($current < $maxPrice) {
                $next = $current + $increment;
                // Mostrar el rango aunque el máximo no lo alcance completamente
                $displayNext = ($next > $maxPrice) ? $next : $next;
                @endphp
                <option value="{{ $current }}-{{ $next }}">
                    ${{ number_format($current, 2) }} - ${{ number_format($displayNext, 2) }}
                </option>
                @php
                $current = $next;
            }
            
            // Opción para "más de" el último rango 
            if($maxPrice > $current - $increment) {
                @endphp
                <option value="{{ $current - $increment }}-{{ $current * 2 }}">
                    Más de ${{ number_format($current, 2) }}
                </option>
                @php
            }
        @endphp
    </select>
     </div>
    </form>
   </div>
   <div class="p-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 mb-4">
<input type="text" id="buscarProducto" class="form-control form-control-sm w-100 w-sm-auto" placeholder="Buscar Producto..." aria-label="Buscar Producto"/>
<select class="form-select form-select-sm w-auto" id="itemsPorPagina" aria-label="Rows per page">
    <option value="7" selected>7</option>
    <option value="10">10</option>
    <option value="15">15</option>
    <option value="25">25</option>
    <option value="50">50</option>
</select>
     <button type="button" class="btn btn-outline-success btn-export-product btn-sm d-flex align-items-center gap-1">
      <i class="fas fa-file-pdf"></i>
      Generar Reporte
     </button>
     <button type="button" class="btn btn-add btn-sm ms-auto ms-sm-0" data-bs-toggle="modal" data-bs-target="#agregarProductoModal">+ Agregar Producto</button>
    </div>
    <div class="table-responsive">
     <table class="table table-borderless align-middle text-secondary-subtle">
    <thead class="bg-light border border-secondary-subtle rounded-2">
        <tr>
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 25%;">
                Producto 
            </th>
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 15%;">
                Categoría 
            </th>
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 10%;">
                Disponibles 
            </th>            
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 10%;">
                Cant. Mínima
            </th>
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 15%;">
                Fecha de Llegada
            </th>
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 15%;">
                Fecha de Caducidad 
            </th>
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 10%;">
                Precio Uni.
            </th>        
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 15%;">
                Acciones 
            </th>
        </tr>
    </thead>
    <tbody class="border border-secondary-subtle rounded-2 bg-white">
        @foreach ($productos as $producto)
        <tr class="@if($producto->estaPorAgotarse()) producto-por-agotarse @endif">
<td class="align-middle" style="width: 25%;">
    <div class="d-flex align-items-center gap-3">
        <img src="{{ asset($producto->imagen) }}" alt="{{ $producto->nombre }}" class="rounded" width="48" height="48" onerror="this.src='https://placehold.co/48x48?text=Imagen'" />
        <div>
            <div class="product-name">{!! $producto->nombreConEstado() !!}</div>
            <div class="product-desc text-muted small">{{ Str::limit($producto->descripcion, 50) }}</div>
        </div>
    </div>
</td>
            <td class="text-center align-middle" style="width: 15%;">
                {{ $producto->categoria->nombre }}
            </td>
            <td class="text-center align-middle" style="width: 10%;">
                {{ $producto->cantidad }}
            </td>
            <td class="text-center align-middle" style="width: 10%;">
                {{ $producto->cantidad_minima }}
            </td>
            <td class="text-center align-middle prod_cad" style="width: 15%;">
                {{ $producto->dia_llegada ? \Carbon\Carbon::parse($producto->dia_llegada)->format('d/m/Y') : 'N/A' }}
            </td>
            <td class="text-center align-middle prod_cad" style="width: 15%;">
                {{ $producto->fecha_caducidad ? \Carbon\Carbon::parse($producto->fecha_caducidad)->format('d/m/Y') : 'N/A' }}
            </td>
            <td class="text-center align-middle" style="width: 10%;">
                ${{ number_format($producto->precio , 2) }}
            </td>
            <td class="text-center align-middle" style="width: 15%;">
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar" data-id="{{ $producto->id }}">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary btn-editar" data-id="{{ $producto->id }}">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted small" id="infoPaginacion">
        Mostrando <span id="desde">0</span> a <span id="hasta">0</span> de <span id="total">0</span> productos
    </div>
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm mb-0" id="paginacion">
            <!-- Los botones de paginación se generarán aquí -->
        </ul>
    </nav>
</div>
    </div>
   </div>

<!-- Modal para agregar producto -->
<div class="modal fade" id="agregarProductoModal" tabindex="-1" aria-labelledby="agregarProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agregarProductoModalLabel">Agregar Nuevo Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAgregarProducto" action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="col-md-4">
                            <label for="imagen" class="form-label">Imagen</label>
                            <input type="file" class="form-control" id="imagen" name="imagen">
                        </div>
                        <div class="col-md-4">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria_id" name="categoria_id" required>
                                <option value="">Seleccione una categoría</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="unidad_medida" class="form-label">Unidad de medida</label>
                            <input type="text" class="form-control" id="unidad_medida" name="unidad_medida">
                        </div>
                        <div class="col-md-3">
                            <label for="cantidad_minima" class="form-label">Cant. Mínima</label>
                            <input type="number" class="form-control" id="cantidad_minima" name="cantidad_minima">
                        </div>
                        <div class="col-md-3">
                            <label for="cantidad" class="form-label">Disponibles</label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" required>
                        </div>
                        <div class="col-md-3">
                            <label for="precio" class="form-label">Precio Unit.</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="dia_llegada" class="form-label">Día de llegada</label>
                            <input type="date" class="form-control" id="dia_llegada" name="dia_llegada">
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_caducidad" class="form-label">Fecha de caducidad</label>
                            <input type="date" class="form-control" id="fecha_caducidad" name="fecha_caducidad">
                        </div>

                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarProducto">Guardar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal para editar producto -->
<div class="modal fade" id="editarProductoModal" tabindex="-1" aria-labelledby="editarProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarProductoModalLabel">Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarProducto">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="edit_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_imagen" class="form-label">Imagen</label>
                            <input type="file" class="form-control" id="edit_imagen" name="imagen">
                            <div class="mt-2">
                                <img id="edit_imagen_preview" src="" alt="Vista previa" style="max-width: 100px; display: none;" class="img-thumbnail">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" id="edit_categoria_id" name="categoria_id" required>
                                <option value="">Seleccione una categoría</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_unidad_medida" class="form-label">Unidad de medida</label>
                            <input type="text" class="form-control" id="edit_unidad_medida" name="unidad_medida">
                        </div>
                        <div class="col-md-3">
                            <label for="edit_cantidad_minima" class="form-label">Cant. Mínima</label>
                            <input type="number" class="form-control" id="edit_cantidad_minima" name="cantidad_minima" required>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_cantidad" class="form-label">Disponibles</label>
                            <input type="number" class="form-control" id="edit_cantidad" name="cantidad" required>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_precio" class="form-label">Precio</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="edit_precio" name="precio" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_dia_llegada" class="form-label">Día de llegada</label>
                            <input type="date" class="form-control" id="edit_dia_llegada" name="dia_llegada">
                        </div>
                        <div class="col-md-3">
                            <label for="edit_fecha_caducidad" class="form-label">Fecha de caducidad</label>
                            <input type="date" class="form-control" id="edit_fecha_caducidad" name="fecha_caducidad">
                        </div>
                        <div class="col-12">
                            <label for="edit_descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnActualizarProducto">Actualizar</button>
            </div>
        </div>
    </div>
</div>

  </div>


<script src="js/productos.js"></script>
<script>
document.querySelector('.btn-export-product').addEventListener('click', function() {
   
    const filtros = {
        categoria_id: document.getElementById('filtroCategoria').value,
        filtroPrecio: document.getElementById('filtroPrecio').value,
        busqueda: document.getElementById('buscarProducto').value
    };

    // Mostrar loader
    const btn = this;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
    btn.disabled = true;

    
    fetch('{{ route("generar.reporte.productos") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(filtros)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Abrir PDF en nueva pestaña
            window.open(data.pdf_url, '_blank');
        } else {
            alert('Error al generar el reporte');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al generar el reporte');
    })
    .finally(() => {
        // Restaurar botón
        btn.innerHTML = '<i class="fas fa-file-pdf"></i> Generar Reporte';
        btn.disabled = false;
    });
});
</script>

@endsection