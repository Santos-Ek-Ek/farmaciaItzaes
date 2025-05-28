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
            'productos.nombre',
            'productos.unidad_medida',
            'productos.cantidad',
            'productos.descripcion',
            'productos.precio',
            'productos.imagen',
            'categorias.nombre AS categoria', 
            'categorias.id AS categoria_id'  
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

    // PDF ticket 80mm - solo para visualización
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


public function verpdfindividual(Request $request)
{
    // Configuración de paginación
    $porPagina = $request->input('porPagina', 10);
    $pagina = $request->input('pagina', 1);
    $porPaginaInventario = $request->input('porPaginaInventario', 10);
    $paginaInventario = $request->input('paginaInventario', 1);

    // Procesar Ventas Individuales
    $rutaVentas = public_path('Ventas_individual');
    $archivosVentas = [];
    $totalArchivosVentas = 0;
    $totalPaginasVentas = 1;

    if (file_exists($rutaVentas)) {
        $todosArchivosVentas = array_diff(scandir($rutaVentas), ['.', '..']);
        usort($todosArchivosVentas, function($a, $b) use ($rutaVentas) {
            return filemtime($rutaVentas.'/'.$b) - filemtime($rutaVentas.'/'.$a);
        });

        $offset = ($pagina - 1) * $porPagina;
        $archivosVentas = array_slice($todosArchivosVentas, $offset, $porPagina);
        
        $totalArchivosVentas = count($todosArchivosVentas);
        $totalPaginasVentas = ceil($totalArchivosVentas / $porPagina);
    }

    // Procesar Reportes de Ventas (Inventario)
    $rutaInventario = public_path('Reporte_ventas');
    $archivosInventario = [];
    $totalArchivosInventario = 0;
    $totalPaginasInventario = 1;

    if (file_exists($rutaInventario)) {
        $todosArchivosInventario = array_diff(scandir($rutaInventario), ['.', '..']);
        usort($todosArchivosInventario, function($a, $b) use ($rutaInventario) {
            return filemtime($rutaInventario.'/'.$b) - filemtime($rutaInventario.'/'.$a);
        });

        $offsetInventario = ($paginaInventario - 1) * $porPaginaInventario;
        $archivosInventario = array_slice($todosArchivosInventario, $offsetInventario, $porPaginaInventario);
        
        $totalArchivosInventario = count($todosArchivosInventario);
        $totalPaginasInventario = ceil($totalArchivosInventario / $porPaginaInventario);
    }

    return view('content.historial', [
        'archivos' => $archivosVentas,
        'totalArchivos' => $totalArchivosVentas,
        'paginaActual' => $pagina,
        'porPagina' => $porPagina,
        'totalPaginas' => $totalPaginasVentas,
        'archivosInventario' => $archivosInventario,
        'totalArchivosInventario' => $totalArchivosInventario,
        'paginaActualInventario' => $paginaInventario,
        'porPaginaInventario' => $porPaginaInventario,
        'totalPaginasInventario' => $totalPaginasInventario
    ]);
}

public function eliminarReporte(Request $request)
{
    $archivo = $request->input('archivo');
    $tipo = $request->input('tipo', 'ventas'); 
    
    $ruta = $tipo === 'inventario' 
        ? public_path('Reporte_ventas/'.$archivo)
        : public_path('Ventas_individual/'.$archivo);
    
    if (file_exists($ruta)) {
        if (unlink($ruta)) {
            return response()->json(['success' => true]);
        }
    }
    
    return response()->json(['success' => false, 'message' => 'El archivo no existe o no se pudo eliminar']);
}

public function obtenerTodosReportes()
{
    // Obtener archivos de Ventas Individuales
    $directorioVentas = public_path('Ventas_individual');
    $archivosVentas = glob($directorioVentas . '/*.pdf');
    
    $resultadosVentas = [];
    foreach ($archivosVentas as $archivo) {
        $nombreArchivo = basename($archivo);
        $timestamp = filemtime($archivo);
        $fechaModificacion = date("d/m/Y H:i", $timestamp);
        
        $resultadosVentas[] = [
            'archivo' => $nombreArchivo,
            'fecha' => $fechaModificacion,
            'timestamp' => $timestamp,
            'tipo' => 'ventas'
        ];
    }

    // Obtener archivos de Reportes de Ventas (Inventario)
    $directorioInventario = public_path('Reporte_ventas');
    $archivosInventario = glob($directorioInventario . '/*.pdf');
    
    $resultadosInventario = [];
    foreach ($archivosInventario as $archivo) {
        $nombreArchivo = basename($archivo);
        $timestamp = filemtime($archivo);
        $fechaModificacion = date("d/m/Y H:i", $timestamp);
        
        $resultadosInventario[] = [
            'archivo' => $nombreArchivo,
            'fecha' => $fechaModificacion,
            'timestamp' => $timestamp,
            'tipo' => 'inventario'
        ];
    }

    
    $resultados = array_merge($resultadosVentas, $resultadosInventario);
    usort($resultados, function($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });

    return response()->json($resultados);
}

public function generarReporteVenta(Request $request)
{
    $request->validate([
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        'tipo' => 'required|in:pdf,excel'
    ]);

    try {
        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        $ventas = DB::select("
            SELECT 
                p.total, 
                v.fecha_venta, 
                v.numero_venta,
                v.cantidad, 
                v.subtotal, 
                pr.nombre
            FROM pagos p
            INNER JOIN ventas v ON p.numero_venta = v.numero_venta
            INNER JOIN productos pr ON v.producto_id = pr.id
            WHERE v.fecha_venta BETWEEN ? AND ?
            ORDER BY numero_venta
        ", [$fechaInicio, $fechaFin]);

        if (empty($ventas)) {
            throw new \Exception("No hay datos para el rango de fechas seleccionado");
        }

        $totalGeneralResult = DB::selectOne("
            SELECT SUM(subtotal) as total_general 
            FROM ventas 
            WHERE fecha_venta BETWEEN ? AND ?  
        ", [$fechaInicio, $fechaFin]);

        $totalGeneral = $totalGeneralResult->total_general ?? 0;

        if ($request->tipo === 'pdf') {
            // Crear directorio si no existe
            $directory = public_path('Reportes');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Generar nombre único para el archivo
            $filename = 'reporte_ventas_'.now()->format('Y-m-d').'.pdf';
            $filepath = $directory.'/'.$filename;

            // Generar y guardar el PDF
            $pdf = PDF::loadView('reportes.ventas_pdf', [
                'ventas' => $ventas,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
                'totalGeneral' => $totalGeneral
            ]);
            
            $pdf->save($filepath);

           
            return response()->json([
                'success' => true,
                'url' => asset('Reportes/'.$filename)
            ]);

        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

}
