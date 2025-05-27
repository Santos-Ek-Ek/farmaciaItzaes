<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ticket #{{ $numero_venta }}</title>
    <style>
        @page { 
            size: 80mm auto; 
            margin: 0; 
            padding: 0;
        }
        body { 
            font-family: 'Arial Narrow', Arial, sans-serif; 
            font-size: 9px;
            width: 76mm;
            margin: 0 auto;
            padding: 1mm;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .header, .footer { 
            text-align: center; 
            padding: 0 2mm;
        }
        .header { 
            margin-bottom: 2mm;
            padding-bottom: 1mm;
            border-bottom: 1px dashed #ccc;
        }
        .empresa-nombre { 
            font-weight: bold; 
            font-size: 11px;
            line-height: 1.2;
            margin-bottom: 1mm;
        }
        .ticket-title { 
            font-weight: bold; 
            text-align: center; 
            margin: 1mm 0;
            font-size: 10px;
        }
        .info-venta { 
            margin-bottom: 2mm;
            padding: 0 1mm;
        }
        .info-venta div { 
            margin-bottom: 0.5mm;
            line-height: 1.2;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 1mm 0;
            table-layout: fixed;
        }
        th { 
            text-align: left; 
            border-bottom: 1px dashed #000; 
            padding: 0.5mm 0;
            font-size: 8.5px;
        }
        td { 
            padding: 0.5mm 0;
            vertical-align: top;
            font-size: 8.5px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total { 
            font-weight: bold; 
            border-top: 1px dashed #000; 
            padding-top: 1mm;
            margin-top: 1mm;
        }
        .metodo-pago { 
            margin-top: 1mm;
            padding: 0 1mm;
        }
        .footer { 
            margin-top: 2mm;
            font-size: 7px;
            line-height: 1.2;
            padding-top: 1mm;
            border-top: 1px dashed #ccc;
        }
        /* Columnas específicas */
        td:nth-child(1) { width: 45%; padding-right: 1mm; }
        td:nth-child(2) { width: 15%; }
        td:nth-child(3) { width: 20%; }
        td:nth-child(4) { width: 20%; }
        

        .cut-line {
            text-align: center;
            font-size: 8px;
            margin-top: 2mm;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="empresa-nombre">FARMACIA ITZAES</div>
    </div>

    <div class="ticket-title">COMPROBANTE DE VENTA</div>

    <div class="info-venta">
        <div><strong>No.:</strong> {{ $numero_venta }}</div>
        <div><strong>Fecha:</strong> {{ $fecha }} {{ $hora }}</div>
        <div><strong>Cliente:</strong> CONSUMIDOR FINAL</div>
        <div><strong>Atendido por:</strong> {{ $usuario }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th class="text-right">Cant.</th>
                <th class="text-right">P. Unit.</th>
                <th class="text-right">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
            <tr>
                <td>{{ substr($producto['nombre'], 0, 30) }}{{ strlen($producto['nombre']) > 30 ? '...' : '' }} {{ $producto['unidad_medida'] }}</td>
                <td class="text-right">{{ $producto['cantidad'] }}</td>
                <td class="text-right">{{ number_format($producto['precio'], 2) }}</td>
                <td class="text-right">{{ number_format($producto['subtotal'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-right total">
        TOTAL: ${{ number_format($subtotal, 2) }}
    </div>

    <div class="metodo-pago">
        <div><strong>Método de pago:</strong> {{ strtoupper($metodo_pago) }}</div>
        @if($metodo_pago === 'efectivo')
        <div><strong>Efectivo recibido:</strong> ${{ number_format($monto_recibido, 2) }}</div>
        <div><strong>Cambio:</strong> ${{ number_format($cambio, 2) }}</div>
        @endif
    </div>

    <div class="footer">
        ¡Gracias por su compra!<br>
        FARMACIA ITZAES<br>
    </div>


    <div class="cut-line">•••••••••••••••</div>
</body>
</html>