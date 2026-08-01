@extends('layouts.app')
@section('title', 'Dossier — {{ $patient->nom }} {{ $patient->prenom }}')
 
@push('styles')
<style>
/*══════════════════════════════════════════════
    THÈME CLAIR — DOSSIER PATIENT
══════════════════════════════════════════════*/

:root{

    /* Fonds */
    --onco-bg:#f0f4f8;
    --onco-surface:#ffffff;
    --onco-card-bg:#ffffff;
    --onco-input-bg:#f8fafc;

    /* Bordures */
    --onco-border:#cbd5e1;

    /* Couleurs */
    --onco-accent:#0284c7;
    --onco-success:#059669;
    --onco-warning:#d97706;
    --onco-danger:#dc2626;
    --onco-info:#4f46e5;
    /* Texte */
    --onco-text:#0f172a;
    --onco-muted:#475569;
    --radius:.75rem;
    --shadow:0 2px 12px rgba(15,23,42,.08);
}

body{
    background:var(--onco-bg);
    color:var(--onco-text);
}

/*════════════ HEADER ════════════*/

.patient-header{
    background:#fff;
    border:1px solid var(--onco-border);
    border-radius:var(--radius);
    box-shadow:var(--shadow);
}

.patient-avatar{
    background:linear-gradient(
        135deg,
        #0ea5e9,
        #0284c7
    );
    color:#fff;
}

/*════════════ CARTES ════════════*/

.info-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:1rem;
}

.info-card{

    background:var(--onco-surface);
    border:1px solid var(--onco-border);
    border-radius:var(--radius);
    overflow:hidden;
    box-shadow:var(--shadow);
    transition:.25s;
}

.info-card:hover{
    transform:translateY(-2px);
}

.info-card__head{
    padding:.8rem 1rem;
    background:#f8fafc;
    border-bottom:1px solid var(--onco-border);
    font-size:.72rem;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.06em;
}

.info-card__body{
    padding:1rem;
}
/*════════════ TABLE ════════════*/
.kv-table{

    width:100%;
    border-collapse:collapse;
    font-size:.86rem;
}

.kv-table th{

    width:42%;
    padding:.55rem 0;
    color:var(--onco-muted);
    font-weight:600;
    text-align:left;
}

.kv-table td{
    color:var(--onco-text);
    font-weight:600;
    padding:.55rem 0;
}

.kv-table tr+tr th,
.kv-table tr+tr td{
    border-top:1px solid #e2e8f0;
}

/*════════════ BADGES ════════════*/

.badge{
    display:inline-flex;
    align-items:center;
    gap:.3rem;
    padding:.25rem .65rem;
    border-radius:999px;
    font-size:.72rem;
    font-weight:700;
}

.badge-green{
    background:#ecfdf5;
    color:var(--onco-success);
    border:1px solid #a7f3d0;
}

.badge-red{
    background:#fef2f2;
    color:var(--onco-danger);
    border:1px solid #fecaca;
}

.badge-accent{
    background:#f0f9ff;
    color:var(--onco-accent);
    border:1px solid #bae6fd;
}

.badge-warn{
    background:#fffbeb;
    color:var(--onco-warning);
    border:1px solid #fde68a;
}

/*════════════ BOUTONS ════════════*/

.btn-action{
    display:inline-flex;
    align-items:center;
    gap:.45rem;
    padding:.6rem 1.15rem;
    border-radius:.6rem;
    font-size:.84rem;
    font-weight:700;
    text-decoration:none;
    border:none;
    cursor:pointer;
    transition:.25s;
}

.btn-action:hover{
    transform:translateY(-2px);
    box-shadow:0 6px 18px rgba(2,132,199,.15);
}

/*════════════ SURFACE CORPORELLE ════════════*/

.sc-ring{
    width:92px;
    height:92px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    position:relative;
}

.sc-ring::before{
    content:"";
    position:absolute;
    inset:9px;
    background:#fff;
    border-radius:50%;
}

.sc-val{
    position:relative;
    z-index:2;
    font-weight:800;
    color:var(--onco-accent);
    font-size:.9rem;
}

/*════════════ ALERTE ════════════*/

.alert-allergy{
    background:#fef2f2;
    border:1px solid #fecaca;
    border-left:5px solid var(--onco-danger);
    color:#991b1b;
    border-radius:.65rem;
    padding:.9rem 1rem;
    box-shadow:var(--shadow);
}

/*════════════ TABLE PRESCRIPTIONS ════════════*/

.rx-table{
    width:100%;
    border-collapse:collapse;
}

.rx-table thead tr{
    background:#f8fafc;
}

.rx-table th{
    padding:.75rem 1rem;
    color:var(--onco-muted);
    font-size:.72rem;
    text-transform:uppercase;
    letter-spacing:.05em;
    border-bottom:1px solid var(--onco-border);
    text-align:left;
}

.rx-table td{
    padding:.75rem 1rem;
    border-top:1px solid #e2e8f0;
    color:var(--onco-text);
}

.rx-table tbody tr:hover{
    background:#f8fafc;
}

/*════════════ NOTES ════════════*/

.note-box{

    line-height:1.7;
    color:var(--onco-muted);
}

/*════════════ LIENS ════════════*/

a{

    transition:.2s;
}

a:hover{

    color:var(--onco-accent);
}

/*════════════ HR ════════════*/

hr{

    border:none;
    border-top:1px solid var(--onco-border);
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
 