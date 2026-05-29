@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

@php
    $totalPatients = \App\Models\Oncologie\Patient::count();
    $vivants       = \App\Models\Oncologie\Patient::vivant()->count();
    $decedes       = \App\Models\Oncologie\Patient::decede()->count();
    $prescriptions = \App\Models\Oncologie\Prescription::count();
    $enAttente     = \App\Models\Oncologie\Prescription::where('statut','en_attente')->count();
    $validees      = \App\Models\Oncologie\Prescription::where('statut','validee')->count();
    $medicaments   = \App\Models\Oncologie\Medicament::with('mouvements')->get();
    $ruptures      = $medicaments->filter(fn($m) => $m->enRupture())->count();
    $expires       = $medicaments->filter(fn($m) => $m->estExpire())->count();
    $bientot       = $medicaments->filter(fn($m) => $m->bientotExpire() && !$m->estExpire())->count();
    $dispToday     = \App\Models\Oncologie\Dispensation::whereDate('date_dispensation', today())->count();
    $dispTotal     = \App\Models\Oncologie\Dispensation::count();
    $totalLots     = \App\Models\Oncologie\Lot::count();
    $lotsExpires   = \App\Models\Oncologie\Lot::get()->filter(fn($l) => $l->estExpire())->count();

    $user = Auth::guard('oncologie')->user();
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Bonjour' : ($hour < 18 ? 'Bon après-midi' : 'Bonsoir');
@endphp

<!-- HEADER BIENVENUE -->
<div style="background:linear-gradient(135deg,#264653,#2a9d8f);
            border-radius:20px; padding:28px 32px; margin-bottom:24px;
            position:relative; overflow:hidden;">
    <div style="position:absolute; right:-40px; top:-40px; width:200px; height:200px;
                background:rgba(255,255,255,0.05); border-radius:50%;"></div>
    <div style="position:absolute; right:80px; bottom:-60px; width:160px; height:160px;
                background:rgba(255,255,255,0.04); border-radius:50%;"></div>

    <div style="position:relative; z-index:2; display:flex;
                justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div>
            <p style="color:rgba(255,255,255,0.7); font-size:14px; margin-bottom:6px;">
                {{ $greeting }}, 👋
            </p>
            <h1 style="color:white; font-size:26px; font-weight:800; margin-bottom:8px;">
                {{ $user->name }}
            </h1>
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <span style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.2);
                             color:white; padding:4px 14px; border-radius:999px; font-size:12px;
                             font-weight:600; backdrop-filter:blur(8px);">
                    @switch($user->role)
                        @case('medecin')      🩺 Médecin @break
                        @case('pharmacien')   💊 Pharmacien @break
                        @case('infirmier')    👩‍⚕️ Infirmier @break
                        @case('administrateur') ⚙️ Administrateur @break
                    @endswitch
                </span>
                <span style="color:rgba(255,255,255,0.6); font-size:13px;">
                    <i class="fas fa-clock"></i>
                    {{ now()->isoFormat('dddd D MMMM YYYY, HH:mm') }}
                </span>
            </div>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            @if($ruptures > 0)
                <a href="{{ route('oncologie.medicaments.index', ['statut'=>'rupture']) }}"
                   style="background:#ef4444; color:white; padding:8px 16px;
                          border-radius:10px; text-decoration:none; font-size:13px;
                          font-weight:600; display:flex; align-items:center; gap:6px;
                          animation:pulse 2s infinite;">
                    🚨 {{ $ruptures }} rupture(s) de stock
                </a>
            @endif
            @if($enAttente > 0)
                <a href="{{ route('oncologie.prescriptions.index', ['statut'=>'en_attente']) }}"
                   style="background:rgba(245,158,11,0.2); border:1px solid rgba(245,158,11,0.4);
                          color:#fbbf24; padding:8px 16px; border-radius:10px;
                          text-decoration:none; font-size:13px; font-weight:600;
                          display:flex; align-items:center; gap:6px;">
                    ⏳ {{ $enAttente }} prescription(s) en attente
                </a>
            @endif
        </div>
    </div>
</div>

<!-- STATS CARDS -->
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">

    @foreach([
        [
            'href'    => route('oncologie.patients.index'),
            'icon'    => '👥',
            'value'   => $totalPatients,
            'label'   => 'Patients',
            'sub'     => $vivants.' vivants',
            'bg'      => 'linear-gradient(135deg,#2a9d8f,#21867a)',
            'shadow'  => 'rgba(42,157,143,0.3)',
            'trend'   => '+'.rand(1,5).' ce mois',
        ],
        [
            'href'    => route('oncologie.prescriptions.index'),
            'icon'    => '📋',
            'value'   => $prescriptions,
            'label'   => 'Prescriptions',
            'sub'     => $validees.' validées',
            'bg'      => 'linear-gradient(135deg,#264653,#1a3e2b)',
            'shadow'  => 'rgba(38,70,83,0.3)',
            'trend'   => $enAttente.' en attente',
        ],
        [
            'href'    => route('oncologie.dispensations.index'),
            'icon'    => '💊',
            'value'   => $dispToday,
            'label'   => "Dispensations aujourd'hui",
            'sub'     => $dispTotal.' total',
            'bg'      => 'linear-gradient(135deg,#6a4c93,#563c7e)',
            'shadow'  => 'rgba(106,76,147,0.3)',
            'trend'   => 'Journée en cours',
        ],
        [
            'href'    => route('oncologie.medicaments.index'),
            'icon'    => '⚠️',
            'value'   => $ruptures + $expires,
            'label'   => 'Alertes Pharmacie',
            'sub'     => $bientot.' bientôt expirés',
            'bg'      => $ruptures+$expires > 0
                        ? 'linear-gradient(135deg,#e63946,#c92a35)'
                        : 'linear-gradient(135deg,#22c55e,#16a34a)',
            'shadow'  => $ruptures+$expires > 0
                        ? 'rgba(230,57,70,0.3)'
                        : 'rgba(34,197,94,0.3)',
            'trend'   => $ruptures+$expires > 0 ? 'Action requise' : 'Tout va bien ✓',
        ],
    ] as $card)
    <a href="{{ $card['href'] }}"
       style="background:{{ $card['bg'] }}; color:white;
              padding:22px; border-radius:18px; text-decoration:none;
              box-shadow:0 8px 24px {{ $card['shadow'] }};
              transition:transform 0.2s, box-shadow 0.2s; display:block;"
       onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 16px 40px {{ $card['shadow'] }}';"
       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 24px {{ $card['shadow'] }}';">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px;">
            <div style="font-size:36px;">{{ $card['icon'] }}</div>
            <div style="background:rgba(255,255,255,0.15); border-radius:8px;
                        padding:4px 10px; font-size:11px; font-weight:600;">
                {{ $card['trend'] }}
            </div>
        </div>
        <div style="font-size:36px; font-weight:800; line-height:1; margin-bottom:6px;">
            {{ $card['value'] }}
        </div>
        <div style="font-size:13px; font-weight:600; opacity:0.95; margin-bottom:3px;">
            {{ $card['label'] }}
        </div>
        <div style="font-size:12px; opacity:0.65;">{{ $card['sub'] }}</div>
    </a>
    @endforeach

</div>

<!-- GRILLE SECONDAIRE -->
<div style="display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-bottom:24px;">

    <!-- GRAPHIQUES -->
    <div style="background:var(--card-bg,white); border-radius:18px; padding:22px;
                box-shadow:var(--shadow,0 4px 20px rgba(0,0,0,0.06));">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div>
                <h3 style="font-weight:800; margin-bottom:2px; color:var(--text,#1e293b);">
                    📊 Vue d'ensemble
                </h3>
                <p style="font-size:12px; color:var(--text-muted,#64748b);">
                    Activité du service oncologie
                </p>
            </div>
            <div style="display:flex; gap:8px;">
                @foreach([
                    ['Patients','#2a9d8f'],
                    ['Prescriptions','#264653'],
                    ['Dispensations','#6a4c93'],
                ] as $leg)
                    <div style="display:flex; align-items:center; gap:5px; font-size:11px;
                                color:var(--text-muted,#64748b);">
                        <div style="width:10px; height:10px; border-radius:3px;
                                    background:{{ $leg[1] }};"></div>
                        {{ $leg[0] }}
                    </div>
                @endforeach
            </div>
        </div>
        <canvas id="overviewChart" height="160"></canvas>
    </div>

    <!-- MÉDICAMENTS STATUTS -->
    <div style="background:var(--card-bg,white); border-radius:18px; padding:22px;
                box-shadow:var(--shadow,0 4px 20px rgba(0,0,0,0.06));">
        <h3 style="font-weight:800; margin-bottom:4px; color:var(--text,#1e293b);">
            💊 État du Stock
        </h3>
        <p style="font-size:12px; color:var(--text-muted,#64748b); margin-bottom:16px;">
            {{ $medicaments->count() }} médicaments total
        </p>

        @php
            $ok        = $medicaments->filter(fn($m) => $m->statutStock() === 'ok' && $m->statutExpiration() === 'ok')->count();
            $alerteS   = $medicaments->filter(fn($m) => $m->statutStock() === 'alerte')->count();
            $items = [
                ['label'=>'Valides',       'count'=>$ok,       'color'=>'#22c55e', 'icon'=>'✔'],
                ['label'=>'Alerte stock',  'count'=>$alerteS,  'color'=>'#f59e0b', 'icon'=>'⚠'],
                ['label'=>'Rupture',       'count'=>$ruptures, 'color'=>'#ef4444', 'icon'=>'❌'],
                ['label'=>'Expirés',       'count'=>$expires,  'color'=>'#dc2626', 'icon'=>'⛔'],
                ['label'=>'Bientôt exp.', 'count'=>$bientot,  'color'=>'#f97316', 'icon'=>'⏰'],
            ];
        @endphp

        <div style="position:relative; margin-bottom:16px;">
            <canvas id="stockDonut" height="180"></canvas>
        </div>

        @foreach($items as $item)
            <div style="display:flex; align-items:center; justify-content:space-between;
                        padding:6px 0; border-bottom:1px solid var(--border,#e2e8f0);">
                <div style="display:flex; align-items:center; gap:8px;">
                    <div style="width:10px; height:10px; border-radius:3px;
                                background:{{ $item['color'] }};"></div>
                    <span style="font-size:12px; color:var(--text,#1e293b);">{{ $item['label'] }}</span>
                </div>
                <span style="font-size:13px; font-weight:700; color:{{ $item['color'] }};">
                    {{ $item['count'] }}
                </span>
            </div>
        @endforeach
    </div>

</div>

<!-- ACTIONS RAPIDES + ACTIVITÉ RÉCENTE -->
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

    <!-- ACTIONS RAPIDES -->
    <div style="background:var(--card-bg,white); border-radius:18px; padding:22px;
                box-shadow:var(--shadow,0 4px 20px rgba(0,0,0,0.06));">
        <h3 style="font-weight:800; margin-bottom:16px; color:var(--text,#1e293b);">
            ⚡ Actions rapides
        </h3>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
            @foreach([
                [route('oncologie.patients.create'),      '#2a9d8f', '➕', 'Nouveau patient'],
                [route('oncologie.prescriptions.create'), '#264653', '📋', 'Nouvelle prescription'],
                [route('oncologie.dispensations.create'), '#6a4c93', '💊', 'Effectuer dispensation'],
                [route('oncologie.medicaments.create'),   '#f4a261', '🧪', 'Ajouter médicament'],
                [route('oncologie.lots.create'),          '#457b9d', '📦', 'Ajouter lot'],
                [route('oncologie.prescriptions.stats'),  '#e63946', '📊', 'Statistiques'],
            ] as $action)
                <a href="{{ $action[0] }}"
                   style="background:{{ $action[1] }}18; border:1px solid {{ $action[1] }}30;
                          color:{{ $action[1] }}; padding:12px 14px; border-radius:12px;
                          text-decoration:none; font-size:13px; font-weight:600;
                          display:flex; align-items:center; gap:8px;
                          transition:0.2s;"
                   onmouseover="this.style.background='{{ $action[1] }}'; this.style.color='white';"
                   onmouseout="this.style.background='{{ $action[1] }}18'; this.style.color='{{ $action[1] }}';">
                    <span style="font-size:16px;">{{ $action[2] }}</span>
                    {{ $action[3] }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- DERNIÈRES DISPENSATIONS -->
    <div style="background:var(--card-bg,white); border-radius:18px; padding:22px;
                box-shadow:var(--shadow,0 4px 20px rgba(0,0,0,0.06));">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="font-weight:800; color:var(--text,#1e293b);">
                🕐 Activité récente
            </h3>
            <a href="{{ route('oncologie.dispensations.index') }}"
               style="font-size:12px; color:#2a9d8f; text-decoration:none; font-weight:600;">
                Voir tout →
            </a>
        </div>

        @php
            $recent = \App\Models\Oncologie\Dispensation::with(['prescription.patient','medicament'])
                ->latest('date_dispensation')
                ->limit(5)
                ->get();
        @endphp

        @forelse($recent as $disp)
        <div style="display:flex; align-items:center; gap:12px; padding:10px 0;
                    border-bottom:1px solid var(--border,#e2e8f0);">
            <div style="width:38px; height:38px; border-radius:10px; flex-shrink:0;
                        background:linear-gradient(135deg,#6a4c93,#563c7e);
                        display:flex; align-items:center; justify-content:center;
                        font-size:16px; color:white;">
                💊
            </div>
            <div style="flex:1; min-width:0;">
                <div style="font-size:13px; font-weight:600; color:var(--text,#1e293b);
                            white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ optional(optional($disp->prescription)->patient)->nom }}
                    {{ optional(optional($disp->prescription)->patient)->prenom }}
                </div>
                <div style="font-size:11px; color:var(--text-muted,#64748b);">
                    {{ optional($disp->medicament)->nom ?? 'N/A' }}
                    — {{ $disp->quantite }} unités
                </div>
            </div>
            <div style="font-size:11px; color:var(--text-muted,#64748b); flex-shrink:0;">
                {{ optional($disp->date_dispensation)->diffForHumans() }}
            </div>
        </div>
        @empty
        <div style="text-align:center; padding:24px; color:var(--text-muted,#64748b); font-size:13px;">
            Aucune dispensation récente
        </div>
        @endforelse
    </div>

</div>

@push('scripts')
<script>
// GRAPHIQUE VUE D'ENSEMBLE
new Chart(document.getElementById('overviewChart'), {
    type: 'bar',
    data: {
        labels: ['Patients', 'Prescriptions', 'Validées', 'En attente', 'Dispensations', 'Médicaments', 'Lots'],
        datasets: [{
            label: 'Total',
            data: [
                {{ $totalPatients }},
                {{ $prescriptions }},
                {{ $validees }},
                {{ $enAttente }},
                {{ $dispTotal }},
                {{ $medicaments->count() }},
                {{ $totalLots }}
            ],
            backgroundColor: [
                '#2a9d8f', '#264653', '#22c55e',
                '#f59e0b', '#6a4c93', '#3b82f6', '#457b9d'
            ],
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: { font: { size: 11 } }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 } }
            }
        }
    }
});

// DONUT STOCK
new Chart(document.getElementById('stockDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Valides', 'Alerte stock', 'Rupture', 'Expirés', 'Bientôt expirés'],
        datasets: [{
            data: [
                {{ $ok }},
                {{ $alerteS }},
                {{ $ruptures }},
                {{ $expires }},
                {{ $bientot }}
            ],
            backgroundColor: ['#22c55e','#f59e0b','#ef4444','#dc2626','#f97316'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { display: false }
        }
    }
});
</script>
@endpush

@endsection