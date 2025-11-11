<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Salida #{{ $salida->n_control }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .section { margin-bottom: 15px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>

    <h1>Salida de Inventario</h1>
    <h2>#{{ $salida->n_control }}</h2>

    <div class="section">
        <p><span class="label">Proyecto:</span> {{ $salida->proyecto ?? '-' }}</p>
        <p><span class="label">Año:</span> {{ $salida->ano ?? '-' }}</p>
        <p><span class="label">Fecha:</span> {{ \Carbon\Carbon::parse($salida->fecha)->format('d/m/Y') }}</p>
        <p><span class="label">Origen:</span> {{ $salida->origen ?? '-' }}</p>
        <p><span class="label">Destino:</span> {{ $salida->destino ?? '-' }}</p>
        <p><span class="label">Observaciones:</span> {{ $salida->observaciones ?? '-' }}</p>
    </div>

    <div class="section">
        <p><span class="label">Entregado por:</span> {{ $salida->entregadoPor->name ?? '-' }} ({{ $salida->entregadoPor->cargo ?? '-' }})</p>
        <p><span class="label">Recibido por:</span> {{ $salida->recibidoPor->name ?? '-' }} ({{ $salida->recibidoPor->cargo ?? '-' }})</p>
    </div>

    <div class="section">
        <h2>Detalles de la Salida</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tipo de Ítem</th>
                    <th>Serial / Placa</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Unidad de Medida</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detalles as $index => $detalle)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $detalle->item_tipo)) }}</td>
                        <td>{{ $detalle->item_serial_placa }}</td>
                        <td>{{ $detalle->descripcion }}</td>
                        <td>{{ $detalle->cantidad_salida }}</td>
                        <td>{{ $detalle->unidad_medida ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>
