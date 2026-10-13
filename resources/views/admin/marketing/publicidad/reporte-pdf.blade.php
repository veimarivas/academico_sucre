<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Publicidad - {{ $periodo }} {{ $quincenaLabel }}</title>
    <style>
        @page { margin: 135px 20px 35px 20px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7.5px; color: #333; margin: 0; }

        .pdf-header {
            position: fixed; top: -108px; left: 0; right: 0; height: 98px;
        }
        .pdf-header .hdr-table { width: 100%; border-collapse: collapse; }
        .pdf-header .hdr-table td { vertical-align: middle; text-align: center; }
        .pdf-header .hdr-table .logo-cell { width: 65px; }
        .pdf-header .hdr-table .logo-cell img { width: 50px; height: auto; }
        .pdf-header h1 { font-size: 10px; margin: 0 0 2px; text-transform: uppercase; }
        .pdf-header .meta { font-size: 7px; color: #555; margin: 0; }
        .pdf-header .barra { border-bottom: 2.5px solid #1f2d50; margin-top: 5px; }

        .pdf-footer {
            position: fixed; bottom: -25px; left: 0; right: 0; height: 18px;
            font-size: 6.5px; color: #999; text-align: center;
            border-top: 1px solid #ccc; padding-top: 3px;
        }
        .pdf-footer .pagenum:before { content: "Página " counter(page); }

        table { width: 100%; border-collapse: collapse; font-size: 7px; }
        table th {
            background: #1f2d50; color: #fff;
            font-size: 6.5px; text-transform: uppercase; letter-spacing: .03em;
            padding: 4px 3px; border: 1px solid #000;
            text-align: center; font-weight: 700;
        }
        table td { padding: 3px 3px; border: 1px solid #000; vertical-align: middle; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: 700; }
        .fw-semibold { font-weight: 600; }
        .text-success { color: #155724; }
        .text-danger { color: #721c24; }
        .text-muted { color: #888; }

        .section-title {
            font-size: 8px; font-weight: 700; text-transform: uppercase;
            padding: 5px 6px; margin: 10px 0 4px;
            background: #eef1f8; border-left: 3px solid #1f2d50;
        }
        .sub-section {
            font-size: 7.5px; font-weight: 700;
            padding: 4px 6px; margin: 6px 0 3px;
            background: #f0f7ff; border-left: 3px solid #299cdb;
        }
        .derivado-header {
            font-size: 7px; font-weight: 600;
            padding: 3px 6px; margin: 4px 0 2px;
            background: #f0f4ff;
        }
        .campana-title {
            font-size: 7px; font-weight: 600;
            padding: 3px 6px; margin: 4px 0 2px;
            background: #fffbeb; border-left: 2px solid #f7b84b;
        }
        .total-row td {
            background: #f8f9fc; font-weight: 700;
            border-top: 2px solid #1f2d50;
        }

        .img-preview { width: 85px; height: 62px; object-fit: contain; border: 1px solid #ccc; border-radius: 2px; }
        .presupuesto-summary td {
            border: 0; padding: 2px 4px;
        }
    </style>
</head>
<body>
    <div class="pdf-header">
        <table class="hdr-table"><tr>
            <td class="logo-cell">
                <img src="{{ public_path('backend/assets/images/logo_universidad.png') }}" alt="Universidad">
            </td>
            <td>
                <h1>Reporte de Publicidad</h1>
                <p style="font-size:8.5px;font-weight:700;margin:0 0 2px;">{{ $periodo }} · {{ $quincenaLabel }}</p>
                <p class="meta">Generado: {{ date('d/m/Y H:i') }}</p>
            </td>
            <td class="logo-cell">
                <img src="{{ public_path('backend/assets/images/logo_postgrado.png') }}" alt="Postgrado">
            </td>
        </tr></table>
        <div class="barra"></div>
    </div>

    <div class="pdf-footer">
        Reporte de Publicidad · {{ $periodo }} {{ $quincenaLabel }} · <span class="pagenum"></span>
    </div>

    @foreach($presupuestos as $p)
    @php
        $derivs = $derivados->get($p->id, collect());
        $totalDerivado = $derivs->sum('monto_asignado');
        $campanas = $comprobantesPorCampana->get($p->id, collect());
    @endphp

    <div class="section-title">
        {{ $p->ase_apellidos }}, {{ $p->ase_nombres }} ({{ $p->cargo_nombre }})
    </div>

    <table>
        <tr class="presupuesto-summary">
            <td style="width:50%;">
                <strong>Presupuesto original:</strong> Bs {{ number_format($p->monto_asignado + $totalDerivado, 2) }}
                @if($totalDerivado > 0)
                <br><span style="color:#0c5460;">Derivado: Bs {{ number_format($totalDerivado, 2) }}</span>
                <br><strong>Neto:</strong> Bs {{ number_format($p->monto_asignado, 2) }}
                @endif
            </td>
            <td style="width:50%;text-align:right;">
                Gastado: <strong>Bs {{ number_format($p->total_gastado, 2) }}</strong><br>
                Aprobado: <strong style="color:#155724;">Bs {{ number_format($p->total_aprobado, 2) }}</strong><br>
                Pendiente: <strong style="color:#856404;">Bs {{ number_format($p->total_pendiente, 2) }}</strong>
            </td>
        </tr>
    </table>

    {{-- Comprobantes del principal --}}
    @forelse($campanas as $campana => $compsCampana)
    @php $campanaTotal = $compsCampana->sum('monto'); @endphp
    <div class="campana-title">
        <i>Campaña:</i> {{ $campana }} ({{ $compsCampana->count() }} comprobante{{ $compsCampana->count() !== 1 ? 's' : '' }})
        <span style="float:right;font-weight:700;">Total: Bs {{ number_format($campanaTotal, 2) }}</span>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width:3%;">#</th>
                <th style="width:10%;">Fechas</th>
                <th style="width:22%;">Descripción</th>
                <th style="width:9%;">Asesor</th>
                <th style="width:6%;">Plataf.</th>
                <th style="width:8%;">Monto</th>
                <th style="width:12%;">Archivo</th>
                <th style="width:12%;">Archivo Débito</th>
                <th style="width:7%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($compsCampana as $idx => $c)
            @php
                $fi = \Carbon\Carbon::parse($c->fecha_inicio);
                $ff = \Carbon\Carbon::parse($c->fecha_fin);
                $preview = $previews->get($c->id);
                $debPreview = $debitoPreviews->get($c->id);
                $isDebImage = $c->archivo_debito_mime && str_starts_with($c->archivo_debito_mime, 'image/');
            @endphp
            <tr>
                <td class="text-center fw-semibold">{{ $idx + 1 }}</td>
                <td style="font-size:6.5px;">{{ $fi->format('d/m/Y') }}<br>al {{ $ff->format('d/m/Y') }}</td>
                <td style="font-size:6.5px;">
                    {{ $c->descripcion }}
                    @if($c->programa_nombre)<br><span class="text-muted">{{ $c->programa_nombre }}</span>@endif
                    @if($c->observaciones_contable)<br><span style="color:#92400e;font-size:6px;">{{ $c->observaciones_contable }}</span>@endif
                </td>
                <td style="font-size:6.5px;">{{ $c->asesor_apellidos }}, {{ $c->asesor_nombres }}</td>
                <td class="text-center" style="font-size:6.5px;">{{ $c->plataforma }}</td>
                <td class="text-end fw-bold">Bs {{ number_format($c->monto, 2) }}</td>
                <td class="text-center">
                    @if($preview)
                    <img src="{{ $preview }}" class="img-preview" alt="Comprobante">
                    @else
                    <span style="font-size:6px;color:#999;">Sin archivo</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($isDebImage && $debPreview)
                    <img src="{{ $debPreview }}" class="img-preview" alt="Débito">
                    @elseif($c->archivo_debito_path)
                    <span style="font-size:6px;">{{ $c->archivo_debito_original }}</span>
                    @else
                    <span style="font-size:6px;color:#999;">Sin archivo</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($c->estado === 'aprobado')
                        <span class="fw-bold text-success">Aprobado</span>
                    @elseif($c->estado === 'rechazado')
                        <span class="fw-bold text-danger">Rechazado</span>
                    @else
                        <span class="fw-bold" style="color:#856404;">Pendiente</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @empty
    @if($derivs->isEmpty())
    <p style="text-align:center;color:#999;font-size:7px;margin:4px 0;">Sin comprobantes ni derivaciones</p>
    @endif
    @endforelse

    {{-- Presupuestos derivados --}}
    @if($derivs->isNotEmpty())
    <div class="sub-section">Presupuestos derivados</div>
    @foreach($derivs as $d)
    @php
        $campanasDeriv = $comprobantesPorCampana->get($d->id, collect());
    @endphp
    <div class="derivado-header">
        {{ $d->ase_apellidos }}, {{ $d->ase_nombres }} ({{ $d->cargo_nombre }}) ·
        Der.: <strong>Bs {{ number_format($d->monto_asignado, 2) }}</strong>
        · Aprob.: <span class="text-success">Bs {{ number_format($d->total_aprobado, 2) }}</span>
    </div>

    @forelse($campanasDeriv as $campana => $compsCampana)
    @php $campanaTotal = $compsCampana->sum('monto'); @endphp
    <div class="campana-title" style="margin-left:8px;">
        <i>Campaña:</i> {{ $campana }} ({{ $compsCampana->count() }} comprobante{{ $compsCampana->count() !== 1 ? 's' : '' }})
        <span style="float:right;font-weight:700;">Total: Bs {{ number_format($campanaTotal, 2) }}</span>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width:3%;">#</th>
                <th style="width:10%;">Fechas</th>
                <th style="width:22%;">Descripción</th>
                <th style="width:9%;">Asesor</th>
                <th style="width:6%;">Plataf.</th>
                <th style="width:8%;">Monto</th>
                <th style="width:12%;">Archivo</th>
                <th style="width:12%;">Archivo Débito</th>
                <th style="width:7%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($compsCampana as $idx => $c)
            @php
                $fi = \Carbon\Carbon::parse($c->fecha_inicio);
                $ff = \Carbon\Carbon::parse($c->fecha_fin);
                $preview = $previews->get($c->id);
                $debPreview = $debitoPreviews->get($c->id);
                $isDebImage = $c->archivo_debito_mime && str_starts_with($c->archivo_debito_mime, 'image/');
            @endphp
            <tr>
                <td class="text-center fw-semibold">{{ $idx + 1 }}</td>
                <td style="font-size:6.5px;">{{ $fi->format('d/m/Y') }}<br>al {{ $ff->format('d/m/Y') }}</td>
                <td style="font-size:6.5px;">
                    {{ $c->descripcion }}
                    @if($c->programa_nombre)<br><span class="text-muted">{{ $c->programa_nombre }}</span>@endif
                    @if($c->observaciones_contable)<br><span style="color:#92400e;font-size:6px;">{{ $c->observaciones_contable }}</span>@endif
                </td>
                <td style="font-size:6.5px;">{{ $c->asesor_apellidos }}, {{ $c->asesor_nombres }}</td>
                <td class="text-center" style="font-size:6.5px;">{{ $c->plataforma }}</td>
                <td class="text-end fw-bold">Bs {{ number_format($c->monto, 2) }}</td>
                <td class="text-center">
                    @if($preview)
                    <img src="{{ $preview }}" class="img-preview" alt="Comprobante">
                    @else
                    <span style="font-size:6px;color:#999;">Sin archivo</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($isDebImage && $debPreview)
                    <img src="{{ $debPreview }}" class="img-preview" alt="Débito">
                    @elseif($c->archivo_debito_path)
                    <span style="font-size:6px;">{{ $c->archivo_debito_original }}</span>
                    @else
                    <span style="font-size:6px;color:#999;">Sin archivo</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($c->estado === 'aprobado')
                        <span class="fw-bold text-success">Aprobado</span>
                    @elseif($c->estado === 'rechazado')
                        <span class="fw-bold text-danger">Rechazado</span>
                    @else
                        <span class="fw-bold" style="color:#856404;">Pendiente</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @empty
    <p style="text-align:center;color:#999;font-size:6.5px;margin:3px 0 3px 8px;">Sin comprobantes cargados por este asesor</p>
    @endforelse
    @endforeach
    @endif

    @endforeach

    {{-- Total general --}}
    <table style="margin-top:12px;">
        <tr class="total-row">
            <td style="text-align:right;font-size:9px;padding:7px 10px;" colspan="4">
                <span style="text-transform:uppercase;">Total aprobado general</span>
            </td>
            <td style="text-align:center;font-size:9px;padding:7px 10px;" colspan="4">
                <span class="text-success" style="font-size:11px;">Bs {{ number_format($totalGeneralAprobado, 2) }}</span>
            </td>
        </tr>
    </table>

    {{-- Firmas --}}
    @php $p = $presupuestos->first(); @endphp
    <div style="margin-top:48px; width:100%;">
        <table style="width:100%; border:none; border-collapse: collapse;">
            <tr>
                <td style="width:45%; border:none; text-align:center; padding:0 10px;">
                    <div style="border-top:1px solid #333; width:80%; margin:0 auto 4px; padding-top:4px;">
                        <span style="font-size:7px; font-weight:700; text-transform:uppercase;">{{ $p->ase_apellidos }}, {{ $p->ase_nombres }}</span><br>
                        <span style="font-size:6.5px; color:#555;">Asesor principal</span>
                    </div>
                </td>
                <td style="width:10%; border:none; text-align:center;">&nbsp;</td>
                <td style="width:45%; border:none; text-align:center; padding:0 10px;">
                    <div style="border-top:1px solid #333; width:80%; margin:0 auto 4px; padding-top:4px;">
                        <span style="font-size:7px; font-weight:700; text-transform:uppercase;">Ejecutiva Contable</span><br>
                        <span style="font-size:6.5px; color:#555;">Firma y sello</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
