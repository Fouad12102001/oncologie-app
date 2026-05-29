@extends('layouts.app')
@section('title', 'Lots du médicament')

@section('content')
@php
    $expiredLots = $medicament->lots->filter(fn($l) => $l->estExpire());
    $soonLots    = $medicament->lots->filter(fn($l) => !$l->estExpire() && $l->expireBientot(90));
@endphp

<div style="max-width:900px; margin:auto;">

    <h1 style="font-weight:800; color:#1f2937; margin-bottom:16px;">
        💊 Lots du médicament : <span style="color:#2a9d8f;">{{ $medicament->nom }}</span>
    </h1>

    @if($expiredLots->count() > 0)
        <div style="background:#ffe5e5; border-left:5px solid #e63946; padding:12px;
                    border-radius:10px; color:#7a1c1c; font-weight:600; margin-bottom:10px;">
            ⛔ {{ $expiredLots->count() }} lot(s) expiré(s)
        </div>
    @endif

    @if($soonLots->count() > 0)
        <div style="background:#fff3cd; border-left:5px solid #f4a261; padding:12px;
                    border-radius:10px; color:#7a4e00; font-weight:600; margin-bottom:10px;">
            ⚠ {{ $soonLots->count() }} lot(s) expirent bientôt (moins de 3 mois)
        </div>
    @endif

    <div style="background:white; border-left:6px solid #2a9d8f; border-radius:14px;
                padding:20px; box-shadow:0 6px 16px rgba(0,0,0,0.05);">
        <table style="width:100%; border-collapse:collapse;">
            <thead style="background:#264653; color:white;">
                <tr>
                    <th style="padding:12px; text-align:left;">Lot</th>
                    <th style="padding:12px; text-align:left;">Quantité initiale</th>
                    <th style="padding:12px; text-align:left;">Stock restant</th>
                    <th style="padding:12px; text-align:left;">Fabrication</th>
                    <th style="padding:12px; text-align:left;">Expiration</th>
                    <th style="padding:12px; text-align:left;">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicament->lots as $lot)
                @php
                    $expDate = $lot->date_expiration;
                    if ($lot->estExpire()) {
                        $badge = ['bg'=>'#e63946','label'=>'⛔ Expiré'];
                    } elseif ($lot->expireBientot(90)) {
                        $badge = ['bg'=>'#f4a261','label'=>'⚠ Bientôt expiré'];
                    } else {
                        $badge = ['bg'=>'#2a9d8f','label'=>'✔ Valide'];
                    }
                @endphp
                <tr style="border-bottom:1px solid #eee;"
                    onmouseover="this.style.background='#f9fbfd'"
                    onmouseout="this.style.background=''">
                    <td style="padding:12px; font-weight:700;">{{ $lot->numero }}</td>
                    <td style="padding:12px;">{{ $lot->quantite_initiale }}</td>
                    <td style="padding:12px; font-weight:700;">{{ $lot->stockRestant() }}</td>
                    <td style="padding:12px; color:#6b7280;">
                        {{ $lot->date_fabrication ? $lot->date_fabrication->format('d/m/Y') : '-' }}
                    </td>
                    <td style="padding:12px; color:#6b7280;">
                        {{ $expDate->format('d/m/Y') }}
                    </td>
                    <td style="padding:12px;">
                        <span style="background:{{ $badge['bg'] }}; color:white;
                                     padding:5px 10px; border-radius:8px; font-size:12px;">
                            {{ $badge['label'] }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:24px; color:#6b7280;">
                        Aucun lot pour ce médicament
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        <a href="{{ route('oncologie.medicaments.index') }}"
           style="background:#334155; color:white; padding:10px 16px;
                  border-radius:9px; text-decoration:none; font-weight:600;">
            ⬅ Retour aux médicaments
        </a>
    </div>
</div>
@endsection