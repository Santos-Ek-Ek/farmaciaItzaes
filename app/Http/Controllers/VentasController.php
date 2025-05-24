<?php

namespace App\Http\Controllers;

use App\Models\Categorias;
use App\Models\Productos;
use Illuminate\Http\Request;

class VentasController extends Controller
{
    public function index(){
        $productos = Productos::where('activo', 1)->get();
        return view('content.ventas', compact('productos'));
    }

public function obtenerProductos() {
    $productos = Productos::where('productos.activo', 1)
        ->select([
            'productos.id',
            'productos.nombre',  // Especifica la tabla para nombre
            'productos.unidad_medida',
            'productos.cantidad',
            'productos.descripcion',
            'productos.precio',
            'productos.imagen',
            'categorias.nombre AS categoria',  // Usa un alias claro
            'categorias.id AS categoria_id'  // También para el ID de categoría
        ])
        ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
        ->get();
    
    return response()->json($productos);
}
}
