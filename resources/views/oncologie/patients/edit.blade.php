@extends('layouts.app')
@section('title', 'Modifier Patient')

@section('content')
<div style="max-width:900px; margin:auto;">
    <div style="background:white; border-left:6px solid #2a9d8f; border-radius:14px;
                padding:24px; box-shadow:0 6px 20px rgba(0,0,0,0.06);">

        <h2 style="text-align:center; color:#264653; font-weight:800; margin-bottom:20px;">
            ✏️ Modifier Patient
        </h2>

        @if($errors->any())
            <div style="background:#fee2e2; border-left:4px solid #ef4444; color:#991b1b;
                        padding:12px; border-radius:8px; margin-bottom:16px;">
                @foreach($errors->all() as $e)<div>⚠️ {{ $e }}</div>@endforeach
            </div>
        @endif

        <form action="{{ route('oncologie.patients.update', $patient) }}" method="POST"
              class="row g-3">
            @csrf @method('PUT')

            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Nom *</label>
                <input type="text" name="nom" class="form-control"
                       value="{{ old('nom', $patient->nom) }}" required>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Prénom *</label>
                <input type="text" name="prenom" class="form-control"
                       value="{{ old('prenom', $patient->prenom) }}" required>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Numéro dossier *</label>
                <input type="text" name="numero_dossier" class="form-control"
                       value="{{ old('numero_dossier', $patient->numero_dossier) }}" required>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Sexe *</label>
                <select name="sexe" class="form-select" required>
                    @foreach($sexes as $sexe)
                        <option value="{{ $sexe }}"
                                {{ old('sexe', $patient->sexe) == $sexe ? 'selected' : '' }}>
                            {{ $sexe }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Date de naissance *</label>
                <input type="date" name="date_naissance" id="date_naissance"
                       class="form-control"
                       value="{{ old('date_naissance', optional($patient->date_naissance)->format('Y-m-d')) }}"
                       required>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Âge</label>
                <input type="number" id="age" class="form-control"
                       value="{{ $patient->age }}" readonly>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Wilaya *</label>
                <select name="wilaya" id="wilaya" class="form-select" required>
                    <option value="">-- Choisir --</option>
                    @foreach(config('wilayas', []) as $w)
                        <option value="{{ $w }}"
                                {{ old('wilaya', $patient->wilaya) == $w ? 'selected' : '' }}>
                            {{ $w }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6" id="daira-container">
                <label style="font-weight:600; color:#264653;">Daïra</label>
                <select name="daira" id="daira" class="form-select">
                    <option value="">-- Choisir --</option>
                </select>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Poids (kg)</label>
                <input type="number" name="poids" class="form-control"
                       step="0.1" value="{{ old('poids', $patient->poids) }}">
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Taille (cm)</label>
                <input type="number" name="taille" class="form-control"
                       step="0.1" value="{{ old('taille', $patient->taille) }}">
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Type de cancer *</label>
                <select name="type_cancer" class="form-select" required>
                    @foreach($types_cancer as $type)
                        <option value="{{ $type }}"
                                {{ old('type_cancer', $patient->type_cancer) == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Statut *</label>
                <select name="statut_vital" id="statut_vital" class="form-select" required>
                    <option value="vivant"
                            {{ old('statut_vital', $patient->statut_vital) == 'vivant' ? 'selected':'' }}>
                        Vivant
                    </option>
                    <option value="decede"
                            {{ old('statut_vital', $patient->statut_vital) == 'decede' ? 'selected':'' }}>
                        Décédé
                    </option>
                </select>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Date de décès</label>
                <input type="date" name="date_deces" id="date_deces"
                       class="form-control"
                       value="{{ old('date_deces', optional($patient->date_deces)->format('Y-m-d')) }}">
            </div>

            <div class="col-12 text-center mt-3" style="display:flex; gap:10px; justify-content:center;">
                <button type="submit"
                        style="background:#2a9d8f; color:white; border:none; padding:12px 28px;
                               border-radius:10px; font-weight:700; cursor:pointer;">
                    💾 Mettre à jour
                </button>
                <a href="{{ route('oncologie.patients.index') }}"
                   style="background:#264653; color:white; padding:12px 24px;
                          border-radius:10px; text-decoration:none; font-weight:700;">
                    🔙 Retour
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const dairasTiziOuzou = [
    "Tizi Ouzou","Azazga","Aïn El Hammam","Beni Douala","Beni Yenni","Boghni",
    "Bouzeguène","Draa Ben Khedda","Draa El Mizan","Freha","Illoula Oumalou",
    "Iferhounène","Larbaâ Nath Irathen","Maatkas","Makouda","Mekla","Ouadhia",
    "Ouacif","Tigzirt","Tizi Gheniff","Tizi Rached","Yakouren","Azeffoun",
    "Beni Zmenzer","Abi Youcef","Idjeur","Illilten","Iboudraren",
    "Aït Aissa Mimoun","Aït Yahia","Aït Mahmoud","Aït Ouacif","Aït Bouadou","Aït Chafâa"
];

document.getElementById('date_naissance').addEventListener('change', function () {
    let birth = new Date(this.value), today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    let m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
    document.getElementById('age').value = age;
});

document.getElementById('statut_vital').addEventListener('change', function () {
    let dateDeces = document.getElementById('date_deces');
    if (this.value === 'decede') {
        dateDeces.disabled = false;
    } else {
        dateDeces.value = '';
        dateDeces.disabled = true;
    }
});

function loadDairas(selected = null) {
    let wilaya = document.getElementById('wilaya').value;
    let daira  = document.getElementById('daira');
    daira.innerHTML = '<option value="">-- Choisir --</option>';
    if (wilaya === 'Tizi Ouzou') {
        dairasTiziOuzou.forEach(d => {
            let opt = document.createElement('option');
            opt.value = opt.textContent = d;
            if (selected && selected === d) opt.selected = true;
            daira.appendChild(opt);
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    loadDairas("{{ $patient->daira ?? '' }}");
    document.getElementById('wilaya').addEventListener('change', () => loadDairas());
});
</script>
@endpush

@endsection