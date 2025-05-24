@extends('layout.layout')
@section('contenido')
@section('titulo', 'Ventas')

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

   <div class="p-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 mb-4">
<div class="position-relative form-control-sm w-100 w-sm-auto">
    <input type="text" id="buscarProducto" class="form-control form-control-sm w-100 w-sm-auto" placeholder="Buscar Producto..." aria-label="Buscar Producto"/>
    <div id="resultadosBusqueda" class="dropdown-menu w-100" style="display: none;"></div>
</div>
<select class="form-select form-select-sm w-auto" id="itemsPorPagina" aria-label="Rows per page">
    <option value="7" selected>7</option>
    <option value="10">10</option>
    <option value="15">15</option>
    <option value="25">25</option>
    <option value="50">50</option>
</select>
     <button type="button" class="btn btn-export btn-sm d-flex align-items-center gap-1">
      <i class="fas fa-upload"></i>
      Exportar
      <i class="fas fa-chevron-down" style="font-size: 8px;"></i>
     </button>
    </div>
    <div class="table-responsive">
            <div class="d-flex justify-content-end mb-2 p-2 bg-light rounded">
        <div class="d-flex align-items-center gap-3">
            <span class="fw-bold">Total General:</span>
            <span id="totalGeneral" class="fs-5 fw-bold text-primary">$0.00</span>
        </div>
    </div>
     <table class="table table-borderless align-middle text-secondary-subtle">
    <thead class="bg-light border border-secondary-subtle rounded-2">
        <tr>
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 25%;">
                Producto 
            </th>
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 15%;">
                Unidad de Medida 
            </th>
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 10%;">
                Categoría 
            </th>            
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 10%;">
                Cantidad
            </th>
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 10%;">
                Subtotal
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






  </div>


<script src="js/ventas.js"></script>

@endsection