<?php

namespace App\Http\Controllers;

use App\Models\Categorias;
use App\Models\Productos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ProductoController extends Controller
{
public function index()
{
    $categorias = Categorias::where('activo', 1)->get();
    
    $productos = Productos::with('categoria')
        ->where('activo', 1)
        ->orderByRaw('
            CASE 
                WHEN fecha_caducidad IS NULL THEN 1
                WHEN fecha_caducidad <= CURDATE() THEN 2
                ELSE 0
            END
        ')
        ->orderBy('fecha_caducidad', 'ASC')
        ->get();
    
    return view('content.productos', compact('categorias', 'productos'));
}



public function store(Request $request)
{
    // Validación de los datos del formulario
    $validatedData = $request->validate([
        'nombre' => 'required|string|max:255',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'cantidad' => 'required|numeric|min:0',
        'cantidad_minima' => 'required|numeric|min:0',
        'precio' => 'required|numeric|min:0',
        'dia_llegada' => 'nullable|date',
        'fecha_caducidad' => 'nullable|date|after_or_equal:today',
        'categoria_id' => 'required|exists:categorias,id',
        'unidad_medida' => 'nullable|string|max:50',
        'descripcion' => 'nullable|string',
    ]);

    try {
        // Procesar la imagen si se subió
        $imagenNombre = null;
        if ($request->hasFile('imagen')) {
            $directorio = public_path('imgProductos');
            if (!File::exists($directorio)) {
                File::makeDirectory($directorio, 0755, true);
            }

            $extension = $request->file('imagen')->getClientOriginalExtension();
            $imagenNombre = 'producto_'.time().'.'.$extension;
            $request->file('imagen')->move($directorio, $imagenNombre);
        }

        // Crear el nuevo producto
        $producto = Productos::create([
            'nombre' => $validatedData['nombre'],
            'imagen' => $imagenNombre ? 'imgProductos/'.$imagenNombre : null,
            'cantidad' => $validatedData['cantidad'],
            'cantidad_minima' => $validatedData['cantidad_minima'],
            'precio' => $validatedData['precio'],
            'dia_llegada' => $validatedData['dia_llegada'],
            'fecha_caducidad' => $validatedData['fecha_caducidad'],
            'categoria_id' => $validatedData['categoria_id'],
            'unidad_medida' => $validatedData['unidad_medida'],
            'descripcion' => $validatedData['descripcion'],
            'activo' => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto creado exitosamente',
            'producto' => $producto,
            'imagen_url' => $imagenNombre ? asset('imgProductos/'.$imagenNombre) : null
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al crear el producto: ' . $e->getMessage()
        ], 500);
    }
}

public function edit($id)
{
    $producto = Productos::findOrFail($id);
    return response()->json([
        'success' => true,
        'producto' => $producto
    ]);
}

public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'nombre' => 'required|string|max:255',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'cantidad' => 'required|numeric|min:0',
        'cantidad_minima' => 'required|numeric|min:0',
        'precio' => 'required|numeric|min:0',
        'dia_llegada' => 'nullable|date',
        'fecha_caducidad' => 'nullable|date|after_or_equal:today',
        'categoria_id' => 'required|exists:categorias,id',
        'unidad_medida' => 'nullable|string|max:50',
        'descripcion' => 'nullable|string',
    ]);

    try {
        $producto = Productos::findOrFail($id);
        
        // Procesar la imagen si se subió una nueva
        if ($request->hasFile('imagen')) {
            // Eliminar la imagen anterior si existe
            if ($producto->imagen && file_exists(public_path($producto->imagen))) {
                unlink(public_path($producto->imagen));
            }
            
            $directorio = public_path('imgProductos');
            if (!File::exists($directorio)) {
                File::makeDirectory($directorio, 0755, true);
            }

            $extension = $request->file('imagen')->getClientOriginalExtension();
            $imagenNombre = 'producto_'.time().'.'.$extension;
            $request->file('imagen')->move($directorio, $imagenNombre);
            $validatedData['imagen'] = 'imgProductos/'.$imagenNombre;
        }

        $producto->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado exitosamente'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar el producto: ' . $e->getMessage()
        ], 500);
    }
}

public function destroy($id)
{
    try {
        $producto = Productos::findOrFail($id);
        
        // Marcar como inactivo en lugar de eliminar
        $producto->update(['activo' => 0]);
        
        return response()->json([
            'success' => true,
            'message' => 'Producto desactivado exitosamente'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al desactivar el producto: ' . $e->getMessage()
        ], 500);
    }
}


public function generarReporte(Request $request)
{
    // Obtener los productos con los mismos filtros que la vista
    $productos = Productos::query()
        ->with('categoria')
        ->when($request->categoria_id, function($query, $categoria_id) {
            return $query->where('categoria_id', $categoria_id);
        })
        ->when($request->filtroPrecio, function($query, $rangoPrecio) {
            $precios = explode('-', $rangoPrecio);
            if(count($precios) == 2) {
                return $query->whereBetween('precio', [$precios[0], $precios[1]]);
            }
            return $query;
        })
        ->when($request->busqueda, function($query, $busqueda) {
            return $query->where('nombre', 'like', "%{$busqueda}%");
        })
        ->where('activo' , 1)
        ->orderBy('nombre')
        ->get();

    // Productos por agotarse
    $porAgotarse = $productos->filter(fn($p) => $p->estaPorAgotarse());
    $responsable = auth()->user()->nombre. ' '. auth()->user()->apellidos;
    
    // Datos para el reporte
    $data = [
        'encargado' => $responsable,
        'fecha' => Carbon::now()->format('d/m/Y H:i:s'),
        'productos' => $productos,
        'porAgotarse' => $porAgotarse,
        'totalProductos' => $productos->count(),
        'totalPorAgotarse' => $porAgotarse->count(),
        'totalValorInventario' => $productos->sum(fn($p) => $p->precio * $p->cantidad),
    ];

    // Generar PDF
    $pdf = PDF::loadView('reportes.inventario', $data);

    $pdf->setPaper('A4', 'landscape');
    $pdf->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => 'sans-serif'
    ]);
    
    // Nombre del archivo
    $filename = 'reporte_inventario_'.Carbon::now()->format('d-m-Y').'.pdf';
    
    // Ruta de almacenamiento 
    $publicPath = public_path('Reporte_ventas/');
    
    // Crear directorio si no existe
    if (!file_exists($publicPath)) {
        mkdir($publicPath, 0777, true);
    }
    
    // Guardar el archivo en public/Reporte_ventas
    $pdf->save($publicPath.$filename);
    
    // URL para acceder al archivo
    $pdfUrl = url('Reporte_ventas/'.$filename);
    
    // Redireccionar a la vista del PDF en nueva pestaña
    return response()->json([
        'success' => true,
        'pdf_url' => $pdfUrl
    ]);
}
}
