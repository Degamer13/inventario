<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>REQUISICIÓN {{ $requisicion->n_requisicion }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            margin: 0;
        }
        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 6px 0 8px;
        }
        .logo {
            height: 44px;
            width: auto;
        }
        .header-center {
            text-align: center;
            flex-grow: 1;
        }
        .header-title {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 13px;
            line-height: 1.2;
        }
        .header-sub {
            font-size: 9px;
            text-transform: uppercase;
        }
        .header-right {
            text-align: center;
        }
        .tipo-badge {
            display: inline-block;
            border: 1.5px solid #000;
            padding: 2px 8px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .prioridad-urgente { color: #b91c1c; font-weight: bold; }
        .prioridad-intermedio { color: #b45309; font-weight: bold; }
        .prioridad-bajo { color: #1d4ed8; font-weight: bold; }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 0.6pt solid #000;
            padding: 2px 3px;
            text-align: left;
            vertical-align: top;
            overflow: hidden;
            word-wrap: break-word;
            line-height: 1.25;
        }
        th {
            font-size: 7.5px;
            text-align: center;
            vertical-align: middle;
            text-transform: uppercase;
            line-height: 1.15;
        }
        .hint-text {
            font-size: 6.5px;
            text-align: center;
            color: #444;
            line-height: 1.2;
            vertical-align: middle;
        }
        .cert-hint {
            font-size: 7.5px;
            text-align: center;
            vertical-align: middle;
        }
        .section-title {
            background-color: #e6e6e6;
            font-weight: bold;
            text-align: center;
            padding: 3px;
        }
        .strong-text { font-weight: bold; }
        .align-center { text-align: center; }
        .cell-label {
            display: block;
            font-weight: bold;
            font-size: 8px;
        }
        .cell-value {
            display: block;
            margin-top: 1px;
        }
        .signature-label { font-size: 8px; }
        .firma-area { height: 30px; }
        .empty-row { height: 12px; }
        .item-row { height: 14px; }
        .item-row td { vertical-align: middle; }
    </style>
</head>
<body>

    {{-- ENCABEZADO: LOGO EMPRESA + TITULO + TIPO --}}
    <div class="header-container">
        @if ($logoData)
            <img src="{{ $logoData }}" alt="Logo" class="logo">
        @endif
        <div class="header-center">
            <div class="header-title">REQUISICIÓN DE PROCURA</div>
            <div class="header-sub">00-PC-F-01 REV A</div>
        </div>
        <div class="header-right">
            <div class="tipo-badge">
                @if ($requisicion->tipo === 'sarcos_porras')
                    SARCOS PORRAS
                @else
                    FABWELD
                @endif
            </div>
            <div class="prioridad-{{ $requisicion->prioridad }}" style="margin-top: 2px;">
                PRIORIDAD: {{ strtoupper($requisicion->prioridad_label) }}
            </div>
        </div>
    </div>

    {{-- TABLA PRINCIPAL (formato oficial 00-PC-F-01) --}}
    <table>
        <colgroup>
            <col style="width: 4%;">
            <col style="width: 7%;">
            <col style="width: 8%;">
            <col style="width: 6.5%;">
            <col style="width: 6.5%;">
            <col style="width: 6.5%;">
            <col style="width: 6.5%;">
            <col style="width: 6.5%;">
            <col style="width: 6.5%;">
            <col style="width: 6.5%;">
            <col style="width: 6%;">
            <col style="width: 6%;">
            <col style="width: 12%;">
            <col style="width: 11%;">
        </colgroup>

        {{-- SECCIÓN DATOS --}}
        <tr>
            <td colspan="14" class="section-title">DATOS</td>
        </tr>
        <tr>
            <td colspan="7">
                <span class="cell-label">PROYECTO:</span>
                <span class="cell-value">{{ $requisicion->proyecto ?? '' }}&nbsp;</span>
            </td>
            <td colspan="7">
                <span class="cell-label">FECHA:</span>
                <span class="cell-value">{{ $requisicion->fecha->format('d/m/Y') }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="7">
                <span class="cell-label">CENTRO DE COSTOS:</span>
                <span class="cell-value">{{ $requisicion->centro_costos ?? '' }}&nbsp;</span>
            </td>
            <td colspan="7">
                <span class="cell-label">N° DE LA REQUISICIÓN:</span>
                <span class="cell-value">{{ $requisicion->n_requisicion }}</span>
                <span class="cell-value signature-label">ESTADO: {{ strtoupper($requisicion->estado_label) }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="14">
                <span class="cell-label">UNIDAD SOLICITANTE:</span>
                <span class="cell-value">{{ $requisicion->unidad_solicitante ?? '' }}&nbsp;</span>
            </td>
        </tr>

        {{-- SECCIÓN DESCRIPCIÓN DE LA SOLICITUD --}}
        <tr>
            <td colspan="14" class="section-title">DESCRIPCIÓN DE LA SOLICITUD</td>
        </tr>
        <tr>
            <th class="align-center">ÍTEM</th>
            <th class="align-center">CANTIDAD</th>
            <th class="align-center">UNIDAD DE MEDIDA</th>
            <th colspan="7" class="align-center">DESCRIPCIÓN DE LOS MATERIALES, HERRAMIENTAS, EQUIPOS Y/O SERVICIOS</th>
            <th colspan="2" class="align-center">CERTIFICADO DE CALIDAD</th>
            <th class="align-center">PROVEEDOR SUGERIDO</th>
            <th class="align-center">O/C</th>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="7" class="hint-text">SERVICIOS, TIPO, REFERENCIA, MODELO, CLASE, GRADO, CARACTERÍSTICAS TÉCNICAS, PLANOS, ESQUEMAS, ESPECIFICACIONES, REQUISITOS DEL PROCESO Y CUALQUIER OTRA DESCRIPCIÓN.</td>
            <td class="cert-hint">SI</td>
            <td class="cert-hint">NO</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>

        @php $maxRows = 10; @endphp
        @forelse ($detalles as $detalle)
            <tr class="item-row">
                <td class="align-center">{{ $detalle->item }}</td>
                <td class="align-center">{{ $detalle->cantidad }}</td>
                <td>{{ $detalle->unidad_medida ?? '' }}&nbsp;</td>
                <td colspan="7">
                    {{ $detalle->descripcion }}
                    @if ($detalle->observaciones)
                        <br><span class="signature-label">OBS: {{ $detalle->observaciones }}</span>
                    @endif
                </td>
                <td class="align-center">@if ($detalle->certificado_calidad) X @endif</td>
                <td class="align-center">@if (!$detalle->certificado_calidad) X @endif</td>
                <td>{{ $detalle->proveedor_sugerido ?? '' }}&nbsp;</td>
                <td>{{ $detalle->oc ?? '' }}&nbsp;</td>
            </tr>
        @empty
            <tr class="item-row">
                <td colspan="14" class="align-center">—</td>
            </tr>
        @endforelse

        @for ($j = $detalles->count() + 1; $j <= $maxRows; $j++)
            <tr class="empty-row">
                <td class="align-center">{{ $j }}</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="7">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        @endfor

        {{-- SECCIÓN OBSERVACIONES --}}
        <tr>
            <td colspan="14" class="section-title">OBSERVACIONES</td>
        </tr>
        <tr>
            <td colspan="14" style="height: 24px;">{{ $requisicion->observaciones ?? '' }}</td>
        </tr>
        <tr class="empty-row">
            <td colspan="14">&nbsp;</td>
        </tr>
        <tr class="empty-row">
            <td colspan="14">&nbsp;</td>
        </tr>

        {{-- SECCIÓN DE FIRMAS --}}
        <tr>
            <td colspan="5" class="strong-text">PREPARADO POR</td>
            <td colspan="3" class="strong-text">REVISADO POR</td>
            <td colspan="6" class="strong-text">APROBADO POR</td>
        </tr>
        <tr>
            <td colspan="5">
                <span class="signature-label">NOMBRE: {{ $requisicion->preparadoPor->name ?? '' }}</span><br>
                <span class="signature-label">FECHA: {{ $requisicion->fecha->format('d/m/Y') }}</span>
            </td>
            <td colspan="3">
                <span class="signature-label">NOMBRE: {{ $requisicion->verificadoPor->name ?? $requisicion->revisadoPor->name ?? '' }}</span><br>
                <span class="signature-label">FECHA:
                    @if ($requisicion->verificada_at) {{ $requisicion->verificada_at->format('d/m/Y') }}
                    @elseif ($requisicion->aprobada_at) {{ $requisicion->aprobada_at->format('d/m/Y') }}
                    @elseif ($requisicion->rechazada_at) {{ $requisicion->rechazada_at->format('d/m/Y') }} @endif
                </span>
            </td>
            <td colspan="6">
                <span class="signature-label">NOMBRE: {{ $requisicion->revisadoPor->name ?? '' }}</span><br>
                <span class="signature-label">FECHA:
                    @if ($requisicion->aprobada_at) {{ $requisicion->aprobada_at->format('d/m/Y') }}
                    @elseif ($requisicion->rechazada_at) {{ $requisicion->rechazada_at->format('d/m/Y') }} @endif
                </span>
            </td>
        </tr>
        <tr class="firma-area">
            <td colspan="5">
                <span class="signature-label">FIRMA: _________________________</span>
            </td>
            <td colspan="3">
                <span class="signature-label">FIRMA: _________________________</span>
            </td>
            <td colspan="6">
                <span class="signature-label">FIRMA: _________________________</span>
            </td>
        </tr>
    </table>

</body>
</html>
