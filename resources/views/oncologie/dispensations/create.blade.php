@extends('layouts.app')
@section('title', 'Nouvelle Dispensation')
 
@push('styles')
<style>
:root { --bg:#0f172a; --surface:#1e293b; --border:#334155; --accent:#2a9d8f; --text:#e2e8f0; --muted:#94a3b8; --radius:.75rem; }
body { background:var(--bg); color:var(--text); }
 
.rx-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; margin-bottom:1.25rem; box-shadow:0 4px 20px rgba(0,0,0,.4); }
.rx-head { padding:.7rem 1rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; border-bottom:1px solid var(--border); background:rgba(255,255,255,.03); }
.rx-body { padding:1.1rem; }
 
.rx-label { display:block; font-size:.72rem; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:.35rem; }
.rx-input { width:100%; background:rgba(255,255,255,.05); border:1px solid var(--border); border-radius:.5rem; color:var(--text); padding:.6rem .85rem; font-size:.9rem; transition:border-color .2s; }
.rx-input:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(42,157,143,.15); }
.rx-input option { background:var(--surface); }
 
.btn-p { background:linear-gradient(135deg,var(--accent),#21867a); color:white; border:none; border-radius:.6rem; padding:.7rem 1.4rem; font-weight:700; font-size:.9rem; cursor:pointer; display:inline-flex; align-items:center; gap:.4rem; transition:all .2s; width:100%; justify-content:center; }
.btn-p:hover { transform:translateY(-1px); }
.btn-s { background:var(--surface); border:1px solid var(--border); color:var(--muted); border-radius:.6rem; padding:.7rem 1.2rem; font-weight:600; font-size:.9rem; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:.4rem; transition:all .2s; width:100%; justify-content:center; }
.btn-s:hover { border-color:var(--accent); color:var(--accent); }
 
.stock-bar { height:8px; background:rgba(255,255,255,.07); border-radius:99px; overflow:hidden; margin-top:.4rem; }
.stock-fill { height:100%; border-radius:99px; transition:width .4s ease; }
 
.info-row { display:flex; justify-content:space-between; align-items:center; padding:.45rem 0; border-bottom:1px solid rgba(255,255,255,.05); font-size:.84rem; }
.info-row:last-child { border-bottom:none; }
.info-lbl { color:var(--muted); font-size:.78rem; }
.info-val { font-weight:700; }
 
.fifo-badge { display:inline-flex; align-items:center; gap:.3rem; padding:.25rem .6rem; background:rgba(6,182,212,.1); border:1px solid rgba(6,182,212,.25); border-radius:.4rem; font-size:.7rem; font-weight:700; color:#67e8f9; font-family:monospace; }
 
.alert-box { padding:.75rem 1rem; border-radius:.5rem; font-size:.84rem; font-weight:600; margin-bottom:1rem; }
.alert-success { background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.3); color:#4ade80; }
.alert-error   { background:rgba(239,68,68,.1);  border:1px solid rgba(239,68,68,.3);  color:#f87171; }
.alert-warn    { background:rgba(245,158,11,.1); border:1px solid rgba(245,158,11,.3); color:#fcd34d; }
</style>
@endpush
 
@section('content')
<div class="container-fluid py-4" style="max-width:900px;">
 
    {{-- HERO --}}
    <div style="background:linear-gradient(135deg,var(--surface) 0%,rgba(42,157,143,.06) 100%);
                border:1px solid var(--border); border-radius:var(--radius);
                padding:1.25rem 1.5rem; margin-bottom:1.5rem;">
        <h1 style="font-size:1.35rem; font-weight:800; color:var(--accent); margin:0;">
            💉 Nouvelle Dispensation FIFO
        </h1>
        <p style="color:var(--muted); font-size:.84rem; margin:.3rem 0 0;">
            Sortie de stock automatique · Premier Entré Premier Sorti · Traçabilité par lot
        </p>
    </div>
 
    {{-- ALERTS --}}
    @if(session('error'))
    <div class="alert-box alert-error">❌ {{ session('error') }}</div>
    @endif
    @if(session('success'))
    <div class="alert-box alert-success">✅ {{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert-box alert-error">
        @foreach($errors->all() as $e)<div>⚠️ {{ $e }}</div>@endforeach
    </div>
    @endif
 
    <form action="{{ route('oncologie.dispensations.store') }}" method="POST" id="dispForm">
        @csrf
 
        <div style="display:grid; grid-template-columns:1fr 320px; gap:1.25rem; align-items:start;">
 
            {{-- MAIN --}}
            <div>
 
                {{-- Prescription --}}
                <div class="rx-card">
                    <div class="rx-head" style="color:#a5b4fc;">📋 Prescription validée</div>
                    <div class="rx-body">
                        <label class="rx-label">Sélectionner une prescription *</label>
                        <select name="prescription_id" id="prescriptionSel" class="rx-input" required>
                            <option value="">— Choisir une prescription —</option>
                            @foreach($prescriptions as $pres)
                            <option value="{{ $pres->id }}"
                                data-patient="{{ optional($pres->patient)->nom }} {{ optional($pres->patient)->prenom }}"
                                data-dossier="{{ optional($pres->patient)->numero_dossier }}"
                                data-cancer="{{ optional($pres->patient)->type_cancer }}"
                                data-date="{{ optional($pres->date_prescription)->format('d/m/Y') }}">
                                {{ optional($pres->patient)->nom }}
                                {{ optional($pres->patient)->prenom }}
                                — {{ optional($pres->date_prescription)->format('d/m/Y') }}
                            </option>
                            @endforeach
                        </select>
 
                        {{-- Panel prescription --}}
                        <div id="prescriptionPanel" style="display:none; margin-top:1rem;
                             background:rgba(129,140,248,.06); border:1px solid rgba(129,140,248,.2);
                             border-radius:.6rem; padding:.85rem;">
                            <div id="prescriptionInfo"></div>
                        </div>
                    </div>
                </div>
 
                {{-- Médicament --}}
                <div class="rx-card">
                    <div class="rx-head" style="color:#5eead4;">💊 Médicament</div>
                    <div class="rx-body">
                        <label class="rx-label">Médicament disponible en stock *</label>
                        <select name="medicament_id" id="medicamentSel" class="rx-input" required>
                            <option value="">— Sélectionner —</option>
                            @foreach($medicaments as $med)
                            <option value="{{ $med->id }}"
                                data-stock="{{ $med->stockActuel() }}"
                                data-nom="{{ $med->nom }}">
                                {{ $med->nom }} — Stock : {{ $med->stockActuel() }} unités
                            </option>
                            @endforeach
                        </select>
 
                        {{-- Stock visual --}}
                        <div id="stockPanel" style="display:none; margin-top:1rem;">
                            <div style="display:flex; justify-content:space-between; font-size:.8rem; margin-bottom:.3rem;">
                                <span style="color:var(--muted);">Stock disponible</span>
                                <span id="stockVal" style="font-weight:700; color:#4ade80;"></span>
                            </div>
                            <div class="stock-bar">
                                <div class="stock-fill" id="stockFill" style="background:linear-gradient(90deg,#22c55e,#4ade80);"></div>
                            </div>
                            <div id="stockWarning" style="display:none; margin-top:.5rem;"></div>
                        </div>
                    </div>
                </div>
 
                {{-- Quantité + notes --}}
                <div class="rx-card">
                    <div class="rx-head" style="color:#fcd34d;">📦 Quantité &amp; Notes</div>
                    <div class="rx-body">
                        <div style="display:grid; grid-template-columns:1fr 2fr; gap:1rem;">
                            <div>
                                <label class="rx-label">Quantité *</label>
                                <input type="number" name="quantite" id="quantiteInput"
                                    min="1" required class="rx-input"
                                    placeholder="Ex: 1"
                                    style="font-size:1.1rem; font-weight:700; text-align:center;">
                            </div>
                            <div>
                                <label class="rx-label">Notes (facultatif)</label>
                                <input type="text" name="notes" class="rx-input"
                                    placeholder="Observations, instructions particulières…">
                            </div>
                        </div>
 
                        <div id="quantiteWarning" style="display:none; margin-top:.75rem;"></div>
                    </div>
                </div>
 
            </div>
 
            {{-- SIDEBAR --}}
            <div>
 
                {{-- FIFO info --}}
                <div class="rx-card">
                    <div class="rx-head" style="color:#67e8f9;">
                        <span class="fifo-badge">FIFO</span>
                        Sélection de lot automatique
                    </div>
                    <div class="rx-body">
                        <p style="font-size:.8rem; color:var(--muted); margin:0 0 .75rem;">
                            Le lot le plus ancien non expiré avec stock suffisant sera sélectionné automatiquement.
                        </p>
                        <div class="info-row">
                            <span class="info-lbl">Méthode</span>
                            <span class="info-val" style="color:#67e8f9;">Premier Entré · Premier Sorti</span>
                        </div>
                        <div class="info-row">
                            <span class="info-lbl">Critère</span>
                            <span class="info-val" style="font-size:.8rem;">Date fabrication la plus ancienne</span>
                        </div>
                        <div class="info-row">
                            <span class="info-lbl">Exclusion</span>
                            <span class="info-val" style="font-size:.8rem; color:#fcd34d;">Lots expirés</span>
                        </div>
                    </div>
                </div>
 
                {{-- Résumé dynamique --}}
                <div class="rx-card" id="resumeCard" style="display:none;">
                    <div class="rx-head" style="color:var(--accent);">📑 Résumé</div>
                    <div class="rx-body" id="resumeContent"></div>
                </div>
 
                {{-- Actions --}}
                <div style="display:flex; flex-direction:column; gap:.6rem;">
                    <button type="submit" class="btn-p">
                        🚀 Effectuer la dispensation
                    </button>
                    <a href="{{ route('oncologie.dispensations.index') }}" class="btn-s">← Retour</a>
                </div>
 
            </div>
        </div>
    </form>
</div>
@endsection
 
@push('scripts')
<script>
const prescriptionSel = document.getElementById('prescriptionSel');
const medicamentSel   = document.getElementById('medicamentSel');
const quantiteInput   = document.getElementById('quantiteInput');
const prescPanel      = document.getElementById('prescriptionPanel');
const prescInfo       = document.getElementById('prescriptionInfo');
const stockPanel      = document.getElementById('stockPanel');
const stockVal        = document.getElementById('stockVal');
const stockFill       = document.getElementById('stockFill');
const stockWarning    = document.getElementById('stockWarning');
const quantiteWarning = document.getElementById('quantiteWarning');
const resumeCard      = document.getElementById('resumeCard');
const resumeContent   = document.getElementById('resumeContent');
 
let stockActuel = 0;
 
prescriptionSel.addEventListener('change', function () {
    const opt = this.selectedOptions[0];
    if (!opt || !opt.value) { prescPanel.style.display = 'none'; return; }
    prescPanel.style.display = 'block';
    prescInfo.innerHTML = `
        <div style="display:flex; flex-direction:column; gap:.3rem;">
            <div style="font-weight:800; font-size:.95rem;">${opt.dataset.patient}</div>
            <div style="font-size:.75rem; font-family:monospace; color:#a5b4fc;">${opt.dataset.dossier}</div>
            <div style="font-size:.78rem; color:var(--muted);">Cancer : ${opt.dataset.cancer} · Date : ${opt.dataset.date}</div>
        </div>`;
    updateResume();
});
 
medicamentSel.addEventListener('change', function () {
    const opt = this.selectedOptions[0];
    if (!opt || !opt.value) { stockPanel.style.display = 'none'; return; }
    stockActuel = parseInt(opt.dataset.stock) || 0;
    stockPanel.style.display = 'block';
    updateStock();
    updateResume();
});
 
quantiteInput.addEventListener('input', function () {
    updateStock();
    updateResume();
});
 
function updateStock() {
    const qte = parseInt(quantiteInput.value) || 0;
    const pct = stockActuel > 0 ? Math.min(100, (stockActuel / Math.max(stockActuel, 200)) * 100) : 0;
    stockVal.textContent = stockActuel + ' unités';
    stockFill.style.width = pct + '%';
    stockFill.style.background = stockActuel > 50 ? 'linear-gradient(90deg,#22c55e,#4ade80)' :
                                  stockActuel > 10 ? 'linear-gradient(90deg,#f59e0b,#fcd34d)' :
                                  'linear-gradient(90deg,#ef4444,#f87171)';
 
    if (qte > 0 && qte > stockActuel) {
        stockWarning.style.display = 'block';
        stockWarning.innerHTML = `<div style="background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); border-radius:.4rem; padding:.5rem .75rem; font-size:.78rem; color:#f87171; font-weight:600;">⚠️ Quantité demandée (${qte}) > stock disponible (${stockActuel})</div>`;
        quantiteWarning.style.display = 'block';
        quantiteWarning.innerHTML = stockWarning.innerHTML;
    } else {
        stockWarning.style.display = 'none';
        quantiteWarning.style.display = 'none';
    }
}
 
function updateResume() {
    const pOpt = prescriptionSel.selectedOptions[0];
    const mOpt = medicamentSel.selectedOptions[0];
    const qte  = parseInt(quantiteInput.value) || 0;
 
    if (!pOpt?.value && !mOpt?.value) { resumeCard.style.display = 'none'; return; }
    resumeCard.style.display = 'block';
 
    resumeContent.innerHTML = `
        ${pOpt?.value ? `<div style="margin-bottom:.6rem;">
            <div style="font-size:.7rem; color:var(--muted); font-weight:600; text-transform:uppercase; margin-bottom:.2rem;">Patient</div>
            <div style="font-weight:700;">${pOpt.dataset.patient || '—'}</div>
        </div>` : ''}
        ${mOpt?.value ? `<div style="margin-bottom:.6rem;">
            <div style="font-size:.7rem; color:var(--muted); font-weight:600; text-transform:uppercase; margin-bottom:.2rem;">Médicament</div>
            <div style="font-weight:700;">${mOpt.dataset.nom || '—'}</div>
        </div>` : ''}
        ${qte > 0 ? `<div>
            <div style="font-size:.7rem; color:var(--muted); font-weight:600; text-transform:uppercase; margin-bottom:.2rem;">Quantité</div>
            <div style="font-size:1.5rem; font-weight:900; color:var(--accent);">${qte}</div>
        </div>` : ''}
    `;
}
 
// Validation avant soumission
document.getElementById('dispForm').addEventListener('submit', function (e) {
    const qte = parseInt(quantiteInput.value) || 0;
    if (qte > stockActuel && stockActuel > 0) {
        e.preventDefault();
        alert('❌ Stock insuffisant — veuillez ajuster la quantité.');
    }
});
</script>
@endpush