@extends('layouts.app')

@section('title', 'Dispensation #'.$dispensation->id)

@push('styles')
<style>
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

.rx-card { background: var(--onco-card-bg); border: 1px solid var(--onco-border); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); margin-bottom: 1.1rem; }
.rx-card__head { display:flex; align-items:center; gap:.5rem; padding:.75rem 1.1rem; font-size:.78rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; background: rgba(255,255,255,.04); border-bottom: 1px solid var(--onco-border); }
.rx-card__head--accent  { color: var(--onco-accent); }
.rx-card__head--success { color: var(--onco-success); }
.rx-card__head--warning { color: var(--onco-warning); }
.rx-card__head--info    { color: var(--onco-info); }
.rx-card__head--muted   { color: var(--onco-muted); }
.rx-card__body { padding: 1.1rem; }

.kv { display:flex; justify-content:space-between; align-items:center; padding:.6rem 0; border-bottom:1px solid var(--onco-border); font-size:.875rem; gap:1rem; }
.kv:last-child { border-bottom:none; }
.kv-lbl { color: var(--onco-muted); font-size:.8rem; white-space:nowrap; }
.kv-val { font-weight:700; text-align:right; }

.badge { display:inline-flex; align-items:center; gap:.3rem; padding:.22rem .7rem; border-radius:99px; font-size:.72rem; font-weight:700; }
.badge-cyan  { background: rgba(2,132,199,.1);  color: var(--onco-accent); }
.badge-green { background: rgba(5,150,105,.1);  color: var(--onco-success); }
.badge-amber { background: rgba(217,119,6,.1);  color: var(--onco-warning); }

.fifo-badge { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .6rem; background: rgba(2,132,199,.08); border:1px solid rgba(2,132,199,.25); border-radius:.4rem; font-size:.68rem; font-weight:700; color: var(--onco-accent); font-family: monospace; }

.copy-btn { background: var(--onco-input-bg); border:1px solid var(--onco-border); color: var(--onco-muted); width:24px; height:24px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; margin-left:.4rem; font-size:.75rem; }
.copy-btn:hover { border-color: var(--onco-accent); color: var(--onco-accent); }
.copy-btn.copied { background: var(--onco-success); border-color: var(--onco-success); color:#fff; }

/* TIMELINE */
.timeline { display:flex; align-items:center; justify-content:space-between; padding: .5rem .5rem 1.2rem; }
.timeline__step { flex:1; text-align:center; position:relative; }
.timeline__dot { width:32px; height:32px; border-radius:50%; background: var(--onco-success); color:#fff; display:flex; align-items:center; justify-content:center; margin:0 auto .4rem; font-weight:800; font-size:.85rem; box-shadow:0 0 0 4px rgba(5,150,105,.15); }
.timeline__label { font-size:.72rem; font-weight:700; color: var(--onco-muted); text-transform:uppercase; letter-spacing:.04em; }
.timeline__line { position:absolute; top:16px; left:-50%; width:100%; height:2px; background: var(--onco-success); z-index:-1; }
.timeline__step:first-child .timeline__line { display:none; }

.btn-s { background: var(--onco-surface); border:1px solid var(--onco-border); color: var(--onco-muted); border-radius:.6rem; padding:.55rem 1rem; font-weight:600; font-size:.84rem; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:.4rem; }
.btn-s:hover { border-color: var(--onco-accent); color: var(--onco-accent); }
.btn-a { background: var(--onco-accent); color:#fff; border:none; border-radius:.6rem; padding:.55rem 1rem; font-weight:700; font-size:.84rem; cursor:pointer; display:inline-flex; align-items:center; gap:.4rem; }
.btn-a:hover { filter: brightness(1.08); }

.note-box { margin-top:.85rem; padding:.7rem .9rem; background: var(--onco-input-bg); border:1px solid var(--onco-border); border-radius:.5rem; font-size:.83rem; color: var(--onco-muted); }

@media print {
    .no-print { display: none !important; }
    body { background: #fff; }
    .rx-card { box-shadow: none; border: 1px solid #ccc; }
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4" style="max-width:760px;">

    <div class="no-print" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:.75rem;">
        <div>
            <h1 style="font-size:1.35rem; font-weight:800; color: var(--onco-accent); margin:0;">
                💉 Dispensation <span style="opacity:.5;">#{{ $dispensation->id }}</span>
            </h1>
            <p style="color: var(--onco-muted); font-size:.83rem; margin:.25rem 0 0;">
                {{ $dispensation->date_formattee ?? '—' }} &nbsp;·&nbsp; <span class="fifo-badge">FIFO</span>
            </p>
        </div>
        <div style="display:flex; gap:.5rem;">
            <button type="button" class="btn-a" onclick="window.print()">🖨️ Imprimer</button>
            <a href="{{ route('oncologie.dispensations.index') }}" class="btn-s">← Retour</a>
        </div>
    </div>

    {{-- Timeline traçabilité --}}
    <div class="rx-card">
        <div class="rx-card__body">
            <div class="timeline">
                <div class="timeline__step">
                    <div class="timeline__dot">1</div>
                    <div class="timeline__label">Prescription</div>
                </div>
                <div class="timeline__step">
                    <div class="timeline__line"></div>
                    <div class="timeline__dot">2</div>
                    <div class="timeline__label">Validation</div>
                </div>
                <div class="timeline__step">
                    <div class="timeline__line"></div>
                    <div class="timeline__dot">3</div>
                    <div class="timeline__label">Dispensation</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Patient --}}
    <div class="rx-card">
        <div class="rx-card__head rx-card__head--info">👤 Patient</div>
        <div class="rx-card__body">
            <div class="kv">
                <span class="kv-lbl">Nom complet</span>
                <span class="kv-val">
                    {{ optional(optional($dispensation->prescription)->patient)->nom ?? 'N/A' }}
                    {{ optional(optional($dispensation->prescription)->patient)->prenom ?? '' }}
                </span>
            </div>
            <div class="kv">
                <span class="kv-lbl">N° dossier</span>
                <span class="kv-val" style="font-family:monospace; color:var(--onco-accent);">
                    {{ optional(optional($dispensation->prescription)->patient)->numero_dossier ?? '—' }}
                    @if(optional(optional($dispensation->prescription)->patient)->numero_dossier)
                    <button type="button" class="copy-btn no-print" onclick="copyText(this,'{{ $dispensation->prescription->patient->numero_dossier }}')" title="Copier">⧉</button>
                    @endif
                </span>
            </div>
            @if(optional(optional($dispensation->prescription)->patient)->type_cancer)
            <div class="kv">
                <span class="kv-lbl">Cancer</span>
                <span class="kv-val">{{ $dispensation->prescription->patient->type_cancer }}</span>
            </div>
            @endif
            @if(optional(optional($dispensation->prescription)->patient)->allergies)
            <div style="margin-top:.75rem; padding:.6rem .85rem; background:rgba(220,38,38,.08); border-left:3px solid var(--onco-danger); border-radius:.5rem; font-size:.82rem; color:#991b1b; font-weight:600;">
                🚫 Allergie : {{ $dispensation->prescription->patient->allergies }}
            </div>
            @endif
        </div>
    </div>

    {{-- Médicament + Lot --}}
    <div class="rx-card">
        <div class="rx-card__head rx-card__head--success">💊 Médicament &amp; Lot FIFO</div>
        <div class="rx-card__body">
            <div class="kv">
                <span class="kv-lbl">Médicament</span>
                <span class="kv-val">{{ optional($dispensation->medicament)->nom ?? 'N/A' }}</span>
            </div>
            <div class="kv">
                <span class="kv-lbl">Lot utilisé</span>
                <span class="kv-val">
                    <span class="badge badge-cyan">{{ optional($dispensation->lot)->numero ?? 'N/A' }}</span>
                    @if(optional($dispensation->lot)->numero)
                    <button type="button" class="copy-btn no-print" onclick="copyText(this,'{{ $dispensation->lot->numero }}')" title="Copier">⧉</button>
                    @endif
                </span>
            </div>
            @if($dispensation->lot && $dispensation->lot->date_expiration)
                @php
                    $exp = \Carbon\Carbon::parse($dispensation->lot->date_expiration);
                    $soon = now()->diffInDays($exp, false) <= 90 && now()->lt($exp);
                @endphp
            <div class="kv">
                <span class="kv-lbl">Expiration du lot</span>
                <span class="kv-val">
                    <span class="badge {{ $soon ? 'badge-amber' : 'badge-green' }}">
                        {{ $soon ? '⚠️' : '✓' }} {{ $exp->format('m/Y') }}
                    </span>
                </span>
            </div>
            @endif
            <div class="kv">
                <span class="kv-lbl">Quantité délivrée</span>
                <span class="kv-val">
                    <span class="badge badge-green" style="font-size:.9rem; padding:.35rem .9rem;">{{ $dispensation->quantite }}</span>
                </span>
            </div>
        </div>
    </div>

    {{-- Prescription liée --}}
    <div class="rx-card">
        <div class="rx-card__head rx-card__head--warning">📋 Prescription</div>
        <div class="rx-card__body">
            <div class="kv">
                <span class="kv-lbl">Protocole</span>
                <span class="kv-val">{{ optional(optional($dispensation->prescription)->protocole)->nom ?? '—' }}</span>
            </div>
            <div class="kv">
                <span class="kv-lbl">Médecin</span>
                <span class="kv-val">{{ optional($dispensation->prescription)->medecin_nom ?? '—' }}</span>
            </div>
            <div class="kv">
                <span class="kv-lbl">Date prescription</span>
                <span class="kv-val">{{ optional(optional($dispensation->prescription)->date_prescription)->format('d/m/Y') ?? '—' }}</span>
            </div>
            <div class="kv no-print">
                <span class="kv-lbl">Voir prescription</span>
                <span class="kv-val">
                    @if($dispensation->prescription_id)
                    <a href="{{ route('oncologie.prescriptions.show', $dispensation->prescription_id) }}" style="color:var(--onco-accent); font-size:.82rem; font-weight:700; text-decoration:none;">Ouvrir →</a>
                    @else — @endif
                </span>
            </div>
        </div>
    </div>

    {{-- Pharmacien + notes --}}
    <div class="rx-card">
        <div class="rx-card__head rx-card__head--muted">ℹ️ Traçabilité</div>
        <div class="rx-card__body">
            <div class="kv">
                <span class="kv-lbl">Pharmacien</span>
                <span class="kv-val">{{ optional($dispensation->user)->name ?? '—' }}</span>
            </div>
            <div class="kv">
                <span class="kv-lbl">Date &amp; heure</span>
                <span class="kv-val">{{ $dispensation->date_formattee ?? '—' }}</span>
            </div>
            @if($dispensation->notes)
            <div class="note-box">📝 {{ $dispensation->notes }}</div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function copyText(btn, text) {
    navigator.clipboard?.writeText(text).then(() => {
        btn.classList.add('copied');
        btn.textContent = '✓';
        setTimeout(() => { btn.classList.remove('copied'); btn.textContent = '⧉'; }, 1200);
    });
}
</script>
@endpush