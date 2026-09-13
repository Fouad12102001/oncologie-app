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

<style>
    .mf-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(15,23,42,0.06), 0 1px 2px rgba(15,23,42,0.04);
        transition: box-shadow .2s ease, transform .2s ease;
    }
    .mf-card:hover { box-shadow: 0 8px 24px rgba(15,23,42,0.10); }
    .mf-kpi {
        border-radius: 12px;
        padding: 14px;
        text-align: center;
        transition: transform .15s ease;
    }
    .mf-kpi:hover { transform: translateY(-2px); }
    .mf-btn {
        border: none;
        cursor: pointer;
        font-weight: 600;
        border-radius: 10px;
        padding: 9px 16px;
        font-size: 13px;
        transition: filter .15s ease, transform .1s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .mf-btn:hover { filter: brightness(0.93); }
    .mf-btn:active { transform: scale(0.97); }
    .mf-btn:disabled { opacity: .6; cursor: not-allowed; }
    .mf-badge {
        padding: 3px 10px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 12px;
        display: inline-block;
    }
    .mf-select {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        background: #f8fafc;
    }
    .mf-fade-in { animation: mfFadeIn .25s ease; }
    @keyframes mfFadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .mf-severite-moderee { background:#fef3c7; color:#92400e; }
    .mf-severite-elevee  { background:#ffedd5; color:#c2410c; }
    .mf-severite-critique{ background:#fee2e2; color:#991b1b; }
    .mf-table th { position: sticky; top: 0; }
    .mf-row-anomalie:hover { background:#fef2f2 !important; }
</style>

<div style="max-width:960px; margin:auto;">

    {{-- EN-TÊTE --}}
    <div style="display:flex; justify-content:space-between; align-items:center;
                background:linear-gradient(135deg,#0f172a,#1e293b); color:white;
                padding:18px 20px; border-radius:16px; margin-bottom:16px;">
        <div>
            <h2 style="margin:0; font-weight:800; font-size:20px;">💊 {{ $medicament->nom }}</h2>
            <p style="margin:4px 0 0; font-size:13px; color:#94a3b8;">Fiche détaillée du médicament</p>
        </div>
        <a href="{{ route('oncologie.medicaments.index') }}"
           style="background:rgba(255,255,255,0.12); color:white; padding:9px 14px;
                  border-radius:9px; text-decoration:none; font-weight:600; font-size:13px;">
            ⬅ Retour
        </a>
    </div>

    {{-- 3 CARTES --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:16px;">

        <div class="mf-card" style="padding:16px; border-left:4px solid #2a9d8f;">
            <h4 style="color:#264653; margin-bottom:12px;">📦 Informations générales</h4>
            <p><strong>Nom :</strong> {{ $medicament->nom }}</p>
            <p style="margin-top:8px;"><strong>Stock actuel :</strong>
                <span class="mf-badge" style="background:{{ $colorStock['bg'] }}; color:{{ $colorStock['c'] }};">
                    {{ $stock }}
                </span>
            </p>
            <p style="margin-top:8px;"><strong>Quantité minimale :</strong> {{ $medicament->quantite_min }}</p>
        </div>

        <div class="mf-card" style="padding:16px; border-left:4px solid #0ea5e9;">
            <h4 style="color:#264653; margin-bottom:12px;">📅 Dates</h4>
            <p><strong>Fabrication :</strong><br>
                {{ $medicament->date_fabrication ? $medicament->date_fabrication->format('d/m/Y') : '-' }}
            </p>
            <p style="margin-top:8px;"><strong>Expiration :</strong><br>
                <span class="mf-badge" style="background:{{ $colorExp['bg'] }}; color:{{ $colorExp['c'] }};">
                    {{ $medicament->date_expiration ? $medicament->date_expiration->format('d/m/Y') : '-' }}
                </span>
            </p>
        </div>

        <div class="mf-card" style="padding:16px; border-left:4px solid #f59e0b;">
            <h4 style="color:#264653; margin-bottom:12px;">⚠️ Statut</h4>
            <p><strong>État stock :</strong><br>
                <span class="mf-badge" style="background:{{ $colorStock['bg'] }}; color:{{ $colorStock['c'] }}; padding:4px 12px;">
                    {{ $colorStock['label'] }}
                </span>
            </p>
            <p style="margin-top:10px;"><strong>État expiration :</strong><br>
                <span class="mf-badge" style="background:{{ $colorExp['bg'] }}; color:{{ $colorExp['c'] }}; padding:4px 12px;">
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
    <div class="mf-card" style="padding:20px; border-left:4px solid #7c3aed; margin-top:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h4 style="color:#264653; margin:0;">🔮 Prévision de rupture de stock (IA)</h4>
            <button type="button" onclick="chargerPrevision()" id="btnPrevision" class="mf-btn"
                    style="background:#7c3aed; color:white;">
                📊 Analyser
            </button>
        </div>

        <div id="previsionPlaceholder" style="color:#9ca3af; font-size:14px; text-align:center; padding:20px;">
            Clique sur "Analyser" pour lancer la prévision basée sur les 60 derniers jours de sorties.
        </div>

        <div id="previsionContent" style="display:none;" class="mf-fade-in">
            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:16px;">
                <div class="mf-kpi" style="background:#f5f3ff;">
                    <div style="font-size:12px; color:#6b7280; font-weight:600;">Jours avant rupture</div>
                    <div id="previsionJours" style="font-size:22px; font-weight:800; color:#7c3aed;">-</div>
                </div>
                <div class="mf-kpi" style="background:#f5f3ff;">
                    <div style="font-size:12px; color:#6b7280; font-weight:600;">Date de rupture estimée</div>
                    <div id="previsionDate" style="font-size:16px; font-weight:800; color:#7c3aed;">-</div>
                </div>
                <div class="mf-kpi" style="background:#f5f3ff;">
                    <div style="font-size:12px; color:#6b7280; font-weight:600;">Fiabilité</div>
                    <div id="previsionFiabilite" style="font-size:16px; font-weight:800;">-</div>
                </div>
            </div>
            <canvas id="previsionChart" height="80"></canvas>
            <p id="previsionMethode" style="font-size:12px; color:#9ca3af; margin-top:8px; text-align:right;"></p>
            <div id="previsionRecommandation" style="margin-top:12px; padding:12px; border-radius:10px;
                        font-size:13px; font-weight:600; display:none;"></div>
        </div>

        <div id="previsionError" style="display:none; background:#fee2e2; color:#991b1b;
                    padding:12px; border-radius:8px; margin-top:10px; font-size:14px;"></div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         🚨 DÉTECTION D'ANOMALIES SUR LES SORTIES (z-score / IA)
    ═══════════════════════════════════════════════════ --}}
    <div class="mf-card" style="padding:20px; border-left:4px solid #dc2626; margin-top:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; flex-wrap:wrap; gap:10px;">
            <h4 style="color:#264653; margin:0;">🚨 Détection d'anomalies sur les sorties (IA)</h4>
            <div style="display:flex; align-items:center; gap:8px;">
                <select id="anomaliesPeriode" class="mf-select">
                    <option value="30">30 derniers jours</option>
                    <option value="60" selected>60 derniers jours</option>
                    <option value="90">90 derniers jours</option>
                    <option value="180">180 derniers jours</option>
                </select>
                <select id="anomaliesSeuil" class="mf-select" title="Seuil de sensibilité (z-score)">
                    <option value="2">Sensible (|z| > 2)</option>
                    <option value="2.5" selected>Standard (|z| > 2.5)</option>
                    <option value="3">Strict (|z| > 3)</option>
                </select>
                <button type="button" onclick="chargerAnomalies()" id="btnAnomalies" class="mf-btn"
                        style="background:#dc2626; color:white;">
                    🔍 Analyser
                </button>
            </div>
        </div>
        <p style="font-size:12px; color:#9ca3af; margin:0 0 12px;">
            Le z-score mesure l'écart d'une sortie par rapport à la moyenne, en nombre d'écarts-types.
            Plus il est élevé, plus la sortie est statistiquement inhabituelle.
        </p>

        <div id="anomaliesPlaceholder" style="color:#9ca3af; font-size:14px; text-align:center; padding:20px;">
            Clique sur "Analyser" pour détecter les sorties de stock statistiquement anormales (z-score).
        </div>

        <div id="anomaliesContent" style="display:none;" class="mf-fade-in">
            {{-- KPIs enrichis --}}
            <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:10px; margin-bottom:16px;">
                <div class="mf-kpi" style="background:#fef2f2;">
                    <div style="font-size:11px; color:#6b7280; font-weight:600;">Moyenne</div>
                    <div id="anomaliesMoyenne" style="font-size:18px; font-weight:800; color:#dc2626;">-</div>
                </div>
                <div class="mf-kpi" style="background:#fef2f2;">
                    <div style="font-size:11px; color:#6b7280; font-weight:600;">Écart-type</div>
                    <div id="anomaliesEcartType" style="font-size:18px; font-weight:800; color:#dc2626;">-</div>
                </div>
                <div class="mf-kpi" style="background:#fef2f2;">
                    <div style="font-size:11px; color:#6b7280; font-weight:600;">Médiane</div>
                    <div id="anomaliesMediane" style="font-size:18px; font-weight:800; color:#dc2626;">-</div>
                </div>
                <div class="mf-kpi" style="background:#fef2f2;">
                    <div style="font-size:11px; color:#6b7280; font-weight:600;">Anomalies</div>
                    <div id="anomaliesCount" style="font-size:18px; font-weight:800; color:#dc2626;">-</div>
                </div>
                <div class="mf-kpi" style="background:#fef2f2;">
                    <div style="font-size:11px; color:#6b7280; font-weight:600;">Taux anomalie</div>
                    <div id="anomaliesTaux" style="font-size:18px; font-weight:800; color:#dc2626;">-</div>
                </div>
            </div>

            {{-- Graphique combiné : sorties + anomalies en surbrillance --}}
            <canvas id="anomaliesChart" height="90" style="margin-bottom:16px;"></canvas>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <h5 style="margin:0; color:#264653; font-size:13px;">Détail des sorties anormales</h5>
                <button type="button" onclick="exporterAnomaliesCSV()" id="btnExportCSV" class="mf-btn"
                        style="background:#334155; color:white; font-size:12px; padding:6px 12px; display:none;">
                    ⬇️ Exporter CSV
                </button>
            </div>

            <div style="max-height:320px; overflow:auto; border:1px solid #f1f5f9; border-radius:10px;">
                <table id="anomaliesTable" style="width:100%; border-collapse:collapse; display:none;" class="mf-table">
                    <thead>
                        <tr style="background:#fef2f2; color:#991b1b;">
                            <th style="padding:8px; text-align:left; font-size:12px;">Date</th>
                            <th style="padding:8px; text-align:center; font-size:12px;">Jour</th>
                            <th style="padding:8px; text-align:center; font-size:12px;">Quantité sortie</th>
                            <th style="padding:8px; text-align:center; font-size:12px;">Écart vs moyenne</th>
                            <th style="padding:8px; text-align:center; font-size:12px;">Z-score</th>
                            <th style="padding:8px; text-align:center; font-size:12px;">Sévérité</th>
                        </tr>
                    </thead>
                    <tbody id="anomaliesTableBody"></tbody>
                </table>
            </div>

            <div id="anomaliesAucune" style="display:none; background:#dcfce7; color:#166534;
                        padding:10px; border-radius:8px; text-align:center; font-weight:600; font-size:14px; margin-top:10px;">
                ✅ Aucune sortie anormale détectée — les mouvements de stock sont cohérents.
            </div>

            <div id="anomaliesInsuffisant" style="display:none; background:#fff7ed; color:#92400e;
                        padding:10px; border-radius:8px; text-align:center; font-weight:600; font-size:14px; margin-top:10px;">
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
let anomaliesChartInstance = null;
let dernieresAnomalies = []; // pour l'export CSV

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

        // Recommandation contextuelle
        const recoEl = document.getElementById("previsionRecommandation");
        if (data.jours_avant_rupture !== null) {
            let msg, bg, color;
            if (data.jours_avant_rupture <= 7) {
                msg = "🔴 Urgent : réapprovisionner sous 7 jours pour éviter une rupture.";
                bg = "#fee2e2"; color = "#991b1b";
            } else if (data.jours_avant_rupture <= 21) {
                msg = "🟠 À anticiper : planifier une commande dans les prochaines semaines.";
                bg = "#ffedd5"; color = "#9a3412";
            } else {
                msg = "🟢 Situation confortable : aucune action immédiate nécessaire.";
                bg = "#dcfce7"; color = "#166534";
            }
            recoEl.style.display = "block";
            recoEl.style.background = bg;
            recoEl.style.color = color;
            recoEl.innerText = msg;
        } else {
            recoEl.style.display = "none";
        }

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
function severiteDe(zscoreAbs) {
    if (zscoreAbs >= 3)   return { label: "CRITIQUE", cls: "mf-severite-critique" };
    if (zscoreAbs >= 2.5) return { label: "ÉLEVÉE",   cls: "mf-severite-elevee"  };
    return                       { label: "MODÉRÉE",  cls: "mf-severite-moderee" };
}

function joursSemaineFr(dateStr) {
    const jours = ["Dim","Lun","Mar","Mer","Jeu","Ven","Sam"];
    return jours[new Date(dateStr).getDay()];
}

async function chargerAnomalies() {
    setBtnLoading("btnAnomalies", true, "🔍 Analyser");
    document.getElementById("anomaliesError").style.display = "none";
    document.getElementById("btnExportCSV").style.display = "none";

    const periode = document.getElementById("anomaliesPeriode").value;
    const seuil = document.getElementById("anomaliesSeuil").value;

    try {
        // NB : jours/seuil transmis en query string — nécessite que le contrôleur
        // backend les lise (Request::get('jours', 60), Request::get('seuil', 2.5)).
        // Si le backend ne les gère pas encore, ils sont simplement ignorés côté serveur.
        const url = new URL(ANOMALIES_URL, window.location.origin);
        url.searchParams.set("jours", periode);
        url.searchParams.set("seuil", seuil);

        let res = await fetch(url, {
            headers: { "Accept": "application/json" },
        });

        if (!res.ok) throw new Error("HTTP " + res.status);
        let data = await res.json();

        if (data.status === "error") {
            throw new Error(data.message || "Erreur du service IA");
        }

        document.getElementById("anomaliesPlaceholder").style.display = "none";
        document.getElementById("anomaliesContent").style.display = "block";

        const moyenne = data.moyenne ?? 0;
        const ecartType = data.ecart_type ?? 0;
        const anomalies = data.anomalies || [];
        const historique = data.historique || data.sorties || []; // { date, quantite } — optionnel selon backend

        dernieresAnomalies = anomalies;

        document.getElementById("anomaliesMoyenne").innerText = moyenne;
        document.getElementById("anomaliesEcartType").innerText = ecartType;
        document.getElementById("anomaliesMediane").innerText = data.mediane ?? "-";
        document.getElementById("anomaliesCount").innerText = anomalies.length;

        const totalPoints = historique.length || (data.nb_observations ?? null);
        document.getElementById("anomaliesTaux").innerText =
            totalPoints ? ((anomalies.length / totalPoints) * 100).toFixed(1) + " %" : "-";

        const table = document.getElementById("anomaliesTable");
        const tbody = document.getElementById("anomaliesTableBody");
        const aucune = document.getElementById("anomaliesAucune");
        const insuffisant = document.getElementById("anomaliesInsuffisant");

        table.style.display = "none";
        aucune.style.display = "none";
        insuffisant.style.display = "none";
        tbody.innerHTML = "";

        // moyenne=0 et ecart_type=0 avec 0 anomalie -> pas assez d'historique (< 5 points), cf anomaly_detection.py
        if (moyenne === 0 && ecartType === 0 && anomalies.length === 0) {
            insuffisant.style.display = "block";
        } else if (anomalies.length === 0) {
            aucune.style.display = "block";
            document.getElementById("btnExportCSV").style.display = "none";
        } else {
            table.style.display = "table";
            document.getElementById("btnExportCSV").style.display = "inline-flex";
            anomalies
                .slice()
                .sort((a, b) => new Date(b.date) - new Date(a.date))
                .forEach(a => {
                    const zAbs = Math.abs(a.zscore);
                    const sev = severiteDe(zAbs);
                    const ecart = (a.quantite - moyenne).toFixed(1);
                    const ecartSigne = ecart > 0 ? "+" + ecart : ecart;

                    const tr = document.createElement("tr");
                    tr.className = "mf-row-anomalie";
                    tr.style.borderBottom = "1px solid #f1f5f9";
                    tr.innerHTML = `
                        <td style="padding:8px;">${new Date(a.date).toLocaleDateString('fr-FR')}</td>
                        <td style="padding:8px; text-align:center; color:#64748b;">${joursSemaineFr(a.date)}</td>
                        <td style="padding:8px; text-align:center; font-weight:700; color:#dc2626;">${a.quantite}</td>
                        <td style="padding:8px; text-align:center; color:#64748b;">${ecartSigne}</td>
                        <td style="padding:8px; text-align:center; font-weight:700;">${a.zscore}</td>
                        <td style="padding:8px; text-align:center;">
                            <span class="mf-badge ${sev.cls}">${sev.label}</span>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
        }

        // Graphique combiné : historique des sorties, points anomalies en rouge
        const ctx = document.getElementById("anomaliesChart").getContext("2d");
        const source = historique.length ? historique : anomalies.map(a => ({ date: a.date, quantite: a.quantite }));
        const labels = source.map(p => new Date(p.date).toLocaleDateString('fr-FR'));
        const valeurs = source.map(p => p.quantite);
        const anomalieDates = new Set(anomalies.map(a => a.date));
        const pointColors = source.map(p => anomalieDates.has(p.date) ? "#dc2626" : "#94a3b8");
        const pointRadii = source.map(p => anomalieDates.has(p.date) ? 6 : 3);

        if (anomaliesChartInstance) anomaliesChartInstance.destroy();
        anomaliesChartInstance = new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Sorties",
                        data: valeurs,
                        borderColor: "#94a3b8",
                        backgroundColor: "rgba(148,163,184,0.08)",
                        pointBackgroundColor: pointColors,
                        pointRadius: pointRadii,
                        pointHoverRadius: pointRadii.map(r => r + 2),
                        fill: true,
                        tension: 0.25,
                    },
                    {
                        label: "Moyenne",
                        data: labels.map(() => moyenne),
                        borderColor: "#7c3aed",
                        borderDash: [6, 4],
                        pointRadius: 0,
                        fill: false,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true, position: "bottom" } },
                scales: { y: { beginAtZero: true } },
            },
        });
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

// ═══════════════════════════════════════
// EXPORT CSV DES ANOMALIES
// ═══════════════════════════════════════
function exporterAnomaliesCSV() {
    if (!dernieresAnomalies.length) return;
    const lignes = ["Date;Quantite;Zscore"];
    dernieresAnomalies.forEach(a => {
        lignes.push(`${a.date};${a.quantite};${a.zscore}`);
    });
    const blob = new Blob([lignes.join("\n")], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "anomalies_{{ \Illuminate\Support\Str::slug($medicament->nom) }}.csv";
    a.click();
    URL.revokeObjectURL(url);
}
</script>
@endpush

@endsection