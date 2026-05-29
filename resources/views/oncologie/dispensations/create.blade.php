@extends('layouts.app')
@section('title', 'Effectuer une Dispensation')

@section('content')
<div style="max-width:700px; margin:auto;">

    <div style="background:#111827; border-radius:16px; padding:30px;
                box-shadow:0 10px 30px rgba(0,0,0,0.3);">

        <h1 style="color:#4ade80; font-weight:800; text-align:center; margin-bottom:24px;">
            💊 Effectuer une Dispensation
        </h1>

        @if(session('error'))
            <div style="background:#dc2626; color:white; padding:12px; border-radius:8px;
                        margin-bottom:14px; font-weight:600;">
                ❌ {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div style="background:#16a34a; color:white; padding:12px; border-radius:8px;
                        margin-bottom:14px; font-weight:600;">
                ✅ {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('oncologie.dispensations.store') }}" method="POST">
            @csrf

            <div style="margin-bottom:16px;">
                <label style="color:#d1d5db; font-weight:600; display:block; margin-bottom:6px;">
                    Prescription (validée) *
                </label>
                <select name="prescription_id"
                        style="width:100%; padding:12px; border-radius:10px;
                               border:1px solid #374151; background:#1f2937; color:white;"
                        required>
                    <option value="">Sélectionner une prescription validée</option>
                    @foreach($prescriptions as $pres)
                        <option value="{{ $pres->id }}">
                            {{ optional($pres->patient)->nom }}
                            {{ optional($pres->patient)->prenom }}
                            — {{ optional($pres->date_prescription)->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label style="color:#d1d5db; font-weight:600; display:block; margin-bottom:6px;">
                    Médicament *
                </label>
                <select name="medicament_id"
                        style="width:100%; padding:12px; border-radius:10px;
                               border:1px solid #374151; background:#1f2937; color:white;"
                        required>
                    <option value="">Sélectionner un médicament</option>
                    @foreach($medicaments as $med)
                        <option value="{{ $med->id }}">{{ $med->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:24px;">
                <label style="color:#d1d5db; font-weight:600; display:block; margin-bottom:6px;">
                    Quantité *
                </label>
                <input type="number" name="quantite" min="1" required
                       style="width:100%; padding:12px; border-radius:10px;
                              border:1px solid #374151; background:#1f2937; color:white;">
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit"
                        style="flex:1; background:#4ade80; color:#111827; border:none;
                               padding:13px; border-radius:10px; font-weight:800;
                               font-size:15px; cursor:pointer;">
                    🚀 Effectuer Dispensation (FIFO)
                </button>
                <a href="{{ route('oncologie.dispensations.index') }}"
                   style="flex:1; background:#374151; color:white; padding:13px;
                          border-radius:10px; text-decoration:none; font-weight:700;
                          text-align:center;">
                    ← Retour
                </a>
            </div>
        </form>
    </div>
</div>
@endsection