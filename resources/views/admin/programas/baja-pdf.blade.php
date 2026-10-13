<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Baja - {{ $inscripcion->apellidos }}, {{ $inscripcion->nombres }}</title>
    <style>
        @page { margin: 130px 20px 40px 20px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 8px; color: #333; margin: 0; }

        .pdf-header {
            position: fixed;
            top: -100px; left: 0; right: 0;
            height: 90px;
        }
        .pdf-header .header-tabla { width: 100%; border-collapse: collapse; }
        .pdf-header .logo-cell { width: 70px; vertical-align: middle; text-align: center; }
        .pdf-header .logo-cell img { width: 55px; height: auto; }
        .pdf-header .titulo-cell { vertical-align: middle; text-align: center; padding: 0 8px; }
        .pdf-header h1 { font-size: 12px; margin: 0 0 2px; text-transform: uppercase; }
        .pdf-header .meta { font-size: 7.5px; color: #777; margin: 0; }
        .pdf-header .barra { border-bottom: 3px solid #000; margin-top: 6px; }

        .pdf-footer {
            position: fixed;
            bottom: -25px; left: 0; right: 0;
            height: 20px;
            font-size: 6.5px; color: #999; text-align: center;
            border-top: 1px solid #ccc; padding-top: 3px;
        }
        .pdf-footer .pagenum:before { content: "Página " counter(page); }

        h3.section-title {
            font-size: 9px; text-transform: uppercase; letter-spacing: .05em;
            background: #1f2d50; color: #fff;
            padding: 4px 8px; margin: 14px 0 6px; border-radius: 2px;
        }
        table.datos { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.datos td { padding: 3px 5px; border: 1px solid #ddd; vertical-align: top; }
        table.datos td.lbl { background: #f5f6fa; font-weight: 700; width: 20%; font-size: 7px; text-transform: uppercase; }
        table.grid { width: 100%; border-collapse: collapse; }
        table.grid th {
            background: #2c3e6e; color: #fff;
            font-size: 7px; text-transform: uppercase; letter-spacing: .03em;
            padding: 4px 5px; border: 1px solid #1f2d50; text-align: center; font-weight: 700;
        }
        table.grid td { padding: 3px 5px; border: 1px solid #ddd; }
        table.grid tr { page-break-inside: avoid; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-success { color: #155724; font-weight: 700; }
        .text-danger { color: #a4282a; font-weight: 700; }
        .text-warning { color: #856404; font-weight: 700; }
        .text-muted { color: #999; }
        .bg-soft-success { background: #d4edda; }
        .bg-soft-danger { background: #f8d7da; }
        .bg-soft-warning { background: #fff3cd; }
        .resumen-box { border: 1px solid #ddd; border-radius: 4px; padding: 6px 8px; margin-bottom: 4px; }
        .resumen-box .rs-titulo { font-size: 7px; text-transform: uppercase; color: #777; font-weight: 700; }
        .resumen-box .rs-valor { font-size: 12px; font-weight: 800; }
        .firma { margin-top: 30px; width: 100%; }
        .firma .linea { border-top: 1px solid #000; margin-top: 40px; text-align: center; font-size: 7.5px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="pdf-header">
        <table class="header-tabla"><tr>
            <td class="logo-cell">
                <img src="{{ public_path('backend/assets/images/logo_universidad.png') }}" alt="Universidad">
            </td>
            <td class="titulo-cell">
                <h1>Reporte de Baja</h1>
                <p style="font-size:9px;font-weight:700;margin:0 0 2px;">{{ $tituloPrograma }} ({{ $inscripcion->gestion }})</p>
                <p class="meta">
                    @if(!empty($inscripcion->codigo))Código: {{ $inscripcion->codigo }} | @endif
                    Gestión: {{ $inscripcion->gestion }} |
                    Generado: {{ date('d/m/Y H:i') }} |
                    Responsable: {{ auth()->user()->fullname ?? '' }}
                </p>
            </td>
            <td class="logo-cell">
                <img src="{{ public_path('backend/assets/images/logo_postgrado.png') }}" alt="Postgrado">
            </td>
        </tr></table>
        <div class="barra"></div>
    </div>

    <div class="pdf-footer">
        {{ $tituloPrograma }} · Reporte de Baja · <span class="pagenum"></span>
    </div>

    @php
        $bajaOperativa = (int)($inscripcion->baja_operativa ?? 0);
        $bajaAcademica = (int)($inscripcion->baja_academica ?? 0);
        $bajaContable = (int)($inscripcion->baja_contable ?? 0);
        $bajaTemprana = (int)$inscripcion->estado === 0 && !$bajaOperativa && !$bajaAcademica;

        $hoy = \Carbon\Carbon::today();
        $cuotasPendientes = collect();
        $cuotasVencidas = collect();
        $totalPagado = 0;
        $totalDeuda = 0;
        foreach ($cuotas as $cc) {
            if ($cc->total <= 0) continue;
            $totalPagado += $cc->pagado;
            $totalDeuda += $cc->saldo;
            if ($cc->saldo > 0) {
                $cc->vencido = false;
                if ($cc->fecha_pago) {
                    $fp = \Carbon\Carbon::parse($cc->fecha_pago)->startOfDay();
                    if ($hoy->diffInDays($fp, false) < 0) {
                        $cc->vencido = true;
                        $cuotasVencidas->push($cc);
                    }
                }
                $cuotasPendientes->push($cc);
            }
        }
        $estaAlDia = $totalDeuda <= 0;

        $modulosConcluidos = $modulos->filter(fn($m) => ($m->desarrollo ?? 'Pendiente') === 'Concluido')->values();
        $academicos = collect();
        $aprobados = 0;
        $reprobados = 0;
        foreach ($modulosConcluidos as $m) {
            $nota = $notas->get($m->id);
            $nm = $nota && $nota->nota_modulo !== null ? (float) $nota->nota_modulo : null;
            $nn = $nota && $nota->nota_nivelacion !== null ? (float) $nota->nota_nivelacion : null;
            $notaFinal = $nm;
            if ($nn !== null && $nm !== null && $nn > $nm) $notaFinal = $nn;
            elseif ($nn !== null && $nm === null) $notaFinal = $nn;
            $academicos->push((object)[
                'modulo' => $m->nombre,
                'nm' => $nm,
                'nn' => $nn,
                'nota_final' => $notaFinal,
                'aprobado' => $notaFinal !== null && $notaFinal >= 71,
                'sin_nota' => $notaFinal === null,
            ]);
            if ($notaFinal !== null) {
                if ($notaFinal >= 71) $aprobados++; else $reprobados++;
            }
        }
    @endphp

    <h3 class="section-title">1. Datos del Participante y Programa</h3>
    <table class="datos">
        <tr>
            <td class="lbl">Participante</td>
            <td><strong>{{ $inscripcion->apellidos }}, {{ $inscripcion->nombres }}</strong></td>
            <td class="lbl">CI</td>
            <td>{{ $inscripcion->ci }}</td>
        </tr>
        <tr>
            <td class="lbl">Programa</td>
            <td>{{ $tituloPrograma }}</td>
            <td class="lbl">Estado</td>
            <td>{{ $inscripcion->estado_nombre ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">Contacto</td>
            <td>{{ $inscripcion->celular ?: '—' }} / {{ $inscripcion->correo ?: '—' }}</td>
            <td class="lbl">Asesor</td>
            <td>{{ $inscripcion->asesor_apellidos ? ($inscripcion->asesor_apellidos . ', ' . $inscripcion->asesor_nombres) : '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">Plan</td>
            <td>{{ (int)$inscripcion->contado ? 'Contado' : 'Regular' }}</td>
            <td class="lbl">F. Inscripción</td>
            <td>{{ $inscripcion->fecha_inscripcion ? \Carbon\Carbon::parse($inscripcion->fecha_inscripcion)->format('d/m/Y') : '—' }}</td>
        </tr>
    </table>

    <h3 class="section-title">2. Detalle de la Baja</h3>
    @if($historial->count() > 0)
    <table class="grid">
        <thead>
            <tr>
                <th style="width:8%;">Fecha</th>
                <th style="width:10%;">Acción</th>
                <th style="width:12%;">Tipo</th>
                <th style="width:10%;">Académica</th>
                <th style="width:10%;">Contable</th>
                <th style="width:22%;">Motivo</th>
                <th style="width:12%;">Respaldo</th>
                <th>Registrado por</th>
            </tr>
        </thead>
        <tbody>
            @foreach($historial as $h)
            @php
                $tipoLabel = $h->tipo === 'temprana' ? 'Temprana' : ($h->tipo === 'academica' ? 'Académica' : 'Operativa');
            @endphp
            <tr>
                <td class="text-center">{{ \Carbon\Carbon::parse($h->created_at)->format('d/m/Y H:i') }}</td>
                <td class="text-center">
                    <strong class="{{ $h->accion === 'baja' ? 'text-danger' : 'text-success' }}">{{ $h->accion === 'baja' ? 'BAJA' : 'ALTA' }}</strong>
                </td>
                <td class="text-center">{{ $tipoLabel }}</td>
                <td class="text-center">{{ ($h->tipo === 'academica' || (int)($h->baja_academica ?? 0)) ? 'Sí' : '—' }}</td>
                <td class="text-center">{{ (int)($h->baja_contable ?? 0) ? 'Sí' : '—' }}</td>
                <td>{{ $h->motivo ?: '—' }}</td>
                <td class="text-center">{{ $h->archivo_original ? 'Adjunto' : '—' }}</td>
                <td>{{ $h->user_nombre }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="text-muted">Sin registros de historial de baja.</p>
    @endif

    <table class="datos" style="margin-top:6px;">
        <tr>
            <td class="lbl">Estado actual</td>
            <td>
                @if($bajaTemprana)
                    <strong class="text-danger">Baja Temprana</strong>
                @elseif($bajaOperativa || $bajaAcademica || $bajaContable)
                    <strong class="text-danger">
                        Baja
                        @if($bajaOperativa) Operativa @endif
                        @if($bajaAcademica) · Académica @endif
                        @if($bajaContable) · Contable @endif
                    </strong>
                @else
                    <strong class="text-success">Activo</strong>
                @endif
            </td>
            <td class="lbl">Código</td>
            <td>{{ $inscripcion->codigo ?: '—' }}</td>
        </tr>
    </table>

    <h3 class="section-title">3. Historial de Pagos (Contable)</h3>
    @if($cuotas->count() > 0)
    <table class="grid">
        <thead>
            <tr>
                <th style="width:6%;">N°</th>
                <th style="width:12%;">Tipo</th>
                <th style="width:16%;">Vencimiento</th>
                <th style="width:14%;">Estado</th>
                <th class="text-end">Deuda</th>
                <th class="text-end">Pagado</th>
                <th class="text-end">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cuotas as $ci => $c)
            @if($c->total <= 0) @continue @endif
            @php
                $pagada = $c->saldo <= 0;
                $tipoLabel = $c->tipo === 'Matricula' ? 'Matrícula' : 'Colegiatura';
                $vencText = '';
                $vencCls = 'text-muted';
                if (!$c->fecha_pago) {
                    $vencText = 'Sin fecha';
                } elseif ($pagada) {
                    $vencText = 'Pagada';
                    $vencCls = 'text-success';
                } else {
                    $fp = \Carbon\Carbon::parse($c->fecha_pago)->startOfDay();
                    $diff = $hoy->diffInDays($fp, false);
                    if ($diff < 0) { $vencText = 'Vencida · ' . abs($diff) . ' día' . (abs($diff) !== 1 ? 's' : ''); $vencCls = 'text-danger'; }
                    elseif ($diff === 0) { $vencText = 'Vence hoy'; $vencCls = 'text-warning'; }
                    else { $vencText = 'En ' . $diff . ' día' . ($diff !== 1 ? 's' : ''); $vencCls = 'text-warning'; }
                }
            @endphp
            <tr>
                <td class="text-center">{{ $ci + 1 }}</td>
                <td class="text-center">{{ $tipoLabel }}</td>
                <td class="text-center">{{ $c->fecha_pago ? \Carbon\Carbon::parse($c->fecha_pago)->format('d/m/Y') : '—' }}</td>
                <td class="text-center {{ $vencCls }}">{{ $vencText }}</td>
                <td class="text-end">Bs {{ number_format($c->total, 2) }}</td>
                <td class="text-end {{ $c->pagado > 0 ? 'text-success' : 'text-muted' }}">Bs {{ number_format($c->pagado, 2) }}</td>
                <td class="text-end {{ $c->saldo > 0 ? 'text-danger' : 'text-success' }}">Bs {{ number_format($c->saldo, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-end" style="background:#f5f6fa;"><strong>Totales</strong></td>
                <td class="text-end" style="background:#f5f6fa;"><strong>Bs {{ number_format($cuotas->sum('total'), 2) }}</strong></td>
                <td class="text-end" style="background:#f5f6fa;"><strong class="text-success">Bs {{ number_format($totalPagado, 2) }}</strong></td>
                <td class="text-end" style="background:#f5f6fa;"><strong class="{{ $totalDeuda > 0 ? 'text-danger' : 'text-success' }}">Bs {{ number_format($totalDeuda, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <table style="width:100%;">
        <tr>
            <td style="width:50%; padding-right:4px;">
                <div class="resumen-box {{ $estaAlDia ? 'bg-soft-success' : 'bg-soft-danger' }}">
                    <div class="rs-titulo">Situación de pago</div>
                    <div class="rs-valor {{ $estaAlDia ? 'text-success' : 'text-danger' }}">
                        {{ $estaAlDia ? 'AL DÍA' : 'CON PAGOS PENDIENTES' }}
                    </div>
                    <div style="font-size:7px; color:#666;">
                        @if($estaAlDia)
                            No registra deudas pendientes de pago.
                        @else
                            {{ $cuotasPendientes->count() }} cuota(s) pendiente(s)
                            @if($cuotasVencidas->count() > 0) · {{ $cuotasVencidas->count() }} vencida(s) @endif
                            · Deuda total Bs {{ number_format($totalDeuda, 2) }}
                        @endif
                    </div>
                </div>
            </td>
            <td style="width:50%; padding-left:4px;">
                <div class="resumen-box bg-soft-warning">
                    <div class="rs-titulo">Cuotas pendientes</div>
                    @if($cuotasPendientes->count() > 0)
                        @foreach($cuotasPendientes as $cp)
                            <div style="font-size:7.5px;">
                                {{ $cp->tipo === 'Matricula' ? 'Matrícula' : 'Colegiatura' }}
                                · Vence {{ $cp->fecha_pago ? \Carbon\Carbon::parse($cp->fecha_pago)->format('d/m/Y') : 'sin fecha' }}
                                · Saldo <strong class="text-danger">Bs {{ number_format($cp->saldo, 2) }}</strong>
                            </div>
                        @endforeach
                    @else
                        <div style="font-size:7.5px;">Sin cuotas pendientes.</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>
    @else
    <p class="text-muted">No se registraron cuotas para este participante en el programa.</p>
    @endif

    <h3 class="section-title">4. Detalle Académico (Módulos Concluidos)</h3>
    @if($academicos->count() > 0)
    <table class="grid">
        <thead>
            <tr>
                <th style="width:6%;">N°</th>
                <th>Módulo</th>
                <th style="width:14%;">Nota Módulo</th>
                <th style="width:14%;">Nota Nivelación</th>
                <th style="width:14%;">Nota Final</th>
                <th style="width:16%;">Resultado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($academicos as $ai => $ac)
            <tr>
                <td class="text-center">{{ $ai + 1 }}</td>
                <td>{{ $ac->modulo }}</td>
                <td class="text-center {{ $ac->nm !== null ? ($ac->nm >= 71 ? 'text-success' : 'text-danger') : 'text-muted' }}">
                    {{ $ac->nm !== null ? number_format($ac->nm, 2) : '—' }}
                </td>
                <td class="text-center {{ $ac->nn !== null ? ($ac->nn >= 71 ? 'text-success' : 'text-danger') : 'text-muted' }}">
                    {{ $ac->nn !== null ? number_format($ac->nn, 2) : '—' }}
                </td>
                <td class="text-center fw-semibold">{{ $ac->nota_final !== null ? number_format($ac->nota_final, 2) : '—' }}</td>
                <td class="text-center">
                    @if($ac->sin_nota)
                        <span class="text-muted">Sin nota</span>
                    @elseif($ac->aprobado)
                        <span class="text-success">Aprobado</span>
                    @else
                        <span class="text-danger">Reprobado</span>
                    @endif
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-end" style="background:#f5f6fa;"><strong>Resumen</strong></td>
                <td colspan="2" style="background:#f5f6fa;">
                    <span class="text-success">Aprobados: {{ $aprobados }}</span>
                    &nbsp;·&nbsp;
                    <span class="text-danger">Reprobados: {{ $reprobados }}</span>
                </td>
            </tr>
        </tbody>
    </table>
    @else
    <p class="text-muted">No hay módulos concluidos al momento de la baja.</p>
    @endif

    @if($bajaOperativa || $bajaAcademica || $bajaContable || $bajaTemprana)
    @php
        $responsablePrograma = $responsablesPrograma->first();
        $nombreResponsablePrograma = $responsablePrograma
            ? $responsablePrograma->nombres . ' ' . $responsablePrograma->apellidos
            : '______________________';
        $nombreAcademicaFijo = 'Helen Jhessica Romero Choque';
        $normNombre = function ($n) {
            $n = mb_strtolower(trim((string) $n));
            $n = str_replace(
                ['á','é','í','ó','ú','ü','ñ'],
                ['a','e','i','o','u','u','n'],
                $n
            );
            return preg_replace('/\s+/', ' ', $n);
        };
        $mismaPersona = $nombreResponsablePrograma !== '______________________'
            && (str_contains($normNombre($nombreResponsablePrograma), $normNombre($nombreAcademicaFijo))
                || str_contains($normNombre($nombreAcademicaFijo), $normNombre($nombreResponsablePrograma)));
    @endphp
    <table class="firma">
        <tr>
            <td style="width:50%; text-align:center;">
                <div style="border-top:1px solid #000; margin:40px 20px 4px 20px;"></div>
                <div style="font-size:8px; font-weight:700;">Ing. Veimar Jaime Rivas Cabezas</div>
                <div style="font-size:7.5px; font-weight:400;">Gerente Administrativo AI</div>
            </td>
            <td style="width:50%; text-align:center;">
                <div style="border-top:1px solid #000; margin:40px 20px 4px 20px;"></div>
                <div style="font-size:8px; font-weight:700;">Lic. Gelmi Natali Sandi Saucedo</div>
                <div style="font-size:7.5px; font-weight:400;">Responsable Contable</div>
            </td>
        </tr>
    </table>
    @if($mismaPersona)
    <table class="firma" style="width:100%;">
        <tr>
            <td style="width:100%; text-align:center;">
                <div style="border-top:1px solid #000; margin:40px 40px 4px 40px;"></div>
                <div style="font-size:8px; font-weight:700;">Lic. {{ $nombreAcademicaFijo }}</div>
                <div style="font-size:7.5px; font-weight:400;">Responsable Área Académica</div>
            </td>
        </tr>
    </table>
    @else
    <table class="firma">
        <tr>
            <td style="width:50%; text-align:center;">
                <div style="border-top:1px solid #000; margin:40px 20px 4px 20px;"></div>
                <div style="font-size:8px; font-weight:700;">Lic. {{ $nombreAcademicaFijo }}</div>
                <div style="font-size:7.5px; font-weight:400;">Responsable Área Académica</div>
            </td>
            <td style="width:50%; text-align:center;">
                <div style="border-top:1px solid #000; margin:40px 20px 4px 20px;"></div>
                <div style="font-size:8px; font-weight:700;">Lic. {{ $nombreResponsablePrograma }}</div>
                <div style="font-size:7.5px; font-weight:400;">Responsable Seguimiento Académico</div>
            </td>
        </tr>
    </table>
    @endif
    @endif
</body>
</html>
