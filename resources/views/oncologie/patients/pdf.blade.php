<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Patient — {{ $patient->nom }} {{ $patient->prenom }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header { text-align:center; margin-bottom:25px; border-bottom:2px solid #2a9d8f; padding-bottom:10px; }
        .header h2 { margin:0; color:#264653; font-size:18px; }
        .header p { margin:4px 0; color:#6b7280; font-size:11px; }
        .box { border:1px solid #e5e7eb; padding:10px 14px; margin-bottom:8px; border-radius:6px; }
        .label { font-weight:bold; color:#264653; display:inline-block; width:160px; }
        .badge { padding:3px 10px; border-radius:5px; color:white; font-size:11px; }
        .vivant { background:#2a9d8f; }
        .decede { background:#e63946; }
        .footer { margin-top:30px; font-size:10px; color:#9ca3af; text-align:center; border-top:1px solid #e5e7eb; padding-top:10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>🏥 Service Oncologie — CLCC Draâ Ben Khedda</h2>
        <p>Fiche Patient — Générée le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    @foreach([
        ['label'=>'ID', 'value'=>$patient->id],
        ['label'=>'Nom', 'value'=>$patient->nom],
        ['label'=>'Prénom', 'value'=>$patient->prenom],
        ['label'=>'Numéro dossier', 'value'=>$patient->numero_dossier],
        ['label'=>'Sexe', 'value'=>$patient->sexe],
        ['label'=>'Âge', 'value'=>$patient->age.' ans'],
        ['label'=>'Wilaya', 'value'=>$patient->wilaya ?? '-'],
        ['label'=>'Daïra', 'value'=>$patient->daira ?? '-'],
        ['label'=>'Type de cancer', 'value'=>$patient->type_cancer],
        ['label'=>'Date de naissance', 'value'=>optional($patient->date_naissance)->format('d/m/Y') ?? '-'],
    ] as $item)
        <div class="box">
            <span class="label">{{ $item['label'] }} :</span>
            {{ $item['value'] }}
        </div>
    @endforeach

    <div class="box">
        <span class="label">Statut vital :</span>
        <span class="badge {{ $patient->est_vivant ? 'vivant' : 'decede' }}">
            {{ $patient->est_vivant ? 'Vivant' : 'Décédé' }}
        </span>
    </div>

    @if($patient->date_deces)
        <div class="box">
            <span class="label">Date de décès :</span>
            {{ $patient->date_deces->format('d/m/Y') }}
        </div>
    @endif

    @if($patient->poids && $patient->taille)
        <div class="box">
            <span class="label">Surface corporelle :</span>
            {{ $patient->surface_corporelle }} m²
            ({{ $patient->poids }} kg / {{ $patient->taille }} cm)
        </div>
    @endif

    <div class="footer">
        CLCC Draâ Ben Khedda — Document confidentiel — Dossier médical
    </div>
</body>
</html>