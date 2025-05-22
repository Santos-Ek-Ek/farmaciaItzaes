<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>@yield('titulo')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <style>
   @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');
   :root {
     --sidebar-width: 16rem;
     --sidebar-collapsed-width: 5rem;
   }
   body {
     font-family: 'Inter', sans-serif;
     overflow-x: hidden;
     height: 100vh;
     margin: 0;
   }
   .wrapper {
     display: flex;
     height: 100vh;
   }
   .sidebar {
     width: var(--sidebar-width);
     background-color: #f3f4f6;
     border-right: 1px solid #d1d5db;
     padding: 1.5rem 1rem;
     display: flex;
     flex-direction: column;
     height: 100vh;
     position: fixed;
     left: 0;
     top: 0;
     overflow-y: auto;
     transition: all 0.3s ease;
     z-index: 1000;
   }
   .main-content {
     margin-left: var(--sidebar-width);
     flex: 1;
     min-height: 100vh;
     overflow-y: auto;
     background-color: #f8f9fc;
     transition: all 0.3s ease;
   }
   .sidebar-collapsed .sidebar {
     width: var(--sidebar-collapsed-width);
   }
   .sidebar-collapsed .main-content {
     margin-left: var(--sidebar-collapsed-width);
   }
   .sidebar-collapsed .sidebar-brand-text,
   .sidebar-collapsed .nav-link-text {
     display: none;
   }
   .sidebar-collapsed .nav-link {
     justify-content: center;
   }
   .sidebar .nav-link {
     color: #4b5563;
     font-size: 0.875rem;
     font-weight: 400;
     display: flex;
     align-items: center;
     gap: 0.75rem;
     padding: 0.5rem;
     border-radius: 0.25rem;
   }
   .sidebar .nav-link:hover, .sidebar .nav-link.active {
     color: #111827;
     background-color: #e5e7eb;
   }
   .user-section {
     margin-top: auto;
     padding-top: 1rem;
   }
   .card {
     border-radius: 0.35rem;
     border: none;
     box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
   }
   .text-gray-800 {
     color: #5a5c69;
   }
   .toggle-sidebar {
     display: none;
     position: fixed;
     top: 1rem;
     left: 1rem;
     z-index: 1040;
     background: #f3f4f6;
     border: 1px solid #d1d5db;
     border-radius: 0.25rem;
     padding: 0.5rem;
   }
   .modal-open .toggle-sidebar {
    display: none;
}
   .user-avatar {
     width: 38px;
     height: 38px;
     border-radius: 50%;
     object-fit: cover;
     border: 2px solid #e5e7eb;
   }
   .main-header {
     padding-left: 3.5rem; /* Espacio para el botón de hamburguesa */
   }
      @media (max-width: 992px) {
     .sidebar {
       transform: translateX(-100%);
       box-shadow: 2px 0 10px rgba(0,0,0,0.1);
       z-index: 1040;
     }
     .sidebar.show {
       transform: translateX(0);
     }
     .main-content {
       margin-left: 0;
     }
     .toggle-sidebar {
       display: block;
       z-index: 1060; 
     }
     .sidebar-header {
       padding-left: 3rem; /* Espacio para el botón cuando el menú está abierto */
     }
     .main-header {
       padding-left: 0;
       margin-left: 3.5rem;
     }
     .sidebar-collapsed .sidebar {
       transform: translateX(-100%);
     }
   }
   
   @media (max-width: 576px) {
     .toggle-sidebar {
       top: 0.5rem;
       left: 0.5rem;
     }
     .sidebar-header {
       padding-left: 2.5rem;
     }
     .main-header {
       margin-left: 2.5rem;
     }
   }
   
   .sidebar-header {
     position: relative;
     transition: padding-left 0.3s ease;
   }
  </style>
</head>
<body>
  <button class="toggle-sidebar" id="toggleSidebar">
    <i class="fas fa-bars"></i>
  </button>

  <div class="wrapper" id="wrapper">
   <aside class="sidebar" id="sidebar">
    <div class="sidebar-content">
     <div class="d-flex justify-content-between align-items-center mb-4 sidebar-header">
      <div class="d-flex align-items-center gap-2">
       <span class="sidebar-brand-text text-primary fw-bold fs-5 m-0">Farmacias Itzaes</span>
      </div>
     </div>
     <nav class="nav flex-column gap-2">
      <a class="nav-link" href="inicio">
       <i class="fas fa-home fa-fw"></i>
       <span class="nav-link-text">Inicio</span>
      </a>
      <a class="nav-link" href="productos">
       <i class="fas fa-pills"></i>
       <span class="nav-link-text">Productos</span>
      </a>      
      <a class="nav-link" href="categorias">
       <i class="fas fa-th-list"></i>
       <span class="nav-link-text">Categorías</span>
      </a>
      <a class="nav-link" href="#">
       <i class="fas fa-book-medical"></i>
       <span class="nav-link-text">Stock</span>
      </a>
      <a class="nav-link" href="#">
       <i class="fas fa-shopping-cart"></i>
       <span class="nav-link-text">Ventas</span>
      </a>
     </nav>
    </div>
    
    <div class="user-section">
     <hr>
     <div class="dropdown">
      <button class="btn user-dropdown dropdown-toggle d-flex align-items-center gap-2 w-100 text-start bg-transparent border-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
       <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nombre) }}&background=2563eb&color=fff" alt="Usuario" class="user-avatar">
       <span class="fw-medium nav-link-text">{{ Auth::user()->nombre }}</span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end w-100" aria-labelledby="userDropdown">
       <li>                
        <form method="POST" action="{{ route('logout') }}">
         @csrf
         <button type="submit" class="dropdown-item text-danger">
          <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
         </button>
        </form>
       </li>
      </ul>
     </div>
    </div>
   </aside>

   <main class="main-content" id="mainContent">
    <div class="container-fluid p-3 p-md-4">
     <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 main-header">
      <h1 class="h3 text-gray-800 font-weight-bold mb-0">
       @yield('titulo')
      </h1>
     </div>
     <hr class="mt-2 mb-3 mb-md-4" style="border-top: 1px solid #e3e6f0;">
     <div class="card shadow-sm">
      <div class="card-body p-3 p-md-4">
       @yield('contenido')
      </div>
     </div>
    </div>
   </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const toggleSidebar = document.getElementById('toggleSidebar');
      const sidebar = document.getElementById('sidebar');
      
      toggleSidebar.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          sidebar.classList.toggle('show');
          
          // Cambiar ícono al abrir/cerrar
          const icon = this.querySelector('i');
          if (sidebar.classList.contains('show')) {
              icon.classList.replace('fa-bars', 'fa-times');
          } else {
              icon.classList.replace('fa-times', 'fa-bars');
          }
      });
      
      document.addEventListener('click', function(e) {
          if (window.innerWidth < 992) {
              const isClickInsideSidebar = sidebar.contains(e.target);
              const isClickOnToggle = toggleSidebar.contains(e.target);
              
              if (!isClickInsideSidebar && !isClickOnToggle) {
                  sidebar.classList.remove('show');
                  toggleSidebar.querySelector('i').classList.replace('fa-times', 'fa-bars');
              }
          }
      });
      
      window.addEventListener('resize', function() {
          if (window.innerWidth >= 992) {
              sidebar.classList.remove('show');
              const icon = toggleSidebar.querySelector('i');
              if (icon.classList.contains('fa-times')) {
                  icon.classList.replace('fa-times', 'fa-bars');
              }
          }
      });
  });
  </script>

</body>
</html>