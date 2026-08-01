@extends('layouts.app')

@section('title', 'Nouvelle Dispensation')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
/* ========== TOKENS — THÈME CLAIR PHARMACIE ONCOLOGIQUE (identique Prescriptions) ========== */
:root {
    --onco-bg:        #f0f4f8;
    --onco-surface:   #ffffff;
    --onco-card-bg:   #ffffff;
    --onco-input-bg:  #f8fafc;
    --onco-border:    #cbd5e1;
    --onco-accent:    #0284c7;
    --onco-success:   #059669;
    --onco-warning:   #d97706;
    --onco-danger:    #dc2626;
    --onco-info:      #4f46e5;
    --onco-text:      #0f172a;
    --onco-muted:     #475569;
    --radius:         0.75rem;
    --shadow:         0 2px 12px rgba(15,23,42,.08);
}
body { background: var(--onco-bg); color: var(--onco-text); }

.rx-grid { display: grid; grid-template-columns: 1fr 320px; gap: 1.25rem; align-items: start; }
@media (max-width: 1000px) { .rx-grid { grid-template-columns: 1fr; } }

.rx-card { background: var(--onco-card-bg); border: 1px solid var(--onco-border); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); margin-bottom: 1.25rem; }
.rx-card__head { display:flex; align-items:center; gap:.5rem; padding:.75rem 1.1rem; font-size:.8rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; background: rgba(255,255,255,.04); border-bottom: 1px solid var(--onco-border); }
.rx-card__head--accent  { color: var(--onco-accent); }
.rx-card__head--success { color: var(--onco-success); }
.rx-card__head--warning { color: var(--onco-warning); }
.rx-card__head--danger  { color: var(--onco-danger); }
.rx-card__head--info    { color: var(--onco-info); }
.rx-card__body { padding: 1.1rem; }

.rx-label { display:block; font-size:.75rem; font-weight:600; color: var(--onco-muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:.35rem; }
.rx-input { width:100%; background: var(--onco-input-bg); border:1px solid var(--onco-border); border-radius:.5rem; color: var(--onco-text); padding:.55rem .85rem; font-size:.9rem; transition: border-color .2s; }
.rx-input:focus { outline:none; border-color: var(--onco-accent); box-shadow: 0 0 0 3px rgba(2,132,199,.15); }
.rx-row { display:grid; grid-template-columns: 1fr 2fr; gap:.75rem; }

/* SELECT2 */
.select2-container--default .select2-selection--single { background: var(--onco-input-bg) !important; border:1px solid var(--onco-border) !important; border-radius:.5rem !important; height:42px !important; color: var(--onco-text) !important; }
.select2-container--default .select2-selection--single .select2-selection__rendered { color: var(--onco-text) !important; line-height:40px !important; padding-left:.85rem !important; }
.select2-container--default .select2-selection--single .select2-selection__arrow { top:8px !important; }
.select2-dropdown { background: var(--onco-surface) !important; border:1px solid var(--onco-border) !important; border-radius:.5rem !important; }
.select2-container--default .select2-results__option { color: var(--onco-text) !important; padding:.6rem .85rem !important; }
.select2-container--default .select2-results__option--highlighted { background: var(--onco-accent) !important; color:#fff !important; }
.select2-search__field { background: var(--onco-input-bg) !important; color: var(--onco-text) !important; border:1px solid var(--onco-border) !important; border-radius:.4rem !important; padding:.3rem .6rem !important; }

/* ALERTES */
.rx-alerte { display:flex; align-items:flex-start; gap:.5rem; padding:.7rem .9rem; border-radius:.5rem; font-size:.84rem; margin-bottom:.75rem; font-weight:600; }
.rx-alerte--danger  { background: rgba(220,38,38,.08);  border-left:3px solid var(--onco-danger);  color:#991b1b; }
.rx-alerte--warning { background: rgba(217,119,6,.08);  border-left:3px solid var(--onco-warning); color:#92400e; }
.rx-alerte--info    { background: rgba(79,70,229,.08);  border-left:3px solid var(--onco-info);    color:#3730a3; }
.rx-alerte--success { background: rgba(5,150,105,.08);  border-left:3px solid var(--onco-success); color:#065f46; }

/* JAUGE DE STOCK (dans l'esprit dfg-badge de Prescriptions) */
.stock-panel { margin-top: 1rem; }
.stock-head { display:flex; justify-content:space-between; font-size:.8rem; margin-bottom:.35rem; }
.stock-bar { height:10px; background: var(--onco-input-bg); border:1px solid var(--onco-border); border-radius:99px; overflow:hidden; }
.stock-fill { height:100%; border-radius:99px; transition: width .35s ease, background .35s ease; }
.stock-badge { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .65rem; border-radius:99px; font-size:.72rem; font-weight:700; }
.stock--ok   { background: rgba(5,150,105,.12);  color: var(--onco-success); }
.stock--low  { background: rgba(217,119,6,.12);  color: var(--onco-warning); }
.stock--crit { background: rgba(220,38,38,.12);  color: var(--onco-danger); }

/* FIFO */
.fifo-badge { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .6rem; background: rgba(2,132,199,.08); border:1px solid rgba(2,132,199,.25); border-radius:.4rem; font-size:.7rem; font-weight:700; color: var(--onco-accent); font-family: monospace; }
.info-row { display:flex; justify-content:space-between; align-items:center; padding:.5rem 0; border-bottom:1px solid var(--onco-border); font-size:.83rem; }
.info-row:last-child { border-bottom:none; }
.info-lbl { color: var(--onco-muted); font-size:.78rem; }
.info-val { font-weight:700; }

/* STEPPER QUANTITÉ */
.qty-stepper { display:flex; align-items:stretch; }
.qty-stepper button { width:42px; background: var(--onco-input-bg); border:1px solid var(--onco-border); color: var(--onco-accent); font-size:1.1rem; font-weight:800; cursor:pointer; }
.qty-stepper button:first-child { border-radius:.5rem 0 0 .5rem; }
.qty-stepper button:last-child { border-radius:0 .5rem .5rem 0; }
.qty-stepper input { border-radius:0; text-align:center; font-size:1.15rem; font-weight:800; border-left:none; border-right:none; }
.qty-stepper button:hover { background: var(--onco-accent); color:#fff; }

/* BOUTONS */
.btn-p { background: linear-gradient(135deg, var(--onco-accent), #0369a1); color:#fff; border:none; border-radius:.6rem; padding:.75rem 1.4rem; font-weight:700; font-size:.92rem; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:.4rem; width:100%; transition: transform .15s, filter .15s; }
.btn-p:hover { transform: translateY(-1px); filter: brightness(1.06); }
.btn-p:disabled { opacity:.5; cursor:not-allowed; transform:none; }
.btn-s { background: var(--onco-surface); border:1px solid var(--onco-border); color: var(--onco-muted); border-radius:.6rem; padding:.75rem 1.2rem; font-weight:600; font-size:.9rem; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; gap:.4rem; width:100%; }
.btn-s:hover { border-color: var(--onco-accent); color: var(--onco-accent); }

/* MODAL CONFIRMATION */
.rx-modal-backdrop { position:fixed; inset:0; background:rgba(15,23,42,.55); display:none; align-items:center; justify-content:center; z-index:1000; padding:1rem; }
.rx-modal-backdrop.active { display:flex; }
.rx-modal { background:#fff; border-radius:var(--radius); max-width:420px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,.3); overflow:hidden; }
.rx-modal__head { padding:1rem 1.25rem; background: var(--onco-accent); color:#fff; font-weight:800; font-size:1rem; }
.rx-modal__body { padding:1.25rem; }
.rx-modal__actions { display:flex; gap:.6rem; padding: 0 1.25rem 1.25rem; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4" style="max-width:1150px;">

    {{-- HERO --}}
    <div style="background: linear-gradient(135deg, var(--onco-card-bg) 0%, rgba(2,132,199,.05) 100%); border:1px solid var(--onco-border); border-radius: var(--radius); padding:1.25rem 1.5rem; margin-bottom:1.5rem; box-shadow: var(--shadow);">
        <h1 style="font-size:1.4rem; font-weight:800; color: var(--onco-accent); margin:0;">💉 Nouvelle Dispensation FIFO</h1>
        <p style="color: var(--onco-muted); font-size:.85rem; margin:.3rem 0 0;">Sortie de stock automatique · Premier Entré Premier Sorti · Traçabilité par lot</p>
    </div>

    {{-- ALERTES --}}
    @if(session('error'))<div class="rx-alerte rx-alerte--danger">❌ {{ session('error') }}</div>@endif
    @if(session('success'))<div class="rx-alerte rx-alerte--success">✅ {{ session('success') }}</div>@endif
    @if($errors->any())
    <div class="rx-alerte rx-alerte--danger">
        @foreach($errors->all() as $e)<div>⚠️ {{ $e }}</div>@endforeach
    </div>
    @endif

    <form action="{{ route('oncologie.dispensations.store') }}" method="POST" id="dispForm">
        @csrf
        <div class="rx-grid">

            {{-- ═══════════ COLONNE PRINCIPALE ═══════════ --}}
            <div>

                {{-- Prescription --}}
                <div class="rx-card">
                    <div class="rx-card__head rx-card__head--info">📋 Prescription validée</div>
                    <div class="rx-card__body">
                        <label class="rx-label">Sélectionner une prescription *</label>
                        <select name="prescription_id" id="prescriptionSel" class="rx-input" style="width:100%;" required>
                            <option value="">— Choisir une prescription —</option>
                            @foreach($prescriptions as $pres)
                            <option value="{{ $pres->id }}"
                                data-patient="{{ optional($pres->patient)->nom }} {{ optional($pres->patient)->prenom }}"
                                data-dossier="{{ optional($pres->patient)->numero_dossier }}"
                                data-cancer="{{ optional($pres->patient)->type_cancer }}"
                                data-allergies="{{ optional($pres->patient)->allergies }}"
                                data-date="{{ optional($pres->date_prescription)->format('d/m/Y') }}">
                                {{ optional($pres->patient)->nom }} {{ optional($pres->patient)->prenom }} — {{ optional($pres->date_prescription)->format('d/m/Y') }}
                            </option>
                            @endforeach
                        </select>

                        <div id="allergyAlert" style="display:none; margin-top:.85rem;"></div>

                        <div id="prescriptionPanel" style="display:none; margin-top:1rem; background: rgba(79,70,229,.05); border:1px solid rgba(79,70,229,.2); border-radius:.6rem; padding:.9rem;">
                            <div id="prescriptionInfo"></div>
                        </div>
                    </div>
                </div>

                {{-- Médicament --}}
                <div class="rx-card">
                    <div class="rx-card__head rx-card__head--success">💊 Médicament</div>
                    <div class="rx-card__body">
                        <label class="rx-label">Médicament disponible en stock *</label>
                        <select name="medicament_id" id="medicamentSel" class="rx-input" style="width:100%;" required>
                            <option value="">— Sélectionner —</option>
                            @foreach($medicaments as $med)
                            <option value="{{ $med->id }}" data-stock="{{ $med->stockActuel() }}" data-nom="{{ $med->nom }}">
                                {{ $med->nom }} — Stock : {{ $med->stockActuel() }} unités
                            </option>
                            @endforeach
                        </select>

                        <div id="stockPanel" class="stock-panel" style="display:none;">
                            <div class="stock-head">
                                <span style="color:var(--onco-muted);">Stock disponible</span>
                                <span id="stockBadge"></span>
                            </div>
                            <div class="stock-bar"><div class="stock-fill" id="stockFill"></div></div>
                            <div id="stockWarning" style="margin-top:.6rem;"></div>
                        </div>
                    </div>
                </div>

                {{-- Quantité + notes --}}
                <div class="rx-card">
                    <div class="rx-card__head rx-card__head--warning">📦 Quantité &amp; Notes</div>
                    <div class="rx-card__body">
                        <div class="rx-row">
                            <div>
                                <label class="rx-label">Quantité *</label>
                                <div class="qty-stepper">
                                    <button type="button" onclick="stepQty(-1)">−</button>
                                    <input type="number" name="quantite" id="quantiteInput" min="1" required class="rx-input" placeholder="1" value="1">
                                    <button type="button" onclick="stepQty(1)">+</button>
                                </div>
                            </div>
                            <div>
                                <label class="rx-label">Notes (facultatif)</label>
                                <input type="text" name="notes" id="notesInput" class="rx-input" placeholder="Observations, instructions particulières…">
                            </div>
                        </div>
                        <div id="quantiteWarning" style="margin-top:.75rem;"></div>
                        <div style="margin-top:.6rem; display:flex; gap:.4rem; flex-wrap:wrap;">
                            <button type="button" class="fifo-badge" style="cursor:pointer;" onclick="quickNote('Dispensation en urgence')">+ Urgence</button>
                            <button type="button" class="fifo-badge" style="cursor:pointer;" onclick="quickNote('Cycle complet délivré')">+ Cycle complet</button>
                            <button type="button" class="fifo-badge" style="cursor:pointer;" onclick="quickNote('Conforme au protocole')">+ Conforme protocole</button>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ═══════════ SIDEBAR ═══════════ --}}
            <div>
                {{-- FIFO info --}}
                <div class="rx-card">
                    <div class="rx-card__head rx-card__head--accent">
                        <span class="fifo-badge">FIFO</span> Sélection de lot automatique
                    </div>
                    <div class="rx-card__body">
                        <p style="font-size:.8rem; color: var(--onco-muted); margin:0 0 .75rem;">
                            Le lot le plus ancien non expiré avec stock suffisant sera sélectionné automatiquement.
                        </p>
                        <div class="info-row"><span class="info-lbl">Méthode</span><span class="info-val" style="color:var(--onco-accent);">Premier Entré · Premier Sorti</span></div>
                        <div class="info-row"><span class="info-lbl">Critère</span><span class="info-val" style="font-size:.8rem;">Date de fabrication la plus ancienne</span></div>
                        <div class="info-row"><span class="info-lbl">Exclusion</span><span class="info-val" style="font-size:.8rem; color:var(--onco-warning);">Lots expirés</span></div>
                    </div>
                </div>

                {{-- Résumé dynamique --}}
                <div class="rx-card" id="resumeCard" style="display:none;">
                    <div class="rx-card__head rx-card__head--accent">📑 Résumé</div>
                    <div class="rx-card__body" id="resumeContent"></div>
                </div>

                {{-- Actions --}}
                <div style="display:flex; flex-direction:column; gap:.6rem;">
                    <button type="button" class="btn-p" id="submitTrigger">🚀 Effectuer la dispensation</button>
                    <a href="{{ route('oncologie.dispensations.index') }}" class="btn-s">← Retour</a>
                </div>
            </div>

        </div>
    </form>
</div>

{{-- ═══════════ MODAL DE CONFIRMATION ═══════════ --}}
<div class="rx-modal-backdrop" id="confirmModal">
    <div class="rx-modal">
        <div class="rx-modal__head">Confirmer la dispensation</div>
        <div class="rx-modal__body" id="confirmBody"></div>
        <div class="rx-modal__actions">
            <button type="button" class="btn-s" onclick="closeModal()">Annuler</button>
            <button type="button" class="btn-p" onclick="doSubmit()">Confirmer</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$('#prescriptionSel, #medicamentSel').select2({ width: '100%', placeholder: '— Sélectionner —' });

const prescriptionSel = document.getElementById('prescriptionSel');
const medicamentSel   = document.getElementById('medicamentSel');
const quantiteInput   = document.getElementById('quantiteInput');
const notesInput      = document.getElementById('notesInput');
const prescPanel      = document.getElementById('prescriptionPanel');
const prescInfo       = document.getElementById('prescriptionInfo');
const allergyAlert    = document.getElementById('allergyAlert');
const stockPanel      = document.getElementById('stockPanel');
const stockBadge      = document.getElementById('stockBadge');
const stockFill       = document.getElementById('stockFill');
const stockWarning    = document.getElementById('stockWarning');
const quantiteWarning = document.getElementById('quantiteWarning');
const resumeCard      = document.getElementById('resumeCard');
const resumeContent   = document.getElementById('resumeContent');

let stockActuel = 0;

$('#prescriptionSel').on('change', function () {
    const opt = prescriptionSel.selectedOptions[0];
    if (!opt || !opt.value) { prescPanel.style.display = 'none'; allergyAlert.style.display = 'none'; updateResume(); return; }
    prescPanel.style.display = 'block';
    prescInfo.innerHTML = `
        <div style="display:flex; flex-direction:column; gap:.3rem;">
            <div style="font-weight:800; font-size:.95rem;">${opt.dataset.patient}</div>
            <div style="font-size:.75rem; font-family:monospace; color:var(--onco-info);">${opt.dataset.dossier || '—'}</div>
            <div style="font-size:.78rem; color:var(--onco-muted);">Cancer : ${opt.dataset.cancer || '—'} · Date : ${opt.dataset.date}</div>
        </div>`;

    const allergies = (opt.dataset.allergies || '').trim();
    if (allergies && allergies !== 'null') {
        allergyAlert.style.display = 'block';
        allergyAlert.innerHTML = `<div class="rx-alerte rx-alerte--danger">🚫 Allergie connue : ${allergies}</div>`;
    } else {
        allergyAlert.style.display = 'none';
    }
    updateResume();
});

$('#medicamentSel').on('change', function () {
    const opt = medicamentSel.selectedOptions[0];
    if (!opt || !opt.value) { stockPanel.style.display = 'none'; updateResume(); return; }
    stockActuel = parseInt(opt.dataset.stock) || 0;
    stockPanel.style.display = 'block';
    updateStock();
    updateResume();
});

quantiteInput.addEventListener('input', function () { updateStock(); updateResume(); });
notesInput.addEventListener('input', updateResume);

function stepQty(delta) {
    let v = parseInt(quantiteInput.value) || 0;
    v = Math.max(1, v + delta);
    quantiteInput.value = v;
    updateStock(); updateResume();
}

function quickNote(text) {
    notesInput.value = notesInput.value ? notesInput.value + ' · ' + text : text;
    updateResume();
}

function updateStock() {
    const qte = parseInt(quantiteInput.value) || 0;
    const pct = stockActuel > 0 ? Math.min(100, (stockActuel / Math.max(stockActuel, 200)) * 100) : 0;
    stockFill.style.width = pct + '%';

    let cls = 'stock--ok', color = 'linear-gradient(90deg,#059669,#10b981)';
    if (stockActuel <= 10) { cls = 'stock--crit'; color = 'linear-gradient(90deg,#dc2626,#ef4444)'; }
    else if (stockActuel <= 50) { cls = 'stock--low'; color = 'linear-gradient(90deg,#d97706,#f59e0b)'; }
    stockFill.style.background = color;
    stockBadge.innerHTML = `<span class="stock-badge ${cls}">${stockActuel} unités</span>`;

    if (qte > 0 && qte > stockActuel) {
        stockWarning.innerHTML = `<div class="rx-alerte rx-alerte--danger" style="margin:0;">⚠️ Quantité demandée (${qte}) supérieure au stock disponible (${stockActuel})</div>`;
        quantiteWarning.innerHTML = stockWarning.innerHTML;
    } else {
        stockWarning.innerHTML = '';
        quantiteWarning.innerHTML = '';
    }
}

function updateResume() {
    const pOpt = prescriptionSel.selectedOptions[0];
    const mOpt = medicamentSel.selectedOptions[0];
    const qte  = parseInt(quantiteInput.value) || 0;

    if (!pOpt?.value && !mOpt?.value) { resumeCard.style.display = 'none'; return; }
    resumeCard.style.display = 'block';

    resumeContent.innerHTML = `
        ${pOpt?.value ? `<div class="info-row"><span class="info-lbl">Patient</span><span class="info-val">${pOpt.dataset.patient || '—'}</span></div>` : ''}
        ${mOpt?.value ? `<div class="info-row"><span class="info-lbl">Médicament</span><span class="info-val">${mOpt.dataset.nom || '—'}</span></div>` : ''}
        ${qte > 0 ? `<div class="info-row"><span class="info-lbl">Quantité</span><span class="info-val" style="font-size:1.3rem; color:var(--onco-accent);">${qte}</span></div>` : ''}
        ${notesInput.value ? `<div class="info-row"><span class="info-lbl">Notes</span><span class="info-val" style="font-size:.78rem; text-align:right;">${notesInput.value}</span></div>` : ''}
    `;
}

// ================= CONFIRMATION MODALE =================
const modal = document.getElementById('confirmModal');
document.getElementById('submitTrigger').addEventListener('click', function () {
    if (!prescriptionSel.value || !medicamentSel.value) { alert('Veuillez sélectionner un patient et un protocole.'); return; }
    const qte = parseInt(quantiteInput.value) || 0;
    if (qte > stockActuel && stockActuel > 0) { alert('❌ Stock insuffisant — veuillez ajuster la quantité.'); return; }

    const pOpt = prescriptionSel.selectedOptions[0];
    const mOpt = medicamentSel.selectedOptions[0];
    document.getElementById('confirmBody').innerHTML = `
        <div class="info-row"><span class="info-lbl">Patient</span><span class="info-val">${pOpt.dataset.patient}</span></div>
        <div class="info-row"><span class="info-lbl">Médicament</span><span class="info-val">${mOpt.dataset.nom}</span></div>
        <div class="info-row"><span class="info-lbl">Quantité</span><span class="info-val" style="color:var(--onco-accent); font-size:1.2rem;">${qte}</span></div>
    `;
    modal.classList.add('active');
});
function closeModal() { modal.classList.remove('active'); }
function doSubmit() { document.getElementById('dispForm').submit(); }

// Raccourci clavier : Ctrl+Entrée pour soumettre
document.addEventListener('keydown', function (e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') { document.getElementById('submitTrigger').click(); }
});
</script>
@endpush