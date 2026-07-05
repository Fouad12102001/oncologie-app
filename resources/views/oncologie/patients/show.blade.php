@extends('layouts.app')
@section('title', 'Dossier — {{ $patient->nom }} {{ $patient->prenom }}')
 
@push('styles')
<style>
:root {
    --bg:#0f172a; --surface:#1e293b; --border:#334155;
    --accent:#2a9d8f; --text:#e2e8f0; --muted:#94a3b8; --radius:.75rem;
}
body { background:var(--bg); color:var(--text); }
 
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
}
.info-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
}
.info-card__head {
    padding: .65rem 1rem;
    font-size: .72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .07em;
    border-bottom: 1px solid var(--border);
    background: rgba(255,255,255,.03);
}
.info-card__body { padding: 1rem; }
 
.kv-table { width: 100%; border-collapse: collapse; font-size: .84rem; }
.kv-table th {
    color: var(--muted); font-weight: 600; text-align: left;
    padding: .4rem 0; width: 45%; vertical-align: top;
    font-size: .78rem;
}
.kv-table td { color: var(--text); padding: .4rem 0; font-weight: 600; }
.kv-table tr + tr th,
.kv-table tr + tr td { border-top: 1px solid rgba(255,255,255,.04); }
 
.badge { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .65rem;
         border-radius:99px; font-size:.72rem; font-weight:700; }
.badge-green { background:rgba(34,197,94,.15); color:#4ade80; border:1px solid rgba(34,197,94,.3); }
.badge-red   { background:rgba(239,68,68,.15);  color:#f87171; border:1px solid rgba(239,68,68,.3); }
.badge-accent{ background:rgba(42,157,143,.15); color:#5eead4; border:1px solid rgba(42,157,143,.3); }
.badge-warn  { background:rgba(245,158,11,.15); color:#fcd34d; border:1px solid rgba(245,158,11,.3); }
 
.btn-action {
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.55rem 1.1rem; border-radius:.55rem; font-weight:700;
    font-size:.84rem; text-decoration:none; border:none; cursor:pointer;
    transition:all .2s;
}
.btn-action:hover { transform:translateY(-1px); }
 
/* SC meter */
.sc-ring {
    width: 90px; height: 90px;
    border-radius: 50%;
    background: conic-gradient(var(--accent) 0%, transparent 0%);
    display: flex; align-items: center; justify-content: center;
    position: relative;
}
.sc-ring::before {
    content: ''; position: absolute; inset: 10px;
    background: var(--surface); border-radius: 50%;
}
.sc-val { position:relative; z-index:1; font-size:.95rem; font-weight:900; color:var(--accent); }
 
.alert-allergy {
    background: rgba(239,68,68,.1);
    border: 1px solid rgba(239,68,68,.35);
    border-left: 4px solid #ef4444;
    border-radius: .5rem;
    padding: .7rem 1rem;
    color: #fca5a5;
    font-size: .84rem;
}
</style>
@endpush
 
@section('content')
<div class="container-fluid py-4" style="max-width:1100px;">
 
    {{-- HEADER --}}
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius);
                padding:1.25rem 1.5rem; margin-bottom:1.25rem;
                display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
        <div style="display:flex; align-items:center; gap:1rem;">
            <div style="width:3.5rem; height:3.5rem; border-radius:50%;
                        background:linear-gradient(135deg, var(--accent), #0891b2);
                        display:flex; align-items:center; justify-content:center;
                        font-size:1.4rem; flex-shrink:0;">
                {{ $patient->sexe === 'Masculin' ? '👨' : '👩' }}
            </div>
            <div>
                <h1 style="font-size:1.3rem; font-weight:800; margin:0;">
                    {{ $patient->nom }} {{ $patient->prenom }}
                </h1>
                <div style="display:flex; gap:.5rem; align-items:center; margin-top:.3rem; flex-wrap:wrap;">
                    <span style="font-size:.78rem; font-family:monospace; color:var(--accent);">
                        {{ $patient->numero_dossier }}
                    </span>
                    @if($patient->est_vivant)
                        <span class="badge badge-green">🟢 Vivant</span>
                    @else
                        <span class="badge badge-red">🔴 Décédé</span>
                    @endif
                    @if($patient->stade_cancer)
                        <span class="badge badge-warn">Stade {{ $patient->stade_cancer }}</span>
                    @endif
                    <span style="font-size:.78rem; color:var(--muted);">{{ $patient->type_cancer }}</span>
                </div>
            </div>
        </div>
        <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
            @canOnco('patients.update')
            <a href="{{ route('oncologie.patients.edit', $patient) }}"
               class="btn-action" style="background:#f59e0b; color:#111;">✏️ Modifier</a>
            @endcanOnco
            @canOnco('patients.export')
            <a href="{{ route('oncologie.patients.export.pdf.single', $patient->id) }}"
               class="btn-action" style="background:#ef4444; color:#fff;" target="_blank">📄 PDF</a>
            <a href="{{ route('oncologie.patients.export.excel.single', $patient->id) }}"
               class="btn-action" style="background:#16a34a; color:#fff;">📊 CSV</a>
            @endcanOnco
            <a href="{{ route('oncologie.patients.index') }}"
               class="btn-action" style="background:var(--surface); border:1px solid var(--border); color:var(--muted);">← Retour</a>
        </div>
    </div>
 
    @if($patient->allergies)
    <div class="alert-allergy" style="margin-bottom:1rem;">
        🚫 <b>Allergies documentées :</b> {{ $patient->allergies }}
    </div>
    @endif
 
    {{-- GRILLE INFORMATIONS --}}
    <div class="info-grid" style="margin-bottom:1.25rem;">
 
        {{-- Identité --}}
        <div class="info-card">
            <div class="info-card__head" style="color:#93c5fd;">👤 Identité</div>
            <div class="info-card__body">
                <table class="kv-table">
                    <tr><th>Nom complet</th><td>{{ $patient->nom }} {{ $patient->prenom }}</td></tr>
                    <tr><th>Date naissance</th><td>{{ optional($patient->date_naissance)->format('d/m/Y') ?? '—' }}</td></tr>
                    <tr><th>Âge</th><td><span class="badge badge-accent">{{ $patient->age }} ans</span></td></tr>
                    <tr><th>Sexe</th><td>{{ $patient->sexe }}</td></tr>
                    <tr><th>Groupe sanguin</th><td>{{ $patient->groupe_sanguin ?? '—' }}</td></tr>
                    <tr><th>Téléphone</th><td>{{ $patient->telephone ?? '—' }}</td></tr>
                    <tr><th>Wilaya</th><td>{{ $patient->wilaya ?? '—' }} {{ $patient->daira ? '/ ' . $patient->daira : '' }}</td></tr>
                </table>
            </div>
        </div>
 
        {{-- Clinique --}}
        <div class="info-card">
            <div class="info-card__head" style="color:#f9a8d4;">🔬 Oncologie</div>
            <div class="info-card__body">
                <table class="kv-table">
                    <tr><th>Type de cancer</th><td>{{ $patient->type_cancer }}</td></tr>
                    <tr><th>Stade</th><td>{{ $patient->stade_cancer ?? '—' }}</td></tr>
                    <tr><th>Statut vital</th><td>{{ $patient->statut_label }}</td></tr>
                    @if(!$patient->est_vivant && $patient->date_deces)
                    <tr><th>Date décès</th><td>{{ $patient->date_deces->format('d/m/Y') }}</td></tr>
                    @endif
                    <tr><th>Médecin traitant</th><td>{{ $patient->medecin_traitant ?? '—' }}</td></tr>
                    <tr><th>Prescriptions</th>
                        <td><span class="badge badge-accent">{{ $patient->prescriptions->count() }}</span></td>
                    </tr>
                </table>
                @if($patient->antecedents)
                <hr style="border-color:var(--border); margin:.75rem 0;">
                <div style="font-size:.78rem; color:var(--muted); font-weight:600; margin-bottom:.3rem;">Antécédents</div>
                <div style="font-size:.82rem;">{{ $patient->antecedents }}</div>
                @endif
            </div>
        </div>
 
        {{-- Paramètres pharmacologiques --}}
        <div class="info-card">
            <div class="info-card__head" style="color:#5eead4;">⚗️ Paramètres Pharmacologiques</div>
            <div class="info-card__body">
                <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1rem;">
                    @php
                        $sc = $patient->surface_corporelle_calculee ?? 0;
                        // SC normale ~1.7 m², ring: map 0.5–2.5 → 0–100%
                        $scPct = $sc > 0 ? min(100, max(0, ($sc - 0.5) / 2.0 * 100)) : 0;
                    @endphp
                    <div class="sc-ring" style="background:conic-gradient(#2a9d8f {{ $scPct }}%, rgba(255,255,255,.07) 0%);">
                        <div class="sc-val">{{ $sc ? $sc . ' m²' : '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size:.75rem; color:var(--muted); font-weight:600;">SC Mosteller</div>
                        <div style="font-size:.72rem; color:var(--muted); font-family:monospace; margin-top:.2rem;">
                            √({{ $patient->taille ?? '?' }} × {{ $patient->poids ?? '?' }} / 3600)
                        </div>
                    </div>
                </div>
                <table class="kv-table">
                    <tr><th>Poids</th><td>{{ $patient->poids ? $patient->poids . ' kg' : '—' }}</td></tr>
                    <tr><th>Taille</th><td>{{ $patient->taille ? $patient->taille . ' cm' : '—' }}</td></tr>
                    <tr><th>IMC</th>
                        <td>
                            @if($patient->imc)
                                {{ $patient->imc }}
                                <small style="color:var(--muted);"> — {{ $patient->imc_label }}</small>
                            @else —
                            @endif
                        </td>
                    </tr>
                    <tr><th>Créatinine</th><td>{{ $patient->creatinine ? $patient->creatinine . ' mg/dL' : '—' }}</td></tr>
                    <tr><th>ClCr Cockcroft</th><td>{{ $patient->clairance_renale ? $patient->clairance_renale . ' ml/min' : '—' }}</td></tr>
                    <tr><th>DFG CKD-EPI</th><td>{{ $patient->dfg_ckdepi ? $patient->dfg_ckdepi . ' ml/min/1.73m²' : '—' }}</td></tr>
                    @if($patient->dfg_ckdepi || $patient->clairance_renale)
                    <tr><th>Statut rénal</th><td><span class="badge badge-accent">{{ $patient->statut_dfg }}</span></td></tr>
                    @endif
                </table>
            </div>
        </div>
 
    </div>
 
    {{-- PRESCRIPTIONS RÉCENTES --}}
    @if($prescriptionsRecentes->count())
    <div class="info-card" style="margin-bottom:1.25rem;">
        <div class="info-card__head" style="color:#fcd34d;">💊 Prescriptions récentes</div>
        <div class="info-card__body" style="padding:0;">
            <table style="width:100%; border-collapse:collapse; font-size:.84rem;">
                <thead>
                    <tr style="background:rgba(255,255,255,.03);">
                        <th style="padding:.65rem 1rem; text-align:left; color:var(--muted); font-size:.7rem; text-transform:uppercase;">Date</th>
                        <th style="padding:.65rem 1rem; text-align:left; color:var(--muted); font-size:.7rem; text-transform:uppercase;">Protocole</th>
                        <th style="padding:.65rem 1rem; text-align:left; color:var(--muted); font-size:.7rem; text-transform:uppercase;">Médecin</th>
                        <th style="padding:.65rem 1rem; text-align:left; color:var(--muted); font-size:.7rem; text-transform:uppercase;">Statut</th>
                        <th style="padding:.65rem 1rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescriptionsRecentes as $rx)
                    <tr style="border-top:1px solid rgba(255,255,255,.04);">
                        <td style="padding:.65rem 1rem;">{{ optional($rx->date_prescription)->format('d/m/Y') }}</td>
                        <td style="padding:.65rem 1rem;">{{ optional($rx->protocole)->nom ?? '—' }}</td>
                        <td style="padding:.65rem 1rem; color:var(--muted);">{{ $rx->medecin_nom ?? '—' }}</td>
                        <td style="padding:.65rem 1rem;">
                            @if($rx->statut === 'validee')
                                <span class="badge badge-green">Validée</span>
                            @elseif($rx->statut === 'en_attente')
                                <span class="badge badge-warn">En attente</span>
                            @else
                                <span class="badge badge-red">{{ $rx->statut }}</span>
                            @endif
                        </td>
                        <td style="padding:.65rem 1rem;">
                            <a href="{{ route('oncologie.prescriptions.show', $rx) }}"
                               style="color:var(--accent); font-size:.78rem; text-decoration:none; font-weight:600;">
                                Voir →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
 
    {{-- NOTES --}}
    @if($patient->notes)
    <div class="info-card">
        <div class="info-card__head" style="color:var(--muted);">📝 Notes cliniques</div>
        <div class="info-card__body" style="font-size:.875rem; line-height:1.6; color:var(--muted);">
            {{ $patient->notes }}
        </div>
    </div>
    @endif
 
</div>
@endsection
 