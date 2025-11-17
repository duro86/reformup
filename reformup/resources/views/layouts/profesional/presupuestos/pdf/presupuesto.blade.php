<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Presupuesto #{{ $presupuestoNumero ?? '' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2, h3 { margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f3f3f3; text-align: left; }
        .text-end { text-align: right; }
        .total-row th, .total-row td { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Presupuesto</h1>

    <p>
        <strong>Profesional:</strong> {{ $profesional->empresa ?? '' }}<br>
        <strong>Cliente:</strong>
        {{ $solicitud->cliente->nombre ?? '' }}
        {{ $solicitud->cliente->apellidos ?? '' }}<br>
        <strong>Obra:</strong> {{ $solicitud->titulo }}<br>
        <strong>Ubicación:</strong>
        {{ $solicitud->ciudad }}
        {{ $solicitud->provincia ? ' - ' . $solicitud->provincia : '' }}
    </p>

    <h3>Detalle del presupuesto</h3>

    <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="text-end">Cantidad</th>
                <th class="text-end">Precio €/u</th>
                <th class="text-end">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lineas as $linea)
                <tr>
                    <td>{{ $linea['concepto'] }}</td>
                    <td class="text-end">{{ $linea['cantidad'] }}</td>
                    <td class="text-end">
                        {{ number_format($linea['precio'], 2, ',', '.') }} €
                    </td>
                    <td class="text-end">
                        {{ number_format($linea['importe'], 2, ',', '.') }} €
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-end">Subtotal</td>
                <td class="text-end">
                    {{ number_format($subtotal, 2, ',', '.') }} €
                </td>
            </tr>
            <tr class="total-row">
                <td colspan="3" class="text-end">IVA ({{ $ivaPorcentaje }}%)</td>
                <td class="text-end">
                    {{ number_format($ivaImporte, 2, ',', '.') }} €
                </td>
            </tr>
            <tr class="total-row">
                <td colspan="3" class="text-end">Total con IVA</td>
                <td class="text-end">
                    {{ number_format($total, 2, ',', '.') }} €
                </td>
            </tr>
        </tfoot>
    </table>

    @if(!empty($notas))
        <h3>Notas</h3>
        <p>{!! nl2br(e($notas)) !!}</p>
    @endif
</body>
</html>
