@extends('layouts.app')
@section('title', 'Modifier Prescription #' . $prescription->id)
 
@section('content')
<div class="container-fluid py-4 px-4" style="max-width:820px;">
 
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
    --radius-sm:  8px;
    --radius-md:  12px;
    --radius-lg:  16px;
    --shadow-md:  0 4px 16px rgba(0,0,0,.08);
}
 
.edit-hero {
    background: linear-gradient(135deg, var(--c-navy), var(--c-navy-mid));
    border-radius: var(--radius-lg);
    padding: 22px 28px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 6px 24px rgba(11,29,53,.3);
}
.edit-hero__icon {
    width: 48px; height: 48px;
    background: rgba(13,148,136,.2);
    border: 1px solid rgba(13,148,136,.35);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--c-teal-lt);
    flex-shrink: 0;
}
.edit-hero__title { font-size:1.25rem; font-weight:800; color:#fff; margin:0 0 3px; letter-spacing:-.02em; }
.edit-hero__sub   { font-size:.8rem; color:rgba(255,255,255,.45); margin:0; }
 
/* ── ERROR PANEL ── */
.error-panel {
    background: #fff1f2;
    border: 1px solid #fecdd3;
    border-left: 4px solid var(--c-rose);
    border-radius: var(--radius-md);
    padding: 14px 18px;
    margin-bottom: 20px;
}
.error-panel__title { font-weight: 700; font-size: .82rem; color: #9f1239; margin-bottom: 8px; display:flex; align-items:center; gap:6px; }
.error-panel__item  { font-size: .8rem; color: #be123c; padding: 2px 0; }
 
/* ── SECTION CARD ── */
.form-section {
    background: var(--c-white);
    border-radius: var(--radius-lg);
    border: 1px solid var(--c-slate-200);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    margin-bottom: 16px;
}
.form-section__head {
    padding: 13px 20px;
    border-bottom: 1px solid var(--c-slate-100);
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    display: flex;
    align-items: center;
    gap: 8px;
}
.form-section__head--patient  { color: var(--c-sky);     border-left: 3px solid var(--c-sky); }
.form-section__head--clinical { color: var(--c-teal);    border-left: 3px solid var(--c-teal); }
.form-section__head--drugs    { color: var(--c-emerald); border-left: 3px solid var(--c-emerald); }
.form-section__head--meta     { color: #6d28d9;          border-left: 3px solid #6d28d9; }
.form-section__body { padding: 20px; }
 
/* ── INPUTS ── */
.field-label {
    display: block;
    font-size: .7rem;
    font-weight: 700;
    color: var(--c-slate-400);
    text-transform: uppercase;
    letter-spacing: .07em;
    margin-bottom: 6px;
}
.field-input {
    width: 100%;
    background: var(--c-white);
    border: 1px solid var(--c-slate-200);
    border-radius: var(--radius-sm);
    color: var(--c-slate-800);
    padding: 9px 13px;
    font-size: .88rem;
    transition: border-color .15s, box-shadow .15s;
    box-sizing: border-box;
}
.field-input:focus {
    outline: none;
    border-color: var(--c-teal);
    box-shadow: 0 0 0 3px rgba(13,148,136,.15);
}
.field-input--readonly {
    background: rgba(14,165,233,.05);
    border-color: rgba(14,165,233,.25);
    color: var(--c-sky);
    font-weight: 800;
    font-family: 'Courier New', monospace;
    cursor: not-allowed;
}
 
/* ── SC FORMULA DISPLAY ── */
.sc-formula {
    background: var(--c-slate-50);
    border: 1px solid var(--c-slate-200);
    border-radius: var(--radius-sm);
    padding: 10px 14px;
    margin-top: 10px;
    font-family: monospace;
    font-size: .8rem;
    color: var(--c-teal);
    font-weight: 600;
}
.sc-formula__label { font-size: .68rem; color: var(--c-slate-400); font-family: sans-serif; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; }
 
/* ── DRUG CARD ── */
.drug-edit-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--c-slate-50);
    border: 1px solid var(--c-slate-200);
    border-radius: var(--radius-md);
    padding: 12px 16px;
    margin-bottom: 8px;
    transition: border-color .15s;
}
.drug-edit-card:hover { border-color: var(--c-teal); }
.drug-edit-card__name { font-weight: 700; font-size: .88rem; color: var(--c-slate-800); }
.drug-edit-card__dose {
    background: var(--c-white);
    border: 1px solid var(--c-slate-200);
    border-radius: var(--radius-sm);
    padding: 6px 12px;
    width: 120px;
    text-align: center;
    font-weight: 700;
    font-size: .85rem;
    color: var(--c-teal);
    font-family: monospace;
}
 
/* ── BUTTONS ── */
.btn-save {
    background: var(--c-teal);
    color: #fff;
    border: none;
    padding: 11px 28px;
    border-radius: var(--radius-md);
    font-weight: 700;
    font-size: .88rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: filter .15s, transform .12s;
}
.btn-save:hover { filter: brightness(1.1); transform: translateY(-1px); }
.btn-cancel {
    background: var(--c-slate-100);
    color: var(--c-slate-800);
    border: 1px solid var(--c-slate-200);
    padding: 11px 22px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: .88rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: background .15s;
}
.btn-cancel:hover { background: var(--c-slate-200); color: var(--c-slate-800); }
</style>
 
{{-- HERO --}}
<div class="edit-hero">
    <div class="edit-hero__icon">
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
    </div>
    <div>
        <h1 class="edit-hero__title">Modifier Prescription #{{ $prescription->id }}</h1>
        <p class="edit-hero__sub">Mise à jour des données de prescription — les doses seront recalculées automatiquement</p>
    </div>
</div>
 
{{-- ERREURS --}}
@if($errors->any())
<div class="error-panel">
    <div class="error-panel__title">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Erreurs de validation
    </div>
    @foreach($errors->all() as $e)
        <div class="error-panel__item">· {{ $e }}</div>
    @endforeach
</div>
@endif
 
<form action="{{ route('oncologie.prescriptions.update', $prescription->id) }}"
      method="POST">
    @csrf @method('PUT')
 
    {{-- ═══ PATIENT + PROTOCOLE ═══ --}}
    <div class="form-section">
        <div class="form-section__head form-section__head--patient">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Patient &amp; Protocole
        </div>
        <div class="form-section__body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="field-label">Patient *</label>
                    <select name="patient_id" id="patient_id" class="field-input" required>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}"
                                    data-poids="{{ $patient->poids }}"
                                    data-taille="{{ $patient->taille }}"
                                    {{ $prescription->patient_id == $patient->id ? 'selected' : '' }}>
                                {{ $patient->nom }} {{ $patient->prenom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="field-label">Protocole *</label>
                    <select name="protocole_id" class="field-input" required>
                        @foreach($protocoles as $protocole)
                            <option value="{{ $protocole->id }}"
                                    {{ $prescription->protocole_id == $protocole->id ? 'selected' : '' }}>
                                {{ $protocole->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="field-label">Médecin prescripteur *</label>
                    <input type="text" name="medecin_nom" class="field-input"
                           value="{{ $prescription->medecin_nom }}" required
                           placeholder="Dr. Nom Prénom">
                </div>
            </div>
        </div>
    </div>
 
    {{-- ═══ PARAMÈTRES CLINIQUES ═══ --}}
    <div class="form-section">
        <div class="form-section__head form-section__head--clinical">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            Paramètres cliniques &amp; calcul SC (Mosteller)
        </div>
        <div class="form-section__body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="field-label">Poids (kg)</label>
                    <input type="number" id="poids" step="0.1" class="field-input"
                           value="{{ optional($prescription->patient)->poids }}"
                           placeholder="ex: 68.5">
                </div>
                <div class="col-md-4">
                    <label class="field-label">Taille (cm)</label>
                    <input type="number" id="taille" step="0.1" class="field-input"
                           value="{{ optional($prescription->patient)->taille }}"
                           placeholder="ex: 172">
                </div>
                <div class="col-md-4">
                    <label class="field-label">Surface corporelle (m²)</label>
                    <input type="text" id="sc" class="field-input field-input--readonly"
                           value="{{ $prescription->surface_corporelle }}" readonly>
                </div>
            </div>
            <div class="sc-formula" id="scFormula">
                <div class="sc-formula__label">Formule Mosteller</div>
                <span id="scFormulaText">SC = √( P × T / 3600 )</span>
            </div>
        </div>
    </div>
 
    {{-- ═══ MÉDICAMENTS ═══ --}}
    <div class="form-section">
        <div class="form-section__head form-section__head--drugs">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 3-7-3 7-3 7 3z"/><path d="M5 6v9l7 3 7-3V6"/></svg>
            Médicaments &amp; doses calculées
        </div>
        <div class="form-section__body">
            <div id="medicaments_container">
                @foreach($prescription->details as $detail)
                <div class="drug-edit-card">
                    <div>
                        <div class="drug-edit-card__name">{{ $detail->medicament->nom }}</div>
                        <div style="font-size:.72rem;color:var(--c-slate-400);margin-top:2px;">
                            Dose standard : {{ $detail->dose_standard ?? '—' }} mg/m²
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <input type="text" class="drug-edit-card__dose" id="display_{{ $detail->medicament_id }}"
                               value="{{ $detail->dose_calculee }} mg" readonly>
                        <input type="hidden"
                               name="medicaments[{{ $detail->medicament_id }}]"
                               id="hidden_{{ $detail->medicament_id }}"
                               value="{{ $detail->dose_calculee }}"
                               data-dose_standard="{{ $detail->dose_standard ?? 0 }}">
                    </div>
                </div>
                @endforeach
            </div>
            <div style="margin-top:8px;font-size:.75rem;color:var(--c-slate-400);">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Les doses sont recalculées automatiquement lors de la modification du poids ou de la taille.
            </div>
        </div>
    </div>
 
    {{-- ═══ DATE ═══ --}}
    <div class="form-section">
        <div class="form-section__head form-section__head--meta">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Date de prescription
        </div>
        <div class="form-section__body">
            <div class="col-md-4" style="padding:0;">
                <label class="field-label">Date *</label>
                <input type="date" name="date_prescription" class="field-input"
                       value="{{ optional($prescription->date_prescription)->format('Y-m-d') }}" required>
            </div>
        </div>
    </div>
 
    {{-- ACTIONS --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:6px;">
        <button type="submit" class="btn-save">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Enregistrer les modifications
        </button>
        <a href="{{ route('oncologie.prescriptions.index') }}" class="btn-cancel">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Annuler
        </a>
    </div>
</form>
 
</div>
 
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const patientSelect = document.getElementById('patient_id');
    const poidsInput    = document.getElementById('poids');
    const tailleInput   = document.getElementById('taille');
    const scInput       = document.getElementById('sc');
    const scFormulaText = document.getElementById('scFormulaText');
 
    function calculerSC(p, t) {
        return Math.sqrt((p * t) / 3600).toFixed(2);
    }
 
    function majSC() {
        const patient = patientSelect.selectedOptions[0];
        let poids  = parseFloat(poidsInput.value)  || parseFloat(patient?.dataset.poids  || 0);
        let taille = parseFloat(tailleInput.value) || parseFloat(patient?.dataset.taille || 0);
        if (!parseFloat(poidsInput.value)  && poids)  poidsInput.value  = poids;
        if (!parseFloat(tailleInput.value) && taille) tailleInput.value = taille;
        if (poids && taille) {
            const sc = calculerSC(poids, taille);
            scInput.value = sc;
            scFormulaText.textContent = `SC = √( ${poids} × ${taille} / 3600 ) = ${sc} m²`;
        } else {
            scInput.value = '';
            scFormulaText.textContent = 'SC = √( P × T / 3600 )';
        }
    }
 
    function recalculerDoses() {
        majSC();
        const sc = parseFloat(scInput.value) || 0;
        document.querySelectorAll('#medicaments_container input[type="hidden"]').forEach(input => {
            const std = parseFloat(input.dataset.dose_standard || 0);
            if (std > 0 && sc > 0) {
                const dose = (sc * std).toFixed(2);
                input.value = dose;
                const medId = input.id.replace('hidden_', '');
                const display = document.getElementById('display_' + medId);
                if (display) display.value = dose + ' mg';
            }
        });
    }
 
    patientSelect.addEventListener('change', () => { majSC(); recalculerDoses(); });
    poidsInput.addEventListener('input',    recalculerDoses);
    tailleInput.addEventListener('input',   recalculerDoses);
 
    // Init
    majSC();
});
</script>
@endpush
 
@endsection