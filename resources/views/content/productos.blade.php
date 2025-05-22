@extends('layout.layout')
@section('contenido')
@section('titulo', 'Productos')

  <link rel="stylesheet" href="{{ asset('css/productos.css') }}">

  <div class="container-fluid p-0 bg-white rounded shadow border border-secondary-subtle">
   <div class="p-4 border-bottom border-secondary-subtle">
    <h2 class="fw-semibold text-secondary mb-3" style="font-size: 0.875rem;">Filtros</h2>
    <form class="row g-3">
     <div class="col-12 col-sm-4 col-md-3 col-lg-2">
      <select class="form-select" aria-label="Category">
       <option selected>Categoría</option>
       <option>Shoes</option>
       <option>Electronics</option>
       <option>Accessories</option>
      </select>
     </div>
     <div class="col-12 col-sm-4 col-md-3 col-lg-2">
      <select class="form-select" aria-label="Stock">
       <option selected>Precio</option>
       <option>In Stock</option>
       <option>Out of Stock</option>
      </select>
     </div>
    </form>
   </div>
   <div class="p-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 mb-4">
     <input type="text" class="form-control form-control-sm w-100 w-sm-auto" placeholder="Buscar Producto" aria-label="Buscar Producto"/>
     <select class="form-select form-select-sm w-auto" aria-label="Rows per page">
      <option selected>7</option>
      <option>10</option>
      <option>15</option>
     </select>
     <button type="button" class="btn btn-export btn-sm d-flex align-items-center gap-1">
      <i class="fas fa-upload"></i>
      Exportar
      <i class="fas fa-chevron-down" style="font-size: 8px;"></i>
     </button>
     <button type="button" class="btn btn-add btn-sm ms-auto ms-sm-0">+ Agregar Producto</button>
    </div>
    <div class="table-responsive">
     <table class="table table-borderless align-middle text-secondary-subtle">
      <thead class="bg-light border border-secondary-subtle rounded-2">
       <tr>
        <th class="text-uppercase fw-semibold" style="font-size: 0.625rem; cursor:pointer;">
         Producto <i class="fas fa-sort-up"></i>
        </th>
        <th class="text-uppercase fw-semibold" style="font-size: 0.625rem; cursor:pointer;">
         Categoría <i class="fas fa-sort-up"></i>
        </th>
        <th class="text-uppercase fw-semibold" style="font-size: 0.625rem; cursor:pointer;">
         Disponibles <i class="fas fa-sort-up"></i>
        </th>
        <th class="text-uppercase fw-semibold" style="font-size: 0.625rem; cursor:pointer;">
         Código <i class="fas fa-sort-up"></i>
        </th>
        <th class="text-uppercase fw-semibold" style="font-size: 0.625rem; cursor:pointer;">
         Precio <i class="fas fa-sort-up"></i>
        </th>
       </tr>
      </thead>
      <tbody class="border border-secondary-subtle rounded-2 bg-white">
       <!-- Row 1 -->
       <tr>
        <td>
         <div class="d-flex align-items-center gap-3">
          <img src="https://placehold.co/32x32/png?text=AJ" alt="Red and black Air Jordan basketball shoe side view" class="rounded" width="32" height="32"/>
          <div>
           <div class="product-name">Air Jordan</div>
           <div class="product-desc">Air Jordan is a line of basketball shoes produced by Nike</div>
          </div>
         </div>
        </td>
        <td>
         <div class="d-flex align-items-center gap-2">
          <div class="icon-circle icon-shoes">
           <i class="fas fa-walking"></i>
          </div>
          Shoes
         </div>
        </td>
        <td class="text-center">
         <label class="switch" aria-label="Stock toggle Air Jordan">
          <input type="checkbox" disabled/>
          <span class="slider"></span>
         </label>
        </td>
        <td class="text-center text-secondary fw-monospace" style="font-size: 0.75rem;">31063</td>
        <td class="text-center text-secondary fw-monospace" style="font-size: 0.75rem;">942</td>
       </tr>
       <!-- Row 2 -->
       <tr>
        <td>
         <div class="d-flex align-items-center gap-3">
          <img src="https://placehold.co/32x32/png?text=AF" alt="Amazon Fire TV device with blue screen" class="rounded" width="32" height="32"/>
          <div>
           <div class="product-name">Amazon Fire TV</div>
           <div class="product-desc">4K UHD smart TV, stream live TV without cable</div>
          </div>
         </div>
        </td>
        <td>
         <div class="d-flex align-items-center gap-2">
          <div class="icon-circle icon-electronics">
           <i class="fas fa-mobile-alt"></i>
          </div>
          Electronics
         </div>
        </td>
        <td class="text-center">
         <label class="switch" aria-label="Stock toggle Amazon Fire TV">
          <input type="checkbox" disabled/>
          <span class="slider"></span>
         </label>
        </td>
        <td class="text-center text-secondary fw-monospace" style="font-size: 0.75rem;">5829</td>
        <td class="text-center text-secondary fw-monospace" style="font-size: 0.75rem;">587</td>
       </tr>
       <!-- Row 3 -->
       <tr>
        <td>
         <div class="d-flex align-items-center gap-3">
          <img src="https://placehold.co/32x32/png?text=IP" alt="Apple iPad front view with colorful screen" class="rounded" width="32" height="32"/>
          <div>
           <div class="product-name">Apple iPad</div>
           <div class="product-desc">10.2-inch Retina Display, 64GB</div>
          </div>
         </div>
        </td>
        <td>
         <div class="d-flex align-items-center gap-2">
          <div class="icon-circle icon-electronics">
           <i class="fas fa-mobile-alt"></i>
          </div>
          Electronics
         </div>
        </td>
        <td class="text-center">
         <label class="switch" aria-label="Stock toggle Apple iPad">
          <input type="checkbox" checked/>
          <span class="slider"></span>
         </label>
        </td>
        <td class="text-center text-secondary fw-monospace" style="font-size: 0.75rem;">35946</td>
        <td class="text-center text-secondary fw-monospace" style="font-size: 0.75rem;">468</td>
       </tr>
       <!-- Row 4 -->
       <tr>
        <td>
         <div class="d-flex align-items-center gap-3">
          <img src="https://placehold.co/32x32/png?text=AW" alt="Apple Watch Series 7 with Starlight Sport Band" class="rounded" width="32" height="32"/>
          <div>
           <div class="product-name">Apple Watch Series 7</div>
           <div class="product-desc">Starlight Aluminum Case with Starlight Sport Band.</div>
          </div>
         </div>
        </td>
        <td>
         <div class="d-flex align-items-center gap-2">
          <div class="icon-circle icon-accessories">
           <i class="fas fa-coins"></i>
          </div>
          Accessories
         </div>
        </td>
        <td class="text-center">
         <label class="switch" aria-label="Stock toggle Apple Watch Series 7">
          <input type="checkbox" disabled/>
          <span class="slider"></span>
         </label>
        </td>
        <td class="text-center text-secondary fw-monospace" style="font-size: 0.75rem;">46658</td>
        <td class="text-center text-secondary fw-monospace" style="font-size: 0.75rem;">851</td>
       </tr>
       <!-- Row 5 -->
       <tr>
        <td>
         <div class="d-flex align-items-center gap-3">
          <img src="https://placehold.co/32x32/png?text=BG" alt="BANGE Anti Theft Backpack in gray color" class="rounded" width="32" height="32"/>
          <div>
           <div class="product-name">BANGE Anti Theft Backpack</div>
           <div class="product-desc">Smart Business Laptop Fits 15.6 Inch Notebook</div>
          </div>
         </div>
        </td>
        <td>
         <div class="d-flex align-items-center gap-2">
          <div class="icon-circle icon-accessories">
           <i class="fas fa-coins"></i>
          </div>
          Accessories
         </div>
        </td>
        <td class="text-center">
         <label class="switch" aria-label="Stock toggle BANGE Anti Theft Backpack">
          <input type="checkbox" checked/>
          <span class="slider"></span>
         </label>
        </td>
        <td class="text-center text-secondary fw-monospace" style="font-size: 0.75rem;">41867</td>
        <td class="text-center text-secondary fw-monospace" style="font-size: 0.75rem;">519</td>
       </tr>
      </tbody>
     </table>
    </div>
   </div>
  </div>

@endsection