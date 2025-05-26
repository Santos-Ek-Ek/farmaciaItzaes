<?php

namespace App\Http\Controllers;

use App\Models\Categorias;
use App\Models\Pagos;
use App\Models\Productos;
use App\Models\Ventas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Psy\Command\WhereamiCommand;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'productos.*.unidad_medida' => 'required|string',
            'total' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'monto_recibido' => 'nullable|required_if:metodo_pago,efectivo|numeric|min:0'
        ]);

        // Generar número de venta único
        $numeroVenta = $this->generarNumeroVentaUnico();
        $fechaVenta = now()->toDateString();
        $usuarioId = auth()->id();
        $totalCalculado = collect($request->productos)->sum('subtotal');
        $usuarioNombre = auth()->user()->nombre . ' ' . auth()->user()->apellidos;
        $horaVenta = now()->toTimeString();

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
            $productosVendidos = [];
            foreach ($request->productos as $producto) {
                $productoDB = Productos::find($producto['id']);
                
                // Validar stock
                if ($productoDB->cantidad < $producto['cantidad']) {
                    throw new \Exception("Stock insuficiente para: {$productoDB->nombre}");
                }

                            // Guardar datos para el PDF
            $productosVendidos[] = [
                'nombre' => $productoDB->nombre,
                'cantidad' => $producto['cantidad'],
                'precio' => $producto['precio'],
                'subtotal' => $producto['subtotal'],
                'unidad_medida' =>$producto['unidad_medida']
            ];

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
            Pagos::create([
                'numero_venta'=>$numeroVenta,
                'total' => $totalCalculado,
            'monto_recibido' => $request->metodo_pago === 'efectivo' ? $request->monto_recibido : null,
            'cambio' => $request->metodo_pago === 'efectivo' ? $request->monto_recibido - $totalCalculado : null
            ]);

            // Aquí podrías registrar también el pago en una tabla de pagos si es necesario
            DB::commit();
                    $data = [
            'numero_venta' => $numeroVenta,
            'fecha' => $fechaVenta,
            'hora' => $horaVenta,
            'usuario' => $usuarioNombre,
            'productos' => $productosVendidos,
            'subtotal' => $totalCalculado,
            'metodo_pago' => $request->metodo_pago,
            'monto_recibido' => $request->metodo_pago === 'efectivo' ? $request->monto_recibido : null,
            'cambio' => $request->metodo_pago === 'efectivo' ? $request->monto_recibido - $totalCalculado : null
        ];

    // PDF normal - guardar en public/Ventas_individual
    $pdfNormal = Pdf::loadView('ventas.ticket', $data);
    $pdfNormalPath = public_path("Ventas_individual/venta_{$numeroVenta}.pdf");
    $pdfNormal->save($pdfNormalPath);

    // PDF ticket 80mm - solo generarlo para visualización
    $pdfTicket = Pdf::loadView('ventas.ticket_80mm', $data)
                  ->setPaper([0, 0, 226.77, 800], 'portrait');
    $pdfTicketContent = $pdfTicket->output();

            return response()->json([
                'success' => true,
                'numero_venta' => $numeroVenta,
                'fecha_venta' => $fechaVenta,
                'total_venta' => $totalCalculado,
                'metodo_pago' => $request->metodo_pago,
                'cambio' => $request->metodo_pago === 'efectivo' ? $request->monto_recibido - $totalCalculado : 0,
'pdf_ticket' => base64_encode($pdfTicketContent),
        'pdf_normal_path' => "/Ventas_individual/venta_{$numeroVenta}.pdf"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }


public function generarTicketVenta($numeroVenta)
{
    // Obtener datos de la venta
    $venta = Ventas::where('numero_venta', $numeroVenta)
                ->with('producto')
                ->get();
                
    $pago = Pagos::where('numero_venta', $numeroVenta)->first();
    
    if ($venta->isEmpty()) {
        abort(404, 'Venta no encontrada');
    }

    // Preparar datos para el ticket
    $data = [
        'numero_venta' => $numeroVenta,
        'fecha' => $venta->first()->fecha_venta,
        'hora' => $venta->first()->created_at->format('H:i:s'),
        'usuario' => $venta->first()->usuario->name,
        'productos' => $venta->map(function($item) {
            return [
                'nombre' => $item->producto->nombre,
                'cantidad' => $item->cantidad,
                'precio' => $item->precio_unitario,
                'subtotal' => $item->subtotal
            ];
        }),
        'subtotal' => $pago->total,
        'metodo_pago' => $pago->metodo_pago,
        'monto_recibido' => $pago->monto_recibido,
        'cambio' => $pago->cambio,
        'empresa' => [
            'nombre' => 'FARMACIA ITZAES',
        ]
    ];

    // Configurar PDF para tamaño ticket (80mm)
    $pdf = Pdf::loadView('ventas.ticket_80mm', $data)
              ->setPaper([0, 0, 226.77, 800], 'portrait'); // 80mm ~ 226.77pt

    return $pdf->stream("ticket_{$numeroVenta}.pdf");
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
