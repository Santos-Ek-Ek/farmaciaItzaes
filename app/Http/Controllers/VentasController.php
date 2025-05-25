<?php

namespace App\Http\Controllers;

use App\Models\Categorias;
use App\Models\Productos;
use App\Models\Ventas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Psy\Command\WhereamiCommand;

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
        ->where('cantidad', '>', '0')
        ->where('fecha_caducidad','>', now())
        ->get();
    
    return response()->json($productos);
}

    public function procesarVenta(Request $request)
    {
        // Validación básica
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0',
            'productos.*.subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'monto_recibido' => 'nullable|required_if:metodo_pago,efectivo|numeric|min:0'
        ]);

        // Generar número de venta único
        $numeroVenta = $this->generarNumeroVentaUnico();
        $fechaVenta = now()->toDateString();
        $usuarioId = auth()->id();
        $totalCalculado = collect($request->productos)->sum('subtotal');

        // Validar que el total coincida
        if (abs($totalCalculado - $request->total) > 0.01) {
            return response()->json([
                'success' => false,
                'error' => 'El total no coincide con la suma de los productos'
            ], 400);
        }

        // Validar monto recibido para efectivo
        if ($request->metodo_pago === 'efectivo' && $request->monto_recibido < $request->total) {
            return response()->json([
                'success' => false,
                'error' => 'El monto recibido es menor al total de la venta'
            ], 400);
        }

        DB::beginTransaction();

        try {
            foreach ($request->productos as $producto) {
                $productoDB = Productos::find($producto['id']);
                
                // Validar stock
                if ($productoDB->cantidad < $producto['cantidad']) {
                    throw new \Exception("Stock insuficiente para: {$productoDB->nombre}");
                }

                // Crear registro de venta
                Ventas::create([
                    'producto_id' => $producto['id'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio'],
                    'subtotal' => $producto['subtotal'],
                    'numero_venta' => $numeroVenta,
                    'usuario_id' => $usuarioId,
                    'fecha_venta' => $fechaVenta,
                    'metodo_pago' => $request->metodo_pago
                ]);
                
                // Actualizar stock
                $productoDB->decrement('cantidad', $producto['cantidad']);
            }

            // Aquí podrías registrar también el pago en una tabla de pagos si es necesario
            DB::commit();

            return response()->json([
                'success' => true,
                'numero_venta' => $numeroVenta,
                'fecha_venta' => $fechaVenta,
                'total_venta' => $totalCalculado,
                'metodo_pago' => $request->metodo_pago,
                'cambio' => $request->metodo_pago === 'efectivo' ? $request->monto_recibido - $totalCalculado : 0
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function generarNumeroVentaUnico()
    {
        $prefix = 'VTA-' . now()->format('Ymd') . '-';
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $numeroVenta = $prefix . Str::upper(Str::random(4));
            $existe = Ventas::where('numero_venta', $numeroVenta)->exists();
            $attempt++;
        } while ($existe && $attempt < $maxAttempts);

        return $attempt >= $maxAttempts 
            ? 'VTA-' . now()->format('YmdHis') . '-' . Str::random(2)
            : $numeroVenta;
    }

}
