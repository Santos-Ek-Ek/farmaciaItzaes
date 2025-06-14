<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css?v=1.0.3" rel="stylesheet" />
    <style>
        @media (max-width: 768px) {
            .form-control-lg {
                font-size: 16px;
                padding: 12px 15px;
                height: auto;
            }
            .card {
                margin: 0 10px;
                border-radius: 0.5rem !important;
                border: 1px solid #dee2e6 !important;
            }
            .btn {
                padding: 12px !important;
                font-size: 16px !important;
            }
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
        }
        .pharmacy-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
<section class="min-vh-100 mb-8">
    <div class="page-header align-items-start min-vh-50 pb-11 mx-3 border-radius-lg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 text-center mx-auto">
                    <h1 class="text-black mb-2 mt-5">Bienvenido!</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row mt-lg-n10 mt-md-n11 mt-n10 justify-content-center">
            <div class="col-sm-10 col-md-8 col-lg-6 col-xl-4 mx-auto">
                <div class="card z-index-0" style="border: 1px solid #dee2e6; border-radius: 0.5rem;">
                    <div class="card-header text-center pt-4">
                        <div class="pharmacy-name">Farmacia Itzaes</div>
                        <h5>Regístrate</h5>
                    <span><small>Nota: Use este registro solo para administradores</small></span>
                    </div>
                    <div class="card-body px-3 px-sm-4">
                        <form role="form text-left" method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="mb-3">
                                <input type="text" class="form-control form-control-lg" placeholder="Nombre" name="nombre" id="nombre"
                                    aria-label="Nombre" aria-describedby="nombre" value="{{ old('nombre') }}">
                                @error('nombre')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>                            
                            <div class="mb-3">
                                <input type="text" class="form-control form-control-lg" placeholder="Apellidos" name="apellido" id="apellido"
                                    aria-label="Apellido" aria-describedby="apellido" value="{{ old('apellido') }}">
                                @error('apellido')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control form-control-lg" placeholder="Email" name="email" id="email"
                                    aria-label="Email" aria-describedby="email-addon" value="{{ old('email') }}">
                                @error('email')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <input type="telefono" class="form-control form-control-lg" placeholder="Telefono" name="telefono" id="telefono"
                                    aria-label="Telefono" aria-describedby="telefono-addon" value="{{ old('telefono') }}">
                                @error('telefono')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control form-control-lg" placeholder="Contraseña" name="password"
                                    id="password" aria-label="Password" aria-describedby="password-addon">
                                @error('password')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>                              
                            <div class="mb-3">
                                <input type="password" class="form-control form-control-lg" placeholder="Confirmar contraseña" name="password_confirmation"
                                    id="password_confirmation" aria-label="Password" aria-describedby="password-addon">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2 py-3">Registrarse</button>
                            </div>
                            <p class="text-sm mt-3 mb-0 text-center">¿Ya tienes una cuenta? <a href="login"
                                    class="text-dark font-weight-bolder">Iniciar Sesión</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</body>
</html>