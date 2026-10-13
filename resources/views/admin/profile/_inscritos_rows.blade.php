@forelse($listaInsc as $i => $r)
    @php
        $iniIns = strtoupper(substr($r->nombres ?? '', 0, 1) . substr($r->apellidos ?? '', 0, 1));
        $plan = $r->contado ? 'Contado' : 'Regular';
        $planCls = $r->contado ? 'plan-con' : 'plan-reg';
        $planIcon = $r->contado ? 'ri-bank-card-line' : 'ri-funds-line';
        $esBajaOperativa = (int)($r->baja_operativa ?? 0);
        $esBajaAcademica = (int)($r->baja_academica ?? 0);
        $esRetirado = !(int)$r->estado || $esBajaOperativa || $esBajaAcademica;
        if ($esBajaOperativa) {
            $estCls = 'est-ret'; $estTxt = 'Baja Operativa'; $estIcon = 'ri-close-circle-line';
        } elseif ($esBajaAcademica) {
            $estCls = 'est-ret'; $estTxt = 'Baja Académica'; $estIcon = 'ri-close-circle-line';
        } elseif (!(int)$r->estado) {
            $estCls = 'est-ret'; $estTxt = 'Baja Temprana'; $estIcon = 'ri-close-line';
        } else {
            $estCls = 'est-act'; $estTxt = 'Activo'; $estIcon = 'ri-check-line';
        }
        $esReal = (int)($r->es_real ?? 0);
        $matReq = (float)($r->matricula_requerido ?? 0);
        $matPag = (float)($r->matricula_pagado ?? 0);
        $matFalta = max(0, $matReq - $matPag);
        $colPag = (float)($r->colegiatura_pagado ?? 0);
        $colFalta = 0;
        $colLabel = 'Colegiatura';
        if ((int)($r->contado ?? 0) === 1) {
            $numMod = max(1, (int)($r->num_modulos ?? 1));
            if ((int)($r->modulo_cero ?? 0) === 1) { $numMod = max(1, $numMod - 1); }
            $colReq = ((float)($r->monto_programa ?? 0) - (float)($r->descuento_colegiatura ?? 0)) / $numMod;
            $colFalta = max(0, $colReq - $colPag);
            $colLabel = 'Coleg. (1/'.$numMod.')';
        } else {
            $colFalta = $colPag > 0 ? 0 : (float)($r->colegiatura_primer_monto ?? 0);
            $colLabel = 'Coleg. (1.er pago)';
        }
    @endphp
    <div class="insc-row" style="background:#fff;border:1px solid #eef0f4;border-radius:10px;padding:10px 14px;margin-bottom:6px;box-shadow:0 1px 4px rgba(0,0,0,.04);display:flex;align-items:center;flex-wrap:wrap;gap:8px;">
        <span class="insc-avatar" style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,{{ $esReal ? '#0d8574' : '#b3243a' }},{{ $esReal ? '#6cdb9c' : '#fb8c7a' }});color:#fff;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;flex-shrink:0;">{{ $iniIns }}</span>
        <div class="insc-info" style="flex:1;min-width:180px;">
            <div class="insc-name" style="font-size:.82rem;font-weight:600;color:#212529;">{{ $loop->iteration }}. {{ $r->apellidos }}, {{ $r->nombres }}</div>
            <div class="insc-meta" style="font-size:.67rem;color:#878a99;display:flex;flex-wrap:wrap;gap:2px 10px;margin-top:1px;">
                <span><i class="ri-passport-line"></i> {{ $r->ci }}</span>
                @if(!empty($r->asesor_nombres) || !empty($r->asesor_apellidos))
                    <span><i class="ri-user-voice-line"></i> {{ $r->asesor_apellidos }}, {{ $r->asesor_nombres }}</span>
                @endif
                @if($r->fecha_inscripcion)
                    <span><i class="ri-calendar-check-line"></i> {{ \Carbon\Carbon::parse($r->fecha_inscripcion)->format('d/m/Y') }}</span>
                @endif
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
            <span class="plan-pill {{ $planCls }}" style="font-size:.65rem;padding:2px 8px;border-radius:5px;"><i class="{{ $planIcon }}" style="font-size:.6rem;"></i> {{ $plan }}</span>
            <span class="estado-pill {{ $estCls }}" style="font-size:.65rem;padding:2px 8px;border-radius:5px;"><i class="{{ $estIcon }}" style="font-size:.6rem;"></i> {{ $estTxt }}</span>
            <span class="estado-pill" style="font-size:.65rem;padding:2px 8px;border-radius:5px;background:{{ $esReal ? '#ecfdf5' : '#fef2f2' }};color:{{ $esReal ? '#0d8574' : '#b3243a' }};border:1px solid {{ $esReal ? '#a7f3d0' : '#fecaca' }};">
                <i class="{{ $esReal ? 'ri-check-double-line' : 'ri-close-circle-line' }}" style="font-size:.6rem;"></i> {{ $esReal ? 'Real' : 'No Real' }}
            </span>
        </div>
        @if(!$esReal && ($matFalta > 0 || $colFalta > 0))
            <div style="width:100%;display:flex;flex-wrap:wrap;gap:6px;padding-top:4px;border-top:1px dashed #eef0f4;margin-top:2px;">
                @if($matFalta > 0)
                    <span style="font-size:.62rem;color:#f06548;background:#fef2f2;padding:2px 8px;border-radius:4px;">
                        <i class="ri-money-dollar-circle-line"></i> Matrícula: Bs {{ number_format($matFalta, 2, ',', '.') }}
                    </span>
                @endif
                @if($colFalta > 0)
                    <span style="font-size:.62rem;color:#f06548;background:#fef2f2;padding:2px 8px;border-radius:4px;">
                        <i class="ri-money-dollar-circle-line"></i> {{ $colLabel }}: Bs {{ number_format($colFalta, 2, ',', '.') }}
                    </span>
                @endif
            </div>
        @endif
    </div>
@empty
    <div style="text-align:center;padding:2.5rem 1rem;color:#878a99;">
        <i class="ri-inbox-2-line" style="font-size:2.5rem;display:block;margin-bottom:.5rem;opacity:.5;"></i>
        <p class="mb-0" style="font-size:.88rem;">No hay inscritos para mostrar.</p>
    </div>
@endforelse
