@extends('layouts.app')
 
@section('title', 'Prescription #' . $prescription->id)
 
@section('content')
<div class="container-fluid py-4 px-4" style="max-width: 1100px;">
 
<style>
:root {
    --c-navy:     #0b1d35;
    --c-navy-mid: #102748;
    --c-teal:     #0d9488;
    --c-teal-lt:  #14b8a6;
    --c-sky:      #0ea5e9;
    --c-emerald:  #10b981;
    --c-amber:    #f59e0b;
    --c-rose:     #f43f5e;
    --c-slate-50: #f8fafc;
    --c-slate-100:#f1f5f9;
    --c-slate-200:#e2e8f0;
    --c-slate-400:#94a3b8;
    --c-slate-600:#475569;
    --c-slate-800:#1e293b;
    --c-white:    #ffffff;
    --radius-lg:  16px;
    --radius-md:  10px;
    --shadow-md:  0 4px 16px rgba(0,0,0,.08);
}
 
/* ── BREADCRUMB ── */
.rx-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .78rem;
    color: var(--c-slate-400);
    margin-bottom: 20px;
}
.rx-breadcrumb a { color: var(--c-teal); text-decoration: none; font-weight: 600; }
.rx-breadcrumb a:hover { color: var(--c-teal-lt); }
 
/* ── HERO ── */
.rx-hero {
    background: linear-gradient(135deg, var(--c-navy) 0%, var(--c-navy-mid) 100%);
    border-radius: var(--radius-lg);
    padding: 24px 30px;
    margin-bottom: 22px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 8px 32px rgba(11,29,53,.3);
    position: relative;
    overflow: hidden;
}
.rx-hero::after {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 180px; height: 180px;
    background: radial-gradient(circle, rgba(13,148,136,.2) 0%, transparent 70%);
    pointer-events: none;
}
.rx-hero__id {
    display: inline-block;
    background: rgba(255,255,255,.1);
    color: rgba(255,255,255,.6);
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    padding: 3px 10px;
    border-radius: 99px;
    margin-bottom: 8px;
}
.rx-hero__title {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--c-white);
    margin: 0 0 4px;
    letter-spacing: -.02em;
}
.rx-hero__meta {
    color: rgba(255,255,255,.5);
    font-size: .82rem;
}
 
/* ── STATUS PILL LARGE ── */
.status-pill-lg {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    border-radius: 99px;
    font-size: .82rem;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
}
.status-pill-lg--ok     { background: rgba(16,185,129,.18); color: #6ee7b7; border: 1px solid rgba(16,185,129,.3); }
.status-pill-lg--wait   { background: rgba(245,158,11,.18); color: #fcd34d; border: 1px solid rgba(245,158,11,.3); }
.status-pill-lg--cancel { background: rgba(244,63,94,.18);  color: #fda4af; border: 1px solid rgba(244,63,94,.3);  }
.status-pill-lg__dot    { width: 8px; height: 8px; border-radius: 50%; }
.status-pill-lg--ok .status-pill-lg__dot     { background: var(--c-emerald); }
.status-pill-lg--wait .status-pill-lg__dot   { background: var(--c-amber); }
.status-pill-lg--cancel .status-pill-lg__dot { background: var(--c-rose); }
 
/* ── SECTION CARD ── */
.rx-section {
    background: var(--c-white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--c-slate-200);
    overflow: hidden;
    margin-bottom: 18px;
}
.rx-section__head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 22px;
    border-bottom: 1px solid var(--c-slate-100);
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
}
.rx-section__head--patient  { color: var(--c-sky); border-left: 3px solid var(--c-sky); }
.rx-section__head--clinical { color: var(--c-teal); border-left: 3px solid var(--c-teal); }
.rx-section__head--proto    { color: #6d28d9; border-left: 3px solid #6d28d9; }
.rx-section__head--drugs    { color: var(--c-emerald); border-left: 3px solid var(--c-emerald); }
.rx-section__body { padding: 22px; }
 
/* ── INFO GRID ── */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
}
.info-field__label {
    font-size: .68rem;
    font-weight: 700;
    color: var(--c-slate-400);
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: 4px;
}
.info-field__value {
    font-size: .9rem;
    font-weight: 600;
    color: var(--c-slate-800);
}
 
/* ── CLINICAL PARAMS ── */
.param-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}
@media (max-width: 700px) { .param-grid { grid-template-columns: 1fr 1fr; } }
 
.param-card {
    background: var(--c-slate-50);
    border-radius: var(--radius-md);
    padding: 16px;
    text-align: center;
    border: 1px solid var(--c-slate-200);
}
.param-card__val {
    font-size: 1.6rem;
    font-weight: 900;
    letter-spacing: -.03em;
    line-height: 1;
    margin-bottom: 4px;
}
.param-card__unit {
    font-size: .72rem;
    color: var(--c-slate-400);
    font-weight: 600;
    display: block;
}
.param-card__label {
    font-size: .72rem;
    color: var(--c-slate-600);
    font-weight: 600;
    margin-top: 6px;
    text-transform: uppercase;
    letter-spacing: .05em;
}
.param-card--sc   .param-card__val { color: var(--c-sky); }
.param-card--dfg-ok   .param-card__val { color: var(--c-emerald); }
.param-card--dfg-mild .param-card__val { color: var(--c-amber); }
.param-card--dfg-mod  .param-card__val { color: var(--c-rose); }
.param-card--dfg-sev  .param-card__val { color: #9f1239; }
.param-card--dfg-ok   { border-color: rgba(16,185,129,.3); background: rgba(16,185,129,.04); }
.param-card--dfg-mild { border-color: rgba(245,158,11,.3); background: rgba(245,158,11,.04); }
.param-card--dfg-mod  { border-color: rgba(244,63,94,.3);  background: rgba(244,63,94,.04); }
.param-card--dfg-sev  { border-color: rgba(159,18,57,.3);  background: rgba(159,18,57,.04); }
 
/* ── DRUG TABLE ── */
.drug-table { width: 100%; border-collapse: collapse; }
.drug-table thead th {
    background: var(--c-slate-50);
    color: var(--c-slate-400);
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    padding: 10px 16px;
    border-bottom: 2px solid var(--c-slate-200);
    text-align: left;
}
.drug-table tbody td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--c-slate-100);
    font-size: .85rem;
    color: var(--c-slate-800);
    vertical-align: middle;
}
.drug-table tbody tr:last-child td { border-bottom: none; }
.drug-table tbody tr:hover td { background: rgba(13,148,136,.03); }
.drug-table tfoot td {
    padding: 12px 16px;
    font-weight: 800;
    font-size: .9rem;
    background: var(--c-slate-50);
    border-top: 2px solid var(--c-slate-200);
    color: var(--c-teal);
}
.drug-name { font-weight: 700; }
.drug-type {
    display: inline-block;
    background: var(--c-slate-100);
    color: var(--c-slate-600);
    padding: 2px 8px;
    border-radius: 6px;
    font-size: .72rem;
    font-family: monospace;
    margin-top: 3px;
}
.dose-val {
    font-size: 1.1rem;
    font-weight: 900;
    color: var(--c-teal);
    letter-spacing: -.01em;
}
.dose-unit { font-size: .75rem; color: var(--c-slate-400); margin-left: 2px; }
 
/* ── ALLERGY ALERT ── */
.allergy-alert {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    background: #fff7ed;
    border: 1px solid #fed7aa;
    border-left: 4px solid #f97316;
    border-radius: var(--radius-md);
    padding: 16px 20px;
    margin-bottom: 18px;
}
.allergy-alert__icon {
    width: 36px; height: 36px;
    background: #ffedd5;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.allergy-alert__title { font-weight: 800; font-size: .85rem; color: #9a3412; margin-bottom: 3px; }
.allergy-alert__text  { font-size: .82rem; color: #7c2d12; }
 
/* ── ACTION BAR ── */
.action-bar {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 20px;
}
.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 22px;
    border-radius: var(--radius-md);
    font-size: .84rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: filter .15s, transform .12s;
}
.btn-action:hover { filter: brightness(.92); transform: translateY(-1px); }
.btn-action--back  { background: var(--c-slate-100); color: var(--c-slate-800); border: 1px solid var(--c-slate-200); }
.btn-action--pdf   { background: #fee2e2; color: #991b1b; }
.btn-action--valid { background: #dcfce7; color: #166534; }
</style>
 
{{-- BREADCRUMB --}}
<div class="rx-breadcrumb">
    <a href="{{ route('oncologie.prescriptions.index') }}">Prescriptions</a>
    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
    <span>Prescription #{{ $prescription->id }}</span>
</div>
 
{{-- ═══ HERO ═══ --}}
<div class="rx-hero">
    <div>
        <div class="rx-hero__id">Prescription N° {{ $prescription->id }}</div>
        <h1 class="rx-hero__title">Fiche de prescription chimiothérapie</h1>
        <p class="rx-hero__meta">
            {{ optional($prescription->date_prescription)->format('d/m/Y') }}
            &nbsp;·&nbsp; Cycle {{ $prescription->cycle }}
            @if($prescription->protocole)
                &nbsp;·&nbsp; {{ $prescription->protocole->nom }}
            @endif
        </p>
    </div>
    <div style="padding-top:4px;">
        @if($prescription->statut == 'validee')
            <span class="status-pill-lg status-pill-lg--ok">
                <span class="status-pill-lg__dot"></span>Validée
            </span>
        @elseif($prescription->statut == 'en_attente')
            <span class="status-pill-lg status-pill-lg--wait">
                <span class="status-pill-lg__dot"></span>En attente
            </span>
        @else
            <span class="status-pill-lg status-pill-lg--cancel">
                <span class="status-pill-lg__dot"></span>Annulée
            </span>
        @endif
    </div>
</div>
 
{{-- ═══ ALLERGIE ═══ --}}
@if($prescription->patient->allergies)
<div class="allergy-alert">
    <div class="allergy-alert__icon">⚠</div>
    <div>
        <div class="allergy-alert__title">Allergie signalée</div>
        <div class="allergy-alert__text">{{ $prescription->patient->allergies }}</div>
    </div>
</div>
@endif
 
<div class="row g-4">
<div class="col-lg-7">
 
{{-- ═══ PATIENT ═══ --}}
<div class="rx-section">
    <div class="rx-section__head rx-section__head--patient">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Informations patient
    </div>
    <div class="rx-section__body">
        <div class="info-grid">
            <div>
                <div class="info-field__label">Nom complet</div>
                <div class="info-field__value">{{ $prescription->patient->nom }} {{ $prescription->patient->prenom }}</div>
            </div>
            <div>
                <div class="info-field__label">N° Dossier</div>
                <div class="info-field__value">{{ $prescription->patient->numero_dossier }}</div>
            </div>
            <div>
                <div class="info-field__label">Sexe</div>
                <div class="info-field__value">{{ $prescription->patient->sexe }}</div>
            </div>
            <div>
                <div class="info-field__label">Âge</div>
                <div class="info-field__value">{{ $prescription->patient->age }} ans</div>
            </div>
            <div>
                <div class="info-field__label">Type de cancer</div>
                <div class="info-field__value">{{ $prescription->patient->type_cancer }}</div>
            </div>
            <div>
                <div class="info-field__label">Wilaya</div>
                <div class="info-field__value">{{ $prescription->patient->wilaya }}</div>
            </div>
        </div>
    </div>
</div>
 
{{-- ═══ PROTOCOLE ═══ --}}
<div class="rx-section">
    <div class="rx-section__head rx-section__head--proto">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
        Protocole de traitement
    </div>
    <div class="rx-section__body">
        <div class="info-grid">
            <div>
                <div class="info-field__label">Protocole</div>
                <div class="info-field__value">{{ $prescription->protocole->nom }}</div>
            </div>
            <div>
                <div class="info-field__label">Fréquence</div>
                <div class="info-field__value">{{ $prescription->protocole->frequence }}</div>
            </div>
            <div>
                <div class="info-field__label">Cycle</div>
                <div class="info-field__value">Cycle {{ $prescription->cycle }}</div>
            </div>
            <div>
                <div class="info-field__label">Médecin prescripteur</div>
                <div class="info-field__value">{{ $prescription->medecin_nom ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>
 
{{-- ═══ MÉDICAMENTS ═══ --}}
<div class="rx-section">
    <div class="rx-section__head rx-section__head--drugs">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 3-7-3 7-3 7 3z"/><path d="M12 12v9"/><path d="M5 6v9l7 3 7-3V6"/></svg>
        Médicaments prescrits
    </div>
    <div class="rx-section__body" style="padding:0;">
        <table class="drug-table">
            <thead>
                <tr>
                    <th>Médicament</th>
                    <th>Dose calculée</th>
                    <th>Méthode de calcul</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescription->details as $detail)
                <tr>
                    <td>
                        <div class="drug-name">{{ $detail->medicament->nom }}</div>
                    </td>
                    <td>
                        <span class="dose-val">{{ number_format($detail->dose_calculee, 2) }}</span>
                        <span class="dose-unit">mg</span>
                    </td>
                    <td>
                        <span class="drug-type">{{ $detail->type_calcul ?? 'N/A' }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>Dose totale</strong></td>
                    <td colspan="2">
                        {{ number_format($prescription->dose_totale, 2) }} mg
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
 
</div>{{-- col-lg-7 --}}
 
{{-- ═══ PARAMÈTRES CLINIQUES ═══ --}}
<div class="col-lg-5">
    <div class="rx-section">
        <div class="rx-section__head rx-section__head--clinical">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            Paramètres cliniques
        </div>
        <div class="rx-section__body">
            <div class="param-grid">
                <div class="param-card">
                    <div class="param-card__val" style="color:var(--c-slate-800);">{{ $prescription->poids }}</div>
                    <span class="param-card__unit">kg</span>
                    <div class="param-card__label">Poids</div>
                </div>
                <div class="param-card">
                    <div class="param-card__val" style="color:var(--c-slate-800);">{{ $prescription->taille }}</div>
                    <span class="param-card__unit">cm</span>
                    <div class="param-card__label">Taille</div>
                </div>
                <div class="param-card param-card--sc">
                    <div class="param-card__val">{{ $prescription->surface_corporelle }}</div>
                    <span class="param-card__unit">m² (Mosteller)</span>
                    <div class="param-card__label">Surface Corp.</div>
                </div>
 
                @php $clr = $prescription->clairance_renale; @endphp
                @if($clr >= 90)
                    <div class="param-card param-card--dfg-ok">
                        <div class="param-card__val">{{ $clr }}</div>
                        <span class="param-card__unit">ml/min · Normal</span>
                        <div class="param-card__label">DFG (Cockroft)</div>
                    </div>
                @elseif($clr >= 60)
                    <div class="param-card param-card--dfg-mild">
                        <div class="param-card__val">{{ $clr }}</div>
                        <span class="param-card__unit">ml/min · Léger</span>
                        <div class="param-card__label">DFG (Cockroft)</div>
                    </div>
                @elseif($clr >= 30)
                    <div class="param-card param-card--dfg-mod">
                        <div class="param-card__val">{{ $clr }}</div>
                        <span class="param-card__unit">ml/min · Modéré</span>
                        <div class="param-card__label">DFG (Cockroft)</div>
                    </div>
                @else
                    <div class="param-card param-card--dfg-sev">
                        <div class="param-card__val">{{ $clr }}</div>
                        <span class="param-card__unit">ml/min · Sévère</span>
                        <div class="param-card__label">DFG (Cockroft)</div>
                    </div>
                @endif
            </div>
 
            {{-- Formule SC --}}
            <div style="margin-top:18px;background:var(--c-slate-50);border-radius:8px;padding:12px 16px;border:1px solid var(--c-slate-200);">
                <div style="font-size:.68rem;font-weight:700;color:var(--c-slate-400);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Formule Mosteller</div>
                <div style="font-family:monospace;font-size:.85rem;color:var(--c-teal);font-weight:700;">
                    SC = √( {{ $prescription->poids }} × {{ $prescription->taille }} / 3600 )
                    = <strong>{{ $prescription->surface_corporelle }} m²</strong>
                </div>
            </div>
        </div>
    </div>
</div>
</div>{{-- row --}}
 
{{-- ═══ ACTIONS ═══ --}}
<div class="action-bar">
    <a href="{{ route('oncologie.prescriptions.index') }}" class="btn-action btn-action--back">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        Retour à la liste
    </a>
    <a href="{{ route('oncologie.prescriptions.pdf', $prescription->id) }}" class="btn-action btn-action--pdf">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Imprimer PDF
    </a>
    @if(!$prescription->isValidee())
    <form action="{{ route('oncologie.prescriptions.valider', $prescription->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn-action btn-action--valid">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Valider la prescription
        </button>
    </form>
    @endif
</div>
 
</div>
@endsection