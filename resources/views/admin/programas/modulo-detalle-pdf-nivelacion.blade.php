<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $modulo->nombre }} - Nivelación</title>
    <style>
        @page { margin: 165px 35px 55px 35px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #333; margin: 0; }

        /* Cabecera fija: se replica en cada página */
        .pdf-header {
            position: fixed;
            top: -140px; left: 0; right: 0;
            height: 130px;
        }
        .pdf-header .header-tabla { width: 100%; border-collapse: collapse; }
        .pdf-header .logo-cell { width: 85px; vertical-align: middle; text-align: center; }
        .pdf-header .logo-cell img { width: 68px; height: auto; }
        .pdf-header .titulo-cell { vertical-align: middle; text-align: center; padding: 0 8px; }
        .pdf-header h1 { font-size: 12px; margin: 0 0 3px; text-transform: uppercase; }
        .pdf-header h2 { font-size: 10.5px; margin: 0 0 3px; text-transform: uppercase; }
        .pdf-header .modulo-linea { font-size: 10px; font-weight: 700; margin: 0 0 3px; }
        .pdf-header .meta { font-size: 8.5px; color: #777; margin: 0; }
        .pdf-header .barra { border-bottom: 4px solid #000; margin-top: 8px; }

        /* Pie fijo con número de página */
        .pdf-footer {
            position: fixed;
            bottom: -38px; left: 0; right: 0;
            height: 28px;
            font-size: 7.5px; color: #999; text-align: center;
            border-top: 1px solid #ccc; padding-top: 4px;
        }
        .pdf-footer .pagenum:before { content: "Página " counter(page); }

        table.notas { width: 100%; border-collapse: collapse; }
        table.notas th {
            background: #1f2d50; color: #fff;
            font-size: 8px; text-transform: uppercase; letter-spacing: .03em;
            padding: 6px 4px; border: 1px solid #000;
            text-align: center; font-weight: 700;
        }
        table.notas td { padding: 5px 4px; border: 1px solid #000; }
        table.notas tr { page-break-inside: avoid; }
        .text-center { text-align: center; }
        .fw-semibold { font-weight: 600; }
        .literal { font-size: 7.5px; text-transform: uppercase; }
        .bg-soft-success { background: #d4edda; }
        .bg-soft-danger { background: #f8d7da; }
        .text-success { color: #155724; }
        .text-danger { color: #721c24; }
        .text-muted { color: #999; }
    </style>
</head>
<body>
    @php
        $tituloPrograma = \Illuminate\Support\Str::startsWith(mb_strtolower(trim($programa->nombre)), mb_strtolower(trim($programa->tipo_nombre)))
            ? $programa->nombre
            : $programa->tipo_nombre . ' ' . $programa->nombre;
    @endphp
    <div class="pdf-header">
        <table class="header-tabla"><tr>
            <td class="logo-cell">
                <img src="{{ public_path('backend/assets/images/logo_universidad.png') }}" alt="Universidad">
            </td>
            <td class="titulo-cell">
                <h1>Notas Nivelación</h1>
                <h2>{{ $tituloPrograma }} ({{ $programa->gestion }})</h2>
                <p class="modulo-linea">
                    {{ $modulo->nombre }}
                    @if($modulo->docente_apellidos)
                        · Docente: {{ $modulo->docente_apellidos }}, {{ $modulo->docente_nombres }}
                    @endif
                </p>
                <p class="meta">
                    @if(!empty($programa->codigo))Código: {{ $programa->codigo }} | @endif
                    Gestión: {{ $programa->gestion }} |
                    Participantes: {{ $inscritosCount }} |
                    Generado: {{ date('d/m/Y H:i') }}
                </p>
            </td>
            <td class="logo-cell">
                <img src="{{ public_path('backend/assets/images/logo_postgrado.png') }}" alt="Postgrado">
            </td>
        </tr></table>
        <div class="barra"></div>
    </div>

    <div class="pdf-footer">
        {{ $programa->nombre }} · Reporte de Nivelación · {{ $modulo->nombre }} · <span class="pagenum"></span>
    </div>

    <table class="notas">
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th style="width:14%;">CI</th>
                <th style="width:41%;">Apellidos, Nombres</th>
                <th style="width:12%;">Nota Nivelación</th>
                <th style="width:28%;">Literal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participantes as $i => $p)
            @php
                $nn = $p->nota_nivelacion !== null ? (float) $p->nota_nivelacion : null;
                $bgNiv = $nn !== null ? ($nn >= 51 ? 'bg-soft-success' : 'bg-soft-danger') : '';
                $txtNiv = $nn !== null ? ($nn >= 51 ? 'text-success' : 'text-danger') : 'text-muted';
            @endphp
            <tr>
                <td class="text-center fw-semibold">{{ $i + 1 }}</td>
                <td class="text-center">{{ $p->ci }}</td>
                <td>{{ $p->apellidos }}, {{ $p->nombres }}</td>
                <td class="text-center fw-semibold {{ $bgNiv }} {{ $txtNiv }}">
                    {{ $nn !== null ? number_format($nn, 2) : '—' }}
                </td>
                <td class="text-center literal {{ $txtNiv }}">
                    {{ $nn !== null ? $p->literal_nivelacion : '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
