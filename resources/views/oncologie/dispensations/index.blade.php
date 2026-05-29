@extends('layouts.app')
@section('title', 'Dispensations')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center;
            background:white; padding:16px; border-radius:12px; margin-bottom:16px;">
    <div>
        <h2 style="margin:0; font-weight:800;">💊 Liste des Dispensations</h2>
        <p style="margin:0; font-size:13px; color:#6b7280;">Historique FIFO complet</p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('oncologie.dispensations.create') }}"
           style="background:#2a9d8f; color:white; padding:10px 16px;
                  border-radius:10px; text-decoration:none; font-weight:700;">
            ➕ Nouvelle dispensation
        </a>
        <a href="{{ route('oncologie.dispensations.export') }}"
           style="background:#264653; color:white; padding:10px 16px;
                  border-radius:10px; text-decoration:none; font-weight:700;">
            📥 Exporter
        </a>
    </div>
</div>

<div style="background:white; padding:12px; border-radius:14px;
            box-shadow:0 6px 20px rgba(0,0,0,0.06); overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse; min-width:750px;">
        <thead style="background:#1f2937; color:white;">
            <tr>
                @foreach(['Patient','Médicament','Lot','Quantité','Date','Actions'] as $h)
                    <th style="padding:12px; text-align:left;">{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($dispensations as $disp)
            <tr style="border-bottom:1px solid #f1f5f9;"
                onmouseover="this.style.background='#f8fafc'"
                onmouseout="this.style.background=''">
                <td style="padding:12px; font-weight:700;">
                    {{ optional($disp->prescription->patient)->nom }}
                    {{ optional($disp->prescription->patient)->prenom }}
                </td>
                <td style="padding:12px;">{{ optional($disp->medicament)->nom ?? 'N/A' }}</td>
                <td style="padding:12px; color:#6b7280;">
                    {{ optional($disp->lot)->numero ?? 'N/A' }}
                </td>
                <td style="padding:12px;">
                    <span style="background:#0ea5e9; color:white; padding:4px 10px;
                                 border-radius:999px; font-weight:700;">
                        {{ $disp->quantite }}
                    </span>
                </td>
                <td style="padding:12px; color:#334155; font-weight:600;">
                    {{ optional($disp->date_formattee)->format('d/m/Y H:i') }}
                </td>
                <td style="padding:10px;">
                    <a href="{{ route('oncologie.dispensations.show', $disp) }}"
                       style="background:#3b82f6; color:white; padding:6px 10px;
                              border-radius:7px; text-decoration:none; font-size:13px;">
                        👁 Voir
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding:24px; text-align:center; color:#6b7280;">
                    Aucune dispensation enregistrée
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:14px;">{{ $dispensations->links() }}</div>

@endsection