@extends('layouts.app')
@section('title', 'Prescriptions')

@section('content')

<h1 style="color:#1a3e2b; font-weight:800; margin-bottom:16px;">
    📋 Toutes les Prescriptions
</h1>

{{-- FILTRES --}}

<form method="GET" action="{{ route('oncologie.prescriptions.index') }}"
      style="background:white; padding:14px; border-radius:12px; margin-bottom:14px;
             display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;
             box-shadow:0 2px 10px rgba(0,0,0,0.04);">

```
<input type="text"
       name="patient"
       placeholder="Nom du patient"
       value="{{ request('patient') }}"
       style="padding:9px 12px; border-radius:9px; border:1px solid #264653;
              color:#1a1a1a; flex:1; min-width:180px;">

<select name="statut"
        style="padding:9px 12px; border-radius:9px; border:1px solid #264653; color:#1a1a1a;">
    <option value="">Toutes</option>
    <option value="validee" {{ request('statut')==='validee' ? 'selected' : '' }}>
        Validée
    </option>
    <option value="en_attente" {{ request('statut')==='en_attente' ? 'selected' : '' }}>
        En attente
    </option>
    <option value="annulee" {{ request('statut')==='annulee' ? 'selected' : '' }}>
        Annulée
    </option>
</select>

<input type="date"
       name="date"
       value="{{ request('date') }}"
       style="padding:9px 12px; border-radius:9px; border:1px solid #264653; color:#1a1a1a;">

<button type="submit"
        style="background:#2a9d8f; color:white; border:none;
               padding:10px 18px; border-radius:10px;
               font-weight:600; cursor:pointer;">
    Filtrer
</button>

<a href="{{ route('oncologie.prescriptions.index') }}"
   style="background:#e5e7eb; color:#374151;
          padding:10px 16px; border-radius:10px;
          text-decoration:none; font-weight:600;">
    Réinitialiser
</a>
```

</form>

{{-- BOUTONS --}}

<div style="display:flex; justify-content:space-between; align-items:center;
            flex-wrap:wrap; gap:12px; margin-bottom:14px;">

```
{{-- Ajouter Prescription : médecin uniquement --}}
@canOnco('prescriptions.create')
<a href="{{ route('oncologie.prescriptions.create') }}"
   style="background:#2a9d8f; color:white;
          padding:10px 18px; border-radius:12px;
          text-decoration:none; font-weight:600;
          display:inline-flex; align-items:center; gap:6px;">
    <span class="material-icons" style="font-size:18px;">add</span>
    Ajouter Prescription
</a>
@endcanOnco

<div style="display:flex; gap:10px;">

    {{-- Exporter --}}
    @canOnco('prescriptions.export')
    <a href="{{ route('oncologie.prescriptions.export') }}"
       style="background:#264653; color:white;
              padding:10px 18px; border-radius:12px;
              text-decoration:none; font-weight:600;
              display:inline-flex; align-items:center; gap:6px;">
        <span class="material-icons" style="font-size:18px;">download</span>
        Exporter
    </a>
    @endcanOnco

    {{-- Statistiques --}}
    @canOnco('prescriptions.stats')
    <a href="{{ route('oncologie.prescriptions.stats') }}"
       style="background:#6a4c93; color:white;
              padding:10px 18px; border-radius:12px;
              text-decoration:none; font-weight:600;
              display:inline-flex; align-items:center; gap:6px;">
        <span class="material-icons" style="font-size:18px;">bar_chart</span>
        Statistiques
    </a>
    @endcanOnco

</div>
```

</div>

{{-- TABLEAU --}}

<div style="background:white; border-radius:12px;
            border:1px solid #d1d5db;
            overflow-x:auto;
            box-shadow:0 4px 14px rgba(0,0,0,0.05);">

```
<table style="width:100%; min-width:900px; border-collapse:collapse;">

    <thead style="background:#264653; color:#f4f8fb;">
    <tr>
        <th style="padding:12px;">ID</th>
        <th style="padding:12px;">Patient</th>
        <th style="padding:12px;">Médicaments</th>
        <th style="padding:12px;">SC (m²)</th>
        <th style="padding:12px;">Dose (mg)</th>
        <th style="padding:12px;">Médecin</th>
        <th style="padding:12px;">Date</th>
        <th style="padding:12px;">Statut</th>
        <th style="padding:12px;">Actions</th>
    </tr>
    </thead>

    <tbody>

    @forelse($prescriptions as $prescription)

        <tr style="border-bottom:1px solid #e5e7eb; color:#1a1a1a;"
            onmouseover="this.style.background='#d2dce4'"
            onmouseout="this.style.background=''">

            <td style="padding:10px;">
                {{ $prescription->id }}
            </td>

            <td style="padding:10px;">
                {{ $prescription->patient->nom ?? '' }}
                {{ $prescription->patient->prenom ?? '' }}
            </td>

            <td style="padding:10px; font-size:12px;">
                {{ $prescription->medicaments_noms }}
            </td>

            <td style="padding:10px; text-align:right;">
                {{ $prescription->surface_corporelle ?? 'N/A' }}
            </td>

            <td style="padding:10px; text-align:right;">
                {{ $prescription->doses_calculees }}
            </td>

            <td style="padding:10px;">
                {{ $prescription->medecin_nom ?? optional($prescription->medecin)->name ?? '-' }}
            </td>

            <td style="padding:10px;">
                {{ optional($prescription->date_prescription)->format('d/m/Y') ?? 'N/A' }}
            </td>

            <td style="padding:10px;">

                @switch($prescription->statut)

                    @case('validee')
                    <span style="background:#dcfce7; color:#166534;
                                 padding:4px 10px; border-radius:999px;
                                 font-size:12px; font-weight:700;">
                        ✅ Validée
                    </span>
                    @break

                    @case('en_attente')
                    <span style="background:#fef3c7; color:#92400e;
                                 padding:4px 10px; border-radius:999px;
                                 font-size:12px; font-weight:700;">
                        ⏳ En attente
                    </span>
                    @break

                    @case('annulee')
                    <span style="background:#fee2e2; color:#991b1b;
                                 padding:4px 10px; border-radius:999px;
                                 font-size:12px; font-weight:700;">
                        ❌ Annulée
                    </span>
                    @break

                @endswitch

            </td>

            <td style="padding:8px;">

                <div style="display:flex; gap:5px; flex-wrap:wrap;">

                    <a href="{{ route('oncologie.prescriptions.show', $prescription->id) }}"
                       style="background:#2196f3; color:white;
                              padding:5px 10px;
                              border-radius:7px;
                              text-decoration:none;
                              font-size:12px;">
                        Voir
                    </a>

                    {{-- Validation : pharmacien uniquement --}}
                    @canOnco('prescriptions.valider')
                        @unless($prescription->isValidee())
                            <form action="{{ route('oncologie.prescriptions.valider', $prescription->id) }}"
                                  method="POST"
                                  style="display:inline;">
                                @csrf

                                <button type="submit"
                                        style="background:#f4a261;
                                               color:white;
                                               border:none;
                                               padding:5px 10px;
                                               border-radius:7px;
                                               cursor:pointer;
                                               font-size:12px;">
                                    ✅ Valider
                                </button>

                            </form>
                        @endunless
                    @endcanOnco

                </div>

            </td>

        </tr>

    @empty

        <tr>
            <td colspan="9"
                style="padding:24px;
                       text-align:center;
                       color:#9ca3af;">
                Aucune prescription trouvée.
            </td>
        </tr>

    @endforelse

    </tbody>

</table>
```

</div>

<div style="margin-top:14px;">
    {{ $prescriptions->links('pagination::tailwind') }}
</div>

@endsection
