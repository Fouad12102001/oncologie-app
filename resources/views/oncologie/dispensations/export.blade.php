@extends('layouts.app')
@section('title', 'Export Dispensations')

@section('content')

<h1 style="font-weight:800; margin-bottom:16px;">📥 Export des Dispensations</h1>

<div style="background:white; border-radius:14px; padding:14px;
            box-shadow:0 4px 14px rgba(0,0,0,0.06); overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse; min-width:700px;">
        <thead style="background:#264653; color:white;">
            <tr>
                <th style="padding:10px; border:1px solid #ddd;">ID</th>
                <th style="padding:10px; border:1px solid #ddd;">Prescription</th>
                <th style="padding:10px; border:1px solid #ddd;">Médicament</th>
                <th style="padding:10px; border:1px solid #ddd;">Lot</th>
                <th style="padding:10px; border:1px solid #ddd;">Quantité</th>
                <th style="padding:10px; border:1px solid #ddd;">Date</th>
            </tr>
        </thead>
        <tbody>

            @foreach($dispensations as $disp)
            <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:10px; border:1px solid #f1f5f9;">{{ $disp->id }}</td>
                <td style="padding:10px; border:1px solid #f1f5f9;">
                    {{ optional(optional($disp->prescription)->patient)->nom ?? 'N/A' }}
                </td>
                <td style="padding:10px; border:1px solid #f1f5f9;">
                    {{ optional($disp->medicament)->nom ?? 'N/A' }}
                </td>
                <td style="padding:10px; border:1px solid #f1f5f9;">
                    {{ optional($disp->lot)->numero ?? 'N/A' }}
                </td>
                <td style="padding:10px; border:1px solid #f1f5f9;">{{ $disp->quantite }}</td>
                <td style="padding:10px; border:1px solid #f1f5f9;">
                    {{ optional($disp->date)->format('d/m/Y H:i') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div style="margin-top:16px;">
    <a href="{{ route('oncologie.dispensations.index') }}"
       style="background:#264653; color:white; padding:10px 18px;
              border-radius:10px; text-decoration:none; font-weight:700;">
        ← Retour
    </a>
</div>

@endsection