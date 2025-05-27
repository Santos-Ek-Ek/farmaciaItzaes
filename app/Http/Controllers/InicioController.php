<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pagos;
use App\Models\Ventas;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InicioController extends Controller
{
public function index()
    {
        $fechaHoy = Carbon::today()->toDateString();
        
        
        $ventasDelDia = Pagos::select(
                'pagos.total',
                'ventas.fecha_venta',
                'ventas.numero_venta'
            )
            ->join('ventas', 'pagos.numero_venta', '=', 'ventas.numero_venta')
            ->whereDate('ventas.fecha_venta', $fechaHoy)
            ->groupBy('ventas.numero_venta', 'pagos.total', 'ventas.fecha_venta')
            ->get();
            
        // Calcula el total sumando todos los pagos agrupados
        $totalGanancias = $ventasDelDia->sum('total');


    
    // Obtener los 3 productos más vendidos del día
    $productosMasVendidos = DB::table('ventas')
        ->select(
            'productos.id',
            'productos.nombre as producto_nombre',
            DB::raw('COUNT(ventas.producto_id) as total_vendido')
        )
        ->join('productos', 'ventas.producto_id', '=', 'productos.id')
        ->whereDate('ventas.fecha_venta', $fechaHoy)
        ->groupBy('productos.id', 'productos.nombre')
        ->orderByDesc('total_vendido')
        ->limit(3)
        ->get();
        
        return view('content.inicio', [
            'ventasDelDia' => $ventasDelDia,
            'productosMasVendidos' => $productosMasVendidos,
            'totalGanancias' => number_format($totalGanancias, 2),
            'fechaActual' => Carbon::parse($fechaHoy)->format('d/m/Y')
        ]);
    }
}
