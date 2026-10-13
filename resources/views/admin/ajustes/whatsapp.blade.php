@extends('admin.layouts.master')

@section('title', 'Ajustes WhatsApp - ' . config('app.name'))

@push('css')
<style>
/* ── Hero ── */
.wa-hero {
    position: relative;
    background: linear-gradient(135deg, #075e54 0%, #128c7e 50%, #25d366 100%);
    border-radius: 18px; padding: 1.5rem 1.75rem; color: #fff; overflow: hidden;
    box-shadow: 0 16px 38px -14px rgba(7,94,84,.65); margin-bottom: 1.35rem;
}
.wa-hero::before { content:""; position:absolute; top:-50px; right:-30px; width:220px; height:220px; background:radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%); border-radius:50%; }
.wa-hero::after { content:""; position:absolute; bottom:-60px; left:25%; width:260px; height:260px; background:radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 70%); border-radius:50%; }
.wa-hero > * { position: relative; z-index: 1; }
.wa-hero .hero-ic {
    width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3);
    border-radius: 14px; font-size: 1.6rem; flex-shrink: 0;
}
.wa-hero h4 { color: #fff; font-weight: 800; margin: 0; letter-spacing: -.01em; }
.wa-hero .hero-sub { color: rgba(255,255,255,.82); font-size: .85rem; margin: 2px 0 0; }
.wa-hero .breadcrumb { background: transparent; padding: 0; margin: 0; }
.wa-hero .breadcrumb-item,
.wa-hero .breadcrumb-item a { color: rgba(255,255,255,.78); }
.wa-hero .breadcrumb-item.active { color: #fff; font-weight: 600; }
.wa-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.5); }

/* ── Cards ── */
.wa-card {
    background: var(--ui-surface, #fff);
    border: 1px solid var(--ui-line, #eef0f4);
    border-radius: 16px;
    box-shadow: var(--ui-shadow, 0 4px 18px -10px rgba(64,81,137,.18));
    overflow: hidden;
    height: 100%;
}
.wa-card-header {
    display: flex; align-items: center; gap: .75rem;
    padding: 1rem 1.25rem;
    background: var(--ui-header-tint, rgba(29,74,128,.045));
    border-bottom: 1px solid var(--ui-line, #eef0f4);
}
.wa-card-header .wa-card-icon {
    width: 40px; height: 40px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.15rem; flex-shrink: 0;
}
.wa-card-header h6 { margin: 0; font-size: .92rem; font-weight: 800; color: var(--ui-ink, #1b2537); }
.wa-card-header small { display: block; font-size: .72rem; color: var(--ui-muted, #98a2b3); margin-top: 1px; font-weight: 500; }
.wa-card-body { padding: 1.25rem; }

/* ── Formulario ── */
.wa-field { margin-bottom: 1.15rem; }
.wa-field:last-of-type { margin-bottom: 1.25rem; }
.wa-field label {
    display: block; font-size: .78rem; font-weight: 700;
    color: var(--ui-ink-2, #475467); margin-bottom: .4rem;
}
.wa-field .form-control {
    border-radius: 10px; font-size: .88rem; padding: .6rem .85rem;
    border-color: var(--ui-line-strong, #d4dae5);
    transition: border-color .18s ease, box-shadow .18s ease;
}
.wa-field .form-control:focus {
    border-color: var(--ui-accent, #163b6b);
    box-shadow: 0 0 0 3px var(--ui-ring, rgba(29,74,128,.16));
}
.wa-field .form-text { font-size: .72rem; color: var(--ui-muted, #98a2b3); margin-top: .35rem; }
.wa-field-code {
    font-family: "SF Mono", "Fira Code", "Fira Mono", "Roboto Mono", monospace;
    font-size: .92rem; font-weight: 600; letter-spacing: .04em;
}

/* ── Status items ── */
.wa-status-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: .65rem;
}
.wa-status-item {
    display: flex; align-items: center; gap: .65rem;
    padding: .7rem .85rem;
    background: var(--ui-surface-2, #f7f9fc);
    border: 1px solid var(--ui-line, #eef0f4);
    border-radius: 12px;
    overflow: hidden;
}
.wa-status-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}
.wa-status-label { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: var(--ui-muted, #98a2b3); }
.wa-status-value { font-size: .88rem; font-weight: 700; color: var(--ui-ink, #1b2537); margin-top: 1px; }

/* ── Connection indicator ── */
.wa-connected-dot {
    width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
    display: inline-block; vertical-align: middle;
}
.wa-connected-dot.on { background: #128c7e; box-shadow: 0 0 0 4px rgba(18,140,126,.18); }
.wa-connected-dot.off { background: #f06548; box-shadow: 0 0 0 4px rgba(240,101,72,.16); }

/* ── Info alert ── */
.wa-info-alert {
    display: flex; align-items: flex-start; gap: .6rem;
    padding: .7rem .85rem; border-radius: 10px;
    font-size: .78rem; line-height: 1.45;
    margin-top: 1rem;
}
.wa-info-alert i { font-size: 1rem; margin-top: 1px; flex-shrink: 0; }
.wa-info-alert.warning { background: var(--ui-warning-soft, #fbf0da); color: #925f13; border: 1px solid #f2dcb0; }
.wa-info-alert.info { background: var(--ui-info-soft, #e7f1fb); color: #175a9c; border: 1px solid #c4def6; }
.wa-info-alert.danger { background: var(--ui-danger-soft, #fbe9ec); color: #8f1b2d; border: 1px solid #f6cdd4; }

/* ── Readonly list ── */
.wa-readonly-item {
    display: flex; align-items: center; justify-content: space-between; gap: .75rem;
    padding: .65rem 0;
    border-bottom: 1px dashed var(--ui-line, #eef0f4);
}
.wa-readonly-item:last-child { border-bottom: 0; }
.wa-readonly-label { font-size: .78rem; color: var(--ui-muted, #98a2b3); }
.wa-readonly-value { font-size: .85rem; font-weight: 700; color: var(--ui-ink, #1b2537); font-family: "SF Mono", "Fira Code", monospace; letter-spacing: .03em; }

/* ── Botón externo ── */
.wa-btn-esam {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .5rem 1rem; border-radius: 10px;
    font-size: .8rem; font-weight: 700;
    border: 1px solid rgba(255,255,255,.35);
    background: rgba(255,255,255,.15);
    color: #fff; text-decoration: none;
    transition: background .18s ease, border-color .18s ease;
    backdrop-filter: blur(4px);
}
.wa-btn-esam:hover {
    background: rgba(255,255,255,.28);
    border-color: rgba(255,255,255,.5);
    color: #fff;
}

@media (max-width: 575.98px) {
    .wa-hero { padding: 1.2rem 1rem; }
    .wa-hero .hero-ic { width: 44px; height: 44px; font-size: 1.3rem; }
    .wa-status-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
@php $esamUrl = config('services.seguimiento_esam.panel_url'); @endphp

{{-- Hero --}}
<div class="wa-hero">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="hero-ic"><i class="ri-whatsapp-line"></i></div>
            <div>
                <h4>Ajustes de WhatsApp</h4>
                <p class="hero-sub">Configuración del número autorizado y estado de conexión con Seguimiento ESAM</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($esamUrl)
                <a href="{{ $esamUrl }}" target="_blank" class="wa-btn-esam">
                    <i class="ri-external-link-line"></i> Abrir Seguimiento ESAM
                </a>
            @endif
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-3-line align-bottom me-1"></i>Admin</a></li>
                <li class="breadcrumb-item active">WhatsApp</li>
            </ol>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    {{-- Número autorizado --}}
    <div class="col-lg-6">
        <div class="wa-card">
            <div class="wa-card-header">
                <div class="wa-card-icon" style="background: var(--ui-accent-soft, #eef1f8); color: var(--ui-accent, #163b6b);">
                    <i class="ri-phone-line"></i>
                </div>
                <div>
                    <h6>Número del usuario</h6>
                    <small>Tu número registrado y su autorización para ESAM</small>
                </div>
            </div>
            <div class="wa-card-body">
                <div class="mb-3">
                    <div class="wa-readonly-item">
                        <span class="wa-readonly-label">Número registrado</span>
                        <span class="wa-readonly-value" style="font-family:'SF Mono','Fira Code',monospace;">{{ $numeroUsuario ?: 'No registrado' }}</span>
                    </div>
                    <div class="wa-readonly-item">
                        <span class="wa-readonly-label">Autorizado</span>
                        <span class="wa-readonly-value">
                            @if($usuarioAutorizado)
                                <span class="badge bg-success-subtle text-success">Sí</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">No</span>
                            @endif
                        </span>
                    </div>
                    @if(!empty($usuarioArea))
                    <div class="wa-readonly-item">
                        <span class="wa-readonly-label">Área</span>
                        <span class="wa-readonly-value">{{ $usuarioArea }}</span>
                    </div>
                    @endif
                    @can('whatsapp.admin')
                    <div class="wa-readonly-item">
                        <span class="wa-readonly-label">Número global (config)</span>
                        <span class="wa-readonly-value" style="font-family:'SF Mono','Fira Code',monospace;">{{ $numero ?: 'No restringido' }}</span>
                    </div>
                    @endcan
                </div>
                <div class="wa-info-alert info mb-0">
                    <i class="ri-information-line"></i>
                    <span>Tu número registrado se administra desde Usuarios. Solo un administrador puede cambiarlo.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Estado de Seguimiento ESAM --}}
    <div class="col-lg-6">
        <div class="wa-card">
            <div class="wa-card-header">
                <div class="wa-card-icon" style="background: var(--ui-success-soft, #e6f7ee); color: var(--ui-success, #0d8574);">
                    <i class="ri-plug-line"></i>
                </div>
                <div>
                    <h6>Estado de Seguimiento ESAM</h6>
                    <small>Conexión activa y verificación del número</small>
                </div>
            </div>
            <div class="wa-card-body">
                @if($error)
                    <div class="wa-info-alert danger mb-0">
                        <i class="ri-error-warning-line"></i>
                        <span>{{ $error }}</span>
                    </div>
                @else
                    @php
                        $session = $health['session'] ?? [];
                        $queue = $health['queue'] ?? [];
                        $connected = (bool) ($session['connected'] ?? false);
                        $verified = (bool) ($session['verified'] ?? false);
                        $phone = $session['phone'] ?? '—';
                        $pending = $queue['pending'] ?? 0;
                        $failed = $queue['failed'] ?? 0;
                    @endphp

                    <div class="wa-status-grid">
                        <div class="wa-status-item">
                            <div class="wa-status-icon" style="background: {{ $connected ? 'var(--ui-success-soft, #e6f7ee)' : 'var(--ui-danger-soft, #fbe9ec)' }}; color: {{ $connected ? 'var(--ui-success, #0d8574)' : 'var(--ui-danger, #b3243a)' }};">
                                <i class="ri-{{ $connected ? 'wifi' : 'wifi-off' }}-line"></i>
                            </div>
                            <div>
                                <div class="wa-status-label">Conexión</div>
                                <div class="wa-status-value d-flex align-items-center gap-2">
                                    <span class="wa-connected-dot {{ $connected ? 'on' : 'off' }}"></span>
                                    {{ $connected ? 'Conectado' : 'Desconectado' }}
                                </div>
                            </div>
                        </div>

                        <div class="wa-status-item">
                            <div class="wa-status-icon" style="background: var(--ui-accent-soft, #eef1f8); color: var(--ui-accent, #163b6b);">
                                <i class="ri-phone-line"></i>
                            </div>
                            <div>
                                <div class="wa-status-label">Número vinculado</div>
                                <div class="wa-status-value" style="font-family: 'SF Mono', 'Fira Code', monospace; font-size: .82rem;">{{ $phone }}</div>
                            </div>
                        </div>

                        <div class="wa-status-item">
                            <div class="wa-status-icon" style="background: {{ $verified ? 'var(--ui-success-soft, #e6f7ee)' : 'var(--ui-warning-soft, #fbf0da)' }}; color: {{ $verified ? 'var(--ui-success, #0d8574)' : 'var(--ui-warning, #b7791f)' }};">
                                <i class="ri-{{ $verified ? 'shield-check' : 'shield-cross' }}-line"></i>
                            </div>
                            <div>
                                <div class="wa-status-label">Verificación</div>
                                <div class="wa-status-value">{{ $verified ? 'Verificado' : 'Pendiente' }}</div>
                            </div>
                        </div>

                        <div class="wa-status-item">
                            <div class="wa-status-icon" style="background: {{ ($pending + $failed) > 0 ? 'var(--ui-warning-soft, #fbf0da)' : 'var(--ui-surface-2, #f7f9fc)' }}; color: {{ ($pending + $failed) > 0 ? 'var(--ui-warning, #b7791f)' : 'var(--ui-muted, #98a2b3)' }};">
                                <i class="ri-bar-chart-2-line"></i>
                            </div>
                            <div>
                                <div class="wa-status-label">Cola / Fallidos</div>
                                <div class="wa-status-value">{{ $pending }} / {{ $failed }}</div>
                            </div>
                        </div>
                    </div>

                    @if(!$connected)
                        <div class="wa-info-alert warning mb-0">
                            <i class="ri-qr-code-line"></i>
                            <span>Vinculá el número desde el panel de Seguimiento ESAM <strong>(Ajustes → Mostrar QR)</strong>, respetando el número autorizado.</span>
                        </div>
                    @elseif(!$verified)
                        <div class="wa-info-alert info mb-0">
                            <i class="ri-information-line"></i>
                            <span>El número vinculado aún no coincide con el autorizado. Guardá el número autorizado correcto.</span>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
