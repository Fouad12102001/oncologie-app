@extends('layouts.app')
@section('title', 'Créer Prescription')

@section('content')
<div style="max-width:900px; margin:auto;">

    <div style="background:linear-gradient(135deg,#f4f8fb,#e9f2f7);
                min-height:100vh; padding-top:5px;">

        <h1 style="color:#264653; font-weight:bold; margin-bottom:20px;">
            📋 Créer une Prescription
        </h1>

        @if($errors->any())
            <div style="background:#d1f7e6; border-left:5px solid #2a9d8f; color:#1b5e20;
                        padding:12px; border-radius:8px; margin-bottom:16px;">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        <form action="{{ route('oncologie.prescriptions.store') }}" method="POST">
            @csrf

            <div style="margin-bottom:14px;">
                <label style="color:#264653; font-weight:bold; display:block; margin-bottom:5px;">
                    Patient *
                </label>
                <select name="patient_id" id="patient_id"
                        style="width:100%; padding:10px; border-radius:10px;
                               border:1px solid #ccc; background:white;" required>
                    <option value="">-- Choisir --</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}"
                                data-poids="{{ $patient->poids }}"
                                data-taille="{{ $patient->taille }}">
                            {{ $patient->nom }} {{ $patient->prenom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:14px;">
                <label style="color:#264653; font-weight:bold; display:block; margin-bottom:5px;">
                    Protocole *
                </label>
                <select name="protocole_id" id="protocole"
                        style="width:100%; padding:10px; border-radius:10px;
                               border:1px solid #ccc; background:white;" required>
                    <option value="">-- Choisir un protocole --</option>
                    @foreach($protocoles as $protocole)
                        <option value="{{ $protocole->id }}">{{ $protocole->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; margin-bottom:14px;">
                <div>
                    <label style="color:#264653; font-weight:bold; display:block; margin-bottom:5px;">
                        Poids (kg)
                    </label>
                    <input type="number" id="poids" step="0.1"
                           style="width:100%; padding:10px; border-radius:10px; border:1px solid #ccc;">
                </div>
                <div>
                    <label style="color:#264653; font-weight:bold; display:block; margin-bottom:5px;">
                        Taille (cm)
                    </label>
                    <input type="number" id="taille" step="0.1"
                           style="width:100%; padding:10px; border-radius:10px; border:1px solid #ccc;">
                </div>
                <div>
                    <label style="color:#264653; font-weight:bold; display:block; margin-bottom:5px;">
                        Surface corporelle (m²)
                    </label>
                    <input type="text" id="sc" readonly
                           style="width:100%; padding:10px; border-radius:10px;
                                  border:1px solid #ccc; background:#f1f5f9; font-weight:700;">
                </div>
            </div>

            <div id="medicaments_form_container" style="margin-bottom:14px;">
                <h3 style="color:#264653; font-weight:bold; margin-bottom:10px;">
                    💊 Doses calculées
                </h3>
                <div id="medicaments_container"
                     style="color:#6b7280;">
                    Sélectionnez un protocole et un patient pour voir les doses
                </div>
            </div>

            <div style="margin-bottom:14px;">
                <label style="color:#264653; font-weight:bold; display:block; margin-bottom:5px;">
                    Médecin *
                </label>
                <select name="medecin_nom"
                        style="width:100%; padding:10px; border-radius:10px;
                               border:1px solid #ccc; background:white;" required>
                    <option value="">-- Choisir --</option>
                    @foreach($medecins as $medecin)
                        <option value="{{ $medecin->name }}">{{ $medecin->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:20px;">
                <label style="color:#264653; font-weight:bold; display:block; margin-bottom:5px;">
                    Date *
                </label>
                <input type="date" name="date_prescription"
                       style="width:100%; padding:10px; border-radius:10px; border:1px solid #ccc;"
                       required>
            </div>

            <button type="submit"
                    style="width:100%; background:#2a9d8f; color:white; border:none;
                           padding:13px; border-radius:10px; font-weight:700;
                           font-size:15px; cursor:pointer;">
                ✅ Créer la prescription
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const patientSelect  = document.getElementById('patient_id');
    const protocoleSelect = document.getElementById('protocole');
    const poidsInput     = document.getElementById('poids');
    const tailleInput    = document.getElementById('taille');
    const scInput        = document.getElementById('sc');
    const container      = document.getElementById('medicaments_container');

    function calculerSC(poids, taille) {
        return Math.sqrt((poids * taille) / 3600).toFixed(2);
    }

    function majSC() {
        let patient = patientSelect.selectedOptions[0];
        let poids   = parseFloat(poidsInput.value || patient?.dataset.poids);
        let taille  = parseFloat(tailleInput.value || patient?.dataset.taille);
        scInput.value = (poids && taille) ? calculerSC(poids, taille) : '';
    }

    function chargerMedicaments() {
        const protocoleId = protocoleSelect.value;
        if (!protocoleId) {
            container.innerHTML = '<p style="color:#6b7280;">Sélectionnez un protocole</p>';
            return;
        }
        fetch(`{{ url('oncologie/protocoles') }}/${protocoleId}/medicaments`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            majSC();
            let sc    = parseFloat(scInput.value) || 0;
            let poids = parseFloat(poidsInput.value) || 0;
            if (!data || data.length === 0) {
                container.innerHTML = '<p style="color:#ef4444;">Aucun médicament dans ce protocole</p>';
                return;
            }
            let html = '';
            data.forEach(med => {
                let dose = med.dose_standard || 0;
                if (med.type_calcul === 'm2') dose = sc * med.dose_standard;
                if (med.type_calcul === 'kg')  dose = poids * med.dose_standard;
                html += `
                <div style="background:white; border:1px solid #e5e7eb; border-radius:10px;
                            padding:12px; margin-bottom:8px; display:flex;
                            justify-content:space-between; align-items:center;">
                    <strong>${med.nom}</strong>
                    <div style="display:flex; gap:8px; align-items:center;">
                        <input type="text" value="${dose.toFixed(2)} mg" readonly
                               style="width:100px; padding:6px; border-radius:8px;
                                      border:1px solid #ccc; text-align:center;">
                        <input type="hidden" name="medicaments[${med.id}]" value="${dose.toFixed(2)}">
                    </div>
                </div>`;
            });
            container.innerHTML = html;
        })
        .catch(() => {
            container.innerHTML = '<p style="color:#ef4444;">Erreur chargement médicaments</p>';
        });
    }

    patientSelect.addEventListener('change', () => {
        let patient = patientSelect.selectedOptions[0];
        poidsInput.value  = patient?.dataset.poids  || '';
        tailleInput.value = patient?.dataset.taille || '';
        majSC();
        chargerMedicaments();
    });

    protocoleSelect.addEventListener('change', chargerMedicaments);
    poidsInput.addEventListener('input',  () => { majSC(); chargerMedicaments(); });
    tailleInput.addEventListener('input', () => { majSC(); chargerMedicaments(); });
});
</script>
@endpush

@endsection