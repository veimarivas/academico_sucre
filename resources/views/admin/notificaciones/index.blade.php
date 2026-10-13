@extends('admin.layouts.master')

@section('title', 'Notificaciones - ' . config('app.name'))

@php
$colorEstadoMod = ['No Inicio'=>'#878a99','En Desarrollo'=>'#299cdb','Pendiente'=>'#f7b84b','Suspendido'=>'#f06548','Concluido'=>'#0ab39c'];
$colorEstadoHor = ['Programado'=>'#878a99','Confirmado'=>'#299cdb','Desarrollado'=>'#0ab39c','Postergado'=>'#f7b84b'];
@endphp

@push('css')
<style>
.noti-hero {
    position: relative;
    background: linear-gradient(135deg, #405189 0%, #5e72b8 50%, #299cdb 100%);
    border-radius: 14px;
    padding: 1.5rem 1.75rem;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 10px 30px -12px rgba(64,81,137,.45);
    margin-bottom: 1.25rem;
}
.noti-hero::before { content: ""; position: absolute; top: -40px; right: -40px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%); border-radius: 50%; }
.noti-hero::after { content: ""; position: absolute; bottom: -60px; left: 30%; width: 260px; height: 260px; background: radial-gradient(circle, rgba(255,255,255,.10) 0%, transparent 70%); border-radius: 50%; }
.noti-hero > * { position: relative; z-index: 1; }
.noti-hero .hero-icon { width: 56px; height: 56px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.25); border-radius: 14px; font-size: 1.6rem; backdrop-filter: blur(4px); }
.noti-hero h4 { color: #fff; font-weight: 700; letter-spacing: .2px; margin: 0; }
.noti-hero .hero-sub { color: rgba(255,255,255,.85); font-size: .88rem; margin: 0; }

/* ===== Programa Card ===== */
.prog-card {
    border: 1px solid #eef0f4;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 4px 20px -10px rgba(64,81,137,.16);
    overflow: hidden;
    margin-bottom: 1.25rem;
}
.prog-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.35rem;
    background: #fafbfc;
    border-bottom: 1px solid #eef0f4;
    cursor: pointer;
    user-select: none;
    transition: background .15s;
}
.prog-card-header:hover { background: #f5f6f8; }
.prog-card-header .prog-icon {
    width: 36px; height: 36px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 10px;
    font-size: 1.1rem;
    background: rgba(64,81,137,.1);
    color: #405189;
}
.prog-card-header .prog-summary {
    display: flex;
    align-items: center;
    gap: .35rem 1rem;
    flex-wrap: wrap;
    font-size: .72rem;
    color: #878a99;
}
.prog-card-body { padding: 0; }
.prog-card-body.empty {
    text-align: center;
    padding: 3rem 1rem;
    color: #adb5bd;
}
.prog-card-body.empty i { font-size: 2.6rem; display: block; margin-bottom: .5rem; color: #d0d4da; }

/* ===== Módulo ===== */
.mod-section {
    border-bottom: 1px solid #f1f3f6;
}
.mod-section:last-child { border-bottom: none; }
.mod-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .65rem 1.35rem .65rem 2.2rem;
    background: #fff;
    border-bottom: 1px solid #f5f6f8;
}
.mod-header .mod-icon {
    width: 28px; height: 28px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 8px;
    font-size: .82rem;
    background: rgba(64,81,137,.07);
    color: #405189;
}

/* ===== Tipo notificación ===== */
.tipo-section { }
.tipo-header {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .4rem 1.35rem .4rem 3.2rem;
    font-size: .75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .3px;
    color: #495057;
}
.tipo-header .tipo-icon { font-size: .82rem; }

/* ===== Estado group ===== */
.estado-group { }
.estado-header {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .25rem 1.35rem .25rem 4.2rem;
    font-size: .7rem;
    font-weight: 500;
    color: #6c757d;
}

/* ===== Notification row ===== */
.noti-row {
    display: flex;
    align-items: flex-start;
    padding: .7rem 1.35rem .7rem 5.4rem;
    border-top: 1px solid #f8f9fa;
    transition: background .15s;
    gap: .85rem;
    text-decoration: none;
    color: inherit;
}
.noti-row:hover { background: rgba(64,81,137,.035); }
.noti-row .nr-content { min-width: 0; flex: 1; }
.noti-row .nr-content .nr-desc { font-size: .82rem; line-height: 1.4; }
.noti-row .nr-content .nr-desc strong { font-weight: 600; }
.noti-row .nr-content .nr-meta {
    display: flex;
    flex-wrap: wrap;
    gap: .25rem .75rem;
    margin-top: .2rem;
    font-size: .7rem;
    color: #878a99;
}
.noti-row .nr-content .nr-meta span { display: inline-flex; align-items: center; gap: .25rem; }
.noti-row .nr-action {
    flex-shrink: 0;
    align-self: center;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: .2rem;
}
.noti-row .nr-action .badge-estado {
    font-size: .65rem;
    font-weight: 600;
    padding: .15rem .55rem;
    border-radius: 20px;
    white-space: nowrap;
}
.noti-row .nr-action .nr-arrow {
    font-size: .78rem;
    color: #c0c4cc;
    transition: color .15s;
}
.noti-row:hover .nr-action .nr-arrow { color: #405189; }
</style>
@endpush

@section('content')
<div class="noti-hero d-flex align-items-center gap-3">
    <span class="hero-icon"><i class="ri-notification-3-line"></i></span>
    <div>
        <h4>Notificaciones</h4>
        <div class="hero-sub">{{ $total }} pendiente(s) por atender</div>
    </div>
</div>

@if($total === 0)
<div class="text-center text-muted py-5">
    <i class="ri-inbox-line fs-40 d-block mb-3" style="color:#d0d4da;"></i>
    <span style="font-size:.95rem;">No hay notificaciones pendientes</span>
</div>
@else
{{-- Tabs por gestión --}}
<ul class="nav nav-tabs nav-tabs-custom mb-4" id="notiTabs" role="tablist">
    @foreach($gestiones as $idx => $gestion)
    @php
        $progs = $groupedByGestion[$gestion];
        $tabTotal = array_sum(array_map(function($p) {
            return array_sum(array_map(function($m) {
                return array_sum(array_map(function($t) {
                    return array_sum(array_map(function($e) { return count($e['items']); }, $t['estados']));
                }, $m['tipos']));
            }, $p['modulos']));
        }, $progs));
    @endphp
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $idx === 0 ? 'active' : '' }}" id="tab-{{ \Illuminate\Support\Str::slug($gestion) }}" data-bs-toggle="tab" data-bs-target="#pane-{{ \Illuminate\Support\Str::slug($gestion) }}" type="button" role="tab" aria-controls="pane-{{ \Illuminate\Support\Str::slug($gestion) }}" aria-selected="{{ $idx === 0 ? 'true' : 'false' }}">
            <i class="ri-calendar-line me-1"></i>Gestión {{ $gestion }}
            <span class="badge bg-danger rounded-pill ms-1" style="font-size:.6rem;">{{ $tabTotal }}</span>
        </button>
    </li>
    @endforeach
</ul>

<div class="tab-content" id="notiTabsContent">
    @foreach($gestiones as $idx => $gestion)
    @php $progs = $groupedByGestion[$gestion]; @endphp
    <div class="tab-pane fade {{ $idx === 0 ? 'show active' : '' }}" id="pane-{{ \Illuminate\Support\Str::slug($gestion) }}" role="tabpanel" aria-labelledby="tab-{{ \Illuminate\Support\Str::slug($gestion) }}">
        @foreach($progs as $prog)
        <div class="prog-card">
            <div class="prog-card-header" onclick="this.nextElementSibling.classList.toggle('d-none');this.querySelector('.collapse-icon').classList.toggle('ri-arrow-down-s-line');this.querySelector('.collapse-icon').classList.toggle('ri-arrow-up-s-line');">
                <div class="d-flex align-items-center gap-2">
                    <span class="prog-icon"><i class="ri-graduation-cap-line"></i></span>
                    <div>
                        <span class="fw-semibold fs-15">{{ $prog['programa_nombre'] }}</span>
                        <div class="prog-summary">
                            @if($prog['tipo_nombre'])
                            <span style="display:inline-flex;align-items:center;gap:.25rem;padding:.05rem .45rem;border-radius:10px;background:rgba(41,156,219,.1);color:#299cdb;font-weight:600;">{{ $prog['tipo_nombre'] }}</span>
                            @endif
                            <span><i class="ri-stack-line"></i>{{ count($prog['modulos']) }} módulo(s)</span>
                            @php
                                $progTotalItems = array_sum(array_map(function($m) {
                                    return array_sum(array_map(function($t) {
                                        return array_sum(array_map(function($e) { return count($e['items']); }, $t['estados']));
                                    }, $m['tipos']));
                                }, $prog['modulos']));
                            @endphp
                            <span><i class="ri-notification-3-line"></i>{{ $progTotalItems }} notificación(es)</span>
                        </div>
                    </div>
                </div>
                <i class="collapse-icon ri-arrow-up-s-line fs-18 text-muted"></i>
            </div>

            <div class="prog-card-body" style="display:block;">
                @foreach($prog['modulos'] as $mod)
                <div class="mod-section">
                    <div class="mod-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="mod-icon"><i class="ri-book-2-line"></i></span>
                            <span class="fw-medium fs-13">{{ $mod['modulo_nombre'] }}</span>
                            @if($mod['docente'] && $mod['docente'] !== '—')
                            <span class="text-muted" style="font-size:.7rem;"><i class="ri-user-star-line"></i>{{ $mod['docente'] }}</span>
                            @endif
                        </div>
                        @php
                            $modTotal = array_sum(array_map(function($t) {
                                return array_sum(array_map(function($e) { return count($e['items']); }, $t['estados']));
                            }, $mod['tipos']));
                        @endphp
                        <span style="font-size:.68rem;font-weight:600;color:#878a99;">{{ $modTotal }} notificación(es)</span>
                    </div>

                    @foreach($mod['tipos'] as $tipo)
                    <div class="tipo-section">
                        <div class="tipo-header">
                            <span class="tipo-icon" style="color:{{ $tipo['color'] }};"><i class="{{ $tipo['icono'] }}"></i></span>
                            {{ $tipo['tipo_label'] }}
                            @php $tipoTotal = array_sum(array_map(function($e) { return count($e['items']); }, $tipo['estados'])); @endphp
                            <span style="font-size:.65rem;font-weight:600;padding:.05rem .45rem;border-radius:10px;background:{{ $tipo['color'] }}18;color:{{ $tipo['color'] }};">{{ $tipoTotal }}</span>
                        </div>

                        @foreach($tipo['estados'] as $est)
                        <div class="estado-group">
                            <div class="estado-header">
                                <i class="ri-flag-2-line" style="font-size:.65rem;"></i>
                                Estado: <strong>{{ $est['estado'] }}</strong>
                                <span style="font-size:.65rem;color:#adb5bd;">({{ count($est['items']) }})</span>
                            </div>

                            @foreach($est['items'] as $item)
                            @php
                            if ($tipo['tipo_key'] === 'cambio_estado') {
                                $eColor = $colorEstadoMod[$item->modulo_estado] ?? '#878a99';
                                $eTexto = $item->modulo_estado;
                            } else {
                                $eColor = $colorEstadoHor[$item->estado] ?? '#878a99';
                                $eTexto = $item->estado;
                            }
                            @endphp
                            <a href="{{ $item->url }}" class="noti-row">
                                <div class="nr-content">
                                    <div class="nr-desc">{!! $item->mensaje !!}</div>
                                    <div class="nr-meta">
                                        <span><i class="ri-calendar-line"></i>{{ $item->fecha }}</span>
                                        @if(($item->hora_inicio ?? null) && $tipo['tipo_key'] !== 'cambio_estado')
                                        <span><i class="ri-time-line"></i>{{ substr($item->hora_inicio, 0, 5) }}{{ $item->hora_fin ? ' - '.substr($item->hora_fin, 0, 5) : '' }}</span>
                                        @endif
                                        @if($tipo['tipo_key'] === 'cambio_estado' && ($item->horarios_pasados ?? null))
                                        <span><i class="ri-time-line"></i>{{ $item->horarios_pasados }} horario(s) pasados</span>
                                        @endif
                                        @if($item->docente && $item->docente !== '—')
                                        <span><i class="ri-user-star-line"></i>{{ $item->docente }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="nr-action">
                                    <span class="badge-estado" style="color:{{ $eColor }};background:{{ $eColor }}18;">{{ $eTexto }}</span>
                                    <span class="nr-arrow"><i class="ri-arrow-right-s-line"></i></span>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endforeach
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tabEls = document.querySelectorAll('#notiTabs .nav-link');
    tabEls.forEach(function (el) {
        el.addEventListener('shown.bs.tab', function () {
            // Guardar último tab activo en localStorage
            localStorage.setItem('notiLastTab', el.id);
        });
    });

    // Restaurar último tab activo
    var lastTab = localStorage.getItem('notiLastTab');
    if (lastTab) {
        var tab = document.getElementById(lastTab);
        if (tab) {
            var tabInstance = new bootstrap.Tab(tab);
            tabInstance.show();
        }
    }
});
</script>
@endpush
@endif
@endsection