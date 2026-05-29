@extends('layouts.app')
@section('title', 'Patients')

@section('content')

<!-- HEADER -->
<div style="display:flex; justify-content:space-between; align-items:center;
            background:var(--card-bg,white); padding:18px 22px; border-radius:16px;
            margin-bottom:18px; box-shadow:var(--shadow,0 4px 20px rgba(0,0,0,0.06));">
    <div>
        <h2 style="margin:0; font-size:20px; font-weight:800;
                   color:var(--text,#1e293b);">
            🧑‍⚕️ Patients Oncologie
        </h2>
        <p style="margin:0; font-size:12px; color:var(--text-muted,#64748b);">
            {{ $totalPatients }} dossier(s) enregistré(s)
        </p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">

        <!-- BOUTON STATISTIQUES -->
        <button onclick="document.getElementById('statsModal').style.display='flex'"
                style="background:linear-gradient(135deg,#6a4c93,#563c7e); color:white;
                       border:none; padding:10px 18px; border-radius:12px;
                       font-weight:700; cursor:pointer; font-size:13px;
                       display:flex; align-items:center; gap:7px; transition:0.2s;"
                onmouseover="this.style.transform='translateY(-2px)'"
                onmouseout="this.style.transform='translateY(0)'">
            <i class="fas fa-chart-bar"></i> Statistiques
        </button>

        <a href="{{ route('oncologie.patients.create') }}"
           style="background:linear-gradient(135deg,#0ea5e9,#0284c7); color:white;
                  padding:10px 18px; border-radius:12px; font-weight:700;
                  text-decoration:none; font-size:13px;
                  display:flex; align-items:center; gap:7px; transition:0.2s;"
           onmouseover="this.style.transform='translateY(-2px)'"
           onmouseout="this.style.transform='translateY(0)'">
            <i class="fas fa-plus"></i> Ajouter patient
        </a>
    </div>
</div>

<!-- FILTRES -->
<form method="GET" action="{{ route('oncologie.patients.index') }}"
      style="background:var(--card-bg,white); padding:16px 20px; border-radius:14px;
             margin-bottom:16px; box-shadow:var(--shadow,0 4px 20px rgba(0,0,0,0.04));">
    <div style="display:grid; grid-template-columns:2fr 2fr 1fr 1fr auto auto; gap:10px; align-items:center;">
        <input type="text" name="search"
               placeholder="👤 Nom ou prénom"
               value="{{ request('search') }}"
               style="padding:10px 14px; border-radius:10px;
                      border:2px solid var(--border,#e2e8f0);
                      font-size:13px; outline:none; transition:0.2s;
                      background:var(--bg,#f0f4f8); color:var(--text,#1e293b);"
               onfocus="this.style.borderColor='#2a9d8f'"
               onblur="this.style.borderColor='var(--border,#e2e8f0)'">

        <input type="text" name="numero_dossier"
               placeholder="📁 N° dossier"
               value="{{ request('numero_dossier') }}"
               style="padding:10px 14px; border-radius:10px;
                      border:2px solid var(--border,#e2e8f0);
                      font-size:13px; outline:none;
                      background:var(--bg,#f0f4f8); color:var(--text,#1e293b);"
               onfocus="this.style.borderColor='#2a9d8f'"
               onblur="this.style.borderColor='var(--border,#e2e8f0)'">

        <select name="sexe"
                style="padding:10px 14px; border-radius:10px;
                       border:2px solid var(--border,#e2e8f0);
                       font-size:13px; outline:none;
                       background:var(--bg,#f0f4f8); color:var(--text,#1e293b);">
            <option value="">⚧ Sexe</option>
            <option value="Masculin" {{ request('sexe')=='Masculin' ? 'selected':'' }}>Masculin</option>
            <option value="Féminin"  {{ request('sexe')=='Féminin'  ? 'selected':'' }}>Féminin</option>
        </select>

        <select name="statut_vital"
                style="padding:10px 14px; border-radius:10px;
                       border:2px solid var(--border,#e2e8f0);
                       font-size:13px; outline:none;
                       background:var(--bg,#f0f4f8); color:var(--text,#1e293b);">
            <option value="">❤️ Statut</option>
            <option value="vivant" {{ request('statut_vital')=='vivant' ? 'selected':'' }}>🟢 Vivant</option>
            <option value="decede" {{ request('statut_vital')=='decede' ? 'selected':'' }}>🔴 Décédé</option>
        </select>

        <button type="submit"
                style="background:linear-gradient(135deg,#0ea5e9,#0284c7); color:white;
                       border:none; padding:10px 18px; border-radius:10px;
                       font-weight:700; cursor:pointer; font-size:13px;">
            🔍 Rechercher
        </button>

        <a href="{{ route('oncologie.patients.index') }}"
           style="background:var(--text-muted,#64748b); color:white; padding:10px 14px;
                  border-radius:10px; text-decoration:none; font-weight:700;
                  font-size:13px; text-align:center;">
            🔄
        </a>
    </div>
</form>

<!-- TABLE -->
<div style="background:var(--card-bg,white); border-radius:16px;
            box-shadow:var(--shadow,0 4px 20px rgba(0,0,0,0.06)); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:950px;">
            <thead>
                <tr style="background:#0f172a; color:white;">
                    @foreach(['#','Patient','Dossier','Sexe','Âge','Wilaya','Daïra','Statut','Date décès','Actions'] as $h)
                        <th style="padding:13px 14px; text-align:center;
                                   font-size:11px; font-weight:700;
                                   text-transform:uppercase; letter-spacing:0.5px;">
                            {{ $h }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                <tr style="border-bottom:1px solid var(--border,#e2e8f0); transition:0.15s;"
                    onmouseover="this.style.background='var(--bg,#f0f4f8)'"
                    onmouseout="this.style.background=''">
                    <td style="padding:13px 14px; text-align:center;
                               font-size:12px; color:var(--text-muted,#64748b);">
                        {{ $patient->id }}
                    </td>
                    <td style="padding:13px 14px; text-align:center;">
                        <div style="font-weight:700; font-size:14px; color:var(--text,#1e293b);">
                            {{ $patient->nom }}
                        </div>
                        <div style="font-size:12px; color:var(--text-muted,#64748b);">
                            {{ $patient->prenom }}
                        </div>
                    </td>
                    <td style="padding:13px 14px; text-align:center;
                               font-size:13px; color:var(--text,#1e293b);">
                        {{ $patient->numero_dossier }}
                    </td>
                    <td style="padding:13px 14px; text-align:center;
                               font-size:13px; color:var(--text,#1e293b);">
                        {{ $patient->sexe }}
                    </td>
                    <td style="padding:13px 14px; text-align:center;">
                        <span style="background:#0ea5e9; color:white; padding:4px 10px;
                                     border-radius:999px; font-weight:700; font-size:12px;">
                            {{ $patient->age }} ans
                        </span>
                    </td>
                    <td style="padding:13px 14px; text-align:center;
                               font-size:13px; color:var(--text,#1e293b);">
                        {{ $patient->wilaya ?? '-' }}
                    </td>
                    <td style="padding:13px 14px; text-align:center;
                               font-size:12px; color:var(--text-muted,#64748b);">
                        {{ $patient->daira ?? '—' }}
                    </td>
                    <td style="padding:13px 14px; text-align:center;">
                        @if($patient->est_vivant)
                            <span style="background:#dcfce7; color:#166534; padding:5px 12px;
                                         border-radius:999px; font-size:11px; font-weight:700;
                                         border:1px solid #86efac;">
                                🟢 Vivant
                            </span>
                        @else
                            <span style="background:#fee2e2; color:#991b1b; padding:5px 12px;
                                         border-radius:999px; font-size:11px; font-weight:700;
                                         border:1px solid #fca5a5;">
                                🔴 Décédé
                            </span>
                        @endif
                    </td>
                    <td style="padding:13px 14px; text-align:center;
                               font-size:12px; color:var(--text-muted,#64748b); font-weight:600;">
                        @if(!$patient->est_vivant && $patient->date_deces)
                            {{ $patient->date_deces->format('d/m/Y') }}
                        @else —
                        @endif
                    </td>
                    <td style="padding:10px 14px; text-align:center;">
                        <div style="display:flex; gap:5px; justify-content:center;">
                            <a href="{{ route('oncologie.patients.show', $patient) }}"
                               style="background:#3b82f6; color:white; padding:6px 10px;
                                      border-radius:8px; text-decoration:none;
                                      font-size:12px; font-weight:600;"
                               title="Voir le dossier">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('oncologie.patients.edit', $patient) }}"
                               style="background:#f59e0b; color:white; padding:6px 10px;
                                      border-radius:8px; text-decoration:none;
                                      font-size:12px; font-weight:600;"
                               title="Modifier">
                                <i class="fas fa-pen"></i>
                            </a>
                            <a href="{{ route('oncologie.patients.export.pdf.single', $patient->id) }}"
                               style="background:#e63946; color:white; padding:6px 10px;
                                      border-radius:8px; text-decoration:none;
                                      font-size:12px; font-weight:600;"
                               title="PDF" target="_blank">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            <form action="{{ route('oncologie.patients.destroy', $patient) }}"
                                  method="POST"
                                  onsubmit="return confirm('Confirmer la suppression de ce patient ?')">
                                @csrf @method('DELETE')
                                <button style="background:#dc2626; color:white; border:none;
                                               padding:6px 10px; border-radius:8px;
                                               cursor:pointer; font-size:12px; font-weight:600;"
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10"
                        style="text-align:center; padding:40px;
                               color:var(--text-muted,#64748b); font-size:14px;">
                        <i class="fas fa-user-slash" style="font-size:32px; margin-bottom:10px; display:block; opacity:0.4;"></i>
                        Aucun patient trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div style="padding:16px 20px; border-top:1px solid var(--border,#e2e8f0);">
        {{ $patients->links() }}
    </div>
</div>

<!-- ======================== MODAL STATISTIQUES ======================== -->
<div id="statsModal"
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.6); z-index:9999; justify-content:center;
            align-items:center; backdrop-filter:blur(4px);"
     onclick="if(event.target===this) this.style.display='none'">

    <div style="background:var(--card-bg,white); border-radius:20px; padding:30px;
                width:90%; max-width:800px; max-height:90vh; overflow-y:auto;
                box-shadow:0 30px 80px rgba(0,0,0,0.3);
                animation:scaleIn 0.3s ease;">

        <div style="display:flex; justify-content:space-between; align-items:center;
                    margin-bottom:24px;">
            <div>
                <h2 style="font-weight:800; font-size:22px; color:var(--text,#1e293b); margin:0;">
                    📊 Statistiques Patients
                </h2>
                <p style="font-size:13px; color:var(--text-muted,#64748b); margin:4px 0 0;">
                    Vue analytique complète du service oncologie
                </p>
            </div>
            <button onclick="document.getElementById('statsModal').style.display='none'"
                    style="background:var(--bg,#f0f4f8); border:none; width:36px; height:36px;
                           border-radius:8px; cursor:pointer; font-size:18px; color:var(--text-muted,#64748b);">
                ×
            </button>
        </div>

        <!-- STATS GRID -->
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:24px;">
            @foreach([
                ['label'=>'Total patients',   'value'=>$totalPatients, 'color'=>'#0ea5e9', 'icon'=>'👥'],
                ['label'=>'Patients vivants', 'value'=>$vivants,       'color'=>'#22c55e', 'icon'=>'💚'],
                ['label'=>'Patients décédés', 'value'=>$decedes,       'color'=>'#ef4444', 'icon'=>'🔴'],
            ] as $stat)
            <div style="background:{{ $stat['color'] }}15; border:1px solid {{ $stat['color'] }}30;
                        border-radius:14px; padding:18px; text-align:center;">
                <div style="font-size:28px; margin-bottom:6px;">{{ $stat['icon'] }}</div>
                <div style="font-size:28px; font-weight:800; color:{{ $stat['color'] }};">
                    {{ $stat['value'] }}
                </div>
                <div style="font-size:12px; color:var(--text-muted,#64748b); font-weight:600;">
                    {{ $stat['label'] }}
                </div>
            </div>
            @endforeach
        </div>

        <!-- GRAPHIQUES -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
            <div style="background:var(--bg,#f9fafb); border-radius:14px; padding:16px;">
                <h4 style="font-weight:700; margin-bottom:12px; font-size:14px;
                           color:var(--text,#1e293b);">
                    📈 Statut vital
                </h4>
                <canvas id="statsDonut" height="180"></canvas>
            </div>
            <div style="background:var(--bg,#f9fafb); border-radius:14px; padding:16px;">
                <h4 style="font-weight:700; margin-bottom:12px; font-size:14px;
                           color:var(--text,#1e293b);">
                    📊 Répartition par sexe
                </h4>
                <canvas id="sexeChart" height="180"></canvas>
            </div>
        </div>

        @php
            $parWilaya = \App\Models\Oncologie\Patient::select('wilaya')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('wilaya')
                ->orderByDesc('total')
                ->limit(6)
                ->get();

            $parCancer = \App\Models\Oncologie\Patient::select('type_cancer')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('type_cancer')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            $masculin = \App\Models\Oncologie\Patient::where('sexe','Masculin')->count();
            $feminin  = \App\Models\Oncologie\Patient::where('sexe','Féminin')->count();
        @endphp

        <!-- TOP CANCERS -->
        <div style="background:var(--bg,#f9fafb); border-radius:14px; padding:16px; margin-bottom:16px;">
            <h4 style="font-weight:700; margin-bottom:14px; font-size:14px; color:var(--text,#1e293b);">
                🔬 Top 5 — Types de cancers
            </h4>
            @foreach($parCancer as $c)
            @php $pct = $totalPatients > 0 ? round($c->total / $totalPatients * 100) : 0; @endphp
            <div style="margin-bottom:10px;">
                <div style="display:flex; justify-content:space-between;
                            font-size:12px; font-weight:600; margin-bottom:4px;
                            color:var(--text,#1e293b);">
                    <span>{{ $c->type_cancer ?? 'Non spécifié' }}</span>
                    <span>{{ $c->total }} ({{ $pct }}%)</span>
                </div>
                <div style="height:6px; background:var(--border,#e2e8f0); border-radius:3px; overflow:hidden;">
                    <div style="height:100%; width:{{ $pct }}%;
                                background:linear-gradient(90deg,#e63946,#e76f51);
                                border-radius:3px; transition:width 1s ease;"></div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- TOP WILAYAS -->
        <div style="background:var(--bg,#f9fafb); border-radius:14px; padding:16px;">
            <h4 style="font-weight:700; margin-bottom:14px; font-size:14px; color:var(--text,#1e293b);">
                🗺️ Top wilayas d'origine
            </h4>
            <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:8px;">
                @foreach($parWilaya as $w)
                <div style="display:flex; justify-content:space-between; align-items:center;
                            background:var(--card-bg,white); padding:10px 14px;
                            border-radius:10px; border:1px solid var(--border,#e2e8f0);">
                    <span style="font-size:13px; font-weight:600; color:var(--text,#1e293b);">
                        📍 {{ $w->wilaya ?? 'N/A' }}
                    </span>
                    <span style="background:#2a9d8f; color:white; padding:3px 10px;
                                 border-radius:999px; font-size:12px; font-weight:700;">
                        {{ $w->total }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
// DONUT STATUT
new Chart(document.getElementById('statsDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Vivants', 'Décédés'],
        datasets: [{
            data: [{{ $vivants }}, {{ $decedes }}],
            backgroundColor: ['#22c55e', '#ef4444'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        cutout: '60%',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
    }
});

// BAR SEXE
new Chart(document.getElementById('sexeChart'), {
    type: 'bar',
    data: {
        labels: ['Masculin', 'Féminin'],
        datasets: [{
            label: 'Patients',
            data: [{{ $masculin ?? 0 }}, {{ $feminin ?? 0 }}],
            backgroundColor: ['#3b82f6', '#ec4899'],
            borderRadius: 8,
            borderSkipped: false
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

// Animation barres progress (cancers)
setTimeout(() => {
    document.querySelectorAll('#statsModal [style*="transition:width"]').forEach(el => {
        el.style.width = el.style.width;
    });
}, 300);
</script>

<style>
@keyframes scaleIn {
    from { transform: scale(0.9); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}
</style>
@endpush

@endsection