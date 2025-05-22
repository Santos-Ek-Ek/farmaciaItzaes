<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;

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
Route::get('productos', function () {
    return view('content.productos');
});
Route::get('categorias', [CategoriaController::class, 'index'])->name('categorias.index');


Route::post('categorias-agregar', [CategoriaController::class, 'store'])->name('categorias.store');
Route::put('categorias-eliminar/{id}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
Route::get('categorias/{id}/edit', [CategoriaController::class, 'edit'])->name('categorias.edit');
Route::put('categorias/{id}', [CategoriaController::class, 'update'])->name('categorias.update');

Route::get('registro',[AuthController::class, 'verRegistro'])->name('registro');
Route::get('login',[AuthController::class, 'verInicioSesion'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/register', [AuthController::class, 'register'])->name('register');