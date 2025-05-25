<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Venta #{{ $numero_venta }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 10px; }
        .title { font-size: 16px; font-weight: bold; }
        .info { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; text-align: right; }
        .footer { margin-top: 15px; text-align: center; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">FARMACIA ITZAES</div>
    </div>

    <div class="info">
        <div><strong>Venta #:</strong> {{ $numero_venta }}</div>
        <div><strong>Fecha:</strong> {{ $fecha }}</div>
        <div><strong>Hora:</strong> {{ $hora }}</div>
        <div><strong>Atendido por:</strong> {{ $usuario }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cant.</th>
                <th>P. Unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
            <tr>
                <td>{{ $producto['nombre'] }} - {{ $producto['unidad_medida'] }}</td>
                <td>{{ $producto['cantidad'] }}</td>
                <td>${{ number_format($producto['precio'], 2) }}</td>
                <td>${{ number_format($producto['subtotal'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <strong>Total: ${{ number_format($subtotal, 2) }}</strong>
    </div>

    <div class="info-pago">
        <div><strong>Método de pago:</strong> {{ ucfirst($metodo_pago) }}</div>
        @if($metodo_pago === 'efectivo')
        <div><strong>Monto recibido:</strong> ${{ number_format($monto_recibido, 2) }}</div>
        <div><strong>Cambio:</strong> ${{ number_format($cambio, 2) }}</div>
        @endif
    </div>

    <div class="footer">
        Gracias por su compra<br>
        ¡Vuelva pronto!
    </div>
</body>
</html>