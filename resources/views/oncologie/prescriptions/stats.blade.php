@extends('layouts.app')
@section('title', 'Statistiques des Prescriptions')
 
@section('content')
<div class="container-fluid py-4 px-4">
 
<style>
:root {
    --c-navy:     #0b1d35;
    --c-navy-mid: #102748;
    --c-teal:     #0d9488;
    --c-teal-lt:  #14b8a6;
    --c-emerald:  #10b981;
    --c-amber:    #f59e0b;
    --c-rose:     #f43f5e;
    --c-violet:   #7c3aed;
    --c-slate-50: #f8fafc;
    --c-slate-100:#f1f5f9;
    --c-slate-200:#e2e8f0;
    --c-slate-400:#94a3b8;
    --c-slate-600:#475569;
    --c-slate-800:#1e293b;
    --c-white:    #ffffff;
    --radius-lg:  16px;
    --radius-md:  10px;
    --shadow-md:  0 4px 16px rgba(0,0,0,.07);
}
 
body { background: var(--c-slate-50); }
 
/* ── HERO ── */
.stats-hero {
    background: linear-gradient(135deg, #0b1d35 0%, #102748 60%, #1a3a5c 100%);
    border-radius: var(--radius-lg);
    padding: 26px 32px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 8px 32px rgba(11,29,53,.35);
    position: relative;
    overflow: hidden;
}
.stats-hero::before {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(124,58,237,.2) 0%, transparent 70%);
    pointer-events: none;
}
.stats-hero__eyebrow {
    font-size: .68rem;
    font-weight: 700;
    color: rgba(255,255,255,.4);
    text-transform: uppercase;
    letter-spacing: .12em;
    margin-bottom: 8px;
}
.stats-hero__title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #fff;
    margin: 0 0 4px;
    letter-spacing: -.02em;
}
.stats-hero__sub { color: rgba(255,255,255,.45); font-size: .82rem; margin: 0; }
.stats-hero__actions { display: flex; gap: 8px; flex-wrap: wrap; }
 
.btn-export-hero {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    border-radius: var(--radius-md);
    font-size: .8rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: filter .15s, transform .12s;
}
.btn-export-hero:hover { filter: brightness(1.1); transform: translateY(-1px); }
.btn-export-hero--pdf   { background: var(--c-rose); color: #fff; }
.btn-export-hero--excel { background: var(--c-emerald); color: #fff; }
.btn-export-hero--back  { background: rgba(255,255,255,.12); color: rgba(255,255,255,.8); border: 1px solid rgba(255,255,255,.15); }
 
/* ── KPI ROW ── */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
@media (max-width:900px) { .kpi-row { grid-template-columns: 1fr 1fr; } }
 
.kpi-tile {
    background: var(--c-white);
    border-radius: var(--radius-lg);
    padding: 20px 22px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--c-slate-200);
    position: relative;
    overflow: hidden;
    transition: transform .15s, box-shadow .15s;
}
.kpi-tile:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
.kpi-tile__bar {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}
.kpi-tile--total   .kpi-tile__bar { background: linear-gradient(90deg, #0b1d35, #0ea5e9); }
.kpi-tile--ok      .kpi-tile__bar { background: linear-gradient(90deg, var(--c-emerald), var(--c-teal-lt)); }
.kpi-tile--wait    .kpi-tile__bar { background: linear-gradient(90deg, var(--c-amber), #fbbf24); }
.kpi-tile--cancel  .kpi-tile__bar { background: linear-gradient(90deg, var(--c-rose), #fb7185); }
.kpi-tile__label { font-size: .7rem; font-weight: 700; color: var(--c-slate-400); text-transform: uppercase; letter-spacing: .07em; margin-bottom: 6px; margin-top: 6px; }
.kpi-tile__value { font-size: 2.4rem; font-weight: 900; letter-spacing: -.04em; line-height: 1; }
.kpi-tile--total  .kpi-tile__value { color: var(--c-navy); }
.kpi-tile--ok     .kpi-tile__value { color: var(--c-emerald); }
.kpi-tile--wait   .kpi-tile__value { color: var(--c-amber); }
.kpi-tile--cancel .kpi-tile__value { color: var(--c-rose); }
.kpi-tile__pct { font-size: .75rem; color: var(--c-slate-400); margin-top: 6px; font-weight: 600; }
 
/* ── CHART PANELS ── */
.chart-panel {
    background: var(--c-white);
    border-radius: var(--radius-lg);
    border: 1px solid var(--c-slate-200);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}
.chart-panel__head {
    padding: 16px 20px;
    border-bottom: 1px solid var(--c-slate-100);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.chart-panel__title {
    font-size: .82rem;
    font-weight: 700;
    color: var(--c-slate-800);
    display: flex;
    align-items: center;
    gap: 8px;
}
.chart-panel__badge {
    font-size: .68rem;
    font-weight: 700;
    color: var(--c-slate-400);
    background: var(--c-slate-100);
    padding: 2px 8px;
    border-radius: 99px;
}
.chart-panel__body { padding: 20px; }
 
/* ── DONUT LEGEND ── */
.donut-legend { margin-top: 16px; }
.donut-legend-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid var(--c-slate-100);
    font-size: .82rem;
}
.donut-legend-item:last-child { border-bottom: none; }
.donut-legend-item__label { display: flex; align-items: center; gap: 8px; color: var(--c-slate-600); font-weight: 500; }
.donut-legend-item__dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
.donut-legend-item__val { font-weight: 800; color: var(--c-slate-800); }
 
/* ── RANKING BARS ── */
.ranking-item { margin-bottom: 16px; }
.ranking-item__row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
    font-size: .82rem;
}
.ranking-item__name { font-weight: 600; color: var(--c-slate-800); }
.ranking-item__count { font-size: .75rem; color: var(--c-slate-400); font-weight: 600; }
.ranking-item__bar-bg {
    height: 6px;
    background: var(--c-slate-100);
    border-radius: 99px;
    overflow: hidden;
}
.ranking-item__bar-fill {
    height: 100%;
    border-radius: 99px;
    transition: width 1.2s cubic-bezier(.34,1.56,.64,1);
}
.ranking-item__bar-fill--medecin  { background: linear-gradient(90deg, #0b1d35, #0ea5e9); }
.ranking-item__bar-fill--protocole{ background: linear-gradient(90deg, var(--c-violet), var(--c-rose)); }
 
/* ── EMPTY ── */
.ranking-empty {
    text-align: center;
    padding: 24px 10px;
    color: var(--c-slate-400);
    font-size: .82rem;
}
</style>
 
{{-- ═══ HERO ═══ --}}
<div class="stats-hero">
    <div>
        <div class="stats-hero__eyebrow">CLCC Draâ Ben Khedda · Pharmacie Oncologie</div>
        <h1 class="stats-hero__title">Tableau de bord statistique</h1>
        <p class="stats-hero__sub">Analyse des prescriptions — Année {{ now()->year }}</p>
    </div>
    <div class="stats-hero__actions">
        <a href="{{ route('oncologie.prescriptions.export', ['format'=>'pdf']) }}"
           class="btn-export-hero btn-export-hero--pdf">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Export PDF
        </a>
        <a href="{{ route('oncologie.prescriptions.export', ['format'=>'excel']) }}"
           class="btn-export-hero btn-export-hero--excel">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Export Excel
        </a>
        <a href="{{ route('oncologie.prescriptions.index') }}"
           class="btn-export-hero btn-export-hero--back">
            ← Retour
        </a>
    </div>
</div>
 
{{-- ═══ KPI ═══ --}}
<div class="kpi-row">
    @php $safe_total = $total > 0 ? $total : 1; @endphp
    <div class="kpi-tile kpi-tile--total">
        <div class="kpi-tile__bar"></div>
        <div class="kpi-tile__label">Total prescriptions</div>
        <div class="kpi-tile__value">{{ $total }}</div>
        <div class="kpi-tile__pct">{{ now()->year }}</div>
    </div>
    <div class="kpi-tile kpi-tile--ok">
        <div class="kpi-tile__bar"></div>
        <div class="kpi-tile__label">Validées</div>
        <div class="kpi-tile__value">{{ $validees }}</div>
        <div class="kpi-tile__pct">{{ round($validees / $safe_total * 100) }}% du total</div>
    </div>
    <div class="kpi-tile kpi-tile--wait">
        <div class="kpi-tile__bar"></div>
        <div class="kpi-tile__label">En attente</div>
        <div class="kpi-tile__value">{{ $enAttente }}</div>
        <div class="kpi-tile__pct">{{ round($enAttente / $safe_total * 100) }}% du total</div>
    </div>
    <div class="kpi-tile kpi-tile--cancel">
        <div class="kpi-tile__bar"></div>
        <div class="kpi-tile__label">Annulées</div>
        <div class="kpi-tile__value">{{ $annulees }}</div>
        <div class="kpi-tile__pct">{{ round($annulees / $safe_total * 100) }}% du total</div>
    </div>
</div>
 
{{-- ═══ LIGNE 1 : ÉVOLUTION + DONUT ═══ --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="chart-panel">
            <div class="chart-panel__head">
                <div class="chart-panel__title">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    Évolution mensuelle des prescriptions
                </div>
                <span class="chart-panel__badge">{{ now()->year }}</span>
            </div>
            <div class="chart-panel__body">
                <canvas id="evolutionChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-panel" style="height:100%;">
            <div class="chart-panel__head">
                <div class="chart-panel__title">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0110 10"/></svg>
                    Répartition par statut
                </div>
            </div>
            <div class="chart-panel__body">
                <canvas id="statutsDonut" height="180"></canvas>
                <div class="donut-legend">
                    @foreach([
                        ['label'=>'Validées',  'value'=>$validees,  'color'=>'#10b981'],
                        ['label'=>'En attente','value'=>$enAttente, 'color'=>'#f59e0b'],
                        ['label'=>'Annulées',  'value'=>$annulees,  'color'=>'#f43f5e'],
                    ] as $s)
                    <div class="donut-legend-item">
                        <span class="donut-legend-item__label">
                            <span class="donut-legend-item__dot" style="background:{{ $s['color'] }};"></span>
                            {{ $s['label'] }}
                        </span>
                        <span class="donut-legend-item__val">{{ $s['value'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
 
{{-- ═══ LIGNE 2 : TOP MÉDECINS + TOP PROTOCOLES ═══ --}}
<div class="row g-4">
    <div class="col-lg-6">
        <div class="chart-panel">
            <div class="chart-panel__head">
                <div class="chart-panel__title">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Top médecins prescripteurs
                </div>
            </div>
            <div class="chart-panel__body">
                @forelse($topMedecins as $med)
                @php $pct = round($med->total / $safe_total * 100); @endphp
                <div class="ranking-item">
                    <div class="ranking-item__row">
                        <span class="ranking-item__name">{{ $med->medecin_nom ?? 'Inconnu' }}</span>
                        <span class="ranking-item__count">{{ $med->total }} ({{ $pct }}%)</span>
                    </div>
                    <div class="ranking-item__bar-bg">
                        <div class="ranking-item__bar-fill ranking-item__bar-fill--medecin"
                             style="width:{{ $pct }}%;"></div>
                    </div>
                </div>
                @empty
                <div class="ranking-empty">Aucune donnée disponible</div>
                @endforelse
            </div>
        </div>
    </div>
 
    <div class="col-lg-6">
        <div class="chart-panel">
            <div class="chart-panel__head">
                <div class="chart-panel__title">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                    Top protocoles utilisés
                </div>
            </div>
            <div class="chart-panel__body">
                @forelse($topProtocoles as $proto)
                @php $pct = round($proto->total / $safe_total * 100); @endphp
                <div class="ranking-item">
                    <div class="ranking-item__row">
                        <span class="ranking-item__name">{{ optional($proto->protocole)->nom ?? 'N/A' }}</span>
                        <span class="ranking-item__count">{{ $proto->total }} ({{ $pct }}%)</span>
                    </div>
                    <div class="ranking-item__bar-bg">
                        <div class="ranking-item__bar-fill ranking-item__bar-fill--protocole"
                             style="width:{{ $pct }}%;"></div>
                    </div>
                </div>
                @empty
                <div class="ranking-empty">Aucune donnée disponible</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
 
</div>
 
@push('scripts')
<script>
const mois = @json($moisLabels);
 
// Couleurs
const NAVY    = '#0b1d35';
const TEAL    = '#0d9488';
const EMERALD = '#10b981';
const AMBER   = '#f59e0b';
const ROSE    = '#f43f5e';
const GRID    = 'rgba(0,0,0,0.04)';
 
// ── ÉVOLUTION ──
new Chart(document.getElementById('evolutionChart'), {
    type: 'line',
    data: {
        labels: mois,
        datasets: [
            {
                label: 'Total',
                data: @json($totauxMois),
                borderColor: NAVY,
                backgroundColor: 'rgba(11,29,53,0.06)',
                fill: true, tension: 0.4, borderWidth: 2.5, pointRadius: 4,
                pointBackgroundColor: NAVY,
            },
            {
                label: 'Validées',
                data: @json($valideesMois),
                borderColor: EMERALD,
                backgroundColor: 'rgba(16,185,129,0.07)',
                fill: true, tension: 0.4, borderWidth: 2.5, pointRadius: 4,
                pointBackgroundColor: EMERALD,
            },
            {
                label: 'Annulées',
                data: @json($annuleesMois),
                borderColor: ROSE,
                backgroundColor: 'rgba(244,63,94,0.05)',
                fill: true, tension: 0.4, borderWidth: 2, pointRadius: 3,
                pointBackgroundColor: ROSE,
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: {
                position: 'top',
                labels: { font: { size: 11, weight: '600' }, usePointStyle: true, pointStyleWidth: 8 }
            },
            tooltip: { padding: 10, cornerRadius: 8 }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: GRID },
                ticks: { font: { size: 11 } }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 } }
            }
        }
    }
});
 
// ── DONUT ──
new Chart(document.getElementById('statutsDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Validées', 'En attente', 'Annulées'],
        datasets: [{
            data: [{{ $validees }}, {{ $enAttente }}, {{ $annulees }}],
            backgroundColor: [EMERALD, AMBER, ROSE],
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        cutout: '68%',
        plugins: {
            legend: { display: false },
            tooltip: { padding: 10, cornerRadius: 8 }
        }
    }
});
</script>
@endpush
 
@endsection