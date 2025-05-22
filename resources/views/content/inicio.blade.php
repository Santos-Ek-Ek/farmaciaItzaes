@extends('layout.layout')
@section('contenido')
@section('titulo','Inicio')

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
 </head>
 <body>
  <div class="container py-4">
   <div class="row g-4">
    <!-- Card 1 -->
    <div class="col-12 col-sm-6">
     <div class="card p-4 d-flex flex-column flex-sm-row align-items-center align-items-sm-start">
      <div class="flex-grow-1" style="max-width: 50%;">
       <h2 class="h5 fw-semibold text-secondary mb-1">Congratulations John!</h2>
       <p class="text-muted small mb-4">Best seller of the month</p>
       <p class="text-primary fw-bold display-6 mb-1">$89k</p>
       <p class="text-muted small mb-4">You have done 57.6% more sales today.</p>
       <button class="btn btn-blue btn-sm text-white px-3 py-1 fw-semibold" type="button">View sales</button>
      </div>
      <img alt="Golden trophy with number 1 on it, surrounded by blue leaves and gold coins with confetti" class="mt-4 mt-sm-0 ms-sm-4" height="120" src="https://storage.googleapis.com/a1aa/image/199c4fbf-31ef-4c94-1b57-89a81bb4991f.jpg" width="120"/>
     </div>
    </div>
    <!-- Card 2 -->
    <div class="col-12 col-sm-6">
     <div class="card p-4">
      <div class="d-flex justify-content-between align-items-start mb-4">
       <h3 class="h6 fw-semibold text-secondary m-0">Visits of 2022</h3>
       <i class="fas fa-ellipsis-v text-muted" style="cursor:pointer;"></i>
      </div>
      <div class="circle-chart">
       <svg width="160" height="160" viewBox="0 0 160 160" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
        <circle class="circle-bg" cx="80" cy="80" r="70"></circle>
        <circle class="circle-outer" cx="80" cy="80" r="70"></circle>
        <circle class="circle-middle" cx="80" cy="80" r="60"></circle>
        <circle class="circle-inner" cx="80" cy="80" r="50"></circle>
       </svg>
       <div class="circle-text">
        <div class="percent">80%</div>
        <div class="label">Total Visits</div>
       </div>
      </div>
      <ul class="list-unstyled d-flex justify-content-center gap-4 mt-4 text-muted small mb-0">
       <li class="d-flex align-items-center">
        <span class="legend-color legend-target"></span> Target
       </li>
       <li class="d-flex align-items-center">
        <span class="legend-color legend-mart"></span> Mart
       </li>
       <li class="d-flex align-items-center">
        <span class="legend-color legend-ebay"></span> Ebay
       </li>
      </ul>
     </div>
    </div>
   </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
  </script>

@endsection
