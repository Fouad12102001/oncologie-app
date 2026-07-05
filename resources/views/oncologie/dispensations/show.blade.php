@extends('layouts.app')
@section('title', 'Dispensation #{{ $dispensation->id }}')
 
@push('styles')
<style>
:root { --bg:#0f172a; --surface:#1e293b; --border:#334155; --accent:#2a9d8f; --text:#e2e8f0; --muted:#94a3b8; --radius:.75rem; }
body { background:var(--bg); color:var(--text); }
.card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; margin-bottom:1rem; }
.card-head { padding:.7rem 1rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; border-bottom:1px solid var(--border); background:rgba(255,255,255,.03); }
.card-body { padding:1.1rem; }
.kv { display:flex; justify-content:space-between; align-items:center; padding:.55rem 0; border-bottom:1px solid rgba(255,255,255,.05); font-size:.875rem; }
.kv:last-child { border-bottom:none; }
.kv-lbl { color:var(--muted); font-size:.8rem; }
.kv-val { font-weight:700; }
.badge { display:inline-flex; align-items:center; padding:.2rem .65rem; border-radius:99px; font-size:.7rem; font-weight:700; }
.badge-cyan { background:rgba(6,182,212,.15); color:#67e8f9; border:1px solid rgba(6,182,212,.3); }
.badge-green { background:rgba(34,197,94,.15); color:#4ade80; border:1px solid rgba(34,197,94,.3); }
.fifo-badge { background:rgba(6,182,212,.1); border:1px solid rgba(6,182,212,.25); border-radius:.4rem; padding:.2rem .55rem; font-size:.68rem; font-weight:700; color:#67e8f9; font-family:monospace; }
</style>
@endpush
 
@section('content')
<div class="container-fluid py-4" style="max-width:700px;">
 
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:.75rem;">
        <div>
            <h1 style="font-size:1.3rem; font-weight:800; color:var(--accent); margin:0;">
                💉 Dispensation <span style="opacity:.6;">#{{ $dispensation->id }}</span>
            </h1>
            <p style="color:var(--muted); font-size:.82rem; margin:.2rem 0 0;">
                {{ $dispensation->date_formattee ?? '—' }}
                &nbsp;·&nbsp; <span class="fifo-badge">FIFO</span>
            </p>
        </div>
        <a href="{{ route('oncologie.dispensations.index') }}"
           style="background:var(--surface); border:1px solid var(--border); color:var(--muted);
                  border-radius:.6rem; padding:.55rem 1rem; font-weight:600; font-size:.84rem;
                  text-decoration:none; display:inline-flex; align-items:center; gap:.4rem;">
            ← Retour
        </a>
    </div>
 
    {{-- Patient --}}
    <div class="card">
        <div class="card-head" style="color:#a5b4fc;">👤 Patient</div>
        <div class="card-body">
            <div class="kv">
                <span class="kv-lbl">Nom complet</span>
                <span class="kv-val">
                    {{ optional(optional($dispensation->prescription)->patient)->nom ?? 'N/A' }}
                    {{ optional(optional($dispensation->prescription)->patient)->prenom ?? '' }}
                </span>
            </div>
            <div class="kv">
                <span class="kv-lbl">N° dossier</span>
                <span class="kv-val" style="font-family:monospace; color:var(--accent);">
                    {{ optional(optional($dispensation->prescription)->patient)->numero_dossier ?? '—' }}
                </span>
            </div>
            @if(optional(optional($dispensation->prescription)->patient)->type_cancer)
            <div class="kv">
                <span class="kv-lbl">Cancer</span>
                <span class="kv-val">{{ $dispensation->prescription->patient->type_cancer }}</span>
            </div>
            @endif
        </div>
    </div>
 
    {{-- Médicament + Lot --}}
    <div class="card">
        <div class="card-head" style="color:#5eead4;">💊 Médicament &amp; Lot FIFO</div>
        <div class="card-body">
            <div class="kv">
                <span class="kv-lbl">Médicament</span>
                <span class="kv-val">{{ optional($dispensation->medicament)->nom ?? 'N/A' }}</span>
            </div>
            <div class="kv">
                <span class="kv-lbl">Lot utilisé</span>
                <span class="kv-val">
                    <span class="badge badge-cyan">{{ optional($dispensation->lot)->numero ?? 'N/A' }}</span>
                </span>
            </div>
            @if($dispensation->lot && $dispensation->lot->date_expiration)
            <div class="kv">
                <span class="kv-lbl">Expiration lot</span>
                <span class="kv-val">
                    {{ \Carbon\Carbon::parse($dispensation->lot->date_expiration)->format('m/Y') }}
                </span>
            </div>
            @endif
            <div class="kv">
                <span class="kv-lbl">Quantité délivrée</span>
                <span class="kv-val">
                    <span class="badge badge-green" style="font-size:.9rem; padding:.3rem .85rem;">
                        {{ $dispensation->quantite }}
                    </span>
                </span>
            </div>
        </div>
    </div>
 
    {{-- Prescription liée --}}
    <div class="card">
        <div class="card-head" style="color:#fcd34d;">📋 Prescription</div>
        <div class="card-body">
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
                <span class="kv-val">
                    {{ optional(optional($dispensation->prescription)->date_prescription)->format('d/m/Y') ?? '—' }}
                </span>
            </div>
            <div class="kv">
                <span class="kv-lbl">Voir prescription</span>
                <span class="kv-val">
                    @if($dispensation->prescription_id)
                    <a href="{{ route('oncologie.prescriptions.show', $dispensation->prescription_id) }}"
                       style="color:var(--accent); font-size:.82rem; font-weight:700; text-decoration:none;">
                        Ouvrir →
                    </a>
                    @else —
                    @endif
                </span>
            </div>
        </div>
    </div>
 
    {{-- Pharmacien + notes --}}
    <div class="card">
        <div class="card-head" style="color:var(--muted);">ℹ️ Traçabilité</div>
        <div class="card-body">
            <div class="kv">
                <span class="kv-lbl">Pharmacien</span>
                <span class="kv-val">{{ optional($dispensation->user)->name ?? '—' }}</span>
            </div>
            <div class="kv">
                <span class="kv-lbl">Date &amp; heure</span>
                <span class="kv-val">{{ $dispensation->date_formattee ?? '—' }}</span>
            </div>
            @if($dispensation->notes)
            <div style="margin-top:.75rem; padding:.65rem .85rem; background:rgba(255,255,255,.04); border-radius:.5rem; font-size:.82rem; color:var(--muted);">
                📝 {{ $dispensation->notes }}
            </div>
            @endif
        </div>
    </div>
 
</div>
@endsection