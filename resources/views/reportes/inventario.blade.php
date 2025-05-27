<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Inventario</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .warning { background-color: #fff3cd; }
        .summary { margin-top: 20px; padding: 10px; background-color: #f8f9fa; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Inventario</h1>
        <p>Generado el: {{ $fecha }}</p>
        <p>Responsable: {{ $encargado }}</p>
    </div>

    @if($porAgotarse->count() > 0)
    <div class="summary">
        <h3>Productos por agotarse: {{ $totalPorAgotarse }}</h3>
    </div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Disponibles</th>
                <th>Cant. Mínima</th>
                <th>Fecha Llegada</th>
                <th>Fecha Caducidad</th>
                <th>Precio Unit.</th>
                <th>Valor Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
            <tr class="@if($producto->estaPorAgotarse()) warning @endif">
                <td>{{ $producto->nombre }}</td>
                <td>{{ $producto->categoria->nombre }}</td>
                <td class="text-center">{{ $producto->cantidad }}</td>
                <td class="text-center">{{ $producto->cantidad_minima }}</td>
                <td class="text-center">
                    {{ $producto->dia_llegada ? \Carbon\Carbon::parse($producto->dia_llegada)->format('d/m/Y') : 'N/A' }}
                </td>
                <td class="text-center">
                    {{ $producto->fecha_caducidad ? \Carbon\Carbon::parse($producto->fecha_caducidad)->format('d/m/Y') : 'N/A' }}
                </td>
                <td class="text-right">${{ number_format($producto->precio, 2) }}</td>
                <td class="text-right">${{ number_format($producto->precio * $producto->cantidad, 2) }}</td>
                <td>
                    @if($producto->cantidad == 0)
                        <span style="color: #dc3545; font-weight: bold;">AGOTADO</span>
                    @elseif($producto->estaPorAgotarse())
                        <span style="color: #ffc107; font-weight: bold;">Por agotarse</span>
                    @else
                        <span style="color: #28a745;">Disponible</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-right"><strong>Total valor inventario:</strong></td>
                <td class="text-right"><strong>${{ number_format($totalValorInventario, 2) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>