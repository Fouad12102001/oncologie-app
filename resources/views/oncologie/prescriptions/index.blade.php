@extends('layouts.app')
 
@section('title', 'Centre de Prescription Oncologique')
 
@section('content')
<div class="container-fluid py-4 px-4">
 
<style>
/* ═══════════════════════════════════════════════════
   TOKENS — CLCC ONCOLOGIE
   Palette: Bleu marine médical + Vert menthe clinique
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
 
/* ── PAGE ── */
body { background: var(--c-slate-50); }
 
/* ── HERO HEADER ── */
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
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(13,148,136,.25) 0%, transparent 70%);
    pointer-events: none;
}
.onco-hero::after {
    content: '';
    position: absolute;
    bottom: -40px; left: 30%;
    width: 180px; height: 180px;
    background: radial-gradient(circle, rgba(14,165,233,.12) 0%, transparent 70%);
    pointer-events: none;
}
.onco-hero__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(13,148,136,.18);
    border: 1px solid rgba(13,148,136,.35);
    color: var(--c-teal-lt);
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    padding: .25rem .75rem;
    border-radius: 99px;
    margin-bottom: 10px;
}
.onco-hero__title {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--c-white);
    margin: 0 0 4px;
    letter-spacing: -.02em;
}
.onco-hero__sub {
    color: rgba(255,255,255,.5);
    font-size: .85rem;
    margin: 0;
}
.onco-hero__date {
    color: rgba(255,255,255,.35);
    font-size: .75rem;
    margin-top: 6px;
}
 
/* ── KPI GRID ── */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
@media (max-width: 1100px) { .kpi-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width:  700px) { .kpi-grid { grid-template-columns: 1fr 1fr; } }
 
.kpi-card {
    background: var(--c-white);
    border-radius: var(--radius-lg);
    padding: 18px 20px;
    box-shadow: var(--shadow-md);
    border-top: 3px solid;
    position: relative;
    overflow: hidden;
    transition: transform .18s, box-shadow .18s;
}
.kpi-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
.kpi-card::after {
    content: attr(data-icon);
    position: absolute;
    right: 14px; top: 12px;
    font-size: 1.6rem;
    opacity: .12;
}
.kpi-card--sky     { border-color: var(--c-sky); }
.kpi-card--emerald { border-color: var(--c-emerald); }
.kpi-card--amber   { border-color: var(--c-amber); }
.kpi-card--rose    { border-color: var(--c-rose); }
.kpi-card--crimson { border-color: var(--c-crimson); }
.kpi-label { font-size: .72rem; font-weight: 600; color: var(--c-slate-400); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px; }
.kpi-value { font-size: 2rem; font-weight: 900; color: var(--c-slate-800); line-height: 1; letter-spacing: -.03em; }
.kpi-card--sky     .kpi-value { color: var(--c-sky); }
.kpi-card--emerald .kpi-value { color: var(--c-emerald); }
.kpi-card--amber   .kpi-value { color: var(--c-amber); }
.kpi-card--rose    .kpi-value { color: var(--c-rose); }
.kpi-card--crimson .kpi-value { color: var(--c-crimson); }
 
/* ── TOOLBAR ── */
.toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}
.toolbar__group { display: flex; gap: 8px; flex-wrap: wrap; }
 
.btn-med {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    border-radius: var(--radius-md);
    font-size: .82rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap;
    transition: filter .15s, transform .12s;
}
.btn-med:hover { filter: brightness(1.1); transform: translateY(-1px); }
.btn-med--add    { background: var(--c-teal);   color: #fff; }
.btn-med--export { background: var(--c-slate-800); color: #fff; }
.btn-med--stats  { background: #6d28d9; color: #fff; }
 
/* ── FILTER PANEL ── */
.filter-panel {
    background: var(--c-white);
    border-radius: var(--radius-lg);
    padding: 20px 24px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--c-slate-200);
}
.filter-panel__title {
    font-size: .75rem;
    font-weight: 700;
    color: var(--c-slate-600);
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.filter-panel__title::before {
    content: '';
    display: block;
    width: 3px; height: 14px;
    background: var(--c-teal);
    border-radius: 2px;
}
 
.form-label-med {
    display: block;
    font-size: .72rem;
    font-weight: 600;
    color: var(--c-slate-400);
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: .3rem;
}
.form-control, .form-select {
    border-radius: var(--radius-sm) !important;
    border-color: var(--c-slate-200) !important;
    font-size: .85rem !important;
    color: var(--c-slate-800) !important;
    transition: border-color .15s, box-shadow .15s;
}
.form-control:focus, .form-select:focus {
    border-color: var(--c-teal) !important;
    box-shadow: 0 0 0 3px rgba(13,148,136,.15) !important;
}
.btn-filter {
    background: var(--c-navy);
    color: #fff;
    border: none;
    padding: 8px 20px;
    border-radius: var(--radius-sm);
    font-weight: 600;
    font-size: .82rem;
    cursor: pointer;
    transition: background .15s;
}
.btn-filter:hover { background: var(--c-navy-lt); }
.btn-reset {
    background: var(--c-slate-100);
    color: var(--c-slate-600);
    border: 1px solid var(--c-slate-200);
    padding: 8px 14px;
    border-radius: var(--radius-sm);
    font-size: .82rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: background .15s;
}
.btn-reset:hover { background: var(--c-slate-200); color: var(--c-slate-800); }
 
/* ── DATA TABLE ── */
.data-table-wrap {
    background: var(--c-white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    border: 1px solid var(--c-slate-200);
    margin-bottom: 24px;
}
.data-table-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 22px;
    background: var(--c-navy);
    color: #fff;
}
.data-table-head__title {
    font-size: .88rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
}
.data-table-head__count {
    background: rgba(255,255,255,.15);
    padding: 2px 10px;
    border-radius: 99px;
    font-size: .72rem;
}
.table { margin: 0; }
.table thead th {
    background: var(--c-slate-50);
    color: var(--c-slate-400);
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    border-bottom: 2px solid var(--c-slate-200) !important;
    border-top: none !important;
    padding: 10px 14px;
    white-space: nowrap;
}
.table tbody td {
    padding: 11px 14px;
    border-color: var(--c-slate-100) !important;
    vertical-align: middle;
    font-size: .83rem;
    color: var(--c-slate-800);
}
.table tbody tr:hover td { background: rgba(13,148,136,.03); }
 
/* ── PATIENT CELL ── */
.patient-cell__name { font-weight: 700; font-size: .85rem; color: var(--c-slate-800); }
.patient-cell__sub  { font-size: .73rem; color: var(--c-slate-400); margin-top: 2px; }
 
/* ── STATUT PILL ── */
.pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 99px;
    font-size: .72rem;
    font-weight: 700;
    white-space: nowrap;
}
.pill-dot { width: 6px; height: 6px; border-radius: 50%; }
.pill--ok      { background: #dcfce7; color: #166534; }
.pill--ok      .pill-dot { background: var(--c-emerald); }
.pill--wait    { background: #fef9c3; color: #854d0e; }
.pill--wait    .pill-dot { background: var(--c-amber); }
.pill--cancel  { background: #ffe4e6; color: #9f1239; }
.pill--cancel  .pill-dot { background: var(--c-rose); }
 
/* ── RENAL BADGE ── */
.renal-badge {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    gap: 1px;
}
.renal-badge__tag {
    padding: 2px 8px;
    border-radius: 6px;
    font-size: .68rem;
    font-weight: 700;
}
.renal-badge__val { font-size: .68rem; color: var(--c-slate-400); }
.renal--ok   .renal-badge__tag { background: #dcfce7; color: #166534; }
.renal--mild .renal-badge__tag { background: #fef9c3; color: #854d0e; }
.renal--mod  .renal-badge__tag { background: #fee2e2; color: #991b1b; }
.renal--sev  .renal-badge__tag { background: #1e293b; color: #fca5a5; }
 
/* ── SC CHIP ── */
.sc-chip {
    display: inline-block;
    background: rgba(14,165,233,.1);
    color: var(--c-sky);
    border: 1px solid rgba(14,165,233,.25);
    padding: 2px 8px;
    border-radius: 6px;
    font-size: .75rem;
    font-weight: 700;
    font-family: 'Courier New', monospace;
}
 
/* ── DRUG LIST ── */
.drug-item { display: flex; align-items: center; gap: 6px; font-size: .78rem; margin-bottom: 3px; }
.drug-item__dot { width: 5px; height: 5px; border-radius: 50%; background: var(--c-teal); flex-shrink: 0; }
.drug-item__dose {
    background: var(--c-slate-100);
    color: var(--c-slate-600);
    padding: 1px 6px;
    border-radius: 4px;
    font-size: .7rem;
    font-weight: 600;
    font-family: monospace;
}
 
/* ── ACTION BUTTONS ── */
.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px; height: 30px;
    border-radius: var(--radius-sm);
    border: none;
    cursor: pointer;
    text-decoration: none;
    font-size: .8rem;
    transition: filter .12s, transform .12s;
}
.action-btn:hover { filter: brightness(.9); transform: scale(1.08); }
.action-btn--view   { background: rgba(14,165,233,.12); color: var(--c-sky); }
.action-btn--edit   { background: rgba(245,158,11,.12); color: #b45309; }
.action-btn--pdf    { background: rgba(244,63,94,.12);  color: var(--c-rose); }
.action-btn--valid  { background: rgba(16,185,129,.12); color: #059669; }
 
/* ── ALLERGY ROW ── */
.allergy-row td {
    background: #fff7ed !important;
    padding: 6px 14px !important;
}
.allergy-banner {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .78rem;
    color: #9a3412;
    font-weight: 600;
}
.allergy-banner__icon {
    width: 20px; height: 20px;
    background: #fed7aa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .72rem;
    flex-shrink: 0;
}
 
/* ── ALERTS PANEL ── */
.alerts-panel {
    background: var(--c-white);
    border-radius: var(--radius-lg);
    border-left: 4px solid var(--c-rose);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-top: 20px;
    border: 1px solid #fecdd3;
    border-left-width: 4px;
}
.alerts-panel__head {
    background: #fff1f2;
    padding: 12px 20px;
    font-weight: 700;
    font-size: .82rem;
    color: var(--c-crimson);
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 1px solid #fecdd3;
}
.alerts-panel__body { padding: 14px 20px; }
.alert-item {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 8px 0;
    border-bottom: 1px solid #fff1f2;
    font-size: .82rem;
    color: var(--c-slate-800);
}
.alert-item:last-child { border-bottom: none; }
.alert-item__dot { width: 6px; height: 6px; border-radius: 50%; background: var(--c-rose); margin-top: 5px; flex-shrink: 0; }
 
/* ── EMPTY STATE ── */
.empty-state { text-align: center; padding: 48px 20px; }
.empty-state__icon { font-size: 2.5rem; margin-bottom: 12px; opacity: .35; }
.empty-state__msg  { color: var(--c-slate-400); font-size: .88rem; }
 
/* ── PAGINATION ── */
.pagination .page-link {
    border-radius: var(--radius-sm) !important;
    border-color: var(--c-slate-200) !important;
    color: var(--c-slate-800) !important;
    font-size: .82rem !important;
    margin: 0 2px;
}
.pagination .page-item.active .page-link {
    background: var(--c-teal) !important;
    border-color: var(--c-teal) !important;
    color: #fff !important;
}
</style>
 
{{-- ═══════════════ HERO ═══════════════ --}}
<div class="onco-hero">
    <span class="onco-hero__eyebrow">
        <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor"><circle cx="5" cy="5" r="5"/></svg>
        CLCC Draâ Ben Khedda · Pharmacie Oncologie
    </span>
    <h1 class="onco-hero__title">Centre de Prescription Chimiothérapie</h1>
    <p class="onco-hero__sub">Gestion des prescriptions, calcul de doses et validation pharmaceutique</p>
    <p class="onco-hero__date">{{ now()->isoFormat('dddd D MMMM YYYY') }}</p>
</div>
 
{{-- ═══════════════ KPI ═══════════════ --}}
<div class="kpi-grid">
    <div class="kpi-card kpi-card--sky" data-icon="📋">
        <div class="kpi-label">Total</div>
        <div class="kpi-value">{{ $stats['total'] ?? 0 }}</div>
    </div>
    <div class="kpi-card kpi-card--emerald" data-icon="✅">
        <div class="kpi-label">Validées</div>
        <div class="kpi-value">{{ $stats['validees'] ?? 0 }}</div>
    </div>
    <div class="kpi-card kpi-card--amber" data-icon="⏳">
        <div class="kpi-label">En attente</div>
        <div class="kpi-value">{{ $stats['attente'] ?? 0 }}</div>
    </div>
    <div class="kpi-card kpi-card--rose" data-icon="❌">
        <div class="kpi-label">Annulées</div>
        <div class="kpi-value">{{ $stats['annulees'] ?? 0 }}</div>
    </div>
    <div class="kpi-card kpi-card--crimson" data-icon="⚠">
        <div class="kpi-label">DFG Critique</div>
        <div class="kpi-value">{{ $stats['risque_renal'] ?? 0 }}</div>
    </div>
</div>
 
{{-- ═══════════════ TOOLBAR ═══════════════ --}}
<div class="toolbar">
    <div class="toolbar__group">
        @canOnco('prescriptions.create')
        <a href="{{ route('oncologie.prescriptions.create') }}" class="btn-med btn-med--add">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Nouvelle Prescription
        </a>
        @endcanOnco
    </div>
    <div class="toolbar__group">
        @canOnco('prescriptions.export')
        <a href="{{ route('oncologie.prescriptions.export') }}" class="btn-med btn-med--export">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            Exporter
        </a>
        @endcanOnco
        @canOnco('prescriptions.stats')
        <a href="{{ route('oncologie.prescriptions.stats') }}" class="btn-med btn-med--stats">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
            Statistiques
        </a>
        @endcanOnco
    </div>
</div>
 
{{-- ═══════════════ FILTRES ═══════════════ --}}
<div class="filter-panel">
    <div class="filter-panel__title">Recherche et filtres</div>
    <form method="GET" action="{{ route('oncologie.prescriptions.index') }}">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label-med">Patient</label>
                <input type="text" class="form-control" name="patient"
                    value="{{ request('patient') }}" placeholder="Nom, prénom ou N° dossier">
            </div>
            <div class="col-md-2">
                <label class="form-label-med">Statut</label>
                <select class="form-select" name="statut">
                    <option value="">Tous</option>
                    <option value="validee"    @selected(request('statut')=='validee')>Validée</option>
                    <option value="en_attente" @selected(request('statut')=='en_attente')>En attente</option>
                    <option value="annulee"    @selected(request('statut')=='annulee')>Annulée</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-med">Fonction rénale</label>
                <select class="form-select" name="renal">
                    <option value="">Tous</option>
                    <option value="normal" @selected(request('renal')=='normal')>Normal ≥ 90</option>
                    <option value="modere" @selected(request('renal')=='modere')>30 – 89</option>
                    <option value="severe" @selected(request('renal')=='severe')}>&lt; 30</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-med">Date prescription</label>
                <input type="date" class="form-control" name="date" value="{{ request('date') }}">
            </div>
            <div class="col-md-1">
                <label class="form-label-med">Cycle</label>
                <input type="number" min="1" class="form-control" name="cycle" value="{{ request('cycle') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <button type="submit" class="btn-filter flex-grow-1">Filtrer</button>
                    <a href="{{ route('oncologie.prescriptions.index') }}" class="btn-reset">↺</a>
                </div>
            </div>
        </div>
    </form>
</div>
 
{{-- ═══════════════ TABLE ═══════════════ --}}
<div class="data-table-wrap">
    <div class="data-table-head">
        <div class="data-table-head__title">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            Liste des prescriptions
            <span class="data-table-head__count">{{ $prescriptions->total() }} enregistrement(s)</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Médecin</th>
                    <th>Poids</th>
                    <th>Taille</th>
                    <th>SC (m²)</th>
                    <th>DFG (ml/min)</th>
                    <th>Cycle</th>
                    <th>Protocole</th>
                    <th>Médicaments</th>
                    <th>Dose totale</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($prescriptions as $prescription)
                <tr>
                    <td>
                        <span style="font-weight:700; color:var(--c-slate-400); font-size:.78rem;">#{{ $prescription->id }}</span>
                    </td>
 
                    {{-- PATIENT --}}
                    <td>
                        <div class="patient-cell__name">
                            {{ optional($prescription->patient)->nom }}
                            {{ optional($prescription->patient)->prenom }}
                        </div>
                        <div class="patient-cell__sub">
                            Dossier {{ optional($prescription->patient)->numero_dossier ?? '—' }}
                        </div>
                    </td>
 
                    {{-- MÉDECIN --}}
                    <td style="font-size:.82rem; color:var(--c-slate-600);">
                        {{ $prescription->medecin_nom ?? '—' }}
                    </td>
 
                    {{-- POIDS --}}
                    <td style="font-size:.82rem;">{{ $prescription->poids }} <span style="color:var(--c-slate-400);font-size:.7rem;">kg</span></td>
 
                    {{-- TAILLE --}}
                    <td style="font-size:.82rem;">{{ $prescription->taille }} <span style="color:var(--c-slate-400);font-size:.7rem;">cm</span></td>
 
                    {{-- SC --}}
                    <td>
                        @if($prescription->surface_corporelle)
                            <span class="sc-chip">{{ $prescription->surface_corporelle }} m²</span>
                        @else
                            <span style="color:var(--c-slate-400);">—</span>
                        @endif
                    </td>
 
                    {{-- CLAIRANCE RÉNALE --}}
                    @php $clr = $prescription->clairance_renale; @endphp
                    <td>
                        @if($clr >= 90)
                            <div class="renal-badge renal--ok">
                                <span class="renal-badge__tag">Normal</span>
                                <span class="renal-badge__val">{{ $clr }}</span>
                            </div>
                        @elseif($clr >= 60)
                            <div class="renal-badge renal--mild">
                                <span class="renal-badge__tag">Léger</span>
                                <span class="renal-badge__val">{{ $clr }}</span>
                            </div>
                        @elseif($clr >= 30)
                            <div class="renal-badge renal--mod">
                                <span class="renal-badge__tag">Modéré</span>
                                <span class="renal-badge__val">{{ $clr }}</span>
                            </div>
                        @else
                            <div class="renal-badge renal--sev">
                                <span class="renal-badge__tag">Sévère</span>
                                <span class="renal-badge__val">{{ $clr }}</span>
                            </div>
                        @endif
                    </td>
 
                    {{-- CYCLE --}}
                    <td>
                        <span style="background:rgba(14,165,233,.1);color:var(--c-sky);padding:2px 10px;border-radius:6px;font-size:.75rem;font-weight:700;">
                            C{{ $prescription->cycle }}
                        </span>
                    </td>
 
                    {{-- PROTOCOLE --}}
                    <td>
                        @if($prescription->protocole)
                            <div style="font-weight:700;font-size:.82rem;">{{ $prescription->protocole->nom }}</div>
                            <div style="font-size:.7rem;color:var(--c-slate-400);">{{ $prescription->protocole->frequence }}</div>
                        @else
                            <span style="color:var(--c-slate-400);">—</span>
                        @endif
                    </td>
 
                    {{-- MÉDICAMENTS --}}
                    <td>
                        @foreach($prescription->details as $detail)
                            <div class="drug-item">
                                <span class="drug-item__dot"></span>
                                <span>{{ $detail->medicament->nom }}</span>
                                <span class="drug-item__dose">{{ $detail->dose_calculee }} mg</span>
                            </div>
                        @endforeach
                    </td>
 
                    {{-- DOSE TOTALE --}}
                    <td>
                        <span style="font-weight:800;font-size:.88rem;color:var(--c-teal);">
                            {{ number_format($prescription->dose_totale, 2) }}
                        </span>
                        <span style="font-size:.7rem;color:var(--c-slate-400);"> mg</span>
                    </td>
 
                    {{-- DATE --}}
                    <td style="font-size:.8rem;white-space:nowrap;color:var(--c-slate-600);">
                        {{ optional($prescription->date_prescription)->format('d/m/Y') }}
                    </td>
 
                    {{-- STATUT --}}
                    <td>
                        @if($prescription->statut == 'validee')
                            <span class="pill pill--ok"><span class="pill-dot"></span>Validée</span>
                        @elseif($prescription->statut == 'en_attente')
                            <span class="pill pill--wait"><span class="pill-dot"></span>En attente</span>
                        @else
                            <span class="pill pill--cancel"><span class="pill-dot"></span>Annulée</span>
                        @endif
                    </td>
 
                    {{-- ACTIONS --}}
                    <td>
                        <div style="display:flex;gap:4px;">
                            <a href="{{ route('oncologie.prescriptions.show', $prescription->id) }}"
                               class="action-btn action-btn--view" title="Voir">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
 
                            @canOnco('prescriptions.edit')
                            <a href="{{ route('oncologie.prescriptions.edit', $prescription->id) }}"
                               class="action-btn action-btn--edit" title="Modifier">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            @endcanOnco
 
                            @canOnco('prescriptions.export')
                            <a href="{{ route('oncologie.prescriptions.pdf', $prescription->id) }}"
                               class="action-btn action-btn--pdf" title="PDF">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </a>
                            @endcanOnco
 
                            @canOnco('prescriptions.valider')
                            @if(!$prescription->isValidee())
                            <form method="POST"
                                  action="{{ route('oncologie.prescriptions.valider', $prescription->id) }}"
                                  style="display:inline;">
                                @csrf
                                <button type="submit" class="action-btn action-btn--valid" title="Valider">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                </button>
                            </form>
                            @endif
                            @endcanOnco
                        </div>
                    </td>
                </tr>
 
                {{-- LIGNE ALLERGIE --}}
                @if($prescription->patient && !empty($prescription->patient->allergies))
                <tr class="allergy-row">
                    <td colspan="14">
                        <div class="allergy-banner">
                            <div class="allergy-banner__icon">⚠</div>
                            <strong>Allergie :</strong>
                            {{ $prescription->patient->allergies }}
                        </div>
                    </td>
                </tr>
                @endif
 
            @empty
                <tr>
                    <td colspan="14">
                        <div class="empty-state">
                            <div class="empty-state__icon">📋</div>
                            <div class="empty-state__msg">Aucune prescription trouvée pour ces critères</div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
 
{{-- ═══════════════ ALERTES MÉDICALES ═══════════════ --}}
@if(!empty($alertes))
<div class="alerts-panel">
    <div class="alerts-panel__head">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Alertes médicales actives ({{ count($alertes) }})
    </div>
    <div class="alerts-panel__body">
        @foreach($alertes as $alerte)
        <div class="alert-item">
            <div class="alert-item__dot"></div>
            <span>{{ $alerte }}</span>
        </div>
        @endforeach
    </div>
</div>
@endif
 
{{-- ═══════════════ PAGINATION ═══════════════ --}}
<div class="mt-4 d-flex justify-content-center">
    {{ $prescriptions->links('pagination::bootstrap-5') }}
</div>
 
</div>
@endsection