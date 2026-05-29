@extends('layouts.app')
@section('title', 'Détail Dispensation')

@section('content')
<div style="max-width:600px; margin:auto;">

    <h1 style="color:#f472b6; font-weight:800; margin-bottom:16px;">
        📋 Détails de la Dispensation #{{ $dispensation->id }}
    </h1>

    <div style="background:#111827; border-radius:14px; padding:24px;
                box-shadow:0 8px 24px rgba(0,0,0,0.2);">

        @foreach([
            ['label'=>'Prescription (patient)', 'value'=>optional(optional($dispensation->prescription)->patient)->nom.' '.optional(optional($dispensation->prescription)->patient)->prenom ?? 'N/A'],
            ['label'=>'Médicament',             'value'=>optional($dispensation->medicament)->nom ?? 'N/A'],
            ['label'=>'Lot (numéro)',            'value'=>optional($dispensation->lot)->numero ?? 'N/A'],
            ['label'=>'Quantité dispensée',      'value'=>$dispensation->quantite],
            ['label'=>'Date',                    'value'=>optional($dispensation->date)->format('d/m/Y H:i') ?? 'N/A'],
        ] as $item)
            <div style="display:flex; justify-content:space-between; align-items:center;
                        padding:12px 0; border-bottom:1px solid #374151;">
                <span style="color:#9ca3af; font-size:13px;">{{ $item['label'] }}</span>
                <span style="color:white; font-weight:700;">{{ $item['value'] }}</span>
            </div>
        @endforeach
    </div>

    <a href="{{ route('oncologie.dispensations.index') }}"
       style="display:inline-block; margin-top:16px; color:#22d3ee;
              text-decoration:none; font-weight:600;">
        ← Retour à la liste
    </a>
</div>
@endsection