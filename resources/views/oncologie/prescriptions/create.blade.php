@extends('layouts.app')
 
@section('title', 'Nouvelle Prescription Oncologique')
 
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
/* ========== TOKENS ========== */
:root {
    --onco-bg:        #0f172a;
    --onco-surface:   #1e293b;
    --onco-border:    #334155;
    --onco-accent:    #06b6d4;        /* cyan médical */
    --onco-success:   #10b981;
    --onco-warning:   #f59e0b;
    --onco-danger:    #ef4444;
    --onco-info:      #818cf8;
    --onco-text:      #e2e8f0;
    --onco-muted:     #94a3b8;
    --onco-card-bg:   #1e293b;
    --onco-input-bg:  #0f172a;
    --radius:         0.75rem;
    --shadow:         0 4px 24px rgba(0,0,0,.45);
}
 
body { background: var(--onco-bg); color: var(--onco-text); }
 
/* ── LAYOUT ── */
.rx-grid {
    display: grid;
    grid-template-columns: 320px 1fr 280px;
    gap: 1.25rem;
    align-items: start;
}
@media (max-width: 1100px) {
    .rx-grid { grid-template-columns: 1fr; }
}
 
/* ── CARDS ── */
.rx-card {
    background: var(--onco-card-bg);
    border: 1px solid var(--onco-border);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    margin-bottom: 1.25rem;
}
.rx-card__head {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .75rem 1.1rem;
    font-size: .8rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    background: rgba(255,255,255,.04);
    border-bottom: 1px solid var(--onco-border);
}
.rx-card__head--accent  { color: var(--onco-accent); }
.rx-card__head--success { color: var(--onco-success);}
.rx-card__head--warning { color: var(--onco-warning);}
.rx-card__head--danger  { color: var(--onco-danger); }
.rx-card__head--info    { color: var(--onco-info);   }
.rx-card__body  { padding: 1.1rem; }
 
/* ── FORM CONTROLS ── */
.rx-label {
    display: block;
    font-size: .75rem;
    font-weight: 600;
    color: var(--onco-muted);
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: .35rem;
}
.rx-input {
    width: 100%;
    background: var(--onco-input-bg);
    border: 1px solid var(--onco-border);
    border-radius: .5rem;
    color: var(--onco-text);
    padding: .55rem .85rem;
    font-size: .9rem;
    transition: border-color .2s;
}
.rx-input:focus {
    outline: none;
    border-color: var(--onco-accent);
    box-shadow: 0 0 0 3px rgba(6,182,212,.15);
}
.rx-input[readonly] {
    background: rgba(6,182,212,.06);
    border-color: rgba(6,182,212,.3);
    color: var(--onco-accent);
    font-weight: 700;
    cursor: not-allowed;
}
.rx-row { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
.rx-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: .75rem; }
 
/* ── SELECT2 THEMING ── */
.select2-container--default .select2-selection--single {
    background: var(--onco-input-bg) !important;
    border: 1px solid var(--onco-border) !important;
    border-radius: .5rem !important;
    height: 38px !important;
    color: var(--onco-text) !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: var(--onco-text) !important;
    line-height: 36px !important;
    padding-left: .85rem !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    top: 6px !important;
}
.select2-dropdown {
    background: var(--onco-surface) !important;
    border: 1px solid var(--onco-border) !important;
    border-radius: .5rem !important;
}
.select2-container--default .select2-results__option {
    color: var(--onco-text) !important;
    padding: .5rem .85rem !important;
}
.select2-container--default .select2-results__option--highlighted {
    background: var(--onco-accent) !important;
    color: #fff !important;
}
.select2-search__field {
    background: var(--onco-input-bg) !important;
    color: var(--onco-text) !important;
    border: 1px solid var(--onco-border) !important;
    border-radius: .4rem !important;
    padding: .3rem .6rem !important;
}
 
/* ── BADGES DFG ── */
.dfg-badge {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    padding: .2rem .65rem;
    border-radius: 99px;
    font-size: .72rem;
    font-weight: 700;
}
.dfg-normal  { background: rgba(16,185,129,.15); color: var(--onco-success); border: 1px solid rgba(16,185,129,.4); }
.dfg-mild    { background: rgba(245,158,11,.12); color: var(--onco-warning); border: 1px solid rgba(245,158,11,.4); }
.dfg-moderate{ background: rgba(239,68,68,.12);  color: var(--onco-danger);  border: 1px solid rgba(239,68,68,.4); }
.dfg-severe  { background: rgba(239,68,68,.22);  color: var(--onco-danger);  border: 1px solid rgba(239,68,68,.6); }
.dfg-terminal{ background: #ef4444;              color: #fff;                border: 1px solid #ef4444; }
 
/* ── ALERTE ── */
.rx-alerte {
    display: flex;
    align-items: flex-start;
    gap: .5rem;
    padding: .6rem .85rem;
    border-radius: .5rem;
    font-size: .82rem;
    margin-bottom: .5rem;
}
.rx-alerte--danger  { background: rgba(239,68,68,.12);  border-left: 3px solid var(--onco-danger);  color: #fca5a5; }
.rx-alerte--warning { background: rgba(245,158,11,.1);  border-left: 3px solid var(--onco-warning); color: #fde68a; }
.rx-alerte--info    { background: rgba(129,140,248,.1); border-left: 3px solid var(--onco-info);    color: #a5b4fc; }
 
/* ── MED CARD ── */
.med-card {
    background: rgba(6,182,212,.05);
    border: 1px solid rgba(6,182,212,.2);
    border-radius: .65rem;
    padding: .95rem 1.1rem;
    margin-bottom: .75rem;
    transition: border-color .2s;
}
.med-card:hover { border-color: var(--onco-accent); }
.med-card__name  { font-weight: 700; font-size: .95rem; color: var(--onco-text); }
.med-card__meta  { font-size: .75rem; color: var(--onco-muted); margin-top: .1rem; }
.med-card__dose  {
    font-size: 1.55rem;
    font-weight: 900;
    color: var(--onco-accent);
    letter-spacing: -.02em;
    line-height: 1;
}
.med-card__formule {
    font-size: .72rem;
    color: var(--onco-muted);
    font-family: monospace;
    margin-top: .25rem;
}
.med-card__limit {
    font-size: .72rem;
    color: var(--onco-warning);
    margin-top: .2rem;
}
 
/* ── MÉTHODE TOGGLE ── */
.methode-toggle {
    display: flex;
    gap: .4rem;
    background: var(--onco-input-bg);
    border: 1px solid var(--onco-border);
    border-radius: .5rem;
    padding: .25rem;
}
.methode-toggle button {
    flex: 1;
    padding: .35rem .5rem;
    border: none;
    border-radius: .35rem;
    background: transparent;
    color: var(--onco-muted);
    font-size: .75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
}
.methode-toggle button.active {
    background: var(--onco-accent);
    color: #fff;
}
 
/* ── SC METER ── */
.sc-meter {
    background: var(--onco-input-bg);
    border-radius: .4rem;
    height: 6px;
    overflow: hidden;
    margin-top: .4rem;
}
.sc-meter__bar {
    height: 100%;
    border-radius: .4rem;
    background: linear-gradient(90deg, var(--onco-success), var(--onco-accent));
    transition: width .4s ease;
}
 
/* ── SECTION SEPARATOR ── */
.rx-separator {
    border: none;
    border-top: 1px solid var(--onco-border);
    margin: 1.25rem 0;
}
 
/* ── HEADER ── */
.rx-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, rgba(6,182,212,.08) 100%);
    border: 1px solid var(--onco-border);
    border-radius: var(--radius);
    padding: 1.5rem 1.75rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}
.rx-hero::before {
    content: '⚗';
    position: absolute;
    right: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 5rem;
    opacity: .05;
}
.rx-hero h1 { font-size: 1.45rem; font-weight: 800; color: var(--onco-accent); margin: 0 0 .3rem; }
.rx-hero p  { color: var(--onco-muted); margin: 0; font-size: .88rem; }
 
/* ── BUTTONS ── */
.rx-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    padding: .7rem 1.5rem;
    border-radius: .6rem;
    font-weight: 700;
    font-size: .92rem;
    cursor: pointer;
    border: none;
    transition: all .2s;
    text-decoration: none;
}
.rx-btn--primary {
    background: linear-gradient(135deg, var(--onco-accent), #0891b2);
    color: #fff;
    box-shadow: 0 4px 14px rgba(6,182,212,.3);
}
.rx-btn--primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(6,182,212,.4); }
.rx-btn--ghost {
    background: transparent;
    border: 1px solid var(--onco-border);
    color: var(--onco-muted);
}
.rx-btn--ghost:hover { border-color: var(--onco-accent); color: var(--onco-accent); }
.rx-btn--full { width: 100%; }
 
/* ── PATIENT TABLE ── */
.pt-table { width: 100%; font-size: .82rem; border-collapse: collapse; }
.pt-table th { color: var(--onco-muted); text-align: left; padding: .3rem 0; width: 42%; font-weight: 600; }
.pt-table td { color: var(--onco-text); padding: .3rem 0; }
 
/* ── FORMULE BOX ── */
.formule-box {
    background: var(--onco-input-bg);
    border: 1px solid var(--onco-border);
    border-radius: .5rem;
    padding: .65rem .95rem;
    font-family: monospace;
    font-size: .8rem;
    color: var(--onco-muted);
    margin-top: .5rem;
}
.formule-box .hl { color: var(--onco-accent); font-weight: 700; }
 
/* ── ERRORS ── */
.rx-errors {
    background: rgba(239,68,68,.1);
    border: 1px solid rgba(239,68,68,.35);
    border-radius: .65rem;
    padding: .85rem 1.1rem;
    margin-bottom: 1.25rem;
    color: #fca5a5;
    font-size: .85rem;
}
 
/* ── LOADING OVERLAY ── */
#loadingOverlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,.7);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 1rem;
    color: var(--onco-accent);
    font-weight: 700;
    font-size: 1.1rem;
}
#loadingOverlay.active { display: flex; }
.spinner {
    width: 3rem; height: 3rem;
    border: 3px solid rgba(6,182,212,.2);
    border-top-color: var(--onco-accent);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush
 
@section('content')
 
{{-- Loading overlay --}}
<div id="loadingOverlay">
    <div class="spinner"></div>
    <span>Calcul des doses en cours…</span>
</div>
 
<div class="container-fluid py-4">
 
    {{-- HERO --}}
    <div class="rx-hero">
        <h1>💊 Nouvelle Prescription Oncologique</h1>
        <p>Calcul automatique des doses · Surface corporelle Mosteller · Cockcroft-Gault · CKD-EPI · Calvert</p>
    </div>
 
    @if($errors->any())
    <div class="rx-errors">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
 
    <form id="rxForm"
          action="{{ route('oncologie.prescriptions.store') }}"
          method="POST">
        @csrf
 
        {{-- ========== GRILLE 3 COLONNES ========== --}}
        <div class="rx-grid">
 
            {{-- ============================================================ --}}
            {{-- COL 1 — PATIENT --}}
            {{-- ============================================================ --}}
            <div>
 
                {{-- Sélection patient --}}
                <div class="rx-card">
                    <div class="rx-card__head rx-card__head--accent">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                        Patient
                    </div>
                    <div class="rx-card__body">
                        <label class="rx-label">Rechercher un patient</label>
                        <select name="patient_id" id="patient_id" class="rx-input" required>
                            <option value="">— Sélectionner —</option>
                            @foreach($patients as $p)
                            <option value="{{ $p->id }}"
                                data-nom="{{ $p->nom }}"
                                data-prenom="{{ $p->prenom }}"
                                data-dossier="{{ $p->numero_dossier }}"
                                data-cancer="{{ $p->type_cancer }}"
                                data-sexe="{{ $p->sexe }}"
                                data-age="{{ $p->age }}"
                                data-poids="{{ $p->poids }}"
                                data-taille="{{ $p->taille }}"
                                data-allergies="{{ $p->allergies }}"
                                data-creatinine="{{ $p->creatinine }}"
                                data-clairance="{{ $p->clairance_renale }}"
                                data-groupe="{{ $p->groupe_sanguin }}"
                                data-telephone="{{ $p->telephone }}"
                            >
                                {{ $p->numero_dossier }} — {{ $p->nom }} {{ $p->prenom }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
 
                {{-- Fiche patient --}}
                <div class="rx-card" id="cardPatient" style="display:none">
                    <div class="rx-card__head rx-card__head--success">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 12l2 2 4-4"/><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                        Dossier Patient
                    </div>
                    <div class="rx-card__body" id="patientInfos"></div>
                </div>
 
                {{-- Allergies --}}
                <div class="rx-card" id="cardAllergies" style="display:none">
                    <div class="rx-card__head rx-card__head--danger">
                        ⚠️ Allergies connues
                    </div>
                    <div class="rx-card__body" id="allergiesContent"></div>
                </div>
 
                {{-- Alertes --}}
                <div class="rx-card" id="cardAlertes" style="display:none">
                    <div class="rx-card__head rx-card__head--warning">
                        🚨 Alertes Cliniques
                    </div>
                    <div class="rx-card__body" id="alertesContent"></div>
                </div>
 
            </div>
 
            {{-- ============================================================ --}}
            {{-- COL 2 — PARAMÈTRES + PROTOCOLE --}}
            {{-- ============================================================ --}}
            <div>
 
                {{-- Paramètres anthropométriques --}}
                <div class="rx-card">
                    <div class="rx-card__head rx-card__head--info">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                        Paramètres Anthropométriques &amp; Rénaux
                    </div>
                    <div class="rx-card__body">
 
                        <div class="rx-row mb-3">
                            <div>
                                <label class="rx-label">Poids (kg) <span style="color:var(--onco-danger)">*</span></label>
                                <input type="number" step="0.1" id="poids" name="poids"
                                    class="rx-input" placeholder="Ex: 70" required
                                    value="{{ old('poids') }}">
                            </div>
                            <div>
                                <label class="rx-label">Taille (cm) <span style="color:var(--onco-danger)">*</span></label>
                                <input type="number" step="0.1" id="taille" name="taille"
                                    class="rx-input" placeholder="Ex: 170" required
                                    value="{{ old('taille') }}">
                            </div>
                        </div>
 
                        {{-- SC calculée --}}
                        <div class="mb-3">
                            <label class="rx-label">
                                Surface Corporelle — Mosteller (m²)
                                <span id="scFormuleHint" style="font-size:.68rem; color:var(--onco-muted); font-weight:400; margin-left:.5rem;">
                                    √( Taille × Poids / 3600 )
                                </span>
                            </label>
                            <input type="text" id="surface" name="surface_corporelle"
                                class="rx-input" readonly placeholder="—" value="{{ old('surface_corporelle') }}">
                            <div class="sc-meter"><div class="sc-meter__bar" id="scMeterBar" style="width:0%"></div></div>
                            <div style="display:flex; justify-content:space-between; font-size:.68rem; color:var(--onco-muted); margin-top:.2rem;">
                                <span>0.5</span><span>1.0</span><span style="color:var(--onco-success); font-weight:700">1.5</span><span style="color:var(--onco-success); font-weight:700">1.8</span><span>2.0</span><span>2.5+</span>
                            </div>
                        </div>
 
                        <hr class="rx-separator">
 
                        {{-- Créatinine --}}
                        <div class="rx-row mb-3">
                            <div>
                                <label class="rx-label">Créatinine (mg/dL)</label>
                                <input type="number" step="0.01" id="creatinine" name="creatinine"
                                    class="rx-input" placeholder="Ex: 0.90"
                                    value="{{ old('creatinine') }}">
                            </div>
                            <div>
                                <label class="rx-label">Méthode DFG</label>
                                <div class="methode-toggle" id="methodeToggle">
                                    <button type="button" data-m="cockcroft" class="active">Cockcroft-Gault</button>
                                    <button type="button" data-m="ckdepi">CKD-EPI 2021</button>
                                </div>
                                <input type="hidden" name="methode_dfg" id="methode_dfg" value="cockcroft">
                            </div>
                        </div>
 
                        {{-- Résultats DFG --}}
                        <div class="rx-row-3 mb-3">
                            <div>
                                <label class="rx-label">Cockcroft-Gault (ml/min)</label>
                                <input type="text" id="clairance_cockcroft" class="rx-input" readonly placeholder="—">
                            </div>
                            <div>
                                <label class="rx-label">CKD-EPI (ml/min/1.73m²)</label>
                                <input type="text" id="clairance_ckdepi" class="rx-input" readonly placeholder="—">
                            </div>
                            <div>
                                <label class="rx-label">DFG retenu <span id="dfgBadge"></span></label>
                                <input type="text" id="clairance" name="clairance_renale" class="rx-input" readonly placeholder="—">
                            </div>
                        </div>
 
                        {{-- Formule détaillée --}}
                        <div id="formuleBox" class="formule-box" style="display:none"></div>
 
                    </div>
                </div>
 
                {{-- Protocole --}}
                <div class="rx-card">
                    <div class="rx-card__head rx-card__head--accent">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                        Protocole Thérapeutique
                    </div>
                    <div class="rx-card__body">
                        <label class="rx-label">Protocole <span style="color:var(--onco-danger)">*</span></label>
                        <select name="protocole_id" id="protocole" class="rx-input" required>
                            <option value="">— Choisir —</option>
                            @foreach($protocoles as $pr)
                            <option value="{{ $pr->id }}"
                                data-nom="{{ $pr->nom }}"
                                data-cycle="{{ $pr->cycle }}"
                                data-frequence="{{ $pr->frequence }}"
                                data-description="{{ $pr->description }}">
                                {{ $pr->nom }}
                            </option>
                            @endforeach
                        </select>
                        <div id="protocoleInfos" class="mt-3"></div>
                    </div>
                </div>
 
                {{-- Notes cliniques --}}
                <div class="rx-card">
                    <div class="rx-card__head rx-card__head--info">📝 Notes Cliniques</div>
                    <div class="rx-card__body">
                        <textarea name="notes_cliniques" id="notes_cliniques"
                            class="rx-input" rows="3"
                            placeholder="Observations, contre-indications spécifiques, ajustements…"
                            style="resize:vertical">{{ old('notes_cliniques') }}</textarea>
                    </div>
                </div>
 
            </div>
 
            {{-- ============================================================ --}}
            {{-- COL 3 — MÉDECIN / DATE / RÉSUMÉ --}}
            {{-- ============================================================ --}}
            <div>
 
                {{-- Médecin + Date --}}
                <div class="rx-card">
                    <div class="rx-card__head rx-card__head--success">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 14l9-5-9-5-9 5z"/><path d="M12 14l6.16-3.422a12 12 0 01.84 4.922"/></svg>
                        Ordonnateur
                    </div>
                    <div class="rx-card__body">
                        <div class="mb-3">
                            <label class="rx-label">Médecin prescripteur <span style="color:var(--onco-danger)">*</span></label>
                            <select name="medecin_nom" class="rx-input" required>
                                <option value="">— Choisir —</option>
                                @foreach($medecins as $m)
                                <option value="{{ $m->name }}" {{ old('medecin_nom') === $m->name ? 'selected' : '' }}>
                                    {{ $m->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="rx-label">Date de prescription</label>
                            <input type="date" name="date_prescription"
                                value="{{ old('date_prescription', date('Y-m-d')) }}"
                                class="rx-input" required>
                        </div>
                    </div>
                </div>
 
                {{-- Résumé dynamique --}}
                <div class="rx-card" id="cardResume" style="display:none">
                    <div class="rx-card__head rx-card__head--accent">📊 Résumé</div>
                    <div class="rx-card__body" id="resumeContent"></div>
                </div>
 
            </div>
 
        </div>
 
        {{-- ========== DOSES CALCULÉES ========== --}}
        <div class="rx-card mt-2">
            <div class="rx-card__head rx-card__head--danger">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Doses Calculées Automatiquement
            </div>
            <div class="rx-card__body" id="medicamentsContainer">
                <div class="rx-alerte rx-alerte--info">
                    Sélectionnez un patient et un protocole pour calculer les doses.
                </div>
            </div>
        </div>
 
        {{-- ========== ACTIONS ========== --}}
        <div style="display:flex; gap:1rem; margin-top:1.5rem; margin-bottom:3rem;">
            <a href="{{ route('oncologie.prescriptions.index') }}" class="rx-btn rx-btn--ghost rx-btn--full">
                ← Retour
            </a>
            <button type="submit" class="rx-btn rx-btn--primary rx-btn--full" id="btnSubmit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v14z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Enregistrer la Prescription
            </button>
        </div>
 
    </form>
</div>
@endsection
 
@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
'use strict';
 
// ================================================================
// DOM refs
// ================================================================
const $patientSel   = $('#patient_id');
const $protocoleSel = $('#protocole');
const poidsEl       = document.getElementById('poids');
const tailleEl      = document.getElementById('taille');
const creatinineEl  = document.getElementById('creatinine');
const surfaceEl     = document.getElementById('surface');
const clairCGEl     = document.getElementById('clairance_cockcroft');
const clairCKEl     = document.getElementById('clairance_ckdepi');
const clairEl       = document.getElementById('clairance');
const methodeInput  = document.getElementById('methode_dfg');
const scBar         = document.getElementById('scMeterBar');
const formuleBox    = document.getElementById('formuleBox');
const dfgBadge      = document.getElementById('dfgBadge');
const medContainer  = document.getElementById('medicamentsContainer');
 
let methodeDFG = 'cockcroft';   // état courant
 
// ================================================================
// Select2 init
// ================================================================
$(document).ready(() => {
    $patientSel.select2({ width: '100%', placeholder: 'Rechercher un patient…' });
    $protocoleSel.select2({ width: '100%', placeholder: 'Choisir un protocole…' });
 
    $patientSel.on('change', onPatientChange);
    $protocoleSel.on('change', onProtocoleChange);
});
 
// ================================================================
// MÉTHODE DFG toggle
// ================================================================
document.querySelectorAll('#methodeToggle button').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('#methodeToggle button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        methodeDFG = btn.dataset.m;
        methodeInput.value = methodeDFG;
        recalcAll();
    });
});
 
// ================================================================
// FORMULES MATHÉMATIQUES
// ================================================================
 
/** Mosteller : SC = √( taille × poids / 3600 ) */
function calcSC(poids, taille) {
    if (!poids || !taille || poids <= 0 || taille <= 0) return 0;
    return Math.sqrt((poids * taille) / 3600);
}
 
/**
 * Cockcroft-Gault
 * Homme : (140 − age) × poids / (72 × créatinine)
 * Femme : × 0.85
 * créatinine en mg/dL
 */
function calcCockcroft(age, poids, creatinine, sexe) {
    if (!age || !poids || !creatinine || creatinine <= 0) return 0;
    let clcr = ((140 - age) * poids) / (72 * creatinine);
    if ((sexe || '').toLowerCase().startsWith('f')) clcr *= 0.85;
    return clcr;
}
 
/**
 * CKD-EPI 2021 (sans ethnie, équation révisée)
 * Femme  κ=0.7  α=−0.241
 * Homme  κ=0.9  α=−0.302
 * Si Scr/κ ≤ 1  → 142 × (Scr/κ)^α × 0.9938^Age (× 1.012 si F)
 * Si Scr/κ > 1  → 142 × (Scr/κ)^−1.200 × 0.9938^Age (× 1.012 si F)
 */
function calcCKDEPI(age, creatinine, sexe) {
    if (!age || !creatinine || creatinine <= 0) return 0;
    const isFemme = (sexe || '').toLowerCase().startsWith('f');
    const kappa   = isFemme ? 0.7  : 0.9;
    const alpha   = isFemme ? -0.241 : -0.302;
    const ratio   = creatinine / kappa;
    let eGFR;
    if (ratio <= 1) {
        eGFR = 142 * Math.pow(ratio, alpha) * Math.pow(0.9938, age);
    } else {
        eGFR = 142 * Math.pow(ratio, -1.200) * Math.pow(0.9938, age);
    }
    if (isFemme) eGFR *= 1.012;
    return eGFR;
}
 
/**
 * Calvert — Carboplatine
 * Dose = AUC × (DFG + 25)
 */
function calcCalvert(auc, dfg) {
    return auc * (dfg + 25);
}
 
// ================================================================
// ALERTES CLINIQUES
// ================================================================
function genAlertes(sc, dfg, allergies, poids) {
    const alertes = [];
    if (!poids)      alertes.push({ n:'warning', m:'Poids non renseigné' });
    if (!surfaceEl.value) alertes.push({ n:'warning', m:'Surface corporelle non calculée' });
    if (!dfg)        alertes.push({ n:'info',    m:'Clairance non calculée — créatinine manquante' });
 
    if (dfg > 0) {
        if (dfg < 15)       alertes.push({ n:'danger',  m:'🚫 Insuffisance rénale terminale (DFG < 15 ml/min) — Contre-indication majeure à de nombreux cytotoxiques' });
        else if (dfg < 30)  alertes.push({ n:'danger',  m:'🔴 Insuffisance rénale sévère (DFG < 30 ml/min) — Adaptation de dose obligatoire' });
        else if (dfg < 60)  alertes.push({ n:'warning', m:'🟡 Insuffisance rénale modérée (DFG < 60 ml/min) — Surveillance renforcée, adapter si nécessaire' });
    }
 
    if (sc > 0 && sc < 1.2)  alertes.push({ n:'info',    m:`SC basse (${sc.toFixed(2)} m²) — Vérifier plafonnement de dose` });
    if (sc > 2.2)             alertes.push({ n:'warning', m:`SC élevée (${sc.toFixed(2)} m²) — Certains protocoles plafonnent à 2,0 m²` });
    if (allergies && allergies.trim() !== '' && allergies !== 'null')
        alertes.push({ n:'danger',  m:`⚠️ Allergies documentées : ${allergies}` });
 
    return alertes;
}
 
function renderAlertes(alertes) {
    const card = document.getElementById('cardAlertes');
    const box  = document.getElementById('alertesContent');
    if (!alertes.length) { card.style.display = 'none'; return; }
    card.style.display = 'block';
    box.innerHTML = alertes.map(a =>
        `<div class="rx-alerte rx-alerte--${a.n}">${a.m}</div>`
    ).join('');
}
 
// ================================================================
// DFG BADGE
// ================================================================
function dfgLabel(dfg) {
    if (!dfg || dfg <= 0) return '';
    if (dfg >= 90)  return '<span class="dfg-badge dfg-normal">Normal ≥90</span>';
    if (dfg >= 60)  return '<span class="dfg-badge dfg-mild">Légère 60-89</span>';
    if (dfg >= 30)  return '<span class="dfg-badge dfg-moderate">Modérée 30-59</span>';
    if (dfg >= 15)  return '<span class="dfg-badge dfg-severe">Sévère 15-29</span>';
    return '<span class="dfg-badge dfg-terminal">Terminale &lt;15</span>';
}
 
// ================================================================
// SC METER
// ================================================================
function updateScMeter(sc) {
    // SC normale ≈ 1.5–2.0 m² → map 0.5–2.5 → 0–100%
    const pct = Math.min(100, Math.max(0, ((sc - 0.5) / 2.0) * 100));
    scBar.style.width = pct + '%';
}
 
// ================================================================
// RECALCUL PRINCIPAL
// ================================================================
function recalcAll() {
    const patient = $patientSel.find(':selected');
    const poids      = parseFloat(poidsEl.value)      || 0;
    const taille     = parseFloat(tailleEl.value)      || 0;
    const creatinine = parseFloat(creatinineEl.value)  || 0;
    const age        = parseFloat(patient.data('age')) || 0;
    const sexe       = patient.data('sexe') || '';
 
    // SC — Mosteller
    const sc = calcSC(poids, taille);
    surfaceEl.value = sc > 0 ? sc.toFixed(2) : '';
    updateScMeter(sc);
 
    // DFG
    const cg   = calcCockcroft(age, poids, creatinine, sexe);
    const ckd  = calcCKDEPI(age, creatinine, sexe);
    clairCGEl.value = cg  > 0 ? cg.toFixed(2)  : '';
    clairCKEl.value = ckd > 0 ? ckd.toFixed(2) : '';
 
    const dfg = methodeDFG === 'ckdepi' ? ckd : cg;
    clairEl.value = dfg > 0 ? dfg.toFixed(2) : '';
    dfgBadge.innerHTML = dfgLabel(dfg);
 
    // Formule détaillée
    if (creatinine > 0 && age > 0 && poids > 0) {
        const sexeLabel = sexe.toLowerCase().startsWith('f') ? 'Femme × 0.85' : 'Homme';
        formuleBox.style.display = 'block';
        formuleBox.innerHTML =
            `<b style="color:var(--onco-accent)">Cockcroft-Gault</b> : ` +
            `<span class="hl">(140 − ${age}) × ${poids}</span> / (72 × <span class="hl">${creatinine}</span>) ` +
            (sexe.toLowerCase().startsWith('f') ? `× <span class="hl">0.85</span> ` : '') +
            `= <span class="hl">${cg > 0 ? cg.toFixed(2) : '?'} ml/min</span>` +
            `<br><b style="color:var(--onco-accent)">CKD-EPI 2021</b> : κ=${sexe.toLowerCase().startsWith('f') ? '0.7' : '0.9'} | ratio=${creatinine > 0 ? (creatinine/(sexe.toLowerCase().startsWith('f') ? 0.7 : 0.9)).toFixed(3) : '?'} → <span class="hl">${ckd > 0 ? ckd.toFixed(2) : '?'} ml/min/1.73m²</span>`;
    } else {
        formuleBox.style.display = 'none';
    }
 
    // Alertes
    const allergies = patient.data('allergies') || '';
    renderAlertes(genAlertes(sc, dfg, allergies, poids));
 
    // Médicaments
    chargerMedicaments(sc, poids, taille, dfg);
}
 
// ================================================================
// PATIENT CHANGE
// ================================================================
function onPatientChange() {
    const opt = $patientSel.find(':selected');
    if (!opt.val()) return;
 
    // Remplir les champs depuis le dataset
    poidsEl.value      = opt.data('poids')     || '';
    tailleEl.value     = opt.data('taille')    || '';
    creatinineEl.value = opt.data('creatinine')|| '';
 
    // Fiche patient
    document.getElementById('cardPatient').style.display = 'block';
 
    const sc = calcSC(parseFloat(opt.data('taille')||0), parseFloat(opt.data('poids')||0));
 
    document.getElementById('patientInfos').innerHTML = `
        <table class="pt-table">
            <tr><th>N° Dossier</th><td><b>${opt.data('dossier') || '—'}</b></td></tr>
            <tr><th>Nom</th><td>${opt.data('nom')} ${opt.data('prenom')}</td></tr>
            <tr><th>Âge</th><td>${opt.data('age') || '—'} ans</td></tr>
            <tr><th>Sexe</th><td>${opt.data('sexe') || '—'}</td></tr>
            <tr><th>Groupe</th><td>${opt.data('groupe') || '—'}</td></tr>
            <tr><th>Cancer</th><td><b style="color:var(--onco-accent)">${opt.data('cancer') || '—'}</b></td></tr>
            <tr><th>Tél.</th><td>${opt.data('telephone') || '—'}</td></tr>
            <tr><th>SC Mosteller</th><td><b style="color:var(--onco-success)">${sc > 0 ? sc.toFixed(2) + ' m²' : '—'}</b></td></tr>
        </table>
    `;
 
    // Allergies
    const allergies = (opt.data('allergies') || '').trim();
    const cardAll = document.getElementById('cardAllergies');
    if (allergies && allergies !== 'null' && allergies !== '') {
        cardAll.style.display = 'block';
        document.getElementById('allergiesContent').innerHTML =
            `<div class="rx-alerte rx-alerte--danger">🚫 ${allergies}</div>`;
    } else {
        cardAll.style.display = 'none';
    }
 
    recalcAll();
    updateResume();
}
 
// ================================================================
// PROTOCOLE CHANGE
// ================================================================
function onProtocoleChange() {
    const opt = $protocoleSel.find(':selected');
    if (!opt.val()) {
        document.getElementById('protocoleInfos').innerHTML = '';
        return;
    }
    document.getElementById('protocoleInfos').innerHTML = `
        <div class="rx-alerte rx-alerte--info">
            <div>
                <b style="color:var(--onco-accent)">${opt.data('nom')}</b><br>
                <span>Cycle : ${opt.data('cycle') || '—'} &nbsp;·&nbsp; Fréquence : ${opt.data('frequence') || '—'}</span><br>
                <small>${opt.data('description') || ''}</small>
            </div>
        </div>
    `;
    recalcAll();
    updateResume();
}
 
// ================================================================
// CHARGER MÉDICAMENTS (AJAX)
// ================================================================
function chargerMedicaments(sc, poids, taille, dfg) {
    const protocoleId = $protocoleSel.val();
    if (!protocoleId) {
        medContainer.innerHTML = '<div class="rx-alerte rx-alerte--info">Sélectionnez un protocole.</div>';
        return;
    }
 
    document.getElementById('loadingOverlay').classList.add('active');
 
    fetch(`{{ url('oncologie/protocoles') }}/${protocoleId}/medicaments`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('loadingOverlay').classList.remove('active');
 
        if (!data.length) {
            medContainer.innerHTML = '<div class="rx-alerte rx-alerte--warning">Aucun médicament associé à ce protocole.</div>';
            return;
        }
 
        let html = '';
 
        data.forEach(med => {
            const result = calcDose(med, poids, taille, sc, dfg);
 
            html += `
            <div class="med-card">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:.75rem;">
                    <div>
                        <div class="med-card__name">💊 ${escHtml(med.nom)}</div>
                        <div class="med-card__meta">
                            Méthode : <b>${escHtml(med.type_calcul)}</b>
                            &nbsp;·&nbsp; Dose std : ${med.dose_standard} ${result.unite}
                            ${med.dose_max ? `&nbsp;·&nbsp; Dose max : <span style="color:var(--onco-warning)">${med.dose_max} mg</span>` : ''}
                        </div>
                        <div class="med-card__formule">${result.formule}</div>
                        ${result.plafonnee ? `<div class="med-card__limit">⚠️ Dose plafonnée à ${med.dose_max} mg</div>` : ''}
                    </div>
                    <div style="text-align:right;">
                        <div class="med-card__dose">${result.dose}</div>
                        <div style="font-size:.72rem; color:var(--onco-muted)">mg</div>
                    </div>
                </div>
                <input type="hidden" name="medicaments[${med.id}][dose]"    value="${result.dose}">
                <input type="hidden" name="medicaments[${med.id}][methode]" value="${escHtml(med.type_calcul)}">
                <input type="hidden" name="medicaments[${med.id}][formule]" value="${escHtml(result.formule)}">
            </div>`;
        });
 
        medContainer.innerHTML = html;
        updateResume();
    })
    .catch(err => {
        document.getElementById('loadingOverlay').classList.remove('active');
        console.error(err);
        medContainer.innerHTML = '<div class="rx-alerte rx-alerte--danger">Erreur de chargement des médicaments.</div>';
    });
}
 
// ================================================================
// CALCUL DE DOSE PAR MÉDICAMENT
// ================================================================
function calcDose(med, poids, taille, sc, dfg) {
    let dose = 0;
    let formule = '';
    let unite  = 'mg';
    let plafonnee = false;
 
    switch (med.type_calcul) {
 
        case 'kg':
            // Dose totale = dose_standard × poids
            dose    = med.dose_standard * poids;
            formule = `${med.dose_standard} mg/kg × ${poids} kg`;
            unite   = 'mg/kg';
            break;
 
        case 'm2':
            // Dose totale = dose_standard × SC (Mosteller)
            dose    = med.dose_standard * sc;
            formule = `${med.dose_standard} mg/m² × ${sc.toFixed(2)} m² (Mosteller)`;
            unite   = 'mg/m²';
            break;
 
        case 'taille':
            dose    = med.dose_standard * taille;
            formule = `${med.dose_standard} mg/cm × ${taille} cm`;
            unite   = 'mg/cm';
            break;
 
        case 'fixe':
            dose    = med.dose_standard;
            formule = 'Dose fixe (indépendante des paramètres)';
            unite   = 'mg';
            break;
 
        case 'auc':
            // Calvert : Dose = AUC × (DFG + 25)
            dose    = calcCalvert(med.dose_standard, dfg);
            formule = `Calvert : AUC ${med.dose_standard} × (DFG ${dfg.toFixed(1)} + 25)`;
            unite   = 'AUC';
            break;
 
        default:
            dose    = med.dose_standard;
            formule = 'Dose standard';
            unite   = 'mg';
    }
 
    // Plafonnement si dose_max défini
    if (med.dose_max && dose > med.dose_max) {
        dose      = med.dose_max;
        plafonnee = true;
        formule  += ` → plafonnée à ${med.dose_max} mg`;
    }
 
    return {
        dose:      Number(dose).toFixed(2),
        formule,
        unite,
        plafonnee,
    };
}
 
// ================================================================
// RÉSUMÉ DYNAMIQUE
// ================================================================
function updateResume() {
    const patient  = $patientSel.find(':selected');
    const protocole = $protocoleSel.find(':selected');
    const card = document.getElementById('cardResume');
    const box  = document.getElementById('resumeContent');
 
    if (!patient.val()) { card.style.display = 'none'; return; }
    card.style.display = 'block';
 
    box.innerHTML = `
        <div style="font-size:.82rem;">
            <b style="color:var(--onco-accent)">${patient.data('nom')} ${patient.data('prenom')}</b>
            <div style="color:var(--onco-muted)">Dossier ${patient.data('dossier')}</div>
            <div style="color:var(--onco-muted)">Cancer : ${patient.data('cancer') || '—'}</div>
            <hr class="rx-separator" style="margin:.65rem 0">
            <div>SC : <b style="color:var(--onco-success)">${surfaceEl.value || '—'} m²</b></div>
            <div>DFG : <b style="color:var(--onco-accent)">${clairEl.value || '—'} ml/min</b></div>
            ${protocole.val() ? `<hr class="rx-separator" style="margin:.65rem 0"><div><b>${protocole.data('nom')}</b></div><div style="color:var(--onco-muted)">Cycle : ${protocole.data('cycle') || '—'}</div>` : ''}
        </div>
    `;
}
 
// ================================================================
// EVENT LISTENERS
// ================================================================
poidsEl.addEventListener('input',      recalcAll);
tailleEl.addEventListener('input',     recalcAll);
creatinineEl.addEventListener('input', recalcAll);
 
// ================================================================
// UTILITAIRES
// ================================================================
function escHtml(s) {
    if (!s) return '';
    return String(s)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
 
// Soumission : vérification basique
document.getElementById('rxForm').addEventListener('submit', function(e) {
    if (!$patientSel.val() || !$protocoleSel.val()) {
        e.preventDefault();
        alert('Veuillez sélectionner un patient et un protocole.');
    }
});
</script>
@endpush