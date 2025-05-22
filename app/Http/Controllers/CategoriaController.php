<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorias;

class CategoriaController extends Controller
{
    public function index(){
        $categorias = Categorias::where('activo', 1)->get();
        return view('content.categorias', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
        ]);

        $categoria = new Categorias();
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;
        $categoria->activo = 1;

        $categoria->save();

        return redirect()->back()
                         ->with('success', 'Categoría creada exitosamente');
    }
}
