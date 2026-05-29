@extends('layouts.app')
@section('title', 'Gestion des Lots')

@section('content')
@php
    $expired = $lots->filter(fn($l) => $l->estExpire());
    $rupture = $lots->filter(fn($l) => !$l->estExpire() && $l->stockRestant() <= 0);
    $soon    = $lots->filter(fn($l) => !$l->estExpire() && $l->stockRestant() > 0 && $l->expireBientot(90));
@endphp

@if($expired->count())
    <div style="background:#ffe5e5; border-left:5px solid #e63946; padding:12px;
                border-radius:10px; color:#7a1c1c; font-weight:600; margin-bottom:10px;">
        ⛔ {{ $expired->count() }} lot(s) expiré(s)
    </div>
@endif
@if($rupture->count())
    <div style="background:#ffe5e5; border-left:5px solid #e63946; padding:12px;
                border-radius:10px; color:#7a1c1c; font-weight:600; margin-bottom:10px;">
        🚨 {{ $rupture->count() }} lot(s) en rupture de stock
    </div>
@endif
@if($soon->count())
    <div style="background:#fff3cd; border-left:5px solid #f4a261; padding:12px;
                border-radius:10px; color:#7a4e00; font-weight:600; margin-bottom:10px;">
        ⚠ {{ $soon->count() }} lot(s) expirent bientôt (moins de 3 mois)
    </div>
@endif

<div style="display:flex; justify-content:space-between; align-items:center;
            background:white; padding:16px; border-radius:12px; margin-bottom:16px;">
    <div>
        <h2 style="margin:0; font-weight:800;">📦 Gestion des Lots</h2>
        <p style="margin:0; font-size:13px; color:#6b7280;">
            @isset($medicament) Médicament : {{ $medicament->nom }} @endisset
        </p>
    </div>
    <a href="{{ route('oncologie.lots.create') }}"
       style="background:#2a9d8f; color:white; padding:10px 16px;
              border-radius:10px; text-decoration:none; font-weight:700;">
        ➕ Ajouter un Lot
    </a>
</div>

<div style="background:white; padding:14px; border-radius:14px;
            box-shadow:0 6px 18px rgba(0,0,0,0.06); overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse; min-width:800px;">
        <thead style="background:#1f2937; color:white;">
            <tr>
                @foreach(['Médicament','N° Lot','Stock initial','Stock restant','Expiration','Statut','Actions'] as $h)
                    <th style="padding:13px; text-align:center;">{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($lots as $lot)
            @php
                $status = $lot->statutAlerte();
                $rowBg  = match(true) {
                    $status==='danger' && $lot->estExpire() => '#fff0f0',
                    $status==='danger'                      => '#fff3f3',
                    $status==='warning'                     => '#fff8f0',
                    default                                 => '',
                };
            @endphp
            <tr style="background:{{ $rowBg }}; border-bottom:1px solid #e5e7eb;"
                onmouseover="this.style.background='#f0f9ff'"
                onmouseout="this.style.background='{{ $rowBg }}'">
                <td style="padding:13px; text-align:center; font-weight:700;">
                    {{ $lot->medicament->nom ?? '-' }}
                </td>
                <td style="padding:13px; text-align:center; color:#6b7280;">
                    #{{ $lot->numero }}
                </td>
                <td style="padding:13px; text-align:center;">{{ $lot->quantite_initiale }}</td>
                <td style="padding:13px; text-align:center; font-weight:700;">
                    {{ $lot->stockRestant() }}
                </td>
                <td style="padding:13px; text-align:center; color:#6b7280;">
                    {{ $lot->date_expiration->format('d/m/Y') }}
                </td>
                <td style="padding:13px; text-align:center;">
                    @if($status==='danger' && $lot->estExpire())
                        <span style="background:#9b2226; color:white; padding:5px 10px; border-radius:8px; font-size:12px;">⛔ Expiré</span>
                    @elseif($status==='danger')
                        <span style="background:#e63946; color:white; padding:5px 10px; border-radius:8px; font-size:12px;">🚨 Rupture</span>
                    @elseif($status==='warning')
                        <span style="background:#f4a261; color:white; padding:5px 10px; border-radius:8px; font-size:12px;">⚠ Bientôt</span>
                    @else
                        <span style="background:#2a9d8f; color:white; padding:5px 10px; border-radius:8px; font-size:12px;">✔ OK</span>
                    @endif
                </td>
                <td style="padding:10px; text-align:center;">
                    <div style="display:flex; gap:5px; justify-content:center;">
                        <a href="{{ route('oncologie.lots.show', $lot) }}"
                           style="background:#3b82f6; color:white; padding:6px 9px;
                                  border-radius:7px; text-decoration:none;">👁</a>
                        <a href="{{ route('oncologie.lots.edit', $lot) }}"
                           style="background:#f4a261; color:white; padding:6px 9px;
                                  border-radius:7px; text-decoration:none;">✏</a>
                        <form action="{{ route('oncologie.lots.destroy', $lot) }}"
                              method="POST" onsubmit="return confirm('Supprimer ce lot ?')">
                            @csrf @method('DELETE')
                            <button style="background:#e63946; color:white; border:none;
                                           padding:6px 9px; border-radius:7px; cursor:pointer;">🗑</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; padding:24px; color:#6b7280;">
                    Aucun lot disponible
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection