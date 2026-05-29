@extends('layouts.app')
@section('title', 'Statistiques Prescriptions')

@section('content')

<div style="background:linear-gradient(135deg,#264653,#1a3e2b);
            border-radius:18px;padding:24px 28px;margin-bottom:20px;
            display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;">
    <div>
        <h1 style="color:white;font-size:22px;font-weight:800;margin:0;">
            📊 Statistiques des Prescriptions
        </h1>
        <p style="color:rgba(255,255,255,0.6);font-size:13px;margin:4px 0 0;">
            Analyse détaillée — {{ now()->year }}
        </p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('oncologie.prescriptions.export', ['format'=>'pdf']) }}"
           style="background:#e63946;color:white;padding:9px 16px;border-radius:10px;
                  text-decoration:none;font-weight:600;font-size:13px;
                  display:flex;align-items:center;gap:6px;">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('oncologie.prescriptions.export', ['format'=>'excel']) }}"
           style="background:#22c55e;color:white;padding:9px 16px;border-radius:10px;
                  text-decoration:none;font-weight:600;font-size:13px;
                  display:flex;align-items:center;gap:6px;">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <a href="{{ route('oncologie.prescriptions.index') }}"
           style="background:rgba(255,255,255,0.15);color:white;padding:9px 16px;
                  border-radius:10px;text-decoration:none;font-weight:600;font-size:13px;">
            ← Retour
        </a>
    </div>
</div>

<!-- KPI -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px;">
    @foreach([
        ['label'=>'Total prescriptions','value'=>$total,    'color'=>'#264653','icon'=>'📋','shadow'=>'rgba(38,70,83,0.25)'],
        ['label'=>'Validées',           'value'=>$validees, 'color'=>'#22c55e','icon'=>'✅','shadow'=>'rgba(34,197,94,0.25)'],
        ['label'=>'En attente',         'value'=>$enAttente,'color'=>'#f59e0b','icon'=>'⏳','shadow'=>'rgba(245,158,11,0.25)'],
        ['label'=>'Annulées',           'value'=>$annulees, 'color'=>'#ef4444','icon'=>'❌','shadow'=>'rgba(239,68,68,0.25)'],
    ] as $k)
    <div style="background:linear-gradient(135deg,{{ $k['color'] }},{{ $k['color'] }}cc);
                color:white;border-radius:16px;padding:20px;
                box-shadow:0 8px 24px {{ $k['shadow'] }};transition:0.2s;"
         onmouseover="this.style.transform='translateY(-3px)'"
         onmouseout="this.style.transform='translateY(0)'">
        <div style="font-size:30px;margin-bottom:10px;">{{ $k['icon'] }}</div>
        <div style="font-size:32px;font-weight:800;line-height:1;">{{ $k['value'] }}</div>
        <div style="font-size:13px;opacity:0.85;margin-top:6px;font-weight:600;">
            {{ $k['label'] }}
        </div>
        <div style="font-size:11px;opacity:0.5;margin-top:4px;">
            {{ $total > 0 ? round($k['value'] / $total * 100) : 0 }}% du total
        </div>
    </div>
    @endforeach
</div>

<!-- GRAPHIQUES LIGNE 1 -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:20px;">

    <!-- ÉVOLUTION MENSUELLE -->
    <div style="background:white;border-radius:18px;padding:22px;
                box-shadow:0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="font-weight:800;font-size:16px;color:#1e293b;margin-bottom:18px;">
            📈 Évolution mensuelle {{ now()->year }}
        </h3>
        <canvas id="evolutionChart" height="130"></canvas>
    </div>

    <!-- DONUT STATUTS -->
    <div style="background:white;border-radius:18px;padding:22px;
                box-shadow:0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="font-weight:800;font-size:16px;color:#1e293b;margin-bottom:14px;">
            🍩 Répartition statuts
        </h3>
        <canvas id="statutsDonut" height="200"></canvas>
        <div style="margin-top:14px;">
            @foreach([
                ['label'=>'Validées', 'value'=>$validees,  'color'=>'#22c55e'],
                ['label'=>'Attente',  'value'=>$enAttente, 'color'=>'#f59e0b'],
                ['label'=>'Annulées', 'value'=>$annulees,  'color'=>'#ef4444'],
            ] as $s)
            <div style="display:flex;justify-content:space-between;
                        padding:6px 0;border-bottom:1px solid #f1f5f9;font-size:13px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:10px;height:10px;border-radius:3px;
                                background:{{ $s['color'] }};"></div>
                    {{ $s['label'] }}
                </div>
                <strong style="color:{{ $s['color'] }};">{{ $s['value'] }}</strong>
            </div>
            @endforeach
        </div>
    </div>

</div>

<!-- GRAPHIQUES LIGNE 2 -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

    <!-- TOP MÉDECINS -->
    <div style="background:white;border-radius:18px;padding:22px;
                box-shadow:0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="font-weight:800;font-size:16px;color:#1e293b;margin-bottom:16px;">
            🩺 Top Médecins prescripteurs
        </h3>
        @forelse($topMedecins as $med)
        @php $pct = $total > 0 ? round($med->total / $total * 100) : 0; @endphp
        <div style="margin-bottom:13px;">
            <div style="display:flex;justify-content:space-between;
                        font-size:13px;font-weight:600;margin-bottom:5px;color:#374151;">
                <span>
                    🩺 {{ $med->medecin_nom ?? 'Inconnu' }}
                </span>
                <span style="color:#64748b;">{{ $med->total }} ({{ $pct }}%)</span>
            </div>
            <div style="height:8px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
                <div style="height:100%;width:{{ $pct }}%;
                            background:linear-gradient(90deg,#264653,#2a9d8f);
                            border-radius:4px;transition:width 1.2s ease;"></div>
            </div>
        </div>
        @empty
        <p style="color:#94a3b8;font-size:13px;text-align:center;padding:16px 0;">
            Aucune donnée disponible
        </p>
        @endforelse
    </div>

    <!-- TOP PROTOCOLES -->
    <div style="background:white;border-radius:18px;padding:22px;
                box-shadow:0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="font-weight:800;font-size:16px;color:#1e293b;margin-bottom:16px;">
            🧬 Top Protocoles utilisés
        </h3>
        @forelse($topProtocoles as $proto)
        @php $pct = $total > 0 ? round($proto->total / $total * 100) : 0; @endphp
        <div style="margin-bottom:13px;">
            <div style="display:flex;justify-content:space-between;
                        font-size:13px;font-weight:600;margin-bottom:5px;color:#374151;">
                <span>🧬 {{ optional($proto->protocole)->nom ?? 'N/A' }}</span>
                <span style="color:#64748b;">{{ $proto->total }} ({{ $pct }}%)</span>
            </div>
            <div style="height:8px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
                <div style="height:100%;width:{{ $pct }}%;
                            background:linear-gradient(90deg,#6a4c93,#e63946);
                            border-radius:4px;transition:width 1.2s ease;"></div>
            </div>
        </div>
        @empty
        <p style="color:#94a3b8;font-size:13px;text-align:center;padding:16px 0;">
            Aucune donnée disponible
        </p>
        @endforelse
    </div>

</div>

@push('scripts')
<script>
const mois = @json($moisLabels);

// ÉVOLUTION
new Chart(document.getElementById('evolutionChart'), {
    type: 'line',
    data: {
        labels: mois,
        datasets: [
            {
                label: 'Total',
                data: @json($totauxMois),
                borderColor: '#264653',
                backgroundColor: 'rgba(38,70,83,0.07)',
                fill: true, tension: 0.4, borderWidth: 2.5, pointRadius: 4
            },
            {
                label: 'Validées',
                data: @json($valideesMois),
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34,197,94,0.07)',
                fill: true, tension: 0.4, borderWidth: 2.5, pointRadius: 4
            },
            {
                label: 'Annulées',
                data: @json($annuleesMois),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239,68,68,0.05)',
                fill: true, tension: 0.4, borderWidth: 2, pointRadius: 3
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { position: 'top', labels: { font: { size: 12 } } } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.03)' } },
            x: { grid: { display: false } }
        }
    }
});

// DONUT
new Chart(document.getElementById('statutsDonut'), {
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
</script>
@endpush

@endsection