@extends('layouts.app')
@section('title', 'Nouveau Patient')

@section('content')
<div style="max-width:900px; margin:auto;">

    <div style="background:white; border-left:6px solid #2a9d8f; border-radius:14px;
                padding:24px; box-shadow:0 6px 20px rgba(0,0,0,0.06);">

        <h2 style="text-align:center; color:#264653; font-weight:800; margin-bottom:20px;">
            ➕ Nouveau Patient Oncologie
        </h2>

        @if($errors->any())
            <div style="background:#fee2e2; border-left:4px solid #ef4444; color:#991b1b;
                        padding:12px; border-radius:8px; margin-bottom:16px;">
                @foreach($errors->all() as $e)<div>⚠️ {{ $e }}</div>@endforeach
            </div>
        @endif

        <form action="{{ route('oncologie.patients.store') }}" method="POST"
              class="row g-3">
            @csrf

            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Nom *</label>
                <input type="text" name="nom" class="form-control"
                       value="{{ old('nom') }}" required>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Prénom *</label>
                <input type="text" name="prenom" class="form-control"
                       value="{{ old('prenom') }}" required>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Numéro dossier *</label>
                <input type="text" name="numero_dossier" class="form-control"
                       value="{{ old('numero_dossier') }}" required>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Sexe *</label>
                <select name="sexe" class="form-select" required>
                    <option value="">-- Choisir --</option>
                    @foreach($sexes as $sexe)
                        <option value="{{ $sexe }}"
                                {{ old('sexe') == $sexe ? 'selected' : '' }}>
                            {{ $sexe }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Date de naissance *</label>
                <input type="date" name="date_naissance" id="date_naissance"
                       class="form-control" value="{{ old('date_naissance') }}" required>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Âge (calculé auto)</label>
                <input type="number" name="age" id="age" class="form-control" readonly>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Wilaya *</label>
                <select name="wilaya" id="wilaya" class="form-select" required>
                    <option value="">-- Choisir une wilaya --</option>
                    @foreach(config('wilayas', []) as $wilaya)
                        <option value="{{ $wilaya }}"
                                {{ old('wilaya') == $wilaya ? 'selected' : '' }}>
                            {{ $wilaya }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6" id="daira-container" style="display:none;">
                <label style="font-weight:600; color:#264653;">Daïra</label>
                <select name="daira" id="daira" class="form-select">
                    <option value="">-- Choisir une daïra --</option>
                </select>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Poids (kg)</label>
                <input type="number" name="poids" class="form-control"
                       step="0.1" value="{{ old('poids') }}">
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Taille (cm)</label>
                <input type="number" name="taille" class="form-control"
                       step="0.1" value="{{ old('taille') }}">
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Type de cancer *</label>
                <select name="type_cancer" class="form-select" required>
                    <option value="">-- Choisir --</option>
                    @foreach($types_cancer as $type)
                        <option value="{{ $type }}"
                                {{ old('type_cancer') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Statut vital *</label>
                <select name="statut_vital" id="statut_vital" class="form-select" required>
                    @foreach($statuts as $statut)
                        <option value="{{ $statut }}"
                                {{ old('statut_vital','vivant') == $statut ? 'selected' : '' }}>
                            {{ ucfirst($statut) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label style="font-weight:600; color:#264653;">Date de décès</label>
                <input type="date" name="date_deces" id="date_deces"
                       class="form-control" value="{{ old('date_deces') }}"
                       disabled>
            </div>

            <div class="col-12 text-center mt-3">
                <button type="submit"
                        style="background:#2a9d8f; color:white; border:none;
                               padding:12px 30px; border-radius:10px;
                               font-weight:700; font-size:15px; cursor:pointer;">
                    💾 Enregistrer Patient
                </button>
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

function calculAge() {
    let input = document.getElementById('date_naissance');
    if (!input.value) return;
    let birth = new Date(input.value), today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    let m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
    document.getElementById('age').value = age;
}

function toggleDateDeces() {
    let statut = document.getElementById('statut_vital').value;
    let dateDeces = document.getElementById('date_deces');
    if (statut === 'decede') {
        dateDeces.disabled = false;
    } else {
        dateDeces.value = '';
        dateDeces.disabled = true;
    }
}

function checkWilaya() {
    let wilaya = document.getElementById('wilaya').value;
    let container = document.getElementById('daira-container');
    let select = document.getElementById('daira');
    select.innerHTML = '<option value="">-- Choisir une daïra --</option>';
    if (wilaya === 'Tizi Ouzou') {
        container.style.display = 'block';
        dairasTiziOuzou.forEach(d => {
            let opt = document.createElement('option');
            opt.value = opt.textContent = d;
            select.appendChild(opt);
        });
    } else {
        container.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('date_naissance').addEventListener('change', calculAge);
    document.getElementById('statut_vital').addEventListener('change', toggleDateDeces);
    document.getElementById('wilaya').addEventListener('change', checkWilaya);
    calculAge();
    toggleDateDeces();
});
</script>
@endpush

@endsection