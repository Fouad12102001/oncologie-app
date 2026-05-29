@extends('layouts.app')
@section('title', 'Modifier médicament')

@section('content')
<div style="max-width:700px; margin:auto;">

    <div style="display:flex; justify-content:space-between; align-items:center;
                background:white; padding:16px; border-radius:12px; margin-bottom:16px;">
        <div>
            <h2 style="margin:0; font-weight:800;">✏️ Modifier médicament</h2>
            <p style="margin:0; font-size:13px; color:#6b7280;">Mise à jour des informations</p>
        </div>
        <a href="{{ route('oncologie.medicaments.index') }}"
           style="background:#334155; color:white; padding:9px 14px;
                  border-radius:9px; text-decoration:none; font-weight:600;">
            ⬅ Retour
        </a>
    </div>

    <div style="background:white; padding:24px; border-radius:14px;
                box-shadow:0 6px 20px rgba(0,0,0,0.06);">

        @if($errors->any())
            <div style="background:#fee2e2; border-left:4px solid #ef4444; color:#991b1b;
                        padding:12px; border-radius:8px; margin-bottom:16px;">
                @foreach($errors->all() as $e)
                    <div>⚠️ {{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('oncologie.medicaments.update', $medicament) }}">
            @csrf
            @method('PUT')

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

                <div>
                    <label style="font-weight:600; color:#1f2937; display:block; margin-bottom:6px;">
                        Nom du médicament *
                    </label>
                    <input type="text" name="nom"
                           value="{{ old('nom', $medicament->nom) }}" required
                           style="width:100%; padding:10px; border-radius:10px;
                                  border:1px solid #d1d5db;">
                </div>

                <div>
                    <label style="font-weight:600; color:#1f2937; display:block; margin-bottom:6px;">
                        Quantité minimale *
                    </label>
                    <input type="number" name="quantite_min" min="0"
                           value="{{ old('quantite_min', $medicament->quantite_min) }}" required
                           style="width:100%; padding:10px; border-radius:10px;
                                  border:1px solid #d1d5db;">
                </div>

                <div>
                    <label style="font-weight:600; color:#1f2937; display:block; margin-bottom:6px;">
                        Date de fabrication
                    </label>
                    <input type="date" name="date_fabrication"
                           value="{{ old('date_fabrication', optional($medicament->date_fabrication)->format('Y-m-d')) }}"
                           style="width:100%; padding:10px; border-radius:10px;
                                  border:1px solid #d1d5db;">
                </div>

                <div>
                    <label style="font-weight:600; color:#1f2937; display:block; margin-bottom:6px;">
                        Date d'expiration
                    </label>
                    <input type="date" name="date_expiration"
                           value="{{ old('date_expiration', optional($medicament->date_expiration)->format('Y-m-d')) }}"
                           style="width:100%; padding:10px; border-radius:10px;
                                  border:1px solid #d1d5db;">
                </div>

            </div>

            {{-- STOCK ACTUEL (READ ONLY) --}}
            <div style="margin-top:16px; padding:12px; background:#f1f5f9;
                        border-radius:10px; font-weight:600;">
                📦 Stock actuel :
                <span style="background:#0ea5e9; color:white; padding:4px 12px;
                             border-radius:999px; margin-left:8px;">
                    {{ $medicament->stockActuel() }}
                </span>
            </div>

            <div style="margin-top:20px; display:flex; gap:10px;">
                <button type="submit"
                        style="flex:1; background:linear-gradient(135deg,#10b981,#059669);
                               color:white; border:none; padding:12px; border-radius:10px;
                               font-weight:700; cursor:pointer;">
                    💾 Enregistrer modifications
                </button>
                <a href="{{ route('oncologie.medicaments.index') }}"
                   style="flex:1; background:#ef4444; color:white; padding:12px;
                          border-radius:10px; text-decoration:none; font-weight:700;
                          text-align:center;">
                    ❌ Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection