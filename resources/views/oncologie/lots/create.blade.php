@extends('layouts.app')
@section('title', 'Ajouter un Lot')

@section('content')
<div style="max-width:700px; margin:auto;">

    <div style="display:flex; justify-content:space-between; align-items:center;
                background:white; padding:16px; border-radius:12px; margin-bottom:16px;">
        <h2 style="margin:0; font-weight:800;">📦 Ajouter un Lot</h2>
        <div style="display:flex; gap:10px;">
            {{-- Bouton visible pour tout utilisateur ayant accès à cette page
                 (pas de @canOnco ici : la permission 'lots.scan_ia' n'existe pas
                 forcément dans ta table RBAC — ajoute-la plus tard si tu veux
                 restreindre le scanner à certains rôles uniquement) --}}
            <button type="button" onclick="openBarcodeScanner()"
                    style="background:#7c3aed; color:white; border:none; padding:9px 14px;
                           border-radius:9px; cursor:pointer; font-weight:600;">
                📷 Scanner code-barres
            </button>
            <a href="{{ route('oncologie.lots.index') }}"
               style="background:#334155; color:white; padding:9px 14px;
                      border-radius:9px; text-decoration:none; font-weight:600;">⬅ Retour</a>
        </div>
    </div>

    <div style="background:white; border-left:6px solid #2a9d8f; border-radius:14px;
                padding:24px; box-shadow:0 6px 20px rgba(0,0,0,0.06);">

        @if($errors->any())
            <div style="background:#ffe5e5; border-left:4px solid #e63946; color:#7a1c1c;
                        padding:12px; border-radius:8px; margin-bottom:16px;">
                @foreach($errors->all() as $e)<div>⚠ {{ $e }}</div>@endforeach
            </div>
        @endif

        <div id="barcodeInfo" style="display:none; background:#ecfdf5; border-left:4px solid #10b981;
                    color:#065f46; padding:10px 14px; border-radius:8px; margin-bottom:16px; font-size:14px;">
        </div>

        <form action="{{ route('oncologie.lots.store') }}" method="POST">
            @csrf

            <div style="margin-bottom:14px;">
                <label style="font-weight:600; display:block; margin-bottom:6px;">
                    Médicament *
                </label>
                <select name="medicament_id" id="medicamentField" required
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
                <small style="display:block; margin-top:4px; color:#6b7280;">
                    Le scanner ne détecte pas le nom du médicament (seulement lot/dates/GTIN) — sélectionnez-le manuellement.
                </small>
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-weight:600; display:block; margin-bottom:6px;">
                    Numéro de lot *
                </label>
                <input type="text" name="numero" id="numeroField" value="{{ old('numero') }}" required
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
                    <input type="date" name="date_fabrication" id="dateFabricationField"
                           value="{{ old('date_fabrication') }}"
                           style="width:100%; padding:10px; border-radius:10px;
                                  border:1px solid #d1d5db;">
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label style="font-weight:600; display:block; margin-bottom:6px;">
                    Date d'expiration *
                </label>
                <input type="date" name="date_expiration" id="dateExpirationField"
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

{{-- MODAL SCANNER CODE-BARRES --}}
<div id="barcodeModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
     background:rgba(0,0,0,0.6); z-index:1000; justify-content:center; align-items:center;">
    <div style="background:white; padding:25px; border-radius:14px; width:420px; text-align:center;">
        <h3 style="margin-bottom:6px;">📷 Scanner code-barres GS1</h3>
        <p style="font-size:12px; color:#6b7280; margin-bottom:15px;">
            Photographiez le DataMatrix/QR sur l'emballage (lot, expiration, fabrication)
        </p>
        <input type="file" id="barcodeInput" accept="image/*" style="margin-bottom:10px;">
        <br>
        <button type="button" onclick="sendBarcodeImage()" id="btnAnalyserBarcode"
                style="background:#10b981; color:white; border:none; padding:10px 20px;
                       border-radius:8px; cursor:pointer; font-weight:600; margin:5px;">
            🔍 Analyser
        </button>
        <div id="barcodeResult" style="margin:10px 0; color:#374151; font-size:14px;"></div>
        <button type="button" onclick="closeBarcodeScanner()"
                style="background:#ef4444; color:white; border:none; padding:8px 16px;
                       border-radius:8px; cursor:pointer; font-weight:600; margin-top:6px;">
            Fermer
        </button>
    </div>
</div>

@push('scripts')
<script>
const SCAN_BARCODE_URL = "{{ route('oncologie.medicaments.scan-code-barres') }}";
const CSRF_TOKEN_LOT = "{{ csrf_token() }}";

function openBarcodeScanner() {
    document.getElementById("barcodeModal").style.display = "flex";
    document.getElementById("barcodeResult").innerText = "";
}
function closeBarcodeScanner() {
    document.getElementById("barcodeModal").style.display = "none";
}

async function sendBarcodeImage() {
    let file = document.getElementById("barcodeInput").files[0];
    if (!file) { alert("❌ Choisir une image"); return; }

    let form = new FormData();
    form.append("file", file);

    const btn = document.getElementById("btnAnalyserBarcode");
    btn.disabled = true;
    btn.innerText = "⏳ Analyse...";
    const resultEl = document.getElementById("barcodeResult");

    try {
        let res = await fetch(SCAN_BARCODE_URL, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": CSRF_TOKEN_LOT,
                "Accept": "application/json",
            },
            body: form,
        });

        if (res.status === 419) {
            resultEl.innerText = "❌ Session expirée, veuillez recharger la page.";
            return;
        }

        let data = await res.json();

        if (data.status === "success") {
            let filled = [];

            if (data.numero_lot) {
                document.getElementById("numeroField").value = data.numero_lot;
                filled.push("N° lot");
            }
            if (data.date_expiration) {
                document.getElementById("dateExpirationField").value = data.date_expiration;
                filled.push("expiration");
            }
            if (data.date_fabrication) {
                document.getElementById("dateFabricationField").value = data.date_fabrication;
                filled.push("fabrication");
            }

            if (filled.length > 0) {
                const sourceLabels = {
                    gs1_datamatrix: { label: "DataMatrix GS1", fiable: true },
                    code_simple:    { label: "Code-barres simple (GTIN)", fiable: true },
                    qr_texte_libre: { label: "QR / texte libre", fiable: false },
                    ocr:            { label: "Lecture OCR (texte imprimé)", fiable: false },
                };
                const src = sourceLabels[data.source] || { label: data.source || "inconnue", fiable: false };

                resultEl.innerHTML = "✅ Champs remplis : " + filled.join(", ") +
                    (data.gtin ? `<br><span style="color:#6b7280;font-size:12px;">GTIN : ${data.gtin}</span>` : "");

                const info = document.getElementById("barcodeInfo");
                info.style.display = "block";
                if (src.fiable) {
                    info.style.background = "#ecfdf5";
                    info.style.borderLeftColor = "#10b981";
                    info.style.color = "#065f46";
                    info.innerText = `✅ Détecté via ${src.label} — champs fiables, vérifiez avant d'enregistrer.`;
                } else {
                    info.style.background = "#fff7ed";
                    info.style.borderLeftColor = "#f59e0b";
                    info.style.color = "#92400e";
                    info.innerText = `⚠️ Détecté via ${src.label} — moins fiable qu'un DataMatrix GS1, vérifiez attentivement les champs avant d'enregistrer.`;
                }

                closeBarcodeScanner();
            } else {
                resultEl.innerText = "⚠️ Code détecté mais aucun champ GS1 reconnu (lot/dates absents de ce code).";
            }
        } else if (data.status === "no_match") {
            resultEl.innerText = "❌ " + (data.message || "Aucun code-barres détecté.");
        } else {
            resultEl.innerText = "❌ " + (data.message || "Erreur lors du scan.");
        }
    } catch (e) {
        resultEl.innerText = "❌ Impossible de contacter le serveur.";
        console.error(e);
    } finally {
        btn.disabled = false;
        btn.innerText = "🔍 Analyser";
    }
}
</script>
@endpush

@endsection