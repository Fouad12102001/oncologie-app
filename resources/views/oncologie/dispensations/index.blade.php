@extends('layouts.app')

@section('title', 'Dispensations')

@section('content')
<div class="container-fluid py-4 px-4">

<style>
/* ═══════════════════════════════════════════════════
   TOKENS — CLCC ONCOLOGIE · DISPENSATIONS
   Même palette que le module Prescriptions
   ═══════════════════════════════════════════════════ */
:root {
    --c-navy:      #0b1d35;
    --c-navy-mid:  #102748;
    --c-navy-lt:   #1a3a5c;
    --c-teal:      #0d9488;
    --c-teal-lt:   #14b8a6;
    --c-teal-pale: #ccfbf1;
    --c-sky:       #0ea5e9;
    --c-emerald:   #10b981;
    --c-amber:     #f59e0b;
    --c-rose:      #f43f5e;
    --c-crimson:   #9f1239;
    --c-violet:    #7c3aed;
    --c-slate-50:  #f8fafc;
    --c-slate-100: #f1f5f9;
    --c-slate-200: #e2e8f0;
    --c-slate-400: #94a3b8;
    --c-slate-600: #475569;
    --c-slate-800: #1e293b;
    --c-white:     #ffffff;
    --radius-sm:   8px;
    --radius-md:   12px;
    --radius-lg:   18px;
    --radius-xl:   24px;
    --shadow-sm:   0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md:   0 4px 16px rgba(0,0,0,.08);
    --shadow-lg:   0 8px 32px rgba(0,0,0,.10);
}

body { background: var(--c-slate-50); }

/* ── HERO ── */
.onco-hero {
    background: linear-gradient(135deg, var(--c-navy) 0%, var(--c-navy-mid) 55%, var(--c-navy-lt) 100%);
    border-radius: var(--radius-xl);
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(11,29,53,.35);
}
.onco-hero::before {
    content: ''; position: absolute; top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(13,148,136,.25) 0%, transparent 70%);
    pointer-events: none;
}
.onco-hero::after {
    content: ''; position: absolute; bottom: -40px; left: 30%;
    width: 180px; height: 180px;
    background: radial-gradient(circle, rgba(14,165,233,.12) 0%, transparent 70%);
    pointer-events: none;
}
.onco-hero__eyebrow {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(13,148,136,.18); border: 1px solid rgba(13,148,136,.35);
    color: var(--c-teal-lt); font-size: .7rem; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; padding: .25rem .75rem; border-radius: 99px; margin-bottom: 10px;
}
.onco-hero__title { font-size: 1.6rem; font-weight: 800; color: var(--c-white); margin: 0 0 4px; letter-spacing: -.02em; }
.onco-hero__sub  { color: rgba(255,255,255,.5); font-size: .85rem; margin: 0; }
.onco-hero__stats { display:flex; gap:20px; margin-top:14px; flex-wrap:wrap; }
.onco-hero__stat { color:rgba(255,255,255,.75); font-size:.78rem; }
.onco-hero__stat b { color:var(--c-teal-lt); font-size:1rem; display:block; }

/* ── KPI GRID ── */
.kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
@media (max-width: 1100px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
.kpi-card {
    background: var(--c-white); border-radius: var(--radius-lg); padding: 18px 20px;
    box-shadow: var(--shadow-md); border-top: 3px solid; position: relative; overflow: hidden;
    transition: transform .18s, box-shadow .18s;
}
.kpi-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
.kpi-card::after {
    content: attr(data-icon); position: absolute; right: 14px; top: 12px;
    font-size: 1.6rem; opacity: .12;
}
.kpi-card--sky     { border-color: var(--c-sky); }
.kpi-card--emerald { border-color: var(--c-emerald); }
.kpi-card--amber   { border-color: var(--c-amber); }
.kpi-card--violet  { border-color: var(--c-violet); }
.kpi-label { font-size: .72rem; font-weight: 600; color: var(--c-slate-400); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px; }
.kpi-value { font-size: 2rem; font-weight: 900; color: var(--c-slate-800); line-height: 1; letter-spacing: -.03em; }
.kpi-card--sky     .kpi-value { color: var(--c-sky); }
.kpi-card--emerald .kpi-value { color: var(--c-emerald); }
.kpi-card--amber   .kpi-value { color: var(--c-amber); }
.kpi-card--violet  .kpi-value { color: var(--c-violet); }

/* ── TOOLBAR ── */
.toolbar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
.toolbar__group { display: flex; gap: 8px; flex-wrap: wrap; }
.btn-med {
    display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: var(--radius-md);
    font-size: .82rem; font-weight: 700; border: none; cursor: pointer; text-decoration: none;
    white-space: nowrap; transition: filter .15s, transform .12s;
}
.btn-med:hover { filter: brightness(1.1); transform: translateY(-1px); }
.btn-med--add    { background: var(--c-teal);      color: #fff; }
.btn-med--export { background: var(--c-slate-800); color: #fff; }
.btn-med--print  { background: var(--c-violet);    color: #fff; }

/* ── FILTER PANEL ── */
.filter-panel { background: var(--c-white); border-radius: var(--radius-lg); padding: 20px 24px; margin-bottom: 20px; box-shadow: var(--shadow-sm); border: 1px solid var(--c-slate-200); }
.filter-panel__title { font-size: .75rem; font-weight: 700; color: var(--c-slate-600); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 14px; display: flex; align-items: center; gap: 6px; }
.filter-panel__title::before { content: ''; display: block; width: 3px; height: 14px; background: var(--c-teal); border-radius: 2px; }
.f-input, .f-select {
    width: 100%; background: var(--c-slate-50); border: 1px solid var(--c-slate-200); border-radius: 10px;
    color: var(--c-slate-800); padding: .6rem .85rem; font-size: .85rem; transition: border-color .2s;
}
.f-input:focus, .f-select:focus { outline: none; border-color: var(--c-teal); box-shadow: 0 0 0 3px rgba(13,148,136,.12); }
.f-label { font-size: .68rem; color: var(--c-slate-400); font-weight: 700; text-transform: uppercase; letter-spacing: .05em; display: block; margin-bottom: .35rem; }

/* live search */
.live-search-wrap { position: relative; }
.live-search-wrap svg { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--c-slate-400); }
.live-search-wrap input { padding-left: 34px; }

/* ── TABLE CARD ── */
.disp-card { background: var(--c-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--c-slate-200); overflow: hidden; }
.disp-table { width: 100%; border-collapse: collapse; min-width: 950px; }
.disp-table thead tr { background: var(--c-slate-50); }
.disp-table th { padding: .8rem 1rem; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--c-slate-600); text-align: left; border-bottom: 1px solid var(--c-slate-200); white-space:nowrap; }
.disp-table th.sortable { cursor: pointer; user-select: none; }
.disp-table th.sortable:hover { color: var(--c-teal); }
.disp-table th.sortable .arrow { opacity:.3; margin-left:3px; font-size:.65rem; }
.disp-table th.sortable.sorted .arrow { opacity:1; color: var(--c-teal); }
.disp-table td { padding: .8rem 1rem; border-bottom: 1px solid var(--c-slate-100); font-size: .85rem; color: var(--c-slate-800); }
.disp-table tbody tr { transition: background .12s; }
.disp-table tbody tr:hover { background: rgba(13,148,136,.04); }

.patient-cell__name { font-weight: 700; }
.patient-cell__sub  { font-size: .72rem; color: var(--c-slate-400); }

.fifo-tag { display: inline-flex; align-items: center; gap: .25rem; padding: .15rem .55rem; background: rgba(14,165,233,.1); border: 1px solid rgba(14,165,233,.3); border-radius: .4rem; font-size: .64rem; font-weight: 700; color: var(--c-sky); font-family: monospace; }
.lot-chip { display:inline-flex; align-items:center; gap:.3rem; background: var(--c-teal-pale); color:#0f766e; border:1px solid rgba(13,148,136,.3); padding:.2rem .6rem; border-radius:99px; font-size:.72rem; font-weight:700; font-family:monospace; }

.exp-badge { display: inline-flex; align-items:center; gap:.25rem; padding: .2rem .6rem; border-radius: 99px; font-size: .72rem; font-weight: 700; }
.exp--ok   { background: rgba(16,185,129,.12); color: var(--c-emerald); }
.exp--soon { background: rgba(245,158,11,.12); color: var(--c-amber); }
.exp--none { color: var(--c-slate-400); }

.qty-chip { display:inline-flex; align-items:center; justify-content:center; min-width:2.2rem; padding:.25rem .55rem; border-radius:8px; background: rgba(16,185,129,.12); color: var(--c-emerald); font-weight:800; font-size:.85rem; }

.action-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 8px; text-decoration: none; transition: all .15s; color:#fff; }
.action-btn:hover { transform: scale(1.1); filter: brightness(1.1); }
.action-btn--view { background: var(--c-sky); }
.copy-btn { background: var(--c-slate-100); border: none; color: var(--c-slate-600); width:22px; height:22px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; margin-left:.35rem; }
.copy-btn:hover { background: var(--c-teal-pale); color:#0f766e; }
.copy-btn.copied { background: var(--c-emerald); color:#fff; }

.empty-state { text-align: center; padding: 3.5rem 1rem; color: var(--c-slate-400); }
.empty-state__icon { font-size: 2.2rem; opacity: .25; margin-bottom: .5rem; }

.alert-flash { border-radius: 12px; padding: .8rem 1.1rem; margin-bottom: 1rem; font-weight: 600; font-size: .85rem; display:flex; align-items:center; gap:.5rem; }
.alert-flash--ok  { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3); color: #047857; }
.alert-flash--err { background: rgba(244,63,94,.1);  border: 1px solid rgba(244,63,94,.3);  color: #be123c; }

/* ── TREND CHART CARD ── */
.trend-card { background: var(--c-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--c-slate-200); padding: 18px 20px; margin-bottom: 20px; }
.trend-card__title { font-size: .75rem; font-weight: 700; color: var(--c-slate-600); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 12px; }

@media print {
    .toolbar, .filter-panel, .kpi-grid, .trend-card, .action-btn, form[method="GET"], .pagination { display: none !important; }
    .onco-hero { background: var(--c-navy) !important; -webkit-print-color-adjust: exact; }
}
</style>

{{-- ═══════════════ HERO ═══════════════ --}}
<div class="onco-hero">
    <div class="onco-hero__eyebrow">💉 Pharmacie Oncologique · CLCC Draâ Ben Khedda</div>
    <h1 class="onco-hero__title">Dispensations</h1>
    <p class="onco-hero__sub">Historique complet des sorties de stock · Traçabilité FIFO par lot</p>
    <div class="onco-hero__stats">
        <div class="onco-hero__stat"><b>{{ $totalDispensations }}</b>Total</div>
        <div class="onco-hero__stat"><b>{{ $dispensationsAujourd }}</b>Aujourd'hui</div>
        <div class="onco-hero__stat"><b>{{ $dispensationsMois }}</b>Ce mois</div>
        <div class="onco-hero__stat"><b>{{ number_format($quantiteTotaleMois) }}</b>Unités / mois</div>
    </div>
</div>

{{-- ═══════════════ KPI ═══════════════ --}}
<div class="kpi-grid">
    <div class="kpi-card kpi-card--sky" data-icon="💉">
        <div class="kpi-label">Total dispensations</div>
        <div class="kpi-value">{{ $totalDispensations }}</div>
    </div>
    <div class="kpi-card kpi-card--emerald" data-icon="📅">
        <div class="kpi-label">Aujourd'hui</div>
        <div class="kpi-value">{{ $dispensationsAujourd }}</div>
    </div>
    <div class="kpi-card kpi-card--violet" data-icon="📆">
        <div class="kpi-label">Ce mois</div>
        <div class="kpi-value">{{ $dispensationsMois }}</div>
    </div>
    <div class="kpi-card kpi-card--amber" data-icon="📦">
        <div class="kpi-label">Unités / mois</div>
        <div class="kpi-value">{{ number_format($quantiteTotaleMois) }}</div>
    </div>
</div>

{{-- ═══════════════ TOOLBAR ═══════════════ --}}
<div class="toolbar">
    <div class="toolbar__group">
        @canOnco('dispensations.create')
        <a href="{{ route('oncologie.dispensations.create') }}" class="btn-med btn-med--add">+ Nouvelle dispensation</a>
        @endcanOnco
    </div>
    <div class="toolbar__group">
        @canOnco('dispensations.export')
        <a href="{{ route('oncologie.dispensations.export', ['format'=>'pdf']) }}" class="btn-med btn-med--export" target="_blank">📄 PDF</a>
        <a href="{{ route('oncologie.dispensations.export', ['format'=>'csv']) }}" class="btn-med btn-med--export">📊 CSV</a>
        @endcanOnco
        <button type="button" class="btn-med btn-med--print" onclick="window.print()">🖨️ Imprimer</button>
    </div>
</div>

{{-- ═══════════════ ALERTS ═══════════════ --}}
@if(session('success'))
<div class="alert-flash alert-flash--ok">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-flash alert-flash--err">❌ {{ session('error') }}</div>
@endif

{{-- ═══════════════ FILTRES ═══════════════ --}}
<form method="GET" action="{{ route('oncologie.dispensations.index') }}" class="filter-panel">
    <div class="filter-panel__title">Filtres de recherche</div>
    <div style="display:grid; grid-template-columns:2fr 1.5fr 1fr 1fr auto auto; gap:.9rem; align-items:end;">
        <div>
            <label class="f-label">Patient</label>
            <input type="text" name="patient" class="f-input" placeholder="Nom, prénom, n° dossier…" value="{{ request('patient') }}">
        </div>
        <div>
            <label class="f-label">Médicament</label>
            <select name="medicament_id" class="f-select">
                <option value="">Tous</option>
                @foreach($medicamentsFiltres as $med)
                    <option value="{{ $med->id }}" {{ request('medicament_id') == $med->id ? 'selected' : '' }}>{{ $med->nom }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="f-label">Du</label>
            <input type="date" name="date_debut" class="f-input" value="{{ request('date_debut') }}">
        </div>
        <div>
            <label class="f-label">Au</label>
            <input type="date" name="date_fin" class="f-input" value="{{ request('date_fin') }}">
        </div>
        <div>
            <button type="submit" class="btn-med btn-med--add" style="width:100%; justify-content:center;">🔍 Rechercher</button>
        </div>
        <div>
            <a href="{{ route('oncologie.dispensations.index') }}" class="btn-med btn-med--export" style="width:100%; justify-content:center;">↺ Réinitialiser</a>
        </div>
    </div>

    {{-- Recherche instantanée côté client (sans rechargement) --}}
    <div style="margin-top:14px;">
        <label class="f-label">Recherche instantanée sur la page</label>
        <div class="live-search-wrap">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="liveSearch" class="f-input" placeholder="Filtrer instantanément par patient, médicament, lot, pharmacien…">
        </div>
    </div>
</form>

{{-- ═══════════════ MINI TENDANCE (données de la page courante) ═══════════════ --}}
<div class="trend-card">
    <div class="trend-card__title">📈 Répartition des dispensations affichées, par jour</div>
    <canvas id="trendChart" height="70"></canvas>
</div>

{{-- ═══════════════ TABLE ═══════════════ --}}
<div class="disp-card">
    <div style="overflow-x:auto;">
        <table class="disp-table" id="dispTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th class="sortable" data-key="patient">Patient <span class="arrow">▲▼</span></th>
                    <th class="sortable" data-key="medicament">Médicament <span class="arrow">▲▼</span></th>
                    <th>Lot <span class="fifo-tag" style="margin-left:.3rem;">FIFO</span></th>
                    <th>Expiration</th>
                    <th class="sortable" data-key="quantite">Quantité <span class="arrow">▲▼</span></th>
                    <th class="sortable" data-key="date">Date &amp; Heure <span class="arrow">▲▼</span></th>
                    <th>Pharmacien</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dispensations as $disp)
                @php
                    $patientNom = trim((optional(optional($disp->prescription)->patient)->nom ?? 'N/A').' '.(optional(optional($disp->prescription)->patient)->prenom ?? ''));
                    $medNom     = optional($disp->medicament)->nom ?? 'N/A';
                    $pharmNom   = optional($disp->user)->name ?? '—';
                    $lotNum     = optional($disp->lot)->numero ?? 'N/A';
                    $dossier    = optional(optional($disp->prescription)->patient)->numero_dossier;
                @endphp
                <tr data-patient="{{ $patientNom }}" data-medicament="{{ $medNom }}" data-lot="{{ $lotNum }}"
                    data-pharmacien="{{ $pharmNom }}" data-quantite="{{ $disp->quantite }}"
                    data-date="{{ optional($disp->created_at ?? null)->format('Y-m-d') ?? '' }}">
                    <td style="color:var(--c-slate-400); font-size:.75rem;">{{ $disp->id }}</td>
                    <td>
                        <div class="patient-cell__name">{{ $patientNom }}</div>
                        @if($dossier)
                        <div class="patient-cell__sub">
                            Dossier {{ $dossier }}
                            <button type="button" class="copy-btn" onclick="copyText(this,'{{ $dossier }}')" title="Copier">⧉</button>
                        </div>
                        @endif
                    </td>
                    <td style="font-weight:600;">{{ $medNom }}</td>
                    <td><span class="lot-chip">{{ $lotNum }}</span></td>
                    <td>
                        @if($disp->lot && $disp->lot->date_expiration)
                            @php
                                $exp = \Carbon\Carbon::parse($disp->lot->date_expiration);
                                $soon = now()->diffInDays($exp, false) <= 90 && now()->lt($exp);
                            @endphp
                            <span class="exp-badge {{ $soon ? 'exp--soon' : 'exp--ok' }}">
                                {{ $soon ? '⚠️' : '✓' }} {{ $exp->format('m/Y') }}
                            </span>
                        @else
                            <span class="exp-badge exp--none">—</span>
                        @endif
                    </td>
                    <td><span class="qty-chip">{{ $disp->quantite }}</span></td>
                    <td style="font-size:.8rem; color:var(--c-slate-600); white-space:nowrap;">{{ $disp->date_formattee ?? '—' }}</td>
                    <td style="font-size:.8rem; color:var(--c-slate-600);">{{ $pharmNom }}</td>
                    <td>
                        @canOnco('dispensations.view')
                        <a href="{{ route('oncologie.dispensations.show', $disp) }}" class="action-btn action-btn--view" title="Voir le détail">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                        @endcanOnco
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <div class="empty-state__icon">💊</div>
                            Aucune dispensation enregistrée pour ces critères
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:1rem 1.25rem; border-top:1px solid var(--c-slate-200);">
        {{ $dispensations->links() }}
    </div>
</div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
// ================= RECHERCHE INSTANTANÉE =================
const liveSearch = document.getElementById('liveSearch');
const rows = () => document.querySelectorAll('#dispTable tbody tr[data-patient]');

liveSearch.addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    rows().forEach(r => {
        const haystack = [r.dataset.patient, r.dataset.medicament, r.dataset.lot, r.dataset.pharmacien]
            .join(' ').toLowerCase();
        r.style.display = haystack.includes(q) ? '' : 'none';
    });
});

// ================= TRI DES COLONNES =================
document.querySelectorAll('#dispTable th.sortable').forEach(th => {
    let asc = true;
    th.addEventListener('click', () => {
        const key = th.dataset.key;
        const tbody = document.querySelector('#dispTable tbody');
        const list = Array.from(tbody.querySelectorAll('tr[data-patient]'));

        list.sort((a, b) => {
            let va = a.dataset[key] ?? '';
            let vb = b.dataset[key] ?? '';
            if (key === 'quantite') { va = parseFloat(va) || 0; vb = parseFloat(vb) || 0; return asc ? va - vb : vb - va; }
            return asc ? va.localeCompare(vb) : vb.localeCompare(va);
        });

        document.querySelectorAll('#dispTable th.sortable').forEach(x => x.classList.remove('sorted'));
        th.classList.add('sorted');
        list.forEach(r => tbody.appendChild(r));
        asc = !asc;
    });
});

// ================= COPIER LE N° DE DOSSIER =================
function copyText(btn, text) {
    navigator.clipboard?.writeText(text).then(() => {
        btn.classList.add('copied');
        btn.textContent = '✓';
        setTimeout(() => { btn.classList.remove('copied'); btn.textContent = '⧉'; }, 1200);
    });
}

// ================= MINI GRAPHIQUE (par jour, page courante) =================
(function () {
    const counts = {};
    rows().forEach(r => {
        const d = r.dataset.date;
        if (!d) return;
        counts[d] = (counts[d] || 0) + (parseInt(r.dataset.quantite) || 0);
    });
    const labels = Object.keys(counts).sort();
    const data = labels.map(l => counts[l]);

    if (labels.length) {
        new Chart(document.getElementById('trendChart'), {
            type: 'bar',
            data: {
                labels: labels.map(l => new Date(l).toLocaleDateString('fr-FR', {day:'2-digit', month:'2-digit'})),
                datasets: [{ label: 'Unités dispensées', data, backgroundColor: '#0d9488', borderRadius: 6, maxBarThickness: 28 }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    } else {
        document.getElementById('trendChart').outerHTML = '<div style="color:var(--c-slate-400); font-size:.85rem; text-align:center; padding:1rem;">Aucune donnée à afficher</div>';
    }
})();
</script>
@endpush