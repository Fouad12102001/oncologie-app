@extends('layouts.app')
@section('title', 'Détail Lot')

@section('content')
@php
    $status = $lot->statutAlerte();
    $badge = match(true) {
        $status==='danger' && $lot->estExpire() => ['bg'=>'#9b2226','label'=>'⛔ Expiré'],
        $status==='danger'                      => ['bg'=>'#e63946','label'=>'🚨 Rupture'],
        $status==='warning'                     => ['bg'=>'#f4a261','label'=>'⚠ Bientôt expiré'],
        default                                 => ['bg'=>'#2a9d8f','label'=>'✔ OK'],
    };
@endphp

<div style="max-width:700px; margin:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center;
                background:white; padding:16px; border-radius:12px; margin-bottom:16px;">
        <h2 style="margin:0; font-weight:800;">📦 Détail du Lot #{{ $lot->numero }}</h2>
        <a href="{{ route('oncologie.lots.index') }}"
           style="background:#334155; color:white; padding:9px 14px;
                  border-radius:9px; text-decoration:none; font-weight:600;">⬅ Retour</a>
    </div>

    <div style="background:white; border-left:6px solid #2a9d8f; border-radius:14px;
                padding:24px; box-shadow:0 6px 20px rgba(0,0,0,0.06);">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            @foreach([
                ['label'=>'Médicament',       'value'=>$lot->medicament->nom ?? '-'],
                ['label'=>'Numéro de lot',     'value'=>$lot->numero],
                ['label'=>'Quantité initiale', 'value'=>$lot->quantite_initiale],
                ['label'=>'Stock restant',     'value'=>$lot->stockRestant()],
                ['label'=>'Date fabrication',  'value'=>optional($lot->date_fabrication)->format('d/m/Y') ?? '-'],
                ['label'=>'Date expiration',   'value'=>$lot->date_expiration->format('d/m/Y')],
            ] as $item)
                <div style="background:#f9fafb; padding:12px; border-radius:10px;">
                    <div style="font-size:12px; color:#6b7280; font-weight:600; margin-bottom:4px;">
                        {{ $item['label'] }}
                    </div>
                    <div style="font-size:16px; font-weight:700; color:#1f2937;">
                        {{ $item['value'] }}
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top:16px; text-align:center;">
            <span style="background:{{ $badge['bg'] }}; color:white; padding:8px 20px;
                         border-radius:10px; font-size:15px; font-weight:700;">
                {{ $badge['label'] }}
            </span>
        </div>
    </div>

    <div style="margin-top:16px; display:flex; gap:10px;">
        <a href="{{ route('oncologie.lots.edit', $lot) }}"
           style="background:#f4a261; color:white; padding:10px 18px;
                  border-radius:10px; text-decoration:none; font-weight:700;">
            ✏️ Modifier
        </a>
        <form action="{{ route('oncologie.lots.destroy', $lot) }}"
              method="POST" onsubmit="return confirm('Supprimer ce lot ?')">
            @csrf @method('DELETE')
            <button style="background:#ef4444; color:white; border:none; padding:10px 18px;
                           border-radius:10px; font-weight:700; cursor:pointer;">
                🗑 Supprimer
            </button>
        </form>
    </div>
</div>
@endsection