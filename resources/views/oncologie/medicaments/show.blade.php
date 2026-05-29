@extends('layouts.app')
@section('title', 'Détail médicament')

@section('content')
@php
    $stock    = $medicament->stockActuel();
    $statSt   = $medicament->statutStock();
    $statExp  = $medicament->statutExpiration();

    $colorStock = match($statSt) {
        'rupture' => ['bg'=>'#fee2e2','c'=>'#991b1b','label'=>'RUPTURE'],
        'alerte'  => ['bg'=>'#fef3c7','c'=>'#92400e','label'=>'ALERTE'],
        default   => ['bg'=>'#dcfce7','c'=>'#166534','label'=>'OK'],
    };
    $colorExp = match($statExp) {
        'expired' => ['bg'=>'#fecaca','c'=>'#7f1d1d','label'=>'EXPIRÉ'],
        'soon'    => ['bg'=>'#ffedd5','c'=>'#9a3412','label'=>'BIENTÔT'],
        default   => ['bg'=>'#dcfce7','c'=>'#166534','label'=>'OK'],
    };
@endphp

<div style="max-width:900px; margin:auto;">

    <div style="display:flex; justify-content:space-between; align-items:center;
                background:white; padding:16px; border-radius:12px; margin-bottom:16px;">
        <h2 style="margin:0; font-weight:800;">💊 Détails du médicament</h2>
        <a href="{{ route('oncologie.medicaments.index') }}"
           style="background:#334155; color:white; padding:9px 14px;
                  border-radius:9px; text-decoration:none; font-weight:600;">
            ⬅ Retour
        </a>
    </div>

    {{-- 3 CARTES --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:16px;">

        <div style="background:white; padding:16px; border-radius:14px;
                    box-shadow:0 4px 14px rgba(0,0,0,0.05); border-left:4px solid #2a9d8f;">
            <h4 style="color:#264653; margin-bottom:12px;">📦 Informations générales</h4>
            <p><strong>Nom :</strong> {{ $medicament->nom }}</p>
            <p style="margin-top:8px;"><strong>Stock actuel :</strong>
                <span style="background:{{ $colorStock['bg'] }}; color:{{ $colorStock['c'] }};
                             padding:3px 10px; border-radius:999px; font-weight:700;">
                    {{ $stock }}
                </span>
            </p>
            <p style="margin-top:8px;"><strong>Quantité minimale :</strong> {{ $medicament->quantite_min }}</p>
        </div>

        <div style="background:white; padding:16px; border-radius:14px;
                    box-shadow:0 4px 14px rgba(0,0,0,0.05); border-left:4px solid #0ea5e9;">
            <h4 style="color:#264653; margin-bottom:12px;">📅 Dates</h4>
            <p><strong>Fabrication :</strong><br>
                {{ $medicament->date_fabrication ? $medicament->date_fabrication->format('d/m/Y') : '-' }}
            </p>
            <p style="margin-top:8px;"><strong>Expiration :</strong><br>
                <span style="background:{{ $colorExp['bg'] }}; color:{{ $colorExp['c'] }};
                             padding:3px 10px; border-radius:999px; font-weight:700;">
                    {{ $medicament->date_expiration ? $medicament->date_expiration->format('d/m/Y') : '-' }}
                </span>
            </p>
        </div>

        <div style="background:white; padding:16px; border-radius:14px;
                    box-shadow:0 4px 14px rgba(0,0,0,0.05); border-left:4px solid #f59e0b;">
            <h4 style="color:#264653; margin-bottom:12px;">⚠️ Statut</h4>
            <p><strong>État stock :</strong><br>
                <span style="background:{{ $colorStock['bg'] }}; color:{{ $colorStock['c'] }};
                             padding:4px 12px; border-radius:999px; font-weight:700;">
                    {{ $colorStock['label'] }}
                </span>
            </p>
            <p style="margin-top:10px;"><strong>État expiration :</strong><br>
                <span style="background:{{ $colorExp['bg'] }}; color:{{ $colorExp['c'] }};
                             padding:4px 12px; border-radius:999px; font-weight:700;">
                    {{ $colorExp['label'] }}
                </span>
            </p>
        </div>
    </div>

    {{-- ALERTES --}}
    @if($statSt === 'rupture')
        <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:10px;
                    border-left:4px solid #ef4444; margin-bottom:10px; font-weight:600;">
            ❌ Stock en rupture totale
        </div>
    @elseif($statSt === 'alerte')
        <div style="background:#fff7ed; color:#92400e; padding:12px; border-radius:10px;
                    border-left:4px solid #f59e0b; margin-bottom:10px; font-weight:600;">
            ⚠️ Stock critique (en dessous du minimum)
        </div>
    @else
        <div style="background:#dcfce7; color:#166534; padding:12px; border-radius:10px;
                    border-left:4px solid #22c55e; margin-bottom:10px; font-weight:600;">
            ✅ Stock normal
        </div>
    @endif

    @if($statExp === 'expired')
        <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:10px;
                    border-left:4px solid #ef4444; margin-bottom:10px; font-weight:600;">
            ❌ Médicament expiré — dispensation impossible
        </div>
    @elseif($statExp === 'soon')
        <div style="background:#fff7ed; color:#92400e; padding:12px; border-radius:10px;
                    border-left:4px solid #f59e0b; margin-bottom:10px; font-weight:600;">
            ⚠️ Médicament bientôt expiré
        </div>
    @endif

    {{-- ACTIONS --}}
    <div style="display:flex; gap:10px; margin-top:16px;">
        <a href="{{ route('oncologie.medicaments.edit', $medicament->id) }}"
           style="background:#f59e0b; color:white; padding:10px 18px;
                  border-radius:10px; text-decoration:none; font-weight:700;">
            ✏️ Modifier
        </a>
        <a href="{{ route('oncologie.medicaments.lots', $medicament->id) }}"
           style="background:#8b5cf6; color:white; padding:10px 18px;
                  border-radius:10px; text-decoration:none; font-weight:700;">
            📦 Voir les lots
        </a>
        <form method="POST"
              action="{{ route('oncologie.medicaments.destroy', $medicament->id) }}"
              onsubmit="return confirm('Supprimer ce médicament ?')">
            @csrf @method('DELETE')
            <button style="background:#ef4444; color:white; border:none; padding:10px 18px;
                           border-radius:10px; font-weight:700; cursor:pointer;">
                🗑 Supprimer
            </button>
        </form>
    </div>
</div>
@endsection