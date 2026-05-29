@extends('layouts.app')
@section('title', 'Statistiques')

@section('content')

<!-- EN-TÊTE -->
<div style="background:linear-gradient(135deg,#6a4c93,#264653);
            border-radius:20px; padding:28px 32px; margin-bottom:24px;
            position:relative; overflow:hidden;">
    <div style="position:absolute;right:-50px;top:-50px;width:200px;height:200px;
                background:rgba(255,255,255,0.05);border-radius:50%;"></div>
    <div style="position:relative;z-index:2;">
        <h1 style="color:white;font-size:26px;font-weight:800;margin-bottom:6px;">
            📊 Tableau de Bord Statistique
        </h1>
        <p style="color:rgba(255,255,255,0.7);font-size:14px;margin:0;">
            CLCC Draâ Ben Khedda — Année {{ now()->year }}
            — Mis à jour {{ now()->format('d/m/Y à H:i') }}
        </p>
    </div>
</div>

<!-- MEGA KPI -->
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:24px;">
    @foreach([
        ['icon'=>'👥','value'=>$totalPatients,'label'=>'Patients','sub'=>$vivants.' vivants','color'=>'#2a9d8f','shadow'=>'rgba(42,157,143,0.25)'],
        ['icon'=>'📋','value'=>$totalPrescriptions,'label'=>'Prescriptions','sub'=>$validees.' validées','color'=>'#264653','shadow'=>'rgba(38,70,83,0.25)'],
        ['icon'=>'💊','value'=>$totalDisp,'label'=>'Dispensations','sub'=>$dispAujourdhui." auj.",'color'=>'#6a4c93','shadow'=>'rgba(106,76,147,0.25)'],
        ['icon'=>'🧪','value'=>$totalMeds,'label'=>'Médicaments','sub'=>$ruptures.' ruptures','color'=>$ruptures>0?'#e63946':'#22c55e','shadow'=>'rgba(230,57,70,0.2)'],
        ['icon'=>'📦','value'=>$totalLots,'label'=>'Lots','sub'=>$lotsExpires.' expirés','color'=>'#f4a261','shadow'=>'rgba(244,162,97,0.25)'],
    ] as $k)
    <div style="background:linear-gradient(135deg,{{ $k['color'] }},{{ $k['color'] }}cc);
                border-radius:16px;padding:20px;color:white;
                box-shadow:0 8px 24px {{ $k['shadow'] }};
                transition:0.2s;"
         onmouseover="this.style.transform='translateY(-3px)'"
         onmouseout="this.style.transform='translateY(0)'">
        <div style="font-size:30px;margin-bottom:10px;">{{ $k['icon'] }}</div>
        <div style="font-size:32px;font-weight:800;line-height:1;margin-bottom:4px;">
            {{ $k['value'] }}
        </div>
        <div style="font-size:13px;font-weight:600;opacity:0.95;">{{ $k['label'] }}</div>
        <div style="font-size:11px;opacity:0.65;margin-top:3px;">{{ $k['sub'] }}</div>
    </div>
    @endforeach
</div>

<!-- LIGNE 1 : ÉVOLUTION ANNUELLE -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:20px;">

    <!-- GRAPHIQUE ÉVOLUTION -->
    <div style="background:white;border-radius:18px;padding:22px;
                box-shadow:0 4px 20px rgba(0,0,0,0.06);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <div>
                <h3 style="font-weight:800;font-size:16px;color:#1e293b;margin:0;">
                    📈 Évolution annuelle {{ now()->year }}
                </h3>
                <p style="font-size:12px;color:#64748b;margin:4px 0 0;">
                    Patients, prescriptions et dispensations par mois
                </p>
            </div>
        </div>
        <canvas id="evolutionChart" height="120"></canvas>
    </div>

    <!-- STATUT PRESCRIPTIONS -->
    <div style="background:white;border-radius:18px;padding:22px;
                box-shadow:0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="font-weight:800;font-size:16px;color:#1e293b;margin-bottom:4px;">
            📋 Statut Prescriptions
        </h3>
        <p style="font-size:12px;color:#64748b;margin-bottom:16px;">
            Répartition par statut
        </p>
        <canvas id="prescDonut" height="200"></canvas>
        <div style="margin-top:16px;">
            @foreach([
                ['label'=>'Validées',  'value'=>$validees,  'color'=>'#22c55e'],
                ['label'=>'En attente','value'=>$enAttente, 'color'=>'#f59e0b'],
                ['label'=>'Annulées',  'value'=>$annulees,  'color'=>'#ef4444'],
            ] as $s)
            <div style="display:flex;justify-content:space-between;
                        align-items:center;padding:6px 0;
                        border-bottom:1px solid #f1f5f9;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:10px;height:10px;border-radius:3px;
                                background:{{ $s['color'] }};"></div>
                    <span style="font-size:13px;color:#374151;">{{ $s['label'] }}</span>
                </div>
                <span style="font-size:13px;font-weight:700;color:{{ $s['color'] }};">
                    {{ $s['value'] }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- LIGNE 2 : PATIENTS -->
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:20px;">

    <!-- DONUT STATUT VITAL -->
    <div style="background:white;border-radius:18px;padding:22px;
                box-shadow:0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="font-weight:800;font-size:15px;color:#1e293b;margin-bottom:4px;">
            ❤️ Statut Vital
        </h3>
        <p style="font-size:12px;color:#64748b;margin-bottom:14px;">
            {{ $totalPatients }} patients total
        </p>
        <canvas id="vitalDonut" height="180"></canvas>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:14px;">
            @foreach([
                ['label'=>'Vivants', 'value'=>$vivants, 'pct'=>$totalPatients>0?round($vivants/$totalPatients*100):0, 'color'=>'#22c55e'],
                ['label'=>'Décédés', 'value'=>$decedes, 'pct'=>$totalPatients>0?round($decedes/$totalPatients*100):0, 'color'=>'#ef4444'],
            ] as $v)
            <div style="background:{{ $v['color'] }}15;border:1px solid {{ $v['color'] }}30;
                        border-radius:10px;padding:10px;text-align:center;">
                <div style="font-size:18px;font-weight:800;color:{{ $v['color'] }};">
                    {{ $v['value'] }}
                </div>
                <div style="font-size:11px;color:#64748b;font-weight:600;">
                    {{ $v['label'] }}
                </div>
                <div style="font-size:10px;color:#94a3b8;">{{ $v['pct'] }}%</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- RÉPARTITION SEXE -->
    <div style="background:white;border-radius:18px;padding:22px;
                box-shadow:0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="font-weight:800;font-size:15px;color:#1e293b;margin-bottom:4px;">
            ⚧ Répartition par sexe
        </h3>
        <p style="font-size:12px;color:#64748b;margin-bottom:14px;">
            Hommes vs Femmes
        </p>
        <canvas id="sexeChart" height="180"></canvas>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:14px;">
            @foreach([
                ['label'=>'Masculin','value'=>$masculin,'color'=>'#3b82f6','icon'=>'♂'],
                ['label'=>'Féminin', 'value'=>$feminin, 'color'=>'#ec4899','icon'=>'♀'],
            ] as $s)
            <div style="background:{{ $s['color'] }}15;border:1px solid {{ $s['color'] }}30;
                        border-radius:10px;padding:10px;text-align:center;">
                <div style="font-size:20px;color:{{ $s['color'] }};">{{ $s['icon'] }}</div>
                <div style="font-size:18px;font-weight:800;color:{{ $s['color'] }};">
                    {{ $s['value'] }}
                </div>
                <div style="font-size:11px;color:#64748b;font-weight:600;">
                    {{ $s['label'] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- DISPENSATIONS PÉRIODES -->
    <div style="background:white;border-radius:18px;padding:22px;
                box-shadow:0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="font-weight:800;font-size:15px;color:#1e293b;margin-bottom:14px;">
            💊 Dispensations
        </h3>
        @foreach([
            ['label'=>"Aujourd'hui",'value'=>$dispAujourdhui,'color'=>'#6a4c93','icon'=>'📅'],
            ['label'=>'Cette semaine','value'=>$dispCeSemaine,'color'=>'#2a9d8f','icon'=>'📆'],
            ['label'=>'Ce mois','value'=>$dispCeMois,'color'=>'#264653','icon'=>'📊'],
            ['label'=>'Total général','value'=>$totalDisp,'color'=>'#f4a261','icon'=>'∑'],
        ] as $d)
        <div style="display:flex;align-items:center;justify-content:space-between;
                    padding:11px 14px;border-radius:10px;margin-bottom:8px;
                    background:{{ $d['color'] }}10;border:1px solid {{ $d['color'] }}25;">
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-size:18px;">{{ $d['icon'] }}</span>
                <span style="font-size:13px;font-weight:600;color:#374151;">
                    {{ $d['label'] }}
                </span>
            </div>
            <span style="font-size:20px;font-weight:800;color:{{ $d['color'] }};">
                {{ $d['value'] }}
            </span>
        </div>
        @endforeach
    </div>

</div>

<!-- LIGNE 3 : CANCERS + WILAYAS + STOCK -->
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:20px;">

    <!-- TOP CANCERS -->
    <div style="background:white;border-radius:18px;padding:22px;
                box-shadow:0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="font-weight:800;font-size:15px;color:#1e293b;margin-bottom:16px;">
            🔬 Types de cancers
        </h3>
        @foreach($parCancer as $c)
        @php $pct = $totalPatients>0 ? round($c->total/$totalPatients*100) : 0; @endphp
        <div style="margin-bottom:12px;">
            <div style="display:flex;justify-content:space-between;
                        font-size:12px;font-weight:600;margin-bottom:4px;color:#374151;">
                <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:150px;">
                    {{ $c->type_cancer ?? 'Non spécifié' }}
                </span>
                <span style="color:#64748b;flex-shrink:0;margin-left:8px;">
                    {{ $c->total }} ({{ $pct }}%)
                </span>
            </div>
            <div style="height:8px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
                <div style="height:100%;width:{{ $pct }}%;
                            background:linear-gradient(90deg,#e63946,#e76f51);
                            border-radius:4px;transition:width 1.2s ease;"
                     data-width="{{ $pct }}"></div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- TOP WILAYAS -->
    <div style="background:white;border-radius:18px;padding:22px;
                box-shadow:0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="font-weight:800;font-size:15px;color:#1e293b;margin-bottom:16px;">
            🗺️ Wilayas d'origine
        </h3>
        @foreach($parWilaya as $w)
        @php $pct = $totalPatients>0 ? round($w->total/$totalPatients*100) : 0; @endphp
        <div style="margin-bottom:12px;">
            <div style="display:flex;justify-content:space-between;
                        font-size:12px;font-weight:600;margin-bottom:4px;color:#374151;">
                <span>📍 {{ $w->wilaya ?? 'N/A' }}</span>
                <span style="color:#64748b;">{{ $w->total }} ({{ $pct }}%)</span>
            </div>
            <div style="height:8px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
                <div style="height:100%;width:{{ $pct }}%;
                            background:linear-gradient(90deg,#2a9d8f,#21867a);
                            border-radius:4px;"
                     data-width="{{ $pct }}"></div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- ÉTAT STOCK MÉDICAMENTS -->
    <div style="background:white;border-radius:18px;padding:22px;
                box-shadow:0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="font-weight:800;font-size:15px;color:#1e293b;margin-bottom:14px;">
            🧪 État du Stock
        </h3>
        <canvas id="stockPie" height="160"></canvas>
        <div style="margin-top:14px;">
            @foreach([
                ['label'=>'Stock OK',         'value'=>$stockOk,      'color'=>'#22c55e'],
                ['label'=>'Alerte stock',      'value'=>$alerteStock,  'color'=>'#f59e0b'],
                ['label'=>'Rupture',           'value'=>$ruptures,     'color'=>'#ef4444'],
                ['label'=>'Expirés',           'value'=>$expires,      'color'=>'#dc2626'],
                ['label'=>'Bientôt expirés',   'value'=>$bientot,      'color'=>'#f97316'],
            ] as $s)
            <div style="display:flex;justify-content:space-between;
                        padding:5px 0;border-bottom:1px solid #f1f5f9;">
                <div style="display:flex;align-items:center;gap:7px;">
                    <div style="width:9px;height:9px;border-radius:2px;
                                background:{{ $s['color'] }};flex-shrink:0;"></div>
                    <span style="font-size:12px;color:#374151;">{{ $s['label'] }}</span>
                </div>
                <span style="font-size:12px;font-weight:700;color:{{ $s['color'] }};">
                    {{ $s['value'] }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

</div>

<!-- GRAPHIQUE DISPENSATIONS MENSUEL -->
<div style="background:white;border-radius:18px;padding:22px;
            box-shadow:0 4px 20px rgba(0,0,0,0.06);margin-bottom:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h3 style="font-weight:800;font-size:16px;color:#1e293b;margin:0;">
                💊 Dispensations mensuelles — {{ now()->year }}
            </h3>
            <p style="font-size:12px;color:#64748b;margin:4px 0 0;">
                Nombre de dispensations et quantités dispensées
            </p>
        </div>
    </div>
    <canvas id="dispChart" height="100"></canvas>
</div>

<!-- LOTS STATUS -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
    @foreach([
        ['label'=>'Lots valides',      'value'=>$lotsValides,  'color'=>'#22c55e','icon'=>'✔','bg'=>'#f0fdf4'],
        ['label'=>'Bientôt expirés',   'value'=>$lotsBientot,  'color'=>'#f97316','icon'=>'⏰','bg'=>'#fff7ed'],
        ['label'=>'Lots expirés',      'value'=>$lotsExpires,  'color'=>'#ef4444','icon'=>'⛔','bg'=>'#fef2f2'],
        ['label'=>'Total lots',        'value'=>$totalLots,    'color'=>'#3b82f6','icon'=>'📦','bg'=>'#eff6ff'],
    ] as $l)
    <div style="background:{{ $l['bg'] }};border:1px solid {{ $l['color'] }}25;
                border-radius:14px;padding:18px;text-align:center;">
        <div style="font-size:28px;margin-bottom:8px;">{{ $l['icon'] }}</div>
        <div style="font-size:28px;font-weight:800;color:{{ $l['color'] }};margin-bottom:4px;">
            {{ $l['value'] }}
        </div>
        <div style="font-size:12px;color:#64748b;font-weight:600;">{{ $l['label'] }}</div>
    </div>
    @endforeach
</div>

@push('scripts')
<script>
const mois = @json($moisLabels);

// ÉVOLUTION ANNUELLE
new Chart(document.getElementById('evolutionChart'), {
    type: 'line',
    data: {
        labels: mois,
        datasets: [
            {
                label: 'Patients',
                data: @json($patientsParMois),
                borderColor: '#2a9d8f',
                backgroundColor: 'rgba(42,157,143,0.08)',
                fill: true, tension: 0.4, borderWidth: 2.5, pointRadius: 4
            },
            {
                label: 'Prescriptions',
                data: @json($prescriptionsMois),
                borderColor: '#264653',
                backgroundColor: 'rgba(38,70,83,0.06)',
                fill: true, tension: 0.4, borderWidth: 2.5, pointRadius: 4
            },
            {
                label: 'Dispensations',
                data: @json($dispensationsMois),
                borderColor: '#6a4c93',
                backgroundColor: 'rgba(106,76,147,0.06)',
                fill: true, tension: 0.4, borderWidth: 2.5, pointRadius: 4
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'top', labels: { font: { size: 12 } } }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.03)' } },
            x: { grid: { display: false } }
        }
    }
});

// DONUT PRESCRIPTIONS
new Chart(document.getElementById('prescDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Validées', 'En attente', 'Annulées'],
        datasets: [{
            data: [{{ $validees }}, {{ $enAttente }}, {{ $annulees }}],
            backgroundColor: ['#22c55e', '#f59e0b', '#ef4444'],
            borderWidth: 0, hoverOffset: 6
        }]
    },
    options: {
        responsive: true, cutout: '65%',
        plugins: { legend: { display: false } }
    }
});

// DONUT VITAL
new Chart(document.getElementById('vitalDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Vivants', 'Décédés'],
        datasets: [{
            data: [{{ $vivants }}, {{ $decedes }}],
            backgroundColor: ['#22c55e', '#ef4444'],
            borderWidth: 0, hoverOffset: 6
        }]
    },
    options: {
        responsive: true, cutout: '60%',
        plugins: { legend: { display: false } }
    }
});

// BAR SEXE
new Chart(document.getElementById('sexeChart'), {
    type: 'bar',
    data: {
        labels: ['Masculin', 'Féminin'],
        datasets: [{
            data: [{{ $masculin }}, {{ $feminin }}],
            backgroundColor: ['#3b82f6', '#ec4899'],
            borderRadius: 10, borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } },
            x: { grid: { display: false } }
        }
    }
});

// PIE STOCK
new Chart(document.getElementById('stockPie'), {
    type: 'doughnut',
    data: {
        labels: ['OK', 'Alerte', 'Rupture', 'Expirés', 'Bientôt'],
        datasets: [{
            data: [{{ $stockOk }}, {{ $alerteStock }}, {{ $ruptures }}, {{ $expires }}, {{ $bientot }}],
            backgroundColor: ['#22c55e', '#f59e0b', '#ef4444', '#dc2626', '#f97316'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true, cutout: '55%',
        plugins: { legend: { display: false } }
    }
});

// BAR DISPENSATIONS
new Chart(document.getElementById('dispChart'), {
    type: 'bar',
    data: {
        labels: mois,
        datasets: [
            {
                label: 'Nb dispensations',
                data: @json($dispensationsMois),
                backgroundColor: 'rgba(106,76,147,0.8)',
                borderRadius: 6, borderSkipped: false, yAxisID: 'y'
            },
            {
                label: 'Quantités',
                data: @json($quantitesMois),
                type: 'line',
                borderColor: '#2a9d8f',
                backgroundColor: 'rgba(42,157,143,0.1)',
                fill: true, tension: 0.4, borderWidth: 2,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { position: 'top' } },
        scales: {
            y:  { beginAtZero: true, position: 'left', grid: { color: 'rgba(0,0,0,0.03)' } },
            y1: { beginAtZero: true, position: 'right', grid: { display: false } },
            x:  { grid: { display: false } }
        }
    }
});
</script>
@endpush

@endsection