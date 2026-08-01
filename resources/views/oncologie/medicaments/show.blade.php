@extends('layouts.app')
@section('title', 'Détail médicament')

@section('content')
@php
    $stock    = $medicament->stockActuel();
    $statSt   = $medicament->statutStock();
    $statExp  = $medicament->statutExpiration();

    $colorStock = match($statSt) {
        'rupture' => ['bg'=>'#fee2e2','c'=>'#991b1b','label'=>'RUPTURE'],
        'alerte'  => ['bg'=>'#fef3c7','c'=>'#92400e','label'=>'ALERTE'],
        default   => ['bg'=>'#dcfce7','c'=>'#166534','label'=>'OK'],
    };
    $colorExp = match($statExp) {
        'expired' => ['bg'=>'#fecaca','c'=>'#7f1d1d','label'=>'EXPIRÉ'],
        'soon'    => ['bg'=>'#ffedd5','c'=>'#9a3412','label'=>'BIENTÔT'],
        default   => ['bg'=>'#dcfce7','c'=>'#166534','label'=>'OK'],
    };
@endphp

<div style="max-width:900px; margin:auto;">

    <div style="display:flex; justify-content:space-between; align-items:center;
                background:white; padding:16px; border-radius:12px; margin-bottom:16px;">
        <h2 style="margin:0; font-weight:800;">💊 Détails du médicament</h2>
        <a href="{{ route('oncologie.medicaments.index') }}"
           style="background:#334155; color:white; padding:9px 14px;
                  border-radius:9px; text-decoration:none; font-weight:600;">
            ⬅ Retour
        </a>
    </div>

    {{-- 3 CARTES --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:16px;">

        <div style="background:white; padding:16px; border-radius:14px;
                    box-shadow:0 4px 14px rgba(0,0,0,0.05); border-left:4px solid #2a9d8f;">
            <h4 style="color:#264653; margin-bottom:12px;">📦 Informations générales</h4>
            <p><strong>Nom :</strong> {{ $medicament->nom }}</p>
            <p style="margin-top:8px;"><strong>Stock actuel :</strong>
                <span style="background:{{ $colorStock['bg'] }}; color:{{ $colorStock['c'] }};
                             padding:3px 10px; border-radius:999px; font-weight:700;">
                    {{ $stock }}
                </span>
            </p>
            <p style="margin-top:8px;"><strong>Quantité minimale :</strong> {{ $medicament->quantite_min }}</p>
        </div>

        <div style="background:white; padding:16px; border-radius:14px;
                    box-shadow:0 4px 14px rgba(0,0,0,0.05); border-left:4px solid #0ea5e9;">
            <h4 style="color:#264653; margin-bottom:12px;">📅 Dates</h4>
            <p><strong>Fabrication :</strong><br>
                {{ $medicament->date_fabrication ? $medicament->date_fabrication->format('d/m/Y') : '-' }}
            </p>
            <p style="margin-top:8px;"><strong>Expiration :</strong><br>
                <span style="background:{{ $colorExp['bg'] }}; color:{{ $colorExp['c'] }};
                             padding:3px 10px; border-radius:999px; font-weight:700;">
                    {{ $medicament->date_expiration ? $medicament->date_expiration->format('d/m/Y') : '-' }}
                </span>
            </p>
        </div>

        <div style="background:white; padding:16px; border-radius:14px;
                    box-shadow:0 4px 14px rgba(0,0,0,0.05); border-left:4px solid #f59e0b;">
            <h4 style="color:#264653; margin-bottom:12px;">⚠️ Statut</h4>
            <p><strong>État stock :</strong><br>
                <span style="background:{{ $colorStock['bg'] }}; color:{{ $colorStock['c'] }};
                             padding:4px 12px; border-radius:999px; font-weight:700;">
                    {{ $colorStock['label'] }}
                </span>
            </p>
            <p style="margin-top:10px;"><strong>État expiration :</strong><br>
                <span style="background:{{ $colorExp['bg'] }}; color:{{ $colorExp['c'] }};
                             padding:4px 12px; border-radius:999px; font-weight:700;">
                    {{ $colorExp['label'] }}
                </span>
            </p>
        </div>
    </div>

    {{-- ALERTES --}}
    @if($statSt === 'rupture')
        <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:10px;
                    border-left:4px solid #ef4444; margin-bottom:10px; font-weight:600;">
            ❌ Stock en rupture totale
        </div>
    @elseif($statSt === 'alerte')
        <div style="background:#fff7ed; color:#92400e; padding:12px; border-radius:10px;
                    border-left:4px solid #f59e0b; margin-bottom:10px; font-weight:600;">
            ⚠️ Stock critique (en dessous du minimum)
        </div>
    @else
        <div style="background:#dcfce7; color:#166534; padding:12px; border-radius:10px;
                    border-left:4px solid #22c55e; margin-bottom:10px; font-weight:600;">
            ✅ Stock normal
        </div>
    @endif

    @if($statExp === 'expired')
        <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:10px;
                    border-left:4px solid #ef4444; margin-bottom:10px; font-weight:600;">
            ❌ Médicament expiré — dispensation impossible
        </div>
    @elseif($statExp === 'soon')
        <div style="background:#fff7ed; color:#92400e; padding:12px; border-radius:10px;
                    border-left:4px solid #f59e0b; margin-bottom:10px; font-weight:600;">
            ⚠️ Médicament bientôt expiré
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════
         🔮 PRÉVISION DE RUPTURE DE STOCK (Holt-Winters / IA)
    ═══════════════════════════════════════════════════ --}}
    <div style="background:white; padding:20px; border-radius:14px;
                box-shadow:0 4px 14px rgba(0,0,0,0.05); border-left:4px solid #7c3aed;
                margin-top:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h4 style="color:#264653; margin:0;">🔮 Prévision de rupture de stock (IA)</h4>
            <button type="button" onclick="chargerPrevision()" id="btnPrevision"
                    style="background:#7c3aed; color:white; border:none; padding:8px 16px;
                           border-radius:8px; cursor:pointer; font-weight:600; font-size:13px;">
                📊 Analyser
            </button>
        </div>

        <div id="previsionPlaceholder" style="color:#9ca3af; font-size:14px; text-align:center; padding:20px;">
            Clique sur "Analyser" pour lancer la prévision basée sur les 60 derniers jours de sorties.
        </div>

        <div id="previsionContent" style="display:none;">
            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:16px;">
                <div style="background:#f5f3ff; padding:12px; border-radius:10px; text-align:center;">
                    <div style="font-size:12px; color:#6b7280; font-weight:600;">Jours avant rupture</div>
                    <div id="previsionJours" style="font-size:22px; font-weight:800; color:#7c3aed;">-</div>
                </div>
                <div style="background:#f5f3ff; padding:12px; border-radius:10px; text-align:center;">
                    <div style="font-size:12px; color:#6b7280; font-weight:600;">Date de rupture estimée</div>
                    <div id="previsionDate" style="font-size:16px; font-weight:800; color:#7c3aed;">-</div>
                </div>
                <div style="background:#f5f3ff; padding:12px; border-radius:10px; text-align:center;">
                    <div style="font-size:12px; color:#6b7280; font-weight:600;">Fiabilité</div>
                    <div id="previsionFiabilite" style="font-size:16px; font-weight:800;">-</div>
                </div>
            </div>
            <canvas id="previsionChart" height="80"></canvas>
            <p id="previsionMethode" style="font-size:12px; color:#9ca3af; margin-top:8px; text-align:right;"></p>
        </div>

        <div id="previsionError" style="display:none; background:#fee2e2; color:#991b1b;
                    padding:12px; border-radius:8px; margin-top:10px; font-size:14px;"></div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         🚨 DÉTECTION D'ANOMALIES SUR LES SORTIES (z-score / IA)
    ═══════════════════════════════════════════════════ --}}
    <div style="background:white; padding:20px; border-radius:14px;
                box-shadow:0 4px 14px rgba(0,0,0,0.05); border-left:4px solid #dc2626;
                margin-top:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h4 style="color:#264653; margin:0;">🚨 Détection d'anomalies sur les sorties (IA)</h4>
            <button type="button" onclick="chargerAnomalies()" id="btnAnomalies"
                    style="background:#dc2626; color:white; border:none; padding:8px 16px;
                           border-radius:8px; cursor:pointer; font-weight:600; font-size:13px;">
                🔍 Analyser
            </button>
        </div>

        <div id="anomaliesPlaceholder" style="color:#9ca3af; font-size:14px; text-align:center; padding:20px;">
            Clique sur "Analyser" pour détecter les sorties de stock statistiquement anormales (z-score).
        </div>

        <div id="anomaliesContent" style="display:none;">
            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:16px;">
                <div style="background:#fef2f2; padding:12px; border-radius:10px; text-align:center;">
                    <div style="font-size:12px; color:#6b7280; font-weight:600;">Moyenne des sorties</div>
                    <div id="anomaliesMoyenne" style="font-size:20px; font-weight:800; color:#dc2626;">-</div>
                </div>
                <div style="background:#fef2f2; padding:12px; border-radius:10px; text-align:center;">
                    <div style="font-size:12px; color:#6b7280; font-weight:600;">Écart-type</div>
                    <div id="anomaliesEcartType" style="font-size:20px; font-weight:800; color:#dc2626;">-</div>
                </div>
                <div style="background:#fef2f2; padding:12px; border-radius:10px; text-align:center;">
                    <div style="font-size:12px; color:#6b7280; font-weight:600;">Anomalies détectées</div>
                    <div id="anomaliesCount" style="font-size:20px; font-weight:800; color:#dc2626;">-</div>
                </div>
            </div>

            <table id="anomaliesTable" style="width:100%; border-collapse:collapse; display:none;">
                <thead>
                    <tr style="background:#fef2f2; color:#991b1b;">
                        <th style="padding:8px; text-align:left; font-size:12px;">Date</th>
                        <th style="padding:8px; text-align:center; font-size:12px;">Quantité sortie</th>
                        <th style="padding:8px; text-align:center; font-size:12px;">Z-score</th>
                    </tr>
                </thead>
                <tbody id="anomaliesTableBody"></tbody>
            </table>

            <div id="anomaliesAucune" style="display:none; background:#dcfce7; color:#166534;
                        padding:10px; border-radius:8px; text-align:center; font-weight:600; font-size:14px;">
                ✅ Aucune sortie anormale détectée — les mouvements de stock sont cohérents.
            </div>

            <div id="anomaliesInsuffisant" style="display:none; background:#fff7ed; color:#92400e;
                        padding:10px; border-radius:8px; text-align:center; font-weight:600; font-size:14px;">
                ℹ️ Historique insuffisant (moins de 5 sorties) pour une détection fiable.
            </div>
        </div>

        <div id="anomaliesError" style="display:none; background:#fee2e2; color:#991b1b;
                    padding:12px; border-radius:8px; margin-top:10px; font-size:14px;"></div>
    </div>

    {{-- ACTIONS --}}
    <div style="display:flex; gap:10px; margin-top:16px;">
        <a href="{{ route('oncologie.medicaments.edit', $medicament->id) }}"
           style="background:#f59e0b; color:white; padding:10px 18px;
                  border-radius:10px; text-decoration:none; font-weight:700;">
            ✏️ Modifier
        </a>
        <a href="{{ route('oncologie.medicaments.lots', $medicament->id) }}"
           style="background:#8b5cf6; color:white; padding:10px 18px;
                  border-radius:10px; text-decoration:none; font-weight:700;">
            📦 Voir les lots
        </a>
        <form method="POST"
              action="{{ route('oncologie.medicaments.destroy', $medicament->id) }}"
              onsubmit="return confirm('Supprimer ce médicament ?')">
            @csrf @method('DELETE')
            <button style="background:#ef4444; color:white; border:none; padding:10px 18px;
                           border-radius:10px; font-weight:700; cursor:pointer;">
                🗑 Supprimer
            </button>
        </form>
    </div>
</div>

@push('scripts')
{{-- Chart.js : ne recharge pas la librairie si elle est déjà incluse par le layout global --}}
@if(!isset($chartJsAlreadyLoaded))
<script>
    if (typeof Chart === "undefined") {
        document.write('<script src="https://cdn.jsdelivr.net/npm/chart.js"><\/script>');
    }
</script>
@endif

<script>
const PREVISION_URL = "{{ route('oncologie.medicaments.prevision-stock', $medicament->id) }}";
const ANOMALIES_URL = "{{ route('oncologie.medicaments.detecter-anomalies', $medicament->id) }}";

let previsionChartInstance = null;

function setBtnLoading(btnId, loading, labelDefault) {
    const btn = document.getElementById(btnId);
    btn.disabled = loading;
    btn.innerText = loading ? "⏳ Analyse..." : labelDefault;
}

// ═══════════════════════════════════════
// PRÉVISION DE RUPTURE
// ═══════════════════════════════════════
async function chargerPrevision() {
    setBtnLoading("btnPrevision", true, "📊 Analyser");
    document.getElementById("previsionError").style.display = "none";

    try {
        let res = await fetch(PREVISION_URL, {
            headers: { "Accept": "application/json" },
        });

        if (!res.ok) throw new Error("HTTP " + res.status);
        let data = await res.json();

        if (data.status === "error") {
            throw new Error(data.message || "Erreur du service IA");
        }

        document.getElementById("previsionPlaceholder").style.display = "none";
        document.getElementById("previsionContent").style.display = "block";

        document.getElementById("previsionJours").innerText =
            data.jours_avant_rupture !== null ? data.jours_avant_rupture + " j" : "Pas de rupture prévue";
        document.getElementById("previsionDate").innerText =
            data.date_rupture_estimee ? new Date(data.date_rupture_estimee).toLocaleDateString('fr-FR') : "-";

        const fiabiliteColors = { bonne: "#166534", moyenne: "#92400e", faible: "#991b1b" };
        const fiabEl = document.getElementById("previsionFiabilite");
        fiabEl.innerText = (data.fiabilite || "-").toUpperCase();
        fiabEl.style.color = fiabiliteColors[data.fiabilite] || "#374151";

        document.getElementById("previsionMethode").innerText =
            "Méthode : " + (data.methode || "-");

        // Graphique de la consommation prévue jour par jour
        const ctx = document.getElementById("previsionChart").getContext("2d");
        const labels = (data.consommation_prevue_par_jour || []).map((_, i) => "J+" + (i + 1));

        if (previsionChartInstance) previsionChartInstance.destroy();
        previsionChartInstance = new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                    label: "Consommation prévue / jour",
                    data: data.consommation_prevue_par_jour || [],
                    borderColor: "#7c3aed",
                    backgroundColor: "rgba(124,58,237,0.1)",
                    fill: true,
                    tension: 0.3,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } },
            },
        });
    } catch (e) {
        document.getElementById("previsionPlaceholder").style.display = "none";
        document.getElementById("previsionContent").style.display = "none";
        const errEl = document.getElementById("previsionError");
        errEl.style.display = "block";
        errEl.innerText = "❌ " + e.message;
        console.error(e);
    } finally {
        setBtnLoading("btnPrevision", false, "📊 Analyser");
    }
}

// ═══════════════════════════════════════
// DÉTECTION D'ANOMALIES
// ═══════════════════════════════════════
async function chargerAnomalies() {
    setBtnLoading("btnAnomalies", true, "🔍 Analyser");
    document.getElementById("anomaliesError").style.display = "none";

    try {
        let res = await fetch(ANOMALIES_URL, {
            headers: { "Accept": "application/json" },
        });

        if (!res.ok) throw new Error("HTTP " + res.status);
        let data = await res.json();

        if (data.status === "error") {
            throw new Error(data.message || "Erreur du service IA");
        }

        document.getElementById("anomaliesPlaceholder").style.display = "none";
        document.getElementById("anomaliesContent").style.display = "block";

        document.getElementById("anomaliesMoyenne").innerText = data.moyenne ?? "-";
        document.getElementById("anomaliesEcartType").innerText = data.ecart_type ?? "-";
        document.getElementById("anomaliesCount").innerText = (data.anomalies || []).length;

        const table = document.getElementById("anomaliesTable");
        const tbody = document.getElementById("anomaliesTableBody");
        const aucune = document.getElementById("anomaliesAucune");
        const insuffisant = document.getElementById("anomaliesInsuffisant");

        table.style.display = "none";
        aucune.style.display = "none";
        insuffisant.style.display = "none";
        tbody.innerHTML = "";

        // moyenne=0 et ecart_type=0 avec 0 anomalie -> pas assez d'historique (< 5 points), cf anomaly_detection.py
        if (data.moyenne === 0 && data.ecart_type === 0 && (data.anomalies || []).length === 0) {
            insuffisant.style.display = "block";
        } else if ((data.anomalies || []).length === 0) {
            aucune.style.display = "block";
        } else {
            table.style.display = "table";
            data.anomalies.forEach(a => {
                const tr = document.createElement("tr");
                tr.style.borderBottom = "1px solid #f1f5f9";
                tr.innerHTML = `
                    <td style="padding:8px;">${new Date(a.date).toLocaleDateString('fr-FR')}</td>
                    <td style="padding:8px; text-align:center; font-weight:700; color:#dc2626;">${a.quantite}</td>
                    <td style="padding:8px; text-align:center;">${a.zscore}</td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (e) {
        document.getElementById("anomaliesPlaceholder").style.display = "none";
        document.getElementById("anomaliesContent").style.display = "none";
        const errEl = document.getElementById("anomaliesError");
        errEl.style.display = "block";
        errEl.innerText = "❌ " + e.message;
        console.error(e);
    } finally {
        setBtnLoading("btnAnomalies", false, "🔍 Analyser");
    }
}
</script>
@endpush

@endsection