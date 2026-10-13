<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cronograma General - {{ $programa->nombre }}</title>
    <style>
        @page { margin: 150px 35px 55px 35px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #333; margin: 0; }

        /* Cabecera fija: se replica en cada página */
        .pdf-header {
            position: fixed;
            top: -125px; left: 0; right: 0;
            height: 115px;
        }
        .pdf-header .header-tabla { width: 100%; border-collapse: collapse; }
        .pdf-header .logo-cell { width: 90px; vertical-align: middle; text-align: center; }
        .pdf-header .logo-cell img { width: 72px; height: auto; }
        .pdf-header .titulo-cell { vertical-align: middle; text-align: center; padding: 0 8px; }
        .pdf-header h1 { font-size: 12px; margin: 0 0 3px; text-transform: uppercase; }
        .pdf-header h2 { font-size: 11px; margin: 0 0 3px; text-transform: uppercase; }
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

        table.cronograma { width: 100%; border-collapse: collapse; }
        table.cronograma th {
            background: #1f2d50; color: #fff;
            font-size: 8px; text-transform: uppercase; letter-spacing: .03em;
            padding: 6px 4px; border: 1px solid #000;
            text-align: center; font-weight: 700;
        }
        table.cronograma td { padding: 5px 4px; border: 1px solid #000; text-align: center; }
        table.cronograma tr { page-break-inside: avoid; }
        .celda-modulo { font-weight: 700; color: #000; text-transform: uppercase; }
        .celda-num { font-weight: 700; }
        .capitalize { text-transform: capitalize; }
        .text-muted { color: #999; }
        .sin-datos { padding: 8px; border: 1px dashed #ccc; color: #999; font-style: italic; text-align: center; }
    </style>
</head>
<body>
    <div class="pdf-header">
        <table class="header-tabla"><tr>
            <td class="logo-cell">
                <img src="{{ public_path('backend/assets/images/logo_universidad.png') }}" alt="Universidad">
            </td>
            <td class="titulo-cell">
                @php
                    $tituloPrograma = \Illuminate\Support\Str::startsWith(mb_strtolower(trim($programa->nombre)), mb_strtolower(trim($programa->tipo_nombre)))
                        ? $programa->nombre
                        : $programa->tipo_nombre . ' ' . $programa->nombre;
                @endphp
                <h1>Cronograma General del Programa</h1>
                <h2>{{ $tituloPrograma }} ({{ $programa->gestion }})</h2>
                <p class="meta">
                    @if(!empty($programa->codigo))Código: {{ $programa->codigo }} | @endif
                    Gestión: {{ $programa->gestion }} |
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
        {{ $programa->nombre }} · Cronograma General · <span class="pagenum"></span>
    </div>

    @if($modulos->isEmpty())
        <div class="sin-datos">Este programa no tiene módulos registrados.</div>
    @else
    <table class="cronograma">
        <thead>
            <tr>
                <th style="width:7%;">N° Módulo</th>
                <th style="width:{{ $conDocentes ? '25%' : '39%' }};">Nombre del Módulo</th>
                @if($conDocentes)
                <th style="width:20%;">Docente</th>
                @endif
                <th style="width:11%;">Día</th>
                <th style="width:19%;">Fecha</th>
                <th style="width:9%;">Hora Inicio</th>
                <th style="width:9%;">Hora Fin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($modulos as $idx => $m)
            @php
                $sesiones = $horariosPorModulo->get($m->id, collect())->values();
                $filas = max(1, $sesiones->count());
                $docente = $m->docente_apellidos ? trim($m->docente_apellidos . ' ' . $m->docente_nombres) : null;
            @endphp
            @if($sesiones->isEmpty())
            <tr>
                <td class="celda-num" rowspan="1">{{ $idx }}</td>
                <td class="celda-modulo" rowspan="1">{{ $m->nombre }}</td>
                @if($conDocentes)
                <td class="celda-modulo" rowspan="1">{{ $docente ?? 'Sin asignar' }}</td>
                @endif
                <td colspan="4" class="text-muted" style="font-style:italic;">Sin horarios registrados</td>
            </tr>
            @else
            @foreach($sesiones as $i => $h)
            @php $fecha = \Carbon\Carbon::parse($h->fecha_desarrollo)->locale('es'); @endphp
            <tr>
                @if($i === 0)
                <td class="celda-num" rowspan="{{ $filas }}">{{ $idx }}</td>
                <td class="celda-modulo" rowspan="{{ $filas }}">{{ $m->nombre }}</td>
                @if($conDocentes)
                <td class="celda-modulo" rowspan="{{ $filas }}">{{ $docente ?? 'Sin asignar' }}</td>
                @endif
                @endif
                <td class="capitalize">{{ $fecha->isoFormat('dddd') }}</td>
                <td>{{ $fecha->isoFormat('D [de] MMMM [del] YYYY') }}</td>
                <td>{{ substr($h->hora_inicio, 0, 5) }}</td>
                <td>{{ substr($h->hora_fin, 0, 5) }}</td>
            </tr>
            @endforeach
            @endif
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>
