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


Route::post('categorias', [CategoriaController::class, 'store'])->name('categorias.store');

Route::get('registro',[AuthController::class, 'verRegistro'])->name('registro');
Route::get('login',[AuthController::class, 'verInicioSesion'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/register', [AuthController::class, 'register'])->name('register');