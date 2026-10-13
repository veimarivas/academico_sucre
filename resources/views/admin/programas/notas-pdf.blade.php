<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $programa->nombre }} - Reporte General de Notas</title>
    <style>
        @page { margin: 145px 25px 45px 25px; size: landscape; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7px; color: #333; margin: 0; }

        .pdf-header {
            position: fixed;
            top: -120px; left: 0; right: 0;
            height: 110px;
        }
        .pdf-header .header-tabla { width: 100%; border-collapse: collapse; }
        .pdf-header .logo-cell { width: 70px; vertical-align: middle; text-align: center; }
        .pdf-header .logo-cell img { width: 55px; height: auto; }
        .pdf-header .titulo-cell { vertical-align: middle; text-align: center; padding: 0 8px; }
        .pdf-header h1 { font-size: 11px; margin: 0 0 2px; text-transform: uppercase; }
        .pdf-header .meta { font-size: 7.5px; color: #777; margin: 0; }
        .pdf-header .barra { border-bottom: 3px solid #000; margin-top: 6px; }

        .pdf-footer {
            position: fixed;
            bottom: -30px; left: 0; right: 0;
            height: 22px;
            font-size: 6.5px; color: #999; text-align: center;
            border-top: 1px solid #ccc; padding-top: 3px;
        }
        .pdf-footer .pagenum:before { content: "Página " counter(page); }

        table.notas { width: 100%; border-collapse: collapse; font-size: 6.5px; }
        table.notas th {
            background: #1f2d50; color: #fff;
            font-size: 6px; text-transform: uppercase; letter-spacing: .02em;
            padding: 4px 2px; border: 1px solid #000;
            text-align: center; font-weight: 700;
        }
        table.notas td { padding: 3px 2px; border: 1px solid #000; }
        table.notas tr { page-break-inside: avoid; }
        .text-center { text-align: center; }
        .fw-semibold { font-weight: 600; }
        .bg-soft-success { background: #d4edda; }
        .bg-soft-danger { background: #f8d7da; }
        .text-success { color: #155724; }
        .text-danger { color: #721c24; }
        .text-muted { color: #999; }
        .mod-title { font-size: 5.5px; line-height: 1.2; }
        .resumen-border { border-left: 2px solid #dee2e6 !important; }
    </style>
</head>
<body>
    @php
        $esDiplomado = ($programa->tipo_id ?? 0) === 2;
        $modulosConcluidos = 0;
        $esPrimerMod = true;
        $modsVisibles = [];
        $totalColModulosPdf = 0;
        foreach ($modulos as $mod) {
            $estaConcluido = ($mod->desarrollo ?? 'Pendiente') === 'Concluido';
            $esModCero = ($programa->modulo_cero ?? 0) == 1 && $esPrimerMod;
            if (!$esModCero) {
                $modsVisibles[] = $mod;
                $enmCol = $estadoNotasPorModulo->get($mod->id);
                $tieneNivCol = $enmCol && (int)$enmCol->con_nota_nivelacion > 0;
                $totalColModulosPdf += $tieneNivCol ? 2 : 1;
                if ($estaConcluido) $modulosConcluidos++;
            }
            $esPrimerMod = false;
        }
        $colspanEmpty = 3 + $totalColModulosPdf + 2;
    @endphp
    <div class="pdf-header">
        <table class="header-tabla"><tr>
            <td class="logo-cell">
                <img src="{{ public_path('backend/assets/images/logo_universidad.png') }}" alt="Universidad">
            </td>
            <td class="titulo-cell">
                <h1>Reporte General de Notas</h1>
                <p style="font-size:9px;font-weight:700;margin:0 0 2px;">{{ $tituloPrograma }} ({{ $programa->gestion }})</p>
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
        {{ $programa->nombre }} · Reporte General de Notas · <span class="pagenum"></span>
    </div>

    <table class="notas">
        <thead>
            <tr>
                <th style="width:2.5%;">#</th>
                <th style="width:8%;">CI</th>
                <th style="width:16%;">Apellidos, Nombres</th>
                @foreach($modsVisibles as $mod)
                @php
                    $enm = $estadoNotasPorModulo->get($mod->id);
                    $tieneNiv = $enm && (int)$enm->con_nota_nivelacion > 0;
                @endphp
                <th colspan="{{ $tieneNiv ? 2 : 1 }}" style="width:{{ $tieneNiv ? 15 : 7.5 }}%;">
                    <span class="mod-title">{{ $mod->nombre }}</span>
                </th>
                @endforeach
                <th style="width:7%;">Prom</th>
                <th style="width:7%;">Estado</th>
            </tr>
            @if(count($modsVisibles) > 0)
            <tr>
                <th colspan="3"></th>
                @foreach($modsVisibles as $mod)
                @php
                    $enm2 = $estadoNotasPorModulo->get($mod->id);
                    $tieneNiv2 = $enm2 && (int)$enm2->con_nota_nivelacion > 0;
                @endphp
                <th>Mod</th>
                @if($tieneNiv2)<th>Niv</th>@endif
                @endforeach
                <th colspan="2"></th>
            </tr>
            @endif
        </thead>
        <tbody>
            @forelse($participantes as $i => $p)
            @php
                $pId = $p->id;
                $sumNotas = 0;
                $countNotas = 0;
                $aprobados = 0;
                $reprobados = 0;
                $esBaja = (int)($p->baja_operativa ?? 0);
                $bajaStyle = $esBaja ? 'text-decoration: line-through; color: #dc3545;' : '';
            @endphp
            <tr style="{{ $bajaStyle }}">
                <td class="text-center fw-semibold">{{ $i + 1 }}</td>
                <td class="text-center">{{ $p->ci }}</td>
                <td>{{ $p->apellidos }}, {{ $p->nombres }}</td>
                @foreach($modsVisibles as $mod)
                @php
                    $key = $pId . '_' . $mod->id;
                    $nota = $notas->get($key);
                    $nm = $nota ? (float) $nota->nota_modulo : null;
                    $nn = $nota ? (float) $nota->nota_nivelacion : null;
                    $bgMod = $nm !== null ? ($nm >= 71 ? 'bg-soft-success' : 'bg-soft-danger') : '';
                    $txtMod = $nm !== null ? ($nm >= 71 ? 'text-success' : 'text-danger') : 'text-muted';
                    $bgNiv = $nn !== null ? ($nn >= 71 ? 'bg-soft-success' : 'bg-soft-danger') : '';
                    $txtNiv = $nn !== null ? ($nn >= 71 ? 'text-success' : 'text-danger') : 'text-muted';

                    $estaConcluido = ($mod->desarrollo ?? 'Pendiente') === 'Concluido';
                    if ($estaConcluido) {
                        $notaFinal = $nm;
                        if ($nn !== null && $nm !== null && $nn > $nm) {
                            $notaFinal = $nn;
                        } elseif ($nn !== null && $nm === null) {
                            $notaFinal = $nn;
                        }
                        if ($notaFinal !== null) {
                            $sumNotas += $notaFinal;
                            $countNotas++;
                            if ($notaFinal >= 71) $aprobados++; else $reprobados++;
                        }
                    }

                    $enm3 = $estadoNotasPorModulo->get($mod->id);
                    $tieneNiv3 = $enm3 && (int)$enm3->con_nota_nivelacion > 0;
                @endphp
                <td class="text-center fw-semibold {{ $bgMod }} {{ $txtMod }}">
                    {{ $nm !== null ? number_format($nm, 2) : '' }}
                </td>
                @if($tieneNiv3)
                <td class="text-center fw-semibold {{ $bgNiv }} {{ $txtNiv }}">
                    {{ $nn !== null ? number_format($nn, 2) : '' }}
                </td>
                @endif
                @endforeach
                @php
                    if ($esBaja) {
                        $estadoFinal = 'Retirado';
                        $estadoColor = '#dc3545';
                    } else {
                        $promedio = $countNotas > 0 ? $sumNotas / $countNotas : null;
                        $todosConcluidos = $modulosConcluidos > 0 && $countNotas >= $modulosConcluidos;
                        if ($promedio === null) {
                            $estadoFinal = 'Sin notas';
                            $estadoColor = '#878a99';
                        } elseif (!$todosConcluidos) {
                            $estadoFinal = 'En curso';
                            $estadoColor = '#299cdb';
                        } elseif ($reprobados === 0 && $promedio >= 71) {
                            $estadoFinal = 'Aprobado';
                            $estadoColor = '#0ab39c';
                        } else {
                            $estadoFinal = 'Reprobado';
                            $estadoColor = '#f06548';
                        }
                    }
                @endphp
                <td class="text-center fw-bold resumen-border">
                    @if($promedio !== null)
                        {{ round($promedio) }}
                    @else
                        —
                    @endif
                </td>
                <td class="text-center fw-bold" style="color:{{ $estadoColor }};">
                    {{ $estadoFinal }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $colspanEmpty }}" class="text-center text-muted py-4">No hay participantes inscritos</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
