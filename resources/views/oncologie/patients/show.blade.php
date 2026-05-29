@extends('layouts.app')
@section('title', 'Détail Patient')

@section('content')
<div style="max-width:900px; margin:auto;">

    <div style="background:white; border-left:6px solid #2a9d8f; border-radius:14px;
                padding:24px; box-shadow:0 6px 20px rgba(0,0,0,0.06);">

        <h2 style="text-align:center; color:#264653; font-weight:800; margin-bottom:20px;">
            🧑‍⚕️ Détails du Patient
        </h2>

        <div class="row g-3">
            @foreach([
                ['label'=>'ID',              'value'=>$patient->id],
                ['label'=>'Nom complet',     'value'=>$patient->nom.' '.$patient->prenom],
                ['label'=>'Numéro dossier',  'value'=>$patient->numero_dossier],
                ['label'=>'Sexe',            'value'=>$patient->sexe],
                ['label'=>'Âge',             'value'=>$patient->age.' ans', 'badge'=>'#2a9d8f'],
                ['label'=>'Wilaya',          'value'=>$patient->wilaya ?? '-'],
                ['label'=>'Daïra',           'value'=>$patient->daira ?? '-'],
                ['label'=>'Date naissance',  'value'=>optional($patient->date_naissance)->format('d/m/Y') ?? '-'],
            ] as $item)
                <div class="col-md-4">
                    <div style="background:#f9fafa; border-radius:10px; padding:12px;">
                        <div style="font-size:12px; color:#264653; font-weight:600;">{{ $item['label'] }}</div>
                        <div style="font-size:15px; font-weight:700; margin-top:4px;">
                            @isset($item['badge'])
                                <span style="background:{{ $item['badge'] }}; color:white;
                                             padding:4px 10px; border-radius:8px;">
                                    {{ $item['value'] }}
                                </span>
                            @else
                                {{ $item['value'] }}
                            @endisset
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="col-md-4">
                <div style="background:#f9fafa; border-radius:10px; padding:12px;">
                    <div style="font-size:12px; color:#264653; font-weight:600;">Statut vital</div>
                    <div style="margin-top:4px;">
                        <span style="background:{{ $patient->est_vivant ? '#2a9d8f' : '#e63946' }};
                                     color:white; padding:4px 12px; border-radius:8px; font-weight:700;">
                            {{ $patient->est_vivant ? '🟢 Vivant' : '🔴 Décédé' }}
                        </span>
                    </div>
                </div>
            </div>

            @if($patient->date_deces)
                <div class="col-md-4">
                    <div style="background:#f9fafa; border-radius:10px; padding:12px;">
                        <div style="font-size:12px; color:#264653; font-weight:600;">Date de décès</div>
                        <div style="font-size:15px; font-weight:700; margin-top:4px;">
                            {{ $patient->date_deces->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-md-6">
                <div style="background:#f9fafa; border-radius:10px; padding:12px;">
                    <div style="font-size:12px; color:#264653; font-weight:600;">Type de cancer</div>
                    <div style="margin-top:4px;">
                        <span style="background:#e76f51; color:white; padding:4px 10px;
                                     border-radius:8px; font-weight:700;">
                            {{ $patient->type_cancer }}
                        </span>
                    </div>
                </div>
            </div>

            @if($patient->poids && $patient->taille)
                <div class="col-md-6">
                    <div style="background:#f9fafa; border-radius:10px; padding:12px;">
                        <div style="font-size:12px; color:#264653; font-weight:600;">
                            Surface corporelle
                        </div>
                        <div style="font-size:15px; font-weight:700; margin-top:4px;">
                            {{ $patient->surface_corporelle }} m²
                            <small style="color:#6b7280;">
                                ({{ $patient->poids }}kg / {{ $patient->taille }}cm)
                            </small>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div style="margin-top:20px; display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('oncologie.patients.edit', $patient) }}"
               style="background:#2a9d8f; color:white; padding:10px 18px;
                      border-radius:10px; text-decoration:none; font-weight:700;">
                ✏️ Modifier
            </a>
            <a href="{{ route('oncologie.patients.index') }}"
               style="background:#334155; color:white; padding:10px 18px;
                      border-radius:10px; text-decoration:none; font-weight:700;">
                🔙 Retour
            </a>
            <a href="{{ route('oncologie.patients.export.pdf.single', $patient->id) }}"
               style="background:#e63946; color:white; padding:10px 18px;
                      border-radius:10px; text-decoration:none; font-weight:700;">
                📄 PDF
            </a>
            <a href="{{ route('oncologie.patients.export.excel.single', $patient->id) }}"
               style="background:#28a745; color:white; padding:10px 18px;
                      border-radius:10px; text-decoration:none; font-weight:700;">
                📊 Excel
            </a>
        </div>
    </div>
</div>
@endsection