<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prescription #{{ $prescription->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size:12px; color:#333; }
        .header { text-align:center; margin-bottom:20px; border-bottom:2px solid #2a9d8f; padding-bottom:10px; }
        .header h2 { margin:0; color:#264653; }
        .info p { margin:4px 0; }
        .badge { padding:3px 8px; border-radius:5px; color:white; }
        .success { background:#2a9d8f; }
        .danger  { background:#e76f51; }
        .warning { background:#f4a261; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th { background:#264653; color:white; padding:8px; border:1px solid #ddd; }
        td { padding:8px; border:1px solid #ddd; }
        .footer { margin-top:30px; font-size:10px; color:#9ca3af; }
        .signature { margin-top:50px; text-align:right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>🏥 Service Oncologie — CLCC Draâ Ben Khedda</h2>
        <p style="color:#6b7280; font-size:11px;">Prescription médicale — {{ now()->format('d/m/Y') }}</p>
    </div>

    <div class="info">
        <p><strong>Prescription # :</strong> {{ $prescription->id }}</p>
        <p><strong>Patient :</strong> {{ $prescription->patient->nom }} {{ $prescription->patient->prenom }}</p>
        <p><strong>Date :</strong> {{ optional($prescription->date_prescription)->format('d/m/Y') }}</p>
        <p><strong>Médecin :</strong> {{ $prescription->medecin_nom ?? optional($prescription->medecin)->name ?? '-' }}</p>
        <p><strong>Protocole :</strong> {{ optional($prescription->protocole)->nom ?? 'Aucun' }}</p>
        <p><strong>Statut :</strong>
            @if($prescription->statut === 'validee')
                <span class="badge success">Validée</span>
            @elseif($prescription->statut === 'annulee')
                <span class="badge danger">Annulée</span>
            @else
                <span class="badge warning">En attente</span>
            @endif
        </p>
    </div>

    <h3>💊 Traitement</h3>
    <table>
        <thead>
            <tr><th>Médicament</th><th>Dose calculée (mg)</th></tr>
        </thead>
        <tbody>
            @foreach($prescription->details as $detail)
                <tr>
                    <td>{{ $detail->medicament->nom }}</td>
                    <td>{{ number_format($detail->dose_calculee, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Remarque : Prescription générée automatiquement par le système d'information oncologique du CLCC.</p>
    </div>

    <div class="signature">
        <p>Signature du médecin prescripteur</p>
        <br><br>______________________
    </div>
</body>
</html>