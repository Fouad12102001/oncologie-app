@extends('layouts.app')
@section('title', 'Ajouter un médicament')

@section('content')
<div style="max-width:800px; margin:auto;">

    <div style="display:flex; justify-content:space-between; align-items:center;
                background:white; padding:16px; border-radius:12px; margin-bottom:16px;">
        <div>
            <h2 style="margin:0; font-weight:800;">➕ Ajouter un médicament</h2>
            <p style="margin:0; font-size:13px; color:#6b7280;">IA + Scanner intelligent</p>
        </div>
        <div style="display:flex; gap:10px;">
            <button onclick="openScanner()"
                    style="background:#7c3aed; color:white; border:none; padding:9px 14px;
                           border-radius:9px; cursor:pointer; font-weight:600;">
                📷 Scanner IA
            </button>
            <button onclick="openCamera()"
                    style="background:#7c3aed; color:white; border:none; padding:9px 14px;
                           border-radius:9px; cursor:pointer; font-weight:600;">
                📱 Caméra IA
            </button>
            <a href="{{ route('oncologie.medicaments.index') }}"
               style="background:#334155; color:white; padding:9px 14px;
                      border-radius:9px; text-decoration:none; font-weight:600;">
                ⬅ Retour
            </a>
        </div>
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

        <form method="POST" action="{{ route('oncologie.medicaments.store') }}">
            @csrf

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

                <div>
                    <label style="font-weight:600; color:#1f2937; display:block; margin-bottom:6px;">
                        Nom du médicament *
                    </label>
                    <input type="text" name="nom" id="nomField"
                           value="{{ old('nom') }}" required
                           style="width:100%; padding:10px; border-radius:10px;
                                  border:1px solid #d1d5db;">
                </div>

                <div>
                    <label style="font-weight:600; color:#1f2937; display:block; margin-bottom:6px;">
                        Quantité minimale *
                    </label>
                    <input type="number" name="quantite_min" value="{{ old('quantite_min', 0) }}"
                           min="0" required
                           style="width:100%; padding:10px; border-radius:10px;
                                  border:1px solid #d1d5db;">
                </div>

                <div>
                    <label style="font-weight:600; color:#1f2937; display:block; margin-bottom:6px;">
                        Quantité initiale *
                    </label>
                    <input type="number" name="quantite_initiale" value="{{ old('quantite_initiale', 0) }}"
                           min="0" required
                           style="width:100%; padding:10px; border-radius:10px;
                                  border:1px solid #d1d5db;">
                </div>

                <div>
                    <label style="font-weight:600; color:#1f2937; display:block; margin-bottom:6px;">
                        Date de fabrication
                    </label>
                    <input type="date" name="date_fabrication" value="{{ old('date_fabrication') }}"
                           style="width:100%; padding:10px; border-radius:10px;
                                  border:1px solid #d1d5db;">
                </div>

                <div style="grid-column:span 2;">
                    <label style="font-weight:600; color:#1f2937; display:block; margin-bottom:6px;">
                        Date d'expiration
                    </label>
                    <input type="date" name="date_expiration" value="{{ old('date_expiration') }}"
                           style="width:100%; padding:10px; border-radius:10px;
                                  border:1px solid #d1d5db;">
                </div>

            </div>

            <div style="margin-top:20px; display:flex; gap:10px;">
                <button type="submit"
                        style="flex:1; background:linear-gradient(135deg,#10b981,#059669);
                               color:white; border:none; padding:12px; border-radius:10px;
                               font-weight:700; cursor:pointer; font-size:15px;">
                    💾 Enregistrer
                </button>
                <a href="{{ route('oncologie.medicaments.index') }}"
                   style="flex:1; background:#ef4444; color:white; padding:12px;
                          border-radius:10px; text-decoration:none; font-weight:700;
                          text-align:center; font-size:15px;">
                    ❌ Annuler
                </a>
            </div>
        </form>
    </div>
</div>

{{-- MODAL SCANNER --}}
<div id="scannerModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
     background:rgba(0,0,0,0.6); z-index:1000; justify-content:center; align-items:center;">
    <div style="background:white; padding:25px; border-radius:14px; width:400px; text-align:center;">
        <h3 style="margin-bottom:15px;">📷 Scanner IA</h3>
        <input type="file" id="imageInput" accept="image/*" style="margin-bottom:10px;">
        <br>
        <button onclick="sendImage()"
                style="background:#10b981; color:white; border:none; padding:10px 20px;
                       border-radius:8px; cursor:pointer; font-weight:600; margin:5px;">
            🔍 Analyser
        </button>
        <p id="result" style="margin:10px 0; color:#374151;"></p>
        <button onclick="closeScanner()"
                style="background:#ef4444; color:white; border:none; padding:8px 16px;
                       border-radius:8px; cursor:pointer; font-weight:600;">
            Fermer
        </button>
    </div>
</div>

{{-- MODAL CAMERA --}}
<div id="cameraModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
     background:rgba(0,0,0,0.6); z-index:1000; justify-content:center; align-items:center;">
    <div style="background:white; padding:20px; border-radius:14px; width:460px; text-align:center;">
        <h3 style="margin-bottom:12px;">📱 Caméra IA HD</h3>
        <video id="video" autoplay playsinline
               style="width:100%; border-radius:10px; margin-bottom:10px;"></video>
        <div style="display:flex; gap:8px; justify-content:center; margin-bottom:10px;">
            <button onclick="switchCamera()"
                    style="background:#7c3aed; color:white; border:none;
                           padding:8px 14px; border-radius:8px; cursor:pointer; font-weight:600;">
                🔄 Switch
            </button>
            <button onclick="capture()"
                    style="background:#10b981; color:white; border:none;
                           padding:8px 14px; border-radius:8px; cursor:pointer; font-weight:600;">
                📸 Capturer HD
            </button>
        </div>
        <p id="cameraResult" style="color:#374151; margin:8px 0;"></p>
        <button onclick="closeCamera()"
                style="background:#ef4444; color:white; border:none; padding:8px 16px;
                       border-radius:8px; cursor:pointer; font-weight:600;">
            Fermer
        </button>
    </div>
</div>

@push('scripts')
<script>
let stream = null;
let facingMode = "environment";

function openScanner() {
    document.getElementById("scannerModal").style.display = "flex";
}
function closeScanner() {
    document.getElementById("scannerModal").style.display = "none";
}

async function sendImage() {
    let file = document.getElementById("imageInput").files[0];
    if (!file) { alert("❌ Choisir une image"); return; }
    let form = new FormData();
    form.append("file", file);
    try {
        let res  = await fetch("http://localhost:8001/scan", { method:"POST", body:form });
        let data = await res.json();
        let text = (data.nom_detecte || "").replace(/\n/g," ").trim();
        document.getElementById("result").innerText = text;
        document.getElementById("nomField").value = text;
    } catch(e) {
        alert("❌ API IA inaccessible");
    }
}

async function openCamera() {
    document.getElementById("cameraModal").style.display = "flex";
    if (stream) stream.getTracks().forEach(t => t.stop());
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: { ideal: facingMode }, width: { ideal: 1920 }, height: { ideal: 1080 } },
            audio: false
        });
        const video = document.getElementById("video");
        video.srcObject = stream;
        await video.play();
    } catch(err) {
        alert("❌ Caméra bloquée ou non disponible");
    }
}

function switchCamera() {
    facingMode = (facingMode === "environment") ? "user" : "environment";
    if (stream) stream.getTracks().forEach(t => t.stop());
    openCamera();
}

function closeCamera() {
    document.getElementById("cameraModal").style.display = "none";
    if (stream) stream.getTracks().forEach(t => t.stop());
}

async function capture() {
    const video  = document.getElementById("video");
    const canvas = document.createElement("canvas");
    const scale  = 2;
    canvas.width  = video.videoWidth  * scale;
    canvas.height = video.videoHeight * scale;
    const ctx = canvas.getContext("2d");
    ctx.imageSmoothingEnabled = true;
    ctx.imageSmoothingQuality = "high";
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    canvas.toBlob(async (blob) => {
        let form = new FormData();
        form.append("file", blob, "camera.jpg");
        try {
            let res  = await fetch("http://127.0.0.1:8001/scan", { method:"POST", body:form });
            let data = await res.json();
            let text = (data.nom_detecte || "").replace(/\n/g," ").replace(/\s+/g," ").trim().substring(0,120);
            document.getElementById("cameraResult").innerText = text;
            document.getElementById("nomField").value = text;
            closeCamera();
        } catch(e) {
            document.getElementById("cameraResult").innerText = "❌ erreur IA";
        }
    }, "image/jpeg", 0.95);
}
</script>
@endpush

@endsection