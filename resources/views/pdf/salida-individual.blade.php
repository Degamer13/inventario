<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SALIDA N° {{ $salida->n_control }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5px;
            margin: 20px 25px;
        }
        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .logo {
            width: 90px;
            height: auto;
        }
        .header-title {
            text-align: center;
            flex-grow: 1;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: left;
            vertical-align: top;
            text-transform: uppercase;
        }
        .section-title {
            background-color: #e6e6e6;
            font-weight: bold;
            text-align: center;
            padding: 2px;
        }
        .signature-table {
            margin-top: 0;
        }
        .signature-label {
            font-size: 8px;
        }
        .firma-area {
            height: 35px;
            padding: 3px 4px;
        }
        .firma-line-text {
            width: 100px;
            text-align: center;
            display: inline-block;
            margin-left: 5px;
            font-size: 8px;
        }
        .strong-text {
            font-weight: bold;
        }
        .align-right {
            text-align: right;
        }
        .align-center {
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO CON LOGO Y TÍTULO --}}
    <div class="header-container">
        <img src="{{ public_path('logo.jpg') }}" class="logo" alt="Logo">

    </div>

    {{-- === TABLA PRINCIPAL === --}}
    <table>
        <tr>
            <td style="width: 35%"><span class="strong-text">PROYECTO:</span> {{ $salida->proyecto }}</td>
            <td style="width: 15%"><span class="strong-text">AÑO:</span> {{ $salida->ano }}</td>
            <td style="width: 35%"><span class="strong-text">N° CONTROL:</span> {{ $salida->n_control }}</td>
            <td style="width: 15%"><span class="strong-text">FECHA:</span> {{ \Carbon\Carbon::parse($salida->fecha)->format('d/m/Y') }}</td>
        </tr>

        <tr><td colspan="4" class="section-title">INFORMACIÓN DEL TRASLADO</td></tr>
        <tr>
            <td colspan="2"><span class="strong-text">ORIGEN:</span> {{ $salida->origen }}</td>
            <td colspan="2"><span class="strong-text">DESTINO:</span> {{ $salida->destino }}</td>
        </tr>

        <tr><td colspan="4" class="section-title">INFORMACIÓN DE LOS MATERIALES, HERRAMIENTAS Y/O EQUIPOS</td></tr>
        <tr>
            <th style="width: 5%;" class="align-center">ÍTEM</th>
            <th style="width: 10%;" class="align-center">CANT.</th>
            <th style="width: 15%;" class="align-center">UNIDAD</th>
            <th style="width: 70%;">DESCRIPCIÓN</th>
        </tr>

        @foreach ($detalles as $i => $detalle)
            <tr>
                <td class="align-center">{{ $i + 1 }}</td>
                <td class="align-center">{{ $detalle->cantidad_salida }}</td>
                <td>{{ $detalle->unidad_medida ?? '' }}</td>
                <td>{{ $detalle->descripcion }}</td>
            </tr>
        @endforeach

        @php $min_rows = 13; @endphp
        @for ($j = count($detalles) + 1; $j <= $min_rows; $j++)
            <tr>
                <td class="align-center">{{ $j }}</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        @endfor

        <tr>
            <td colspan="4"><span class="strong-text">OBSERVACIONES:</span> {{ $salida->observaciones ?? '' }}</td>
        </tr>
    </table>

    {{-- SECCIÓN DE FIRMAS DE TRASLADO --}}
    <table class="signature-table">
        <tr>
            <td style="width: 30%;"><span class="strong-text">ENTREGADO POR:</span></td>
            <td style="width: 40%;"><span class="strong-text">NOMBRE Y APELLIDO:</span> {{ $salida->entregadoPor->name ?? '---' }}</td>
            <td style="width: 30%;"><span class="strong-text">FIRMA:</span> <span class="firma-line-text">&nbsp;</span></td>
        </tr>
        <tr>
            <td class="signature-label">(ALMACÉN PRINCIPAL)</td>
            <td class="signature-label">&nbsp;</td>
            <td class="signature-label">&nbsp;</td>
        </tr>

        <tr>
            <td><span class="strong-text">RECIBIDO POR:</span></td>
            <td><span class="strong-text">NOMBRE Y APELLIDO:</span> {{ $salida->recibidoPor->name ?? '---' }}</td>
            <td><span class="strong-text">FIRMA:</span> <span class="firma-line-text">&nbsp;</span></td>
        </tr>
        <tr>
            <td class="signature-label">(LOGÍSTICA)</td>
            <td class="signature-label">&nbsp;</td>
            <td class="signature-label">&nbsp;</td>
        </tr>
    </table>

    {{-- CONTROL EN PROYECTO EXTERNO --}}
    <table class="signature-table" style="margin-top: 10px;">
        <tr>
            <td colspan="3" class="section-title">CONTROL EN PROYECTO EXTERNO</td>
        </tr>
        <tr>
            <td style="width: 20%;"><span class="strong-text">CONFORME</span></td>
            <td style="width: 20%;"><span class="strong-text">NO CONFORME</span></td>
            <td style="width: 60%;"><span class="strong-text">EXPLIQUE:</span></td>
        </tr>
        <tr class="firma-area">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>

        <tr>
            <td style="width: 30%; font-weight: bold;">RECIBIDO POR:<br><span class="signature-label">(ALMACENISTA GRAL)</span></td>
            <td style="width: 40%;"><span class="strong-text">NOMBRE Y APELLIDO:</span> {{ $salida->recibidoPorAlmacen->nombre ?? '---' }}</td>
            <td style="width: 30%;"><span class="strong-text">FIRMA:</span> <span class="firma-line-text">&nbsp;</span></td>
        </tr>

        <tr>
            <td style="font-weight: bold;">REVISADO POR:<br><span class="signature-label">(LOGÍSTICA)</span></td>
            <td><span class="strong-text">NOMBRE Y APELLIDO:</span> {{ $salida->revisadoPor->nombre ?? '---' }}</td>
            <td><span class="strong-text">FIRMA:</span> <span class="firma-line-text">&nbsp;</span></td>
        </tr>

        <tr>
            <td style="font-weight: bold;">APROBADO POR:<br><span class="signature-label">(ALMACÉN PRINCIPAL)</span></td>
            <td><span class="strong-text">NOMBRE Y APELLIDO:</span> {{ $salida->aprobadoPor->nombre ?? '---' }}</td>
            <td><span class="strong-text">FIRMA:</span> <span class="firma-line-text">&nbsp;</span></td>
        </tr>
    </table>

</body>
</html>
