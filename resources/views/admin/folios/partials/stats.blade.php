<div class="col-md-3">
    <div class="stat-card p-3 d-flex align-items-center gap-3" style="--stat-accent:#405189;">
        <div class="stat-icon" style="background:rgba(64,81,137,.12);color:#405189;"><i class="ri-file-list-line"></i></div>
        <div>
            <div class="stat-label">Total Folios</div>
            <div class="stat-value">{{ $resumen['total'] }}</div>
        </div>
    </div>
</div>
<div class="col-md-3">
    <div class="stat-card p-3 d-flex align-items-center gap-3" style="--stat-accent:#0ab39c;">
        <div class="stat-icon" style="background:rgba(10,179,156,.12);color:#0ab39c;"><i class="ri-lock-unlock-line"></i></div>
        <div>
            <div class="stat-label">Abiertos</div>
            <div class="stat-value">{{ $resumen['abiertos'] }}</div>
        </div>
    </div>
</div>
<div class="col-md-3">
    <div class="stat-card p-3 d-flex align-items-center gap-3" style="--stat-accent:#f06548;">
        <div class="stat-icon" style="background:rgba(240,101,72,.12);color:#f06548;"><i class="ri-lock-line"></i></div>
        <div>
            <div class="stat-label">Cerrados</div>
            <div class="stat-value">{{ $resumen['cerrados'] }}</div>
        </div>
    </div>
</div>
<div class="col-md-3">
    <div class="stat-card p-3 d-flex align-items-center gap-3" style="--stat-accent:#299cdb;">
        <div class="stat-icon" style="background:rgba(41,156,219,.12);color:#299cdb;"><i class="ri-team-line"></i></div>
        <div>
            <div class="stat-label">Total Participantes</div>
            <div class="stat-value">{{ $resumen['total_participantes'] }}</div>
        </div>
    </div>
</div>
