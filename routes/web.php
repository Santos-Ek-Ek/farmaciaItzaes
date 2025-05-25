<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentasController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('inicio', function () {
    return view('content.inicio');
});

Route::get('ventas', [VentasController::class, 'index'])->name('ventas.index');
Route::get('/obtener-productos', [VentasController::class, 'obtenerProductos']);

Route::post('/procesar-venta', [VentasController::class, 'procesarVenta']);
Route::get('/ventas/ticket/{numeroVenta}', [VentasController::class, 'generarTicketVenta'])
     ->name('ventas.ticket');

Route::get('categorias', [CategoriaController::class, 'index'])->name('categorias.index');
Route::get('productos', [ProductoController::class, 'index'])->name('productos.index');
Route::post('productos-agregar', [ProductoController::class, 'store'])->name('productos.store');
Route::get('/productos/{id}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
Route::put('/productos-eliminar/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');

Route::post('categorias-agregar', [CategoriaController::class, 'store'])->name('categorias.store');
Route::put('categorias-eliminar/{id}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
Route::get('categorias/{id}/edit', [CategoriaController::class, 'edit'])->name('categorias.edit');
Route::put('categorias/{id}', [CategoriaController::class, 'update'])->name('categorias.update');

Route::get('registro',[AuthController::class, 'verRegistro'])->name('registro');
Route::get('login',[AuthController::class, 'verInicioSesion'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/register', [AuthController::class, 'register'])->name('register');