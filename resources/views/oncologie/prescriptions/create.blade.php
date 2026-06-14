@extends('layouts.app')

@section('title', 'Nouvelle Prescription')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="container-fluid py-4">

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-body">

                    <h2 class="fw-bold text-success">
                        💊 Création d'une Prescription Oncologique
                    </h2>

                    <p class="text-muted mb-0">
                        Prescription personnalisée selon protocole, poids,
                        taille, surface corporelle et fonction rénale.
                    </p>

                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

<form action="{{ route('oncologie.prescriptions.store') }}"
      method="POST">

    @csrf

    <div class="row">

        {{-- ========================= --}}
        {{-- COLONNE PATIENT --}}
        {{-- ========================= --}}

        <div class="col-lg-4">

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-primary text-white">
                    👤 Patient
                </div>

                <div class="card-body">

                    <label class="form-label fw-bold">
                        Rechercher un patient
                    </label>

                    <select
                        name="patient_id"
                        id="patient_id"
                        class="form-select"
                        required>

                        <option value="">
                            -- Sélectionner --
                        </option>

                        @foreach($patients as $patient)

                        <option
                            value="{{ $patient->id }}"
                            data-nom="{{ $patient->nom }}"
                            data-prenom="{{ $patient->prenom }}"
                            data-dossier="{{ $patient->numero_dossier }}"
                            data-cancer="{{ $patient->type_cancer }}"
                            data-sexe="{{ $patient->sexe }}"
                            data-age="{{ $patient->age }}"
                            data-poids="{{ $patient->poids }}"
                            data-taille="{{ $patient->taille }}"
                            data-allergies="{{ $patient->allergies }}"
                            data-creatinine="{{ $patient->creatinine }}"
                            data-clairance="{{ $patient->clairance_renale }}"
                            data-groupe="{{ $patient->groupe_sanguin }}"
                            data-telephone="{{ $patient->telephone }}"
                            data-medecin="{{ $patient->medecin_traitant }}"
                        >

                            {{ $patient->numero_dossier }}
                            -
                            {{ $patient->nom }}
                            {{ $patient->prenom }}

                        </option>

                        @endforeach

                    </select>

                </div>
            </div>

            {{-- ========================= --}}
            {{-- FICHE PATIENT --}}
            {{-- ========================= --}}

            <div
                class="card shadow-sm border-0 mb-4"
                id="patientCard"
                style="display:none;">

                <div class="card-header bg-success text-white">
                    📄 Informations Patient
                </div>

                <div class="card-body">

                    <div id="patientInfos"></div>

                </div>

            </div>

            {{-- ========================= --}}
            {{-- ALERTES --}}
            {{-- ========================= --}}

            <div
                class="card shadow-sm border-0"
                id="alertesCard"
                style="display:none;">

                <div class="card-header bg-warning">
                    ⚠️ Alertes Cliniques
                </div>

                <div class="card-body">

                    <div id="alertesContainer"></div>

                </div>

            </div>

        </div>

        {{-- ========================= --}}
        {{-- COLONNE CENTRALE --}}
        {{-- ========================= --}}

        <div class="col-lg-5">

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-info text-white">
                    📋 Protocole Thérapeutique
                </div>

                <div class="card-body">

                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            Protocole
                        </label>

                        <select
                            name="protocole_id"
                            id="protocole"
                            class="form-select"
                            required>

                            <option value="">
                                -- Choisir --
                            </option>

                            @foreach($protocoles as $protocole)

                            <option
                                value="{{ $protocole->id }}"
                                data-nom="{{ $protocole->nom }}"
                                data-cycle="{{ $protocole->cycle }}"
                                data-frequence="{{ $protocole->frequence }}"
                                data-description="{{ $protocole->description }}"
                            >
                                {{ $protocole->nom }}
                            </option>

                            @endforeach

                        </select>

                    </div>

                    <div id="protocoleInfos"></div>

                </div>

            </div>

            {{-- ========================= --}}
            {{-- PARAMETRES CLINIQUES --}}
            {{-- ========================= --}}

            <div class="card shadow-sm border-0">

                <div class="card-header bg-secondary text-white">
                    ⚕️ Paramètres Cliniques
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Poids (kg)
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                id="poids"
                                class="form-control">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Taille (cm)
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                id="taille"
                                class="form-control">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Surface Corporelle
                            </label>

                            <input
                                type="text"
                                id="surface"
                                class="form-control bg-light"
                                readonly>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Clairance rénale
                            </label>

                            <input type="number" id="clairance" class="form-control bg-light" readonly>

                        </div>
                        <div class="col-md-6 mb-3">
    <label class="form-label">
        Créatinine (mg/dL)
    </label>

    <input
        type="number"
        step="0.01"
        id="creatinine"
        class="form-control">
</div>

                    </div>

                </div>

            </div>

        </div>

        {{-- ========================= --}}
        {{-- COLONNE DROITE --}}
        {{-- ========================= --}}

        <div class="col-lg-3">

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-dark text-white">
                    👨‍⚕️ Prescription
                </div>

                <div class="card-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Médecin
                        </label>

                        <select
                            name="medecin_nom"
                            class="form-select"
                            required>

                            <option value="">
                                -- Choisir --
                            </option>

                            @foreach($medecins as $medecin)

                            <option value="{{ $medecin->name }}">
                                {{ $medecin->name }}
                            </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Date
                        </label>

                        <input
                            type="date"
                            name="date_prescription"
                            value="{{ date('Y-m-d') }}"
                            class="form-control"
                            required>

                    </div>

                </div>

            </div>

            {{-- RESUME --}}
            <div class="card shadow-sm border-0">

                <div class="card-header bg-success text-white">
                    📑 Résumé
                </div>

                <div class="card-body">

                    <div id="resumePatient">

                        Sélectionner un patient

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- Partie 2 ici --}}

    {{-- ========================================= --}}
{{-- DOSES CALCULÉES --}}
{{-- ========================================= --}}

<div class="row mt-4">

    <div class="col-12">

        <div class="card shadow border-0">

            <div class="card-header bg-danger text-white">

                💊 Doses calculées automatiquement

            </div>

            <div class="card-body">

                <div id="medicaments_container">

                    <div class="alert alert-info">

                        Sélectionnez un patient et un protocole.

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- ========================================= --}}
{{-- RESUME FINAL --}}
{{-- ========================================= --}}

<div class="row mt-4">

    <div class="col-12">

        <div class="card shadow border-0">

            <div class="card-header bg-success text-white">

                📑 Résumé de la prescription

            </div>

            <div class="card-body">

                <div id="resumePrescription">

                    Aucun protocole sélectionné

                </div>

            </div>

        </div>

    </div>

</div>

{{-- ========================================= --}}
{{-- BOUTONS --}}
{{-- ========================================= --}}

<div class="row mt-4 mb-5">

    <div class="col-md-6">

        <a href="{{ route('oncologie.prescriptions.index') }}"
           class="btn btn-secondary btn-lg w-100">

            ← Retour

        </a>

    </div>

    <div class="col-md-6">

        <button
            type="submit"
            class="btn btn-success btn-lg w-100">

            💾 Enregistrer la Prescription

        </button>

    </div>

</div>

</form>

</div>

@endsection

@push('scripts')

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

const patientSelect      = document.getElementById('patient_id');
const protocoleSelect    = document.getElementById('protocole');

const poidsInput         = document.getElementById('poids');
const tailleInput        = document.getElementById('taille');
const surfaceInput       = document.getElementById('surface');
const clairanceInput     = document.getElementById('clairance');

const patientCard        = document.getElementById('patientCard');
const patientInfos       = document.getElementById('patientInfos');

const alertesCard        = document.getElementById('alertesCard');
const alertesContainer   = document.getElementById('alertesContainer');

const resumePatient      = document.getElementById('resumePatient');
const resumePrescription = document.getElementById('resumePrescription');

const creatinineInput = document.getElementById('creatinine');

const protocoleInfos     = document.getElementById('protocoleInfos');

const medicamentsContainer =
document.getElementById('medicaments_container');

$(document).ready(function() {

    $('#patient_id').select2({
        width:'100%',
        placeholder:'Rechercher un patient'
    });

    $('#protocole').select2({
        width:'100%',
        placeholder:'Choisir un protocole'
    });

});

function calculSC(poids, taille)
{
    if(!poids || !taille) return 0;

    return Math.sqrt(
        (poids * taille) / 3600
    ).toFixed(2);
}

function calculClairance(age, poids, creatinine, sexe)
{
    if(!age || !poids || !creatinine || creatinine <= 0)
{
    return 0;
}

    let clcr =
        ((140 - age) * poids) / (72 * creatinine);

    sexe = (sexe || '').toLowerCase();

    if(sexe.includes('f'))
    {
        clcr *= 0.85;
    }

    return clcr.toFixed(2);
}

function majPatient()
{
    const patient =
    patientSelect.selectedOptions[0];

    if(!patient) return;

    let poids =
    patient.dataset.poids || '';

    let taille =
    patient.dataset.taille || '';

    let clairance =
    patient.dataset.clairance || '';

    poidsInput.value = poids;
    tailleInput.value = taille;
    clairanceInput.value = clairance;

    let creatinine = patient.dataset.creatinine || '';
    let sexe = patient.dataset.sexe || '';

    creatinineInput.value = creatinine;

    let sc =
    calculSC(poids, taille);

    surfaceInput.value = sc;

    patientCard.style.display='block';

    patientInfos.innerHTML=`

        <table class="table table-sm">

            <tr>
                <th>Dossier</th>
                <td>${patient.dataset.dossier}</td>
            </tr>

            <tr>
                <th>Patient</th>
                <td>
                    ${patient.dataset.nom}
                    ${patient.dataset.prenom}
                </td>
            </tr>

            <tr>
                <th>Age</th>
                <td>${patient.dataset.age}</td>
            </tr>

            <tr>
                <th>Sexe</th>
                <td>${patient.dataset.sexe}</td>
            </tr>

            <tr>
                <th>Cancer</th>
                <td>${patient.dataset.cancer}</td>
            </tr>

            <tr>
                <th>Téléphone</th>
                <td>${patient.dataset.telephone ?? '-'}</td>
            </tr>

            <tr>
                <th>SC</th>
                <td>${sc} m²</td>
            </tr>

            <tr>
                <th>Créatinine</th>
                <td>${creatinine || '-'} mg/dL</td>
            </tr>

            <tr>
               <th>Clairance</th>
               <td>${clairanceInput.value || '-'} ml/min</td>
            </tr>

        </table>

    `;

    resumePatient.innerHTML=`

        <b>${patient.dataset.nom}
        ${patient.dataset.prenom}</b>

        <hr>

        Dossier :
        ${patient.dataset.dossier}

        <br>

        Cancer :
        ${patient.dataset.cancer}

        <br>

        SC :
        ${sc} m²

    `;

    verifierAlertes();

    chargerMedicaments();

    let age = parseFloat(patient.dataset.age || 0);

let clcrAuto = calculClairance(
    age,
    poids,
    creatinine,
    sexe
);

clairanceInput.value = clcrAuto;
}

function verifierAlertes()
{
    let alertes=[];

    if(!poidsInput.value)
    {
        alertes.push(
        '⚠️ Poids non renseigné');
    }

    if(!tailleInput.value)
    {
        alertes.push(
        '⚠️ Taille non renseignée');
    }

    if(!clairanceInput.value)
    {
        alertes.push(
        '⚠️ Clairance rénale absente');
    }

    if(alertes.length)
    {
        alertesCard.style.display='block';

        alertesContainer.innerHTML=
        alertes.map(a =>
        `<div>${a}</div>`).join('');
    }
    else
    {
        alertesCard.style.display='none';
    }
}

function afficherProtocole()
{
    const protocole =
    protocoleSelect.selectedOptions[0];

    if(!protocole) return;

    protocoleInfos.innerHTML=`

        <div class="alert alert-primary">

            <b>${protocole.dataset.nom}</b>

            <hr>

            Cycle :
            ${protocole.dataset.cycle ?? '-'}

            <br>

            Fréquence :
            ${protocole.dataset.frequence ?? '-'}

            <br>

            ${protocole.dataset.description ?? ''}

        </div>

    `;

    chargerMedicaments();
}

function chargerMedicaments()
{
    const protocoleId = protocoleSelect.value;

    if(!protocoleId)
    {
        medicamentsContainer.innerHTML = `
            <div class="alert alert-warning">
                Sélectionnez un protocole.
            </div>
        `;
        return;
    }

    fetch(
        `{{ url('oncologie/protocoles') }}/${protocoleId}/medicaments`,
        {
            headers:{
                'Accept':'application/json'
            }
        }
    )
    .then(response => response.json())
    .then(data => {

        let poids =
        parseFloat(poidsInput.value || 0);

        let taille =
        parseFloat(tailleInput.value || 0);

        let clairance =
        parseFloat(clairanceInput.value || 0);

        let sc =
        parseFloat(surfaceInput.value || 0);

        let html = '';

        let resume = `
            <h5 class="text-success mb-3">
                Prescription calculée
            </h5>
        `;

        if(data.length === 0)
        {
            medicamentsContainer.innerHTML = `
                <div class="alert alert-danger">
                    Aucun médicament associé au protocole.
                </div>
            `;
            return;
        }

        data.forEach(med => {

            let dose = 0;
            let formule = '';
            let unite = '';

            switch(med.type_calcul)
            {
                case 'kg':

                    dose =
                    poids * med.dose_standard;

                    unite = 'mg/kg';

                    formule =
                    `${poids} × ${med.dose_standard}`;

                break;

                case 'm2':

                    dose =
                    sc * med.dose_standard;

                    unite = 'mg/m²';

                    formule =
                    `${sc} × ${med.dose_standard}`;

                break;

                case 'taille':

                    dose =
                    taille * med.dose_standard;

                    unite = 'mg/cm';

                    formule =
                    `${taille} × ${med.dose_standard}`;

                break;

                case 'fixe':

                    dose =
                    med.dose_standard;

                    unite = 'mg';

                    formule =
                    `Dose fixe`;

                break;

                case 'auc':

                    dose =
                    med.dose_standard *
                    (clairance + 25);

                    unite = 'AUC';

                    formule =
                    `${med.dose_standard} × (${clairance}+25)`;

                break;

                default:

                    dose =
                    med.dose_standard;

                    unite='mg';

                    formule='Dose standard';

            }

            dose = Number(dose).toFixed(2);

            html += `

            <div class="card mb-3 border-0 shadow-sm">

                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col-md-4">

                            <h5 class="mb-1">
                                💊 ${med.nom}
                            </h5>

                            <small class="text-muted">

                                Méthode :
                                <b>${med.type_calcul}</b>

                            </small>

                        </div>

                        <div class="col-md-4">

                            <small class="text-secondary">

                                ${formule}

                            </small>

                        </div>

                        <div class="col-md-4 text-end">

                            <span
                            class="badge bg-success fs-6">

                                ${dose} mg

                            </span>

                        </div>

                    </div>

                    <input
                        type="hidden"
                        name="medicaments[${med.id}]"
                        value="${dose}"
                    >

                </div>

            </div>

            `;

            resume += `

                <div class="border-bottom py-2">

                    <b>${med.nom}</b>

                    <br>

                    Dose :

                    <span class="text-primary">

                        ${dose} mg

                    </span>

                    <br>

                    <small class="text-muted">

                        ${unite}

                    </small>

                </div>

            `;

        });

        medicamentsContainer.innerHTML = html;

        const patient =
        patientSelect.selectedOptions[0];

        const protocole =
        protocoleSelect.selectedOptions[0];

        resumePrescription.innerHTML = `

            <div class="row">

                <div class="col-md-6">

                    <div class="card border-0 bg-light">

                        <div class="card-body">

                            <h5>👤 Patient</h5>

                            <hr>

                            <b>

                                ${patient.dataset.nom}
                                ${patient.dataset.prenom}

                            </b>

                            <br>

                            Dossier :
                            ${patient.dataset.dossier}

                            <br>

                            Cancer :
                            ${patient.dataset.cancer}

                            <br>

                            Poids :
                            ${poids} kg

                            <br>

                            Taille :
                            ${taille} cm

                            <br>

                            SC :
                            ${sc} m²

                            <br>

                            Clairance :
                            ${clairance} ml/min

                        </div>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="card border-0 bg-light">

                        <div class="card-body">

                            <h5>📋 Protocole</h5>

                            <hr>

                            <b>

                                ${protocole.dataset.nom}

                            </b>

                            <br>

                            Cycle :
                            ${protocole.dataset.cycle}

                            <br>

                            Fréquence :
                            ${protocole.dataset.frequence}

                        </div>

                    </div>

                </div>

            </div>

            <div class="mt-4">

                ${resume}

            </div>

        `;

    })
    .catch(error => {

        console.error(error);

        medicamentsContainer.innerHTML = `

            <div class="alert alert-danger">

                Erreur de chargement des médicaments

            </div>

        `;

    });
}

patientSelect.addEventListener(
    'change',
    majPatient
);

protocoleSelect.addEventListener(
    'change',
    afficherProtocole
);

poidsInput.addEventListener(
    'input',
    () => {

        surfaceInput.value =
        calculSC(
            poidsInput.value,
            tailleInput.value
        );
        updateClairanceAuto();


        verifierAlertes();

        chargerMedicaments();
    }
);

tailleInput.addEventListener(
    'input',
    () => {

        surfaceInput.value =
        calculSC(
            poidsInput.value,
            tailleInput.value
        );

        verifierAlertes();

        chargerMedicaments();
    }
);

clairanceInput.addEventListener(
    'input',
    () => {

        verifierAlertes()
        
        if(parseFloat(clairanceInput.value) < 60 &&
   parseFloat(clairanceInput.value) > 0)
{
    alertes.push(
        '⚠️ Insuffisance rénale modérée (ClCr < 60 ml/min)'
    );
}

if(parseFloat(clairanceInput.value) < 30 &&
   parseFloat(clairanceInput.value) > 0)
{
    alertes.push(
        '🚨 Insuffisance rénale sévère (ClCr < 30 ml/min)'
    );
};

        chargerMedicaments();
    }
);

creatinineInput.addEventListener('input', () => {
    updateClairanceAuto();
    verifierAlertes();
});

function updateClairanceAuto()
{
    const patient = patientSelect.selectedOptions[0];
    if(!patient) return;

    let age = parseFloat(patient.dataset.age || 0);
    let poids = parseFloat(poidsInput.value || 0);
    let creatinine = parseFloat(creatinineInput.value || 0);
    let sexe = patient.dataset.sexe || '';

    if(!creatinine)
    {
        clairanceInput.value = '';
        verifierAlertes();
        return;
    }

    let clcr = calculClairance(age, poids, creatinine, sexe);

    clairanceInput.value = clcr;

    verifierAlertes();
    chargerMedicaments();
}

</script>

@endpush