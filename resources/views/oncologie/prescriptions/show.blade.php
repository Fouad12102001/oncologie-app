@extends('layouts.app')
@section('title', 'Détail Prescription')

@section('content')
<div style="max-width:900px; margin:auto; background:linear-gradient(135deg,#f4f8fb,#e9f2f7); padding:10px;">

    <h1 style="color:#264653; font-weight:bold; margin-bottom:16px;">
        📋 Prescription #{{ $prescription->id }}
    </h1>

    {{-- INFOS PRINCIPALES --}}
    <div style="background:white; border-left:6px solid #2a9d8f; border-radius:14px;
                padding:20px; margin-bottom:16px; box-shadow:0 4px 14px rgba(0,0,0,0.05);">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <p><strong>👤 Patient :</strong>
                {{ $prescription->patient->nom }} {{ $prescription->patient->prenom }}
            </p>
            <p><strong>📅 Date :</strong>
                {{ optional($prescription->date_prescription)->format('d/m/Y') }}
            </p>
            <p><strong>🧑‍⚕️ Médecin :</strong>
                {{ $prescription->medecin_nom ?? 'Non défini' }}
            </p>
            <p><strong>🧪 Protocole :</strong>
                <span style="background:#264653; color:white; padding:3px 10px; border-radius:8px;">
                    {{ optional($prescription->protocole)->nom ?? 'Aucun' }}
                </span>
            </p>
            <p><strong>📌 Statut :</strong>
                @if($prescription->statut === 'validee')
                    <span style="background:#2a9d8f; color:white; padding:3px 10px; border-radius:8px;">
                        ✅ Validée
                    </span>
                @elseif($prescription->statut === 'annulee')
                    <span style="background:#e76f51; color:white; padding:3px 10px; border-radius:8px;">
                        ❌ Annulée
                    </span>
                @else
                    <span style="background:#f4a261; color:white; padding:3px 10px; border-radius:8px;">
                        ⏳ En attente
                    </span>
                @endif
            </p>
            <p><strong>🔬 SC :</strong>
                {{ $prescription->surface_corporelle ?? 'N/A' }} m²
            </p>
        </div>
    </div>

    {{-- DÉTAILS MÉDICAMENTS --}}
    <h3 style="color:#264653; font-weight:bold; margin-bottom:10px;">💊 Détails du traitement</h3>

    @if($prescription->details->count() > 0)
        <div style="background:white; border-radius:12px; border:1px solid #e5e7eb;
                    overflow:hidden; margin-bottom:16px;">
            <table style="width:100%; border-collapse:collapse;">
                <thead style="background:#264653; color:white;">
                    <tr>
                        <th style="padding:10px;">Médicament</th>
                        <th style="padding:10px;">Dose calculée (mg)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescription->details as $detail)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:10px;">{{ $detail->medicament->nom }}</td>
                            <td style="padding:10px; text-align:center; font-weight:700;">
                                {{ number_format($detail->dose_calculee, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p style="color:#6b7280; margin-bottom:16px;">Aucun détail de prescription.</p>
    @endif

    {{-- DISPENSATIONS --}}
    <h3 style="color:#264653; font-weight:bold; margin-bottom:10px;">📦 Dispensations</h3>

    @if($prescription->dispensations->count() > 0)
        <div style="background:white; border-radius:12px; border:1px solid #e5e7eb;
                    overflow:hidden; margin-bottom:16px;">
            <table style="width:100%; border-collapse:collapse;">
                <thead style="background:#264653; color:white;">
                    <tr>
                        <th style="padding:10px;">Médicament</th>
                        <th style="padding:10px;">Lot</th>
                        <th style="padding:10px;">Quantité</th>
                        <th style="padding:10px;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescription->dispensations as $dispense)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:10px;">{{ $dispense->medicament->nom }}</td>
                            <td style="padding:10px;">{{ $dispense->lot->numero ?? 'N/A' }}</td>
                            <td style="padding:10px; text-align:center;">{{ $dispense->quantite }}</td>
                            <td style="padding:10px;">
                                {{ optional($dispense->date)->format('d/m/Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p style="color:#9ca3af; margin-bottom:16px;">Aucune dispensation.</p>
    @endif

    <div style="display:flex; gap:10px;">
        <a href="{{ route('oncologie.prescriptions.index') }}"
           style="background:#264653; color:white; padding:10px 18px;
                  border-radius:10px; text-decoration:none; font-weight:700;">
            ← Retour
        </a>
        <a href="{{ route('oncologie.prescriptions.pdf', $prescription->id) }}"
           style="background:#2a9d8f; color:white; padding:10px 18px;
                  border-radius:10px; text-decoration:none; font-weight:700;">
            📄 Export PDF
        </a>
    </div>
</div>
@endsection