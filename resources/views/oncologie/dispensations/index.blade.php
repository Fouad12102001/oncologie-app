ade · PHP
@extends('layouts.app')
@section('title', 'Dispensations')
 
@push('styles')
<style>
:root {
    --bg:#0f172a; --surface:#1e293b; --border:#334155;
    --accent:#2a9d8f; --text:#e2e8f0; --muted:#94a3b8; --radius:.75rem;
}
body { background:var(--bg); color:var(--text); }
 
.disp-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.4); }
.stat-tile { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:1rem 1.2rem; display:flex; align-items:center; gap:.85rem; }
.stat-icon { width:2.75rem; height:2.75rem; border-radius:.55rem; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
.stat-val  { font-size:1.5rem; font-weight:900; line-height:1; }
.stat-lbl  { font-size:.7rem; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:.05em; margin-top:.1rem; }
 
.disp-table { width:100%; border-collapse:collapse; min-width:900px; }
.disp-table thead tr { background:rgba(255,255,255,.03); }
.disp-table th { padding:.7rem 1rem; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--muted); text-align:left; border-bottom:1px solid var(--border); }
.disp-table td { padding:.75rem 1rem; border-bottom:1px solid rgba(255,255,255,.04); font-size:.875rem; }
.disp-table tbody tr:hover { background:rgba(255,255,255,.025); }
 
.badge { display:inline-flex; align-items:center; gap:.25rem; padding:.2rem .65rem; border-radius:99px; font-size:.7rem; font-weight:700; }
.badge-cyan   { background:rgba(6,182,212,.15);  color:#67e8f9; border:1px solid rgba(6,182,212,.3); }
.badge-green  { background:rgba(34,197,94,.15);  color:#4ade80; border:1px solid rgba(34,197,94,.3); }
.badge-warn   { background:rgba(245,158,11,.15); color:#fcd34d; border:1px solid rgba(245,158,11,.3); }
.badge-red    { background:rgba(239,68,68,.15);  color:#f87171; border:1px solid rgba(239,68,68,.3); }
 
.filter-input { background:rgba(255,255,255,.05); border:1px solid var(--border); border-radius:.5rem; color:var(--text); padding:.5rem .85rem; font-size:.84rem; transition:border-color .2s; width:100%; }
.filter-input:focus { outline:none; border-color:var(--accent); }
.filter-input option { background:var(--surface); }
 
.btn-p { background:linear-gradient(135deg,var(--accent),#21867a); color:white; border:none; border-radius:.6rem; padding:.55rem 1.2rem; font-weight:700; font-size:.84rem; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:.4rem; transition:all .2s; }
.btn-p:hover { transform:translateY(-1px); }
.btn-s { background:var(--surface); border:1px solid var(--border); color:var(--muted); border-radius:.6rem; padding:.55rem 1rem; font-weight:600; font-size:.84rem; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:.4rem; transition:all .2s; }
.btn-s:hover { border-color:var(--accent); color:var(--accent); }
 
.act-btn { display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:.4rem; text-decoration:none; font-size:.75rem; transition:all .15s; }
.act-btn:hover { transform:scale(1.1); }
 
.fifo-tag { display:inline-flex; align-items:center; gap:.25rem; padding:.15rem .5rem; background:rgba(6,182,212,.1); border:1px solid rgba(6,182,212,.25); border-radius:.35rem; font-size:.65rem; font-weight:700; color:#67e8f9; font-family:monospace; }
</style>
@endpush
 
@section('content')
<div class="container-fluid py-4">
 
    {{-- HEADER --}}
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; margin-bottom:1.25rem;">
        <div>
            <h1 style="font-size:1.45rem; font-weight:800; color:var(--accent); margin:0;">💊 Dispensations</h1>
            <p style="color:var(--muted); font-size:.85rem; margin:.2rem 0 0;">Historique FIFO complet · Gestion des sorties de stock</p>
        </div>
        <div style="display:flex; gap:.6rem; flex-wrap:wrap;">
            @canOnco('dispensations.create')
            <a href="{{ route('oncologie.dispensations.create') }}" class="btn-p">+ Nouvelle dispensation</a>
            @endcanOnco
            @canOnco('dispensations.export')
            <a href="{{ route('oncologie.dispensations.export', ['format'=>'pdf']) }}" class="btn-s" target="_blank">📄 PDF</a>
            <a href="{{ route('oncologie.dispensations.export', ['format'=>'csv']) }}" class="btn-s">📊 CSV</a>
            @endcanOnco
        </div>
    </div>
 
    {{-- STAT TILES --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin-bottom:1.25rem;">
        <div class="stat-tile">
            <div class="stat-icon" style="background:rgba(6,182,212,.12);">💉</div>
            <div>
                <div class="stat-val" style="color:#67e8f9;">{{ $totalDispensations }}</div>
                <div class="stat-lbl">Total dispensations</div>
            </div>
        </div>
        <div class="stat-tile">
            <div class="stat-icon" style="background:rgba(34,197,94,.12);">📅</div>
            <div>
                <div class="stat-val" style="color:#4ade80;">{{ $dispensationsAujourd }}</div>
                <div class="stat-lbl">Aujourd'hui</div>
            </div>
        </div>
        <div class="stat-tile">
            <div class="stat-icon" style="background:rgba(129,140,248,.12);">📆</div>
            <div>
                <div class="stat-val" style="color:#a5b4fc;">{{ $dispensationsMois }}</div>
                <div class="stat-lbl">Ce mois</div>
            </div>
        </div>
        <div class="stat-tile">
            <div class="stat-icon" style="background:rgba(245,158,11,.12);">📦</div>
            <div>
                <div class="stat-val" style="color:#fcd34d;">{{ number_format($quantiteTotaleMois) }}</div>
                <div class="stat-lbl">Unités / mois</div>
            </div>
        </div>
    </div>
 
    {{-- FILTRES --}}
    <form method="GET" action="{{ route('oncologie.dispensations.index') }}"
          class="disp-card" style="padding:1rem 1.25rem; margin-bottom:1.25rem;">
        <div style="display:grid; grid-template-columns:2fr 1.5fr 1fr 1fr auto auto; gap:.75rem; align-items:end;">
            <div>
                <label style="font-size:.68rem; color:var(--muted); font-weight:600; text-transform:uppercase; display:block; margin-bottom:.3rem;">Patient</label>
                <input type="text" name="patient" class="filter-input"
                    placeholder="Nom, prénom, n° dossier…"
                    value="{{ request('patient') }}">
            </div>
            <div>
                <label style="font-size:.68rem; color:var(--muted); font-weight:600; text-transform:uppercase; display:block; margin-bottom:.3rem;">Médicament</label>
                <select name="medicament_id" class="filter-input">
                    <option value="">Tous</option>
                    @foreach($medicamentsFiltres as $med)
                        <option value="{{ $med->id }}" {{ request('medicament_id') == $med->id ? 'selected' : '' }}>
                            {{ $med->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:.68rem; color:var(--muted); font-weight:600; text-transform:uppercase; display:block; margin-bottom:.3rem;">Du</label>
                <input type="date" name="date_debut" class="filter-input" value="{{ request('date_debut') }}">
            </div>
            <div>
                <label style="font-size:.68rem; color:var(--muted); font-weight:600; text-transform:uppercase; display:block; margin-bottom:.3rem;">Au</label>
                <input type="date" name="date_fin" class="filter-input" value="{{ request('date_fin') }}">
            </div>
            <div>
                <label style="font-size:.68rem; color:transparent; display:block; margin-bottom:.3rem;">.</label>
                <button type="submit" class="btn-p" style="width:100%;">🔍</button>
            </div>
            <div>
                <label style="font-size:.68rem; color:transparent; display:block; margin-bottom:.3rem;">.</label>
                <a href="{{ route('oncologie.dispensations.index') }}" class="btn-s" style="width:100%; justify-content:center;">↺</a>
            </div>
        </div>
    </form>
 
    {{-- ALERTS --}}
    @if(session('success'))
    <div style="background:rgba(34,197,94,.12); border:1px solid rgba(34,197,94,.3); color:#4ade80; border-radius:.6rem; padding:.75rem 1rem; margin-bottom:1rem; font-weight:600; font-size:.875rem;">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.3); color:#f87171; border-radius:.6rem; padding:.75rem 1rem; margin-bottom:1rem; font-weight:600; font-size:.875rem;">
        {{ session('error') }}
    </div>
    @endif
 
    {{-- TABLE --}}
    <div class="disp-card">
        <div style="overflow-x:auto;">
            <table class="disp-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Patient</th>
                        <th>Médicament</th>
                        <th>Lot <span class="fifo-tag" style="margin-left:.3rem;">FIFO</span></th>
                        <th>Expiration</th>
                        <th>Quantité</th>
                        <th>Date &amp; Heure</th>
                        <th>Pharmacien</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dispensations as $disp)
                    <tr>
                        <td style="color:var(--muted); font-size:.75rem;">{{ $disp->id }}</td>
                        <td>
                            <div style="font-weight:700;">
                                {{ optional(optional($disp->prescription)->patient)->nom ?? 'N/A' }}
                                {{ optional(optional($disp->prescription)->patient)->prenom ?? '' }}
                            </div>
                            @if(optional(optional($disp->prescription)->patient)->numero_dossier)
                            <div style="font-size:.72rem; color:var(--accent); font-family:monospace;">
                                {{ $disp->prescription->patient->numero_dossier }}
                            </div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600;">{{ optional($disp->medicament)->nom ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="badge badge-cyan">{{ optional($disp->lot)->numero ?? 'N/A' }}</span>
                        </td>
                        <td style="font-size:.8rem;">
                            @if($disp->lot && $disp->lot->date_expiration)
                                @php
                                    $exp = \Carbon\Carbon::parse($disp->lot->date_expiration);
                                    $soon = $exp->diffInDays(now(), false) > -90;
                                @endphp
                                <span class="{{ $soon ? 'badge badge-warn' : '' }}" style="font-size:.78rem;">
                                    {{ $exp->format('m/Y') }}
                                </span>
                            @else
                                <span style="color:var(--muted);">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-green">{{ $disp->quantite }}</span>
                        </td>
                        <td style="font-size:.8rem; color:var(--muted);">
                            {{ $disp->date_formattee ?? '—' }}
                        </td>
                        <td style="font-size:.8rem; color:var(--muted);">
                            {{ optional($disp->user)->name ?? '—' }}
                        </td>
                        <td>
                            @canOnco('dispensations.view')
                            <a href="{{ route('oncologie.dispensations.show', $disp) }}"
                               class="act-btn" style="background:#3b82f6; color:white;" title="Voir">👁</a>
                            @endcanOnco
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center; padding:3rem; color:var(--muted);">
                            <div style="font-size:2rem; opacity:.2; margin-bottom:.5rem;">💊</div>
                            Aucune dispensation enregistrée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:1rem 1.25rem; border-top:1px solid var(--border);">
            {{ $dispensations->links() }}
        </div>
    </div>
 
</div>
@endsection