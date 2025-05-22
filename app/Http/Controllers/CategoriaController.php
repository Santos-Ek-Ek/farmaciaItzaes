<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorias;

class CategoriaController extends Controller
{
public function index(Request $request)
{
    $perPage = $request->input('per_page', 7);
    
    // Si se selecciona "TODOS" (valor 0), obtenemos todos los registros sin paginación
    $query = Categorias::where('activo', 1);
    
    if ($perPage == 0) {
        $categorias = $query->get();
    } else {
        $categorias = $query->paginate($perPage);
        // Mantener el parámetro per_page en los links de paginación
        $categorias->appends(['per_page' => $perPage]);
    }
    
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

public function edit($id)
{
    $categoria = Categorias::findOrFail($id);
    return response()->json($categoria);
}

public function update(Request $request, $id)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string'
    ]);

    $categoria = Categorias::findOrFail($id);
    $categoria->update([
        'nombre' => $request->nombre,
        'descripcion' => $request->descripcion
    ]);

    return redirect()->route('categorias.index')
                     ->with('success', 'Categoría actualizada correctamente');
}


public function destroy($id)
{
    $categoria = Categorias::findOrFail($id);
    $categoria->activo = 0;
    $categoria->save();
    
    return redirect()->route('categorias.index')
                     ->with('success', 'Categoría desactivada correctamente');
}
}