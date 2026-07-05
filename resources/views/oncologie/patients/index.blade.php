@extends('layouts.app')
@section('title', 'Patients Oncologie')
 
@push('styles')
<style>
/* ══════════ TOKENS ══════════ */
:root {
    --bg:       #0f172a;
    --surface:  #1e293b;
    --border:   #334155;
    --accent:   #2a9d8f;
    --blue:     #3b82f6;
    --red:      #ef4444;
    --yellow:   #f59e0b;
    --green:    #22c55e;
    --pink:     #ec4899;
    --text:     #e2e8f0;
    --muted:    #94a3b8;
    --radius:   .75rem;
    --shadow:   0 4px 24px rgba(0,0,0,.45);
}
 
body { background: var(--bg); color: var(--text); }
 
/* ── CARDS ── */
.pt-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}
 
/* ── STAT TILES ── */
.stat-tile {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform .2s;
}
.stat-tile:hover { transform: translateY(-2px); }
.stat-icon {
    width: 3rem; height: 3rem;
    border-radius: .6rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.35rem; flex-shrink: 0;
}
.stat-val  { font-size: 1.65rem; font-weight: 900; line-height: 1; }
.stat-lbl  { font-size: .72rem; color: var(--muted); font-weight: 600;
             text-transform: uppercase; letter-spacing: .05em; margin-top: .15rem; }
 
/* ── TABLE ── */
.pt-table { width: 100%; border-collapse: collapse; min-width: 1000px; }
.pt-table thead tr { background: rgba(255,255,255,.03); }
.pt-table th {
    padding: .75rem 1rem;
    font-size: .7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .07em;
    color: var(--muted); text-align: left;
    border-bottom: 1px solid var(--border);
}
.pt-table td { padding: .75rem 1rem; border-bottom: 1px solid rgba(255,255,255,.04); font-size: .875rem; }
.pt-table tbody tr { transition: background .15s; }
.pt-table tbody tr:hover { background: rgba(255,255,255,.03); }
 
/* ── BADGE ── */
.badge {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .25rem .65rem; border-radius: 99px;
    font-size: .72rem; font-weight: 700;
}
.badge-green  { background: rgba(34,197,94,.15);  color: #4ade80; border: 1px solid rgba(34,197,94,.3); }
.badge-red    { background: rgba(239,68,68,.15);   color: #f87171; border: 1px solid rgba(239,68,68,.3); }
.badge-blue   { background: rgba(59,130,246,.15);  color: #93c5fd; border: 1px solid rgba(59,130,246,.3); }
.badge-pink   { background: rgba(236,72,153,.15);  color: #f9a8d4; border: 1px solid rgba(236,72,153,.3); }
.badge-yellow { background: rgba(245,158,11,.15);  color: #fcd34d; border: 1px solid rgba(245,158,11,.3); }
.badge-accent { background: rgba(42,157,143,.15);  color: #5eead4; border: 1px solid rgba(42,157,143,.3); }
 
/* ── ACTION BTNs ── */
.act-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: .4rem;
    text-decoration: none; border: none; cursor: pointer;
    font-size: .8rem; transition: opacity .15s, transform .15s;
}
.act-btn:hover { opacity: .85; transform: scale(1.08); }
 
/* ── FILTERS ── */
.filter-input {
    background: rgba(255,255,255,.05);
    border: 1px solid var(--border);
    border-radius: .5rem;
    color: var(--text);
    padding: .5rem .85rem;
    font-size: .84rem;
    transition: border-color .2s;
    width: 100%;
}
.filter-input:focus { outline: none; border-color: var(--accent); }
.filter-input option { background: var(--surface); }
 
/* ── BUTTONS ── */
.btn-primary {
    background: linear-gradient(135deg, var(--accent), #21867a);
    color: white; border: none; border-radius: .6rem;
    padding: .55rem 1.2rem; font-weight: 700; font-size: .85rem;
    cursor: pointer; text-decoration: none; display: inline-flex;
    align-items: center; gap: .4rem; transition: all .2s;
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(42,157,143,.35); }
.btn-secondary {
    background: var(--surface); border: 1px solid var(--border);
    color: var(--muted); border-radius: .6rem;
    padding: .55rem 1rem; font-weight: 600; font-size: .85rem;
    cursor: pointer; text-decoration: none; display: inline-flex;
    align-items: center; gap: .4rem; transition: all .2s;
}
.btn-secondary:hover { border-color: var(--accent); color: var(--accent); }
 
/* ── MODAL ── */
.modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.7); z-index: 9999;
    align-items: center; justify-content: center;
    backdrop-filter: blur(6px);
}
.modal-overlay.open { display: flex; }
.modal-box {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1rem; padding: 1.75rem;
    width: 90%; max-width: 860px; max-height: 90vh;
    overflow-y: auto; box-shadow: 0 30px 80px rgba(0,0,0,.5);
    animation: modalIn .25s ease;
}
@keyframes modalIn { from { transform: scale(.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
 
/* ── PROGRESS ── */
.progress-bar {
    height: 6px; background: rgba(255,255,255,.07);
    border-radius: 99px; overflow: hidden; margin-top: .35rem;
}
.progress-fill {
    height: 100%; border-radius: 99px;
    background: linear-gradient(90deg, #e63946, #e76f51);
}
</style>
@endpush
 
@section('content')
<div class="container-fluid py-4">
 
    {{-- ══ HEADER ══ --}}
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; margin-bottom:1.25rem;">
        <div>
            <h1 style="font-size:1.45rem; font-weight:800; color:var(--accent); margin:0;">
                🧑‍⚕️ Patients Oncologie
            </h1>
            <p style="color:var(--muted); font-size:.85rem; margin:.2rem 0 0;">
                Service — CLCC Draâ Ben Khedda ·
                <b style="color:var(--text)">{{ $totalPatients }}</b> dossier(s)
            </p>
        </div>
        <div style="display:flex; gap:.6rem; flex-wrap:wrap;">
            <button onclick="document.getElementById('statsModal').classList.add('open')"
                    class="btn-secondary">
                📊 Statistiques
            </button>
            @canOnco('patients.create')
            <a href="{{ route('oncologie.patients.create') }}" class="btn-primary">
                + Nouveau patient
            </a>
            @endcanOnco
            @canOnco('patients.export')
            <a href="{{ route('oncologie.patients.export.pdf.liste') }}" class="btn-secondary" target="_blank">
                📄 PDF liste
            </a>
            @endcanOnco
        </div>
    </div>
 
    {{-- ══ STAT TILES ══ --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin-bottom:1.25rem;">
        <div class="stat-tile">
            <div class="stat-icon" style="background:rgba(59,130,246,.15);">👥</div>
            <div>
                <div class="stat-val" style="color:#93c5fd;">{{ $totalPatients }}</div>
                <div class="stat-lbl">Total patients</div>
            </div>
        </div>
        <div class="stat-tile">
            <div class="stat-icon" style="background:rgba(34,197,94,.15);">💚</div>
            <div>
                <div class="stat-val" style="color:#4ade80;">{{ $vivants }}</div>
                <div class="stat-lbl">Patients vivants</div>
            </div>
        </div>
        <div class="stat-tile">
            <div class="stat-icon" style="background:rgba(239,68,68,.15);">🔴</div>
            <div>
                <div class="stat-val" style="color:#f87171;">{{ $decedes }}</div>
                <div class="stat-lbl">Décédés</div>
            </div>
        </div>
        <div class="stat-tile">
            <div class="stat-icon" style="background:rgba(59,130,246,.15);">♂</div>
            <div>
                <div class="stat-val" style="color:#93c5fd;">{{ $masculin }}</div>
                <div class="stat-lbl">Masculin</div>
            </div>
        </div>
        <div class="stat-tile">
            <div class="stat-icon" style="background:rgba(236,72,153,.15);">♀</div>
            <div>
                <div class="stat-val" style="color:#f9a8d4;">{{ $feminin }}</div>
                <div class="stat-lbl">Féminin</div>
            </div>
        </div>
    </div>
 
    {{-- ══ FILTRES ══ --}}
    <form method="GET" action="{{ route('oncologie.patients.index') }}"
          class="pt-card" style="padding:1rem 1.25rem; margin-bottom:1.25rem;">
        <div style="display:grid; grid-template-columns:2fr 1.5fr 1fr 1fr 1fr auto auto; gap:.75rem; align-items:end;">
            <div>
                <label style="font-size:.7rem; color:var(--muted); font-weight:600; text-transform:uppercase; display:block; margin-bottom:.3rem;">Nom / Dossier</label>
                <input type="text" name="search" class="filter-input"
                    placeholder="Nom, prénom, n° dossier…"
                    value="{{ request('search') }}">
            </div>
            <div>
                <label style="font-size:.7rem; color:var(--muted); font-weight:600; text-transform:uppercase; display:block; margin-bottom:.3rem;">Type de cancer</label>
                <select name="type_cancer" class="filter-input">
                    <option value="">Tous</option>
                    @foreach($typesCancerFilter as $tc)
                        <option value="{{ $tc }}" {{ request('type_cancer') === $tc ? 'selected' : '' }}>{{ $tc }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:.7rem; color:var(--muted); font-weight:600; text-transform:uppercase; display:block; margin-bottom:.3rem;">Wilaya</label>
                <select name="wilaya" class="filter-input">
                    <option value="">Toutes</option>
                    @foreach($wilayasFilter as $w)
                        <option value="{{ $w }}" {{ request('wilaya') === $w ? 'selected' : '' }}>{{ $w }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:.7rem; color:var(--muted); font-weight:600; text-transform:uppercase; display:block; margin-bottom:.3rem;">Sexe</label>
                <select name="sexe" class="filter-input">
                    <option value="">Tous</option>
                    <option value="Masculin" {{ request('sexe') === 'Masculin' ? 'selected' : '' }}>Masculin</option>
                    <option value="Féminin"  {{ request('sexe') === 'Féminin'  ? 'selected' : '' }}>Féminin</option>
                </select>
            </div>
            <div>
                <label style="font-size:.7rem; color:var(--muted); font-weight:600; text-transform:uppercase; display:block; margin-bottom:.3rem;">Statut</label>
                <select name="statut_vital" class="filter-input">
                    <option value="">Tous</option>
                    <option value="vivant" {{ request('statut_vital') === 'vivant' ? 'selected' : '' }}>🟢 Vivant</option>
                    <option value="decede" {{ request('statut_vital') === 'decede' ? 'selected' : '' }}>🔴 Décédé</option>
                </select>
            </div>
            <div>
                <label style="font-size:.7rem; color:transparent; display:block; margin-bottom:.3rem;">.</label>
                <button type="submit" class="btn-primary" style="width:100%;">🔍</button>
            </div>
            <div>
                <label style="font-size:.7rem; color:transparent; display:block; margin-bottom:.3rem;">.</label>
                <a href="{{ route('oncologie.patients.index') }}" class="btn-secondary" style="width:100%; justify-content:center;">↺</a>
            </div>
        </div>
    </form>
 
    {{-- ══ TABLE ══ --}}
    @if(session('success'))
    <div style="background:rgba(34,197,94,.12); border:1px solid rgba(34,197,94,.3); color:#4ade80;
                border-radius:.6rem; padding:.75rem 1rem; margin-bottom:1rem; font-size:.875rem; font-weight:600;">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.3); color:#f87171;
                border-radius:.6rem; padding:.75rem 1rem; margin-bottom:1rem; font-size:.875rem; font-weight:600;">
        {{ session('error') }}
    </div>
    @endif
 
    <div class="pt-card">
        <div style="overflow-x:auto;">
            <table class="pt-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Patient</th>
                        <th>Dossier</th>
                        <th>Sexe</th>
                        <th>Âge</th>
                        <th>Cancer</th>
                        <th>Wilaya</th>
                        <th>SC (m²)</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                    <tr>
                        <td style="color:var(--muted); font-size:.75rem;">{{ $patient->id }}</td>
                        <td>
                            <div style="font-weight:700;">{{ $patient->nom }}</div>
                            <div style="color:var(--muted); font-size:.78rem;">{{ $patient->prenom }}</div>
                        </td>
                        <td style="font-family:monospace; color:var(--accent);">{{ $patient->numero_dossier }}</td>
                        <td>
                            <span class="badge {{ $patient->sexe === 'Masculin' ? 'badge-blue' : 'badge-pink' }}">
                                {{ $patient->sexe === 'Masculin' ? '♂' : '♀' }} {{ $patient->sexe }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-accent">{{ $patient->age }} ans</span>
                        </td>
                        <td style="font-size:.8rem; max-width:160px;">{{ $patient->type_cancer }}</td>
                        <td style="font-size:.82rem; color:var(--muted);">{{ $patient->wilaya ?? '—' }}</td>
                        <td>
                            @if($patient->surface_corporelle_calculee)
                                <span style="font-weight:700; color:#5eead4;">{{ $patient->surface_corporelle_calculee }} m²</span>
                            @else
                                <span style="color:var(--muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($patient->est_vivant)
                                <span class="badge badge-green">🟢 Vivant</span>
                            @else
                                <span class="badge badge-red">🔴 Décédé</span>
                                @if($patient->date_deces)
                                    <div style="font-size:.68rem; color:var(--muted); margin-top:.2rem;">{{ $patient->date_deces->format('d/m/Y') }}</div>
                                @endif
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:.3rem;">
                                @canOnco('patients.view')
                                <a href="{{ route('oncologie.patients.show', $patient) }}"
                                   class="act-btn" style="background:#3b82f6;" title="Voir">👁</a>
                                @endcanOnco
                                @canOnco('patients.update')
                                <a href="{{ route('oncologie.patients.edit', $patient) }}"
                                   class="act-btn" style="background:#f59e0b;" title="Modifier">✏️</a>
                                @endcanOnco
                                @canOnco('patients.export')
                                <a href="{{ route('oncologie.patients.export.pdf.single', $patient->id) }}"
                                   class="act-btn" style="background:#e63946;" title="PDF" target="_blank">📄</a>
                                @endcanOnco
                                @canOnco('patients.delete')
                                <form action="{{ route('oncologie.patients.destroy', $patient) }}" method="POST"
                                      onsubmit="return confirm('Supprimer ce patient ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="act-btn" style="background:#7f1d1d;" title="Supprimer">🗑</button>
                                </form>
                                @endcanOnco
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="text-align:center; padding:3rem; color:var(--muted);">
                            <div style="font-size:2.5rem; opacity:.2; margin-bottom:.5rem;">👤</div>
                            Aucun patient trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:1rem 1.25rem; border-top:1px solid var(--border);">
            {{ $patients->links() }}
        </div>
    </div>
 
</div>
 
{{-- ══ MODAL STATISTIQUES ══ --}}
<div id="statsModal" class="modal-overlay"
     onclick="if(event.target===this) this.classList.remove('open')">
    <div class="modal-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <div>
                <h2 style="font-size:1.2rem; font-weight:800; color:var(--accent); margin:0;">📊 Statistiques Patients</h2>
                <p style="color:var(--muted); font-size:.82rem; margin:.2rem 0 0;">Vue analytique — Service Oncologie</p>
            </div>
            <button onclick="document.getElementById('statsModal').classList.remove('open')"
                    style="background:rgba(255,255,255,.08); border:1px solid var(--border);
                           color:var(--muted); border-radius:.5rem; width:2rem; height:2rem;
                           cursor:pointer; font-size:1.1rem;">×</button>
        </div>
 
        {{-- KPIs --}}
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; margin-bottom:1.25rem;">
            @foreach([
                ['label'=>'Total',   'value'=>$totalPatients, 'color'=>'#93c5fd',  'bg'=>'rgba(59,130,246,.12)',  'icon'=>'👥'],
                ['label'=>'Vivants', 'value'=>$vivants,       'color'=>'#4ade80',  'bg'=>'rgba(34,197,94,.12)',   'icon'=>'💚'],
                ['label'=>'Décédés', 'value'=>$decedes,       'color'=>'#f87171',  'bg'=>'rgba(239,68,68,.12)',   'icon'=>'🔴'],
            ] as $s)
            <div style="background:{{ $s['bg'] }}; border-radius:.75rem; padding:1rem; text-align:center;">
                <div style="font-size:1.8rem; margin-bottom:.3rem;">{{ $s['icon'] }}</div>
                <div style="font-size:1.8rem; font-weight:900; color:{{ $s['color'] }};">{{ $s['value'] }}</div>
                <div style="font-size:.7rem; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:.05em;">{{ $s['label'] }}</div>
            </div>
            @endforeach
        </div>
 
        {{-- Graphiques --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
            <div style="background:rgba(255,255,255,.03); border:1px solid var(--border); border-radius:.75rem; padding:1rem;">
                <h4 style="font-size:.8rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.05em; margin:0 0 .75rem;">Statut vital</h4>
                <canvas id="chartStatut" height="180"></canvas>
            </div>
            <div style="background:rgba(255,255,255,.03); border:1px solid var(--border); border-radius:.75rem; padding:1rem;">
                <h4 style="font-size:.8rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.05em; margin:0 0 .75rem;">Répartition sexe</h4>
                <canvas id="chartSexe" height="180"></canvas>
            </div>
        </div>
 
        {{-- Top cancers --}}
        <div style="background:rgba(255,255,255,.03); border:1px solid var(--border); border-radius:.75rem; padding:1rem; margin-bottom:1rem;">
            <h4 style="font-size:.8rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.05em; margin:0 0 .85rem;">🔬 Top cancers</h4>
            @foreach($parCancer as $c)
            @php $pct = $totalPatients > 0 ? round($c->total / $totalPatients * 100) : 0; @endphp
            <div style="margin-bottom:.65rem;">
                <div style="display:flex; justify-content:space-between; font-size:.8rem; color:var(--text); margin-bottom:.3rem;">
                    <span>{{ $c->type_cancer ?? 'Non spécifié' }}</span>
                    <span style="color:var(--muted);">{{ $c->total }} · {{ $pct }}%</span>
                </div>
                <div class="progress-bar"><div class="progress-fill" style="width:{{ $pct }}%"></div></div>
            </div>
            @endforeach
        </div>
 
        {{-- Top wilayas --}}
        <div style="background:rgba(255,255,255,.03); border:1px solid var(--border); border-radius:.75rem; padding:1rem;">
            <h4 style="font-size:.8rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.05em; margin:0 0 .75rem;">🗺️ Top wilayas</h4>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:.5rem;">
                @foreach($parWilaya as $w)
                <div style="display:flex; justify-content:space-between; align-items:center;
                            background:rgba(255,255,255,.04); border:1px solid var(--border);
                            border-radius:.5rem; padding:.5rem .85rem;">
                    <span style="font-size:.82rem;">📍 {{ $w->wilaya ?? 'N/A' }}</span>
                    <span class="badge badge-accent">{{ $w->total }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
 
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.color = '#94a3b8';
Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
 
new Chart(document.getElementById('chartStatut'), {
    type: 'doughnut',
    data: {
        labels: ['Vivants', 'Décédés'],
        datasets: [{ data: [{{ $vivants }}, {{ $decedes }}],
            backgroundColor: ['#22c55e', '#ef4444'], borderWidth: 0 }]
    },
    options: { cutout: '65%', plugins: { legend: { position: 'bottom' } } }
});
 
new Chart(document.getElementById('chartSexe'), {
    type: 'bar',
    data: {
        labels: ['Masculin', 'Féminin'],
        datasets: [{ data: [{{ $masculin }}, {{ $feminin }}],
            backgroundColor: ['#3b82f6', '#ec4899'],
            borderRadius: 8, borderSkipped: false }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
    }
});
</script>
@endpush
 
@endsection