@extends('layouts.app')
@section('title', 'Tableau de bord IA')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center;
            background:white; padding:16px; border-radius:12px; margin-bottom:16px;">
    <div>
        <h2 style="margin:0; font-weight:800;">🧠 Tableau de bord IA</h2>
        <p style="margin:0; font-size:13px; color:#6b7280;">
            Prévisions de rupture et détection d'anomalies sur l'ensemble de la pharmacie
        </p>
    </div>
    <button type="button" onclick="lancerAnalyseGlobale()" id="btnAnalyseGlobale"
            style="background:linear-gradient(135deg,#7c3aed,#5b21b6); color:white; border:none;
                   padding:10px 18px; border-radius:10px; font-weight:700; cursor:pointer; font-size:14px;">
        🚀 Lancer l'analyse IA ({{ $medicaments->count() }} médicaments)
    </button>
</div>

@if($medicaments->isEmpty())
    <div style="background:white; padding:30px; border-radius:14px; text-align:center; color:#6b7280;">
        Aucun médicament n'a assez d'historique de sorties (minimum 5) pour une analyse IA fiable.
    </div>
@else

    {{-- BARRE DE PROGRESSION --}}
    <div id="progressWrap" style="display:none; background:white; padding:16px; border-radius:14px;
                margin-bottom:16px; box-shadow:0 4px 14px rgba(0,0,0,0.05);">
        <div style="display:flex; justify-content:space-between; font-size:13px; color:#6b7280; margin-bottom:8px;">
            <span id="progressLabel">Analyse en cours...</span>
            <span id="progressCount">0 / {{ $medicaments->count() }}</span>
        </div>
        <div style="background:#e5e7eb; border-radius:999px; height:10px; overflow:hidden;">
            <div id="progressBar" style="background:linear-gradient(135deg,#7c3aed,#5b21b6);
                        height:100%; width:0%; transition:width 0.2s;"></div>
        </div>
    </div>

    {{-- RÉSUMÉ CHIFFRÉ --}}
    <div id="summaryCards" style="display:none; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:16px;">
        <div style="background:white; padding:16px; border-radius:14px; text-align:center;
                    box-shadow:0 4px 14px rgba(0,0,0,0.05); border-left:4px solid #ef4444;">
            <div style="font-size:12px; color:#6b7280; font-weight:600;">Ruptures prévues &lt; 14 jours</div>
            <div id="countUrgent" style="font-size:26px; font-weight:800; color:#ef4444;">0</div>
        </div>
        <div style="background:white; padding:16px; border-radius:14px; text-align:center;
                    box-shadow:0 4px 14px rgba(0,0,0,0.05); border-left:4px solid #f59e0b;">
            <div style="font-size:12px; color:#6b7280; font-weight:600;">Ruptures prévues 14-30 jours</div>
            <div id="countModere" style="font-size:26px; font-weight:800; color:#f59e0b;">0</div>
        </div>
        <div style="background:white; padding:16px; border-radius:14px; text-align:center;
                    box-shadow:0 4px 14px rgba(0,0,0,0.05); border-left:4px solid #dc2626;">
            <div style="font-size:12px; color:#6b7280; font-weight:600;">Anomalies détectées</div>
            <div id="countAnomalies" style="font-size:26px; font-weight:800; color:#dc2626;">0</div>
        </div>
    </div>

    {{-- TABLEAU RUPTURES --}}
    <div id="ruptureSection" style="display:none; background:white; padding:20px; border-radius:14px;
                box-shadow:0 4px 14px rgba(0,0,0,0.05); margin-bottom:16px;">
        <h4 style="color:#264653; margin-bottom:14px;">📉 Médicaments à risque de rupture (triés par urgence)</h4>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#fef2f2; color:#7f1d1d;">
                    <th style="padding:10px; text-align:left; font-size:12px;">Médicament</th>
                    <th style="padding:10px; text-align:center; font-size:12px;">Jours avant rupture</th>
                    <th style="padding:10px; text-align:center; font-size:12px;">Date estimée</th>
                    <th style="padding:10px; text-align:center; font-size:12px;">Fiabilité</th>
                    <th style="padding:10px; text-align:center; font-size:12px;">Action</th>
                </tr>
            </thead>
            <tbody id="ruptureTableBody"></tbody>
        </table>
        <p id="ruptureAucune" style="display:none; text-align:center; color:#6b7280; padding:20px;">
            ✅ Aucune rupture prévue dans les 30 prochains jours parmi les médicaments analysés.
        </p>
    </div>

    {{-- TABLEAU ANOMALIES --}}
    <div id="anomaliesSection" style="display:none; background:white; padding:20px; border-radius:14px;
                box-shadow:0 4px 14px rgba(0,0,0,0.05); margin-bottom:16px;">
        <h4 style="color:#264653; margin-bottom:14px;">🚨 Sorties de stock anormales détectées</h4>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#fef2f2; color:#7f1d1d;">
                    <th style="padding:10px; text-align:left; font-size:12px;">Médicament</th>
                    <th style="padding:10px; text-align:center; font-size:12px;">Date</th>
                    <th style="padding:10px; text-align:center; font-size:12px;">Quantité</th>
                    <th style="padding:10px; text-align:center; font-size:12px;">Z-score</th>
                    <th style="padding:10px; text-align:center; font-size:12px;">Action</th>
                </tr>
            </thead>
            <tbody id="anomaliesTableBody"></tbody>
        </table>
        <p id="anomaliesAucune" style="display:none; text-align:center; color:#6b7280; padding:20px;">
            ✅ Aucune sortie anormale détectée sur les médicaments analysés.
        </p>
    </div>

    {{-- ERREURS --}}
    <div id="erreursSection" style="display:none; background:#fff7ed; border-left:4px solid #f59e0b;
                padding:14px; border-radius:10px; color:#92400e; font-size:13px;"></div>

@endif

@push('scripts')
<script>
const MEDICAMENTS = @json($medicaments->map(fn($m) => ['id' => $m->id, 'nom' => $m->nom]));

function urlPrevision(id) {
    return "{{ url('oncologie/medicaments') }}/" + id + "/prevision-stock";
}
function urlAnomalies(id) {
    return "{{ url('oncologie/medicaments') }}/" + id + "/detecter-anomalies";
}
function urlShow(id) {
    return "{{ url('oncologie/medicaments') }}/" + id;
}

async function fetchJson(url) {
    const res = await fetch(url, { headers: { "Accept": "application/json" } });
    if (!res.ok) throw new Error("HTTP " + res.status);
    return res.json();
}

async function lancerAnalyseGlobale() {
    const btn = document.getElementById("btnAnalyseGlobale");
    btn.disabled = true;
    btn.innerText = "⏳ Analyse en cours...";

    document.getElementById("progressWrap").style.display = "block";
    document.getElementById("summaryCards").style.display = "grid";
    document.getElementById("ruptureSection").style.display = "block";
    document.getElementById("anomaliesSection").style.display = "block";

    const ruptureBody = document.getElementById("ruptureTableBody");
    const anomaliesBody = document.getElementById("anomaliesTableBody");
    ruptureBody.innerHTML = "";
    anomaliesBody.innerHTML = "";

    const ruptures = [];
    const anomaliesTotal = [];
    const erreurs = [];

    const total = MEDICAMENTS.length;
    let done = 0;

    // Analyse séquentielle volontaire (pas de Promise.all en parallèle) :
    // évite de saturer le service Python (statsmodels/Holt-Winters n'est
    // pas gratuit en CPU) si la pharmacie a beaucoup de médicaments.
    for (const med of MEDICAMENTS) {
        try {
            const [prevision, anomalies] = await Promise.all([
                fetchJson(urlPrevision(med.id)),
                fetchJson(urlAnomalies(med.id)),
            ]);

            if (prevision.status === "error") {
                erreurs.push(`${med.nom} : prévision indisponible (${prevision.message || "service IA"})`);
            } else if (prevision.jours_avant_rupture !== null && prevision.jours_avant_rupture !== undefined) {
                ruptures.push({
                    id: med.id,
                    nom: med.nom,
                    jours: prevision.jours_avant_rupture,
                    date: prevision.date_rupture_estimee,
                    fiabilite: prevision.fiabilite,
                });
            }

            if (anomalies.status === "error") {
                erreurs.push(`${med.nom} : détection anomalies indisponible (${anomalies.message || "service IA"})`);
            } else {
                (anomalies.anomalies || []).forEach(a => {
                    anomaliesTotal.push({
                        id: med.id,
                        nom: med.nom,
                        date: a.date,
                        quantite: a.quantite,
                        zscore: a.zscore,
                    });
                });
            }
        } catch (e) {
            erreurs.push(`${med.nom} : erreur réseau (${e.message})`);
        }

        done++;
        document.getElementById("progressCount").innerText = `${done} / ${total}`;
        document.getElementById("progressBar").style.width = Math.round((done / total) * 100) + "%";
    }

    // ── Tri et affichage des ruptures (les plus urgentes en premier) ──
    ruptures.sort((a, b) => a.jours - b.jours);

    let countUrgent = 0, countModere = 0;
    ruptures.forEach(r => {
        const isUrgent = r.jours < 14;
        const isModere = r.jours >= 14 && r.jours <= 30;
        if (isUrgent) countUrgent++;
        if (isModere) countModere++;

        const bg = isUrgent ? "#fee2e2" : (isModere ? "#fff7ed" : "");
        const color = isUrgent ? "#991b1b" : (isModere ? "#92400e" : "#374151");
        const fiabiliteColors = { bonne: "#166534", moyenne: "#92400e", faible: "#991b1b" };

        const tr = document.createElement("tr");
        tr.style.background = bg;
        tr.style.borderBottom = "1px solid #f1f5f9";
        tr.innerHTML = `
            <td style="padding:10px; font-weight:700;">${r.nom}</td>
            <td style="padding:10px; text-align:center; color:${color}; font-weight:700;">${r.jours} j</td>
            <td style="padding:10px; text-align:center;">${r.date ? new Date(r.date).toLocaleDateString('fr-FR') : '-'}</td>
            <td style="padding:10px; text-align:center; color:${fiabiliteColors[r.fiabilite] || '#374151'}; font-weight:600;">
                ${(r.fiabilite || '-').toUpperCase()}
            </td>
            <td style="padding:10px; text-align:center;">
                <a href="${urlShow(r.id)}" style="color:#7c3aed; font-weight:600; text-decoration:none;">Voir →</a>
            </td>
        `;
        ruptureBody.appendChild(tr);
    });

    document.getElementById("ruptureAucune").style.display = ruptures.length === 0 ? "block" : "none";
    document.getElementById("countUrgent").innerText = countUrgent;
    document.getElementById("countModere").innerText = countModere;

    // ── Affichage des anomalies (les plus fortes en premier) ──
    anomaliesTotal.sort((a, b) => Math.abs(b.zscore) - Math.abs(a.zscore));

    anomaliesTotal.forEach(a => {
        const tr = document.createElement("tr");
        tr.style.borderBottom = "1px solid #f1f5f9";
        tr.innerHTML = `
            <td style="padding:10px; font-weight:700;">${a.nom}</td>
            <td style="padding:10px; text-align:center;">${new Date(a.date).toLocaleDateString('fr-FR')}</td>
            <td style="padding:10px; text-align:center; color:#dc2626; font-weight:700;">${a.quantite}</td>
            <td style="padding:10px; text-align:center;">${a.zscore}</td>
            <td style="padding:10px; text-align:center;">
                <a href="${urlShow(a.id)}" style="color:#7c3aed; font-weight:600; text-decoration:none;">Voir →</a>
            </td>
        `;
        anomaliesBody.appendChild(tr);
    });

    document.getElementById("anomaliesAucune").style.display = anomaliesTotal.length === 0 ? "block" : "none";
    document.getElementById("countAnomalies").innerText = anomaliesTotal.length;

    // ── Erreurs éventuelles (service IA down pour certains médicaments) ──
    const erreursSection = document.getElementById("erreursSection");
    if (erreurs.length > 0) {
        erreursSection.style.display = "block";
        erreursSection.innerHTML = "⚠️ " + erreurs.length + " médicament(s) n'ont pas pu être analysés :<br>" +
            erreurs.slice(0, 5).map(e => "• " + e).join("<br>") +
            (erreurs.length > 5 ? `<br>... et ${erreurs.length - 5} autre(s).` : "");
    } else {
        erreursSection.style.display = "none";
    }

    document.getElementById("progressLabel").innerText = "✅ Analyse terminée";
    btn.disabled = false;
    btn.innerText = "🔄 Relancer l'analyse";
}
</script>
@endpush

@endsection