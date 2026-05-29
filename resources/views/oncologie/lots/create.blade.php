@extends('layouts.app')
@section('title', 'Ajouter un Lot')

@section('content')
<div style="max-width:700px; margin:auto;">

    <div style="display:flex; justify-content:space-between; align-items:center;
                background:white; padding:16px; border-radius:12px; margin-bottom:16px;">
        <h2 style="margin:0; font-weight:800;">📦 Ajouter un Lot</h2>
        <a href="{{ route('oncologie.lots.index') }}"
           style="background:#334155; color:white; padding:9px 14px;
                  border-radius:9px; text-decoration:none; font-weight:600;">⬅ Retour</a>
    </div>

    <div style="background:white; border-left:6px solid #2a9d8f; border-radius:14px;
                padding:24px; box-shadow:0 6px 20px rgba(0,0,0,0.06);">

        @if($errors->any())
            <div style="background:#ffe5e5; border-left:4px solid #e63946; color:#7a1c1c;
                        padding:12px; border-radius:8px; margin-bottom:16px;">
                @foreach($errors->all() as $e)<div>⚠ {{ $e }}</div>@endforeach
            </div>
        @endif

        <form action="{{ route('oncologie.lots.store') }}" method="POST">
            @csrf

            <div style="margin-bottom:14px;">
                <label style="font-weight:600; display:block; margin-bottom:6px;">
                    Médicament *
                </label>
                <select name="medicament_id" required
                        style="width:100%; padding:10px; border-radius:10px;
                               border:1px solid #d1d5db;">
                    <option value="">-- Sélectionner --</option>
                    @foreach($medicaments as $med)
                        <option value="{{ $med->id }}"
                                {{ old('medicament_id') == $med->id ? 'selected' : '' }}>
                            {{ $med->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-weight:600; display:block; margin-bottom:6px;">
                    Numéro de lot *
                </label>
                <input type="text" name="numero" value="{{ old('numero') }}" required
                       style="width:100%; padding:10px; border-radius:10px;
                              border:1px solid #d1d5db;">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                <div>
                    <label style="font-weight:600; display:block; margin-bottom:6px;">
                        Quantité initiale *
                    </label>
                    <input type="number" name="quantite_initiale" min="1"
                           value="{{ old('quantite_initiale') }}" required
                           style="width:100%; padding:10px; border-radius:10px;
                                  border:1px solid #d1d5db;">
                </div>
                <div>
                    <label style="font-weight:600; display:block; margin-bottom:6px;">
                        Date de fabrication
                    </label>
                    <input type="date" name="date_fabrication"
                           value="{{ old('date_fabrication') }}"
                           style="width:100%; padding:10px; border-radius:10px;
                                  border:1px solid #d1d5db;">
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label style="font-weight:600; display:block; margin-bottom:6px;">
                    Date d'expiration *
                </label>
                <input type="date" name="date_expiration"
                       value="{{ old('date_expiration') }}" required
                       style="width:100%; padding:10px; border-radius:10px;
                              border:1px solid #d1d5db;">
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit"
                        style="flex:1; background:#2a9d8f; color:white; border:none;
                               padding:12px; border-radius:10px; font-weight:700;
                               cursor:pointer; font-size:15px;">
                    💾 Enregistrer le lot
                </button>
                <a href="{{ route('oncologie.lots.index') }}"
                   style="flex:1; background:#374151; color:white; padding:12px;
                          border-radius:10px; text-decoration:none; font-weight:700;
                          text-align:center;">
                    ↩ Retour
                </a>
            </div>
        </form>
    </div>
</div>
@endsection