@extends('layout.layout')
@section('contenido')
@section('titulo','Inicio')

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
 </head>
 <style>

.circle-chart {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto;
}

.circle-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.legend-color {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 3px;
    flex-shrink: 0;
}

/* En móviles */
@media (max-width: 576px) {
    .card {
        overflow: hidden;
    }
    
    ul {
        justify-content: flex-start !important;
        flex-wrap: wrap !important;
    }
    
    li {
        flex-basis: calc(50% - 10px); 
    }
}
 </style>
 <body>
  <div class="container py-4">
   <div class="row g-4">
    <!-- Card 1 -->
<div class="col-12 col-sm-6">
    <div class="card p-3 p-sm-4"> 
        <div class="d-flex justify-content-between align-items-start mb-3 mb-sm-4">
            <h3 class="h6 fw-semibold text-secondary m-0">Ventas de hoy</h3>
            <span class="badge bg-primary">{{ $fechaActual }}</span>
        </div>
        
        @if($productosMasVendidos->count() > 0)
            <div class="circle-chart mb-3 mb-sm-4">
                <svg width="120" height="120" viewBox="0 0 160 160" xmlns="http://www.w3.org/2000/svg" class="mx-auto">
                    <circle class="circle-bg" cx="80" cy="80" r="70"></circle>
                    @foreach($productosMasVendidos as $index => $producto)
                        @php
                            $percentage = ($producto->total_vendido / $productosMasVendidos->sum('total_vendido')) * 100;
                            $offset = 25 * $index;
                        @endphp
                        <circle class="circle-segment" 
                                cx="80" cy="80" r="70" 
                                stroke="{{ ['#4e73df', '#1cc88a', '#36b9cc'][$index] }}"
                                stroke-width="10" 
                                stroke-dasharray="{{ $percentage }} 100"
                                stroke-dashoffset="{{ $offset }}"
                                fill="none"></circle>
                    @endforeach
                </svg>
                <div class="circle-text">
                    <div class="percent">{{ $productosMasVendidos->sum('total_vendido') }}</div>
                    <div class="label">Ventas totales</div>
                </div>
            </div>
            
            <ul class="list-unstyled d-flex flex-wrap justify-content-center gap-2 gap-sm-3 mt-3 text-muted small mb-0">
                @foreach($productosMasVendidos as $index => $producto)
                    <li class="d-flex align-items-center flex-shrink-0 px-2">
                        <span class="legend-color flex-shrink-0" style="background-color: {{ ['#4e73df', '#1cc88a', '#36b9cc'][$index] }}"></span>
                        <span class="text-truncate ms-1" style="max-width: 150px;" title="{{ $producto->producto_nombre }} ({{ $producto->total_vendido }})">
                            {{ Str::limit($producto->producto_nombre, 50) }} ({{ $producto->total_vendido }})
                        </span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="alert alert-info py-2">No hay ventas hoy</div>
        @endif
    </div>
</div>

    <!-- Card 2 -->
<div class="col-12 col-sm-6">
    <div class="card p-4 d-flex flex-column flex-sm-row align-items-center align-items-sm-start">
        <div class="flex-grow-1" style="max-width: 50%;">
            <h2 class="h5 fw-semibold text-secondary mb-1">Ganancias del día</h2>
            <p class="text-muted small mb-4">Total de ventas hoy</p>
            <p class="text-primary fw-bold display-6 mb-1">${{ $totalGanancias }}</p>
            <p class="text-muted small mb-4">Fecha: {{ $fechaActual }}</p>
        </div>
    </div>
</div>
   </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
  </script>

@endsection
