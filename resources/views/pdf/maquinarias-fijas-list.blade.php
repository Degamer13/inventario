<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Maquinarias Fijas</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #555;
            font-size: 20px;
        }
        .date-header {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 10px;
            color: #777;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #444;
            font-size: 13px;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="date-header">
        Fecha de Emisión: <?php echo date('d/m/Y'); ?>
    </div>
    <h1>Lista de Maquinarias Fijas</h1>

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Modelo</th>
                <th>Color</th>
                <th>Marca</th>
                <th>Serial</th>
                <th>Código</th>
                <th>Cantidad</th>
                <th>Ubicación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($maquinarias as $m)
                <tr>
                    <td>{{ $m->descripcion }}</td>
                    <td>{{ $m->modelo }}</td>
                    <td>{{ $m->color }}</td>
                    <td>{{ $m->marca }}</td>
                    <td>{{ $m->serial }}</td>
                    <td>{{ $m->codigo }}</td>
                    <td>{{ $m->cantidad }}</td>
                    <td>{{ $m->ubicacion }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 10;
            $pageWidth = $pdf->get_width();
            $pageHeight = $pdf->get_height();
            $x = ($pageWidth - $fontMetrics->get_text_width($text, $font, $size)) / 2;
            $y = $pageHeight - 20;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
