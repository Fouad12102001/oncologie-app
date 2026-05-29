@extends('layouts.app')
@section('title', 'Modifier Prescription')

@section('content')
<div style="max-width:900px; margin:auto; background:linear-gradient(135deg,#f4f8fb,#e9f2f7); padding:10px;">

    <h1 style="color:#264653; font-weight:bold; margin-bottom:20px;">
        ✏️ Modifier Prescription #{{ $prescription->id }}
    </h1>

    @if($errors->any())
        <div style="background:#d1f7e6; border-left:5px solid #2a9d8f; color:#1b5e20;
                    padding:12px; border-radius:8px; margin-bottom:16px;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form action="{{ route('oncologie.prescriptions.update', $prescription->id) }}"
          method="POST" style="display:flex; flex-direction:column; gap:14px;">
        @csrf @method('PUT')

        <div>
            <label style="color:#264653; font-weight:bold; display:block; margin-bottom:5px;">
                Patient *
            </label>
            <select name="patient_id" id="patient_id"
                    style="width:100%; padding:10px; border-radius:10px;
                           border:1px solid #ccc; background:white;" required>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}"
                            data-poids="{{ $patient->poids }}"
                            data-taille="{{ $patient->taille }}"
                            {{ $prescription->patient_id == $patient->id ? 'selected':'' }}>
                        {{ $patient->nom }} {{ $patient->prenom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="color:#264653; font-weight:bold; display:block; margin-bottom:5px;">
                Protocole *
            </label>
            <select name="protocole_id"
                    style="width:100%; padding:10px; border-radius:10px;
                           border:1px solid #ccc; background:white;" required>
                @foreach($protocoles as $protocole)
                    <option value="{{ $protocole->id }}"
                            {{ $prescription->protocole_id == $protocole->id ? 'selected':'' }}>
                        {{ $protocole->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="color:#264653; font-weight:bold; display:block; margin-bottom:5px;">
                Médecin *
            </label>
            <input type="text" name="medecin_nom"
                   value="{{ $prescription->medecin_nom }}"
                   style="width:100%; padding:10px; border-radius:10px; border:1px solid #ccc;"
                   required>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
            <div>
                <label style="color:#264653; font-weight:bold; display:block; margin-bottom:5px;">
                    Poids (kg)
                </label>
                <input type="number" id="poids" step="0.1"
                       value="{{ optional($prescription->patient)->poids }}"
                       style="width:100%; padding:10px; border-radius:10px; border:1px solid #ccc;">
            </div>
            <div>
                <label style="color:#264653; font-weight:bold; display:block; margin-bottom:5px;">
                    Taille (cm)
                </label>
                <input type="number" id="taille" step="0.1"
                       value="{{ optional($prescription->patient)->taille }}"
                       style="width:100%; padding:10px; border-radius:10px; border:1px solid #ccc;">
            </div>
            <div>
                <label style="color:#264653; font-weight:bold; display:block; margin-bottom:5px;">
                    SC (m²)
                </label>
                <input type="text" id="sc" readonly
                       value="{{ $prescription->surface_corporelle }}"
                       style="width:100%; padding:10px; border-radius:10px;
                              border:1px solid #ccc; background:#f1f5f9; font-weight:700;">
            </div>
        </div>

        <div>
            <h3 style="color:#264653; font-weight:bold; margin-bottom:10px;">💊 Médicaments</h3>
            <div id="medicaments_container">
                @foreach($prescription->details as $detail)
                    <div style="background:white; border:1px solid #e5e7eb; border-radius:10px;
                                padding:12px; margin-bottom:8px; display:flex;
                                justify-content:space-between; align-items:center;">
                        <strong>{{ $detail->medicament->nom }}</strong>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <input type="text" value="{{ $detail->dose_calculee }} mg" readonly
                                   style="width:100px; padding:6px; border-radius:8px;
                                          border:1px solid #ccc; text-align:center;">
                            <input type="hidden"
                                   name="medicaments[{{ $detail->medicament_id }}]"
                                   value="{{ $detail->dose_calculee }}"
                                   data-dose_standard="{{ $detail->dose_standard ?? 0 }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <label style="color:#264653; font-weight:bold; display:block; margin-bottom:5px;">
                Date *
            </label>
            <input type="date" name="date_prescription"
                   value="{{ optional($prescription->date_prescription)->format('Y-m-d') }}"
                   style="width:100%; padding:10px; border-radius:10px; border:1px solid #ccc;"
                   required>
        </div>

        <div style="display:flex; gap:10px;">
            <button type="submit"
                    style="flex:1; background:#2a9d8f; color:white; border:none;
                           padding:12px; border-radius:10px; font-weight:700; cursor:pointer;">
                💾 Mettre à jour
            </button>
            <a href="{{ route('oncologie.prescriptions.index') }}"
               style="flex:1; background:#264653; color:white; padding:12px;
                      border-radius:10px; text-decoration:none; font-weight:700;
                      text-align:center;">
                Annuler
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const patientSelect = document.getElementById('patient_id');
    const poidsInput    = document.getElementById('poids');
    const tailleInput   = document.getElementById('taille');
    const scInput       = document.getElementById('sc');
    const container     = document.getElementById('medicaments_container');

    function calculerSC(p, t) { return Math.sqrt((p * t) / 3600).toFixed(2); }

    function majSC() {
        const patient = patientSelect.selectedOptions[0];
        let poids  = parseFloat(poidsInput.value)  || parseFloat(patient?.dataset.poids  || 0);
        let taille = parseFloat(tailleInput.value) || parseFloat(patient?.dataset.taille || 0);
        if (!parseFloat(poidsInput.value)  && poids)  poidsInput.value  = poids;
        if (!parseFloat(tailleInput.value) && taille) tailleInput.value = taille;
        scInput.value = (poids && taille) ? calculerSC(poids, taille) : '';
    }

    function recalculerDoses() {
        majSC();
        const sc = parseFloat(scInput.value) || 0;
        container.querySelectorAll('input[type="hidden"]').forEach(input => {
            const std = parseFloat(input.dataset.dose_standard || 0);
            if (std > 0) {
                input.value = (sc * std).toFixed(2);
                const display = input.previousElementSibling;
                if (display) display.value = input.value + ' mg';
            }
        });
    }

    patientSelect.addEventListener('change', () => { majSC(); recalculerDoses(); });
    poidsInput.addEventListener('input',    () => { majSC(); recalculerDoses(); });
    tailleInput.addEventListener('input',   () => { majSC(); recalculerDoses(); });
});
</script>
@endpush

@endsection