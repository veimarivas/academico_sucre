{{-- Modal reutilizable: progreso de un lote de Seguimiento ESAM --}}
<div class="modal fade" id="esamLoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-whatsapp-line align-bottom me-1 text-success"></i>
                    Enviando por WhatsApp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-2" id="esamLoteNombre">Lote</p>
                <div class="progress" style="height:10px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" id="esamLoteBar" role="progressbar" style="width:0%"></div>
                </div>
                <div class="d-flex justify-content-between mt-2 small">
                    <span>Enviados <strong id="esamLoteSent">0</strong> / <span id="esamLoteTotal">0</span></span>
                    <span id="esamLotePct">0%</span>
                </div>
                <div class="mt-2 small text-muted" id="esamLoteInfo"></div>
                <div class="mt-2 p-2 rounded small" id="esamLoteAntiBan" style="display:none;background:rgba(64,81,137,.06);border:1px dashed rgba(64,81,137,.25);"></div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('admin.seguimiento-esam') }}" target="_blank" class="btn btn-light btn-sm">
                    <i class="ri-external-link-line align-bottom"></i> Ver en Seguimiento ESAM
                </a>
                <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
window.EsamLote = (function () {
    var modal = null;
    var timer = null;
    var base = @json($loteBase ?? url('admin/whatsapp/lote'));

    function el(id) { return document.getElementById(id); }

    function render(d) {
        var sent = d.sent || 0;
        var total = d.total || 0;
        var failed = d.failed || 0;
        var skipped = d.skipped || 0;
        var pct = (d.progress != null) ? d.progress : (total ? Math.round(((sent + failed + skipped) / total) * 100) : 0);

        el('esamLoteSent').textContent = sent;
        el('esamLoteTotal').textContent = total;
        el('esamLotePct').textContent = pct + '%';
        el('esamLoteBar').style.width = pct + '%';

        var done = (d.status === 'done');
        el('esamLoteBar').className = 'progress-bar' + (done ? ' bg-success' : ' progress-bar-striped progress-bar-animated');

        var partes = [];
        partes.push('estado: ' + (d.status || '—'));
        if (failed > 0) partes.push(failed + ' fallido(s)');
        if (skipped > 0) partes.push(skipped + ' omitido(s)');
        if (d.pending > 0) partes.push(d.pending + ' pendiente(s)');
        // Motivo real que informa Seguimiento ESAM (con la hora estimada de reanudación).
        if (d.status === 'running' && d.waiting_reason) {
            var hora = '';
            if (d.waiting_until) {
                var t = new Date(d.waiting_until);
                if (!isNaN(t)) hora = ' · sigue ' + (t.toDateString() === new Date().toDateString() ? 'a las ' : 'el ' + t.toLocaleDateString('es-BO', { weekday: 'long' }) + ' a las ') +
                    t.toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' });
            }
            partes.push('en espera: ' + d.waiting_reason + hora);
        } else if (d.status === 'running' && (sent + failed + skipped) === 0) {
            partes.push('en cola: el primer mensaje sale en breve');
        }
        if (d.status === 'paused') {
            partes.push(d.paused_reason ? 'pausado: ' + d.paused_reason : 'pausado por el sistema');
        }
        el('esamLoteInfo').textContent = partes.join(' · ');

        // Detalle anti-baneo de la línea: ritmo, límites y tiempo estimado.
        var ab = d.anti_ban, box = el('esamLoteAntiBan');
        if (ab) {
            var esc = function (s) { return String(s == null ? '' : s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); };
            var eta = ab.eta_minutes > 0 && d.status !== 'done'
                ? (ab.eta_minutes >= 60 ? Math.floor(ab.eta_minutes / 60) + ' h ' + (ab.eta_minutes % 60) + ' min' : ab.eta_minutes + ' min')
                : null;
            box.innerHTML =
                '<div class="fw-semibold mb-1"><i class="ri-shield-star-line"></i> Anti-baneo de la línea</div>' +
                (ab.line_connected === false ? '<div class="text-danger fw-semibold mb-1"><i class="ri-error-warning-line"></i> La línea no está conectada: revisá «Conexión» en Seguimiento ESAM.</div>' : '') +
                '<div>Ritmo: <b>' + esc(ab.pace) + '</b>' + (eta ? ' · Tiempo estimado: <b>~' + esc(eta) + '</b>' : '') + '</div>' +
                '<div>Hoy: <b>' + esc(ab.daily_used) + ' / ' + esc(ab.daily_limit) + '</b> · Última hora: <b>' + esc(ab.hourly_used) + ' / ' + esc(ab.hourly_limit) + '</b>' +
                ' · Contactos nuevos: <b>' + esc(ab.new_contacts_today) + ' / ' + esc(ab.new_contacts_limit) + '</b></div>' +
                '<div class="text-muted">Horario de envío: ' + esc(ab.window) + '. Si un límite se alcanza, el lote espera solo y continúa.</div>';
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    }

    function poll(batchId) {
        fetch(base + '/' + batchId, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (!d || d.success === false) {
                    el('esamLoteInfo').textContent = (d && d.message) || 'No se pudo consultar el lote.';
                    clearInterval(timer); timer = null;
                    return;
                }
                render(d);
                if (d.status === 'done' || d.status === 'cancelled') {
                    clearInterval(timer); timer = null;
                }
            })
            .catch(function () { /* reintenta en el próximo ciclo */ });
    }

    return {
        abrir: function (batchId, nombre) {
            if (!batchId) return;
            if (!modal) modal = new bootstrap.Modal(el('esamLoteModal'));
            el('esamLoteNombre').textContent = nombre || ('Lote #' + batchId);
            render({ status: 'running', sent: 0, total: 0, failed: 0, skipped: 0, pending: 0, progress: 0 });
            modal.show();
            clearInterval(timer);
            poll(batchId);
            timer = setInterval(function () { poll(batchId); }, 3000);
        }
    };
})();
</script>
