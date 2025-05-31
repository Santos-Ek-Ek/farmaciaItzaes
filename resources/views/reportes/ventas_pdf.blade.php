<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Reporte de Ventas</h2>
        <p>Del {{ date('d/m/Y', strtotime($fechaInicio)) }} al {{ date('d/m/Y', strtotime($fechaFin)) }}</p>
    </div>

    @if(!empty($ventas))
    <table class="table">
        <thead>
            <tr>
                <th>N° Venta</th>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $currentVenta = null; @endphp
            @foreach($ventas as $venta)
                <tr>
                    <td>{{ $venta->numero_venta }}</td>
                    <td>{{ date('d/m/Y', strtotime($venta->fecha_venta)) }}</td>
                    <td>{{ $venta->nombre }} - {{ $venta->unidad_medida }}</td>
                    <td class="text-center">{{ $venta->cantidad }}</td>
                    <td class="text-right">${{ number_format($venta->subtotal, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3"></td>
                <th class="text-right">Total General:</th>
                <th class="text-right">${{ number_format($totalGeneral, 2) }}</th>
            </tr>
        </tbody>
    </table>
    @else
    <p>No se encontraron ventas en el período seleccionado</p>
    @endif
</body>
</html>