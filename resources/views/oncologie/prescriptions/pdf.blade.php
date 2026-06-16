df.blade · PHP
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prescription #{{ $prescription->id }} — CLCC</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
 
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10.5px;
            color: #1e293b;
            background: #fff;
            padding: 20px 24px;
        }
 
        /* ── EN-TÊTE INSTITUTIONNEL ── */
        .doc-header {
            border-bottom: 2px solid #0b1d35;
            padding-bottom: 14px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .doc-header__left {}
        .doc-header__republic {
            font-size: 7.5px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 3px;
        }
        .doc-header__institution {
            font-size: 14px;
            font-weight: bold;
            color: #0b1d35;
            margin-bottom: 2px;
        }
        .doc-header__service {
            font-size: 9px;
            color: #475569;
            margin-bottom: 2px;
        }
        .doc-header__address {
            font-size: 8px;
            color: #94a3b8;
        }
        .doc-header__right {
            text-align: right;
        }
        .doc-header__rx {
            font-size: 28px;
            font-weight: bold;
            color: #0d9488;
            line-height: 1;
            margin-bottom: 4px;
        }
        .doc-header__num {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 2px;
        }
        .doc-header__date {
            font-size: 8.5px;
            color: #94a3b8;
        }
 
        /* ── STATUS BADGE ── */
        .status-line {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-bottom: 14px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        .status-badge--ok     { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .status-badge--wait   { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
        .status-badge--cancel { background: #ffe4e6; color: #9f1239; border: 1px solid #fda4af; }
 
        /* ── SECTION TITLE ── */
        .sec-title {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #fff;
            background: #0b1d35;
            padding: 5px 10px;
            margin-bottom: 0;
        }
        .sec-title--teal { background: #0d9488; }
 
        /* ── INFO BLOCK ── */
        .info-block {
            border: 1px solid #e2e8f0;
            border-top: none;
            margin-bottom: 14px;
        }
        .info-row {
            display: flex;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-row:last-child { border-bottom: none; }
        .info-row__label {
            width: 120px;
            min-width: 120px;
            background: #f8fafc;
            padding: 6px 10px;
            font-size: 8.5px;
            font-weight: bold;
            color: #475569;
            border-right: 1px solid #e2e8f0;
        }
        .info-row__value {
            padding: 6px 10px;
            font-size: 9.5px;
            color: #1e293b;
            flex: 1;
        }
        .info-row__value strong { color: #0b1d35; }
 
        /* ── PARAMS GRID ── */
        .params-grid {
            border: 1px solid #e2e8f0;
            border-top: none;
            display: flex;
            margin-bottom: 14px;
        }
        .param-cell {
            flex: 1;
            text-align: center;
            padding: 10px 8px;
            border-right: 1px solid #e2e8f0;
        }
        .param-cell:last-child { border-right: none; }
        .param-cell__val  { font-size: 16px; font-weight: bold; color: #0b1d35; line-height: 1; }
        .param-cell__val--teal  { color: #0d9488; }
        .param-cell__val--sky   { color: #0ea5e9; }
        .param-cell__val--ok    { color: #059669; }
        .param-cell__val--warn  { color: #d97706; }
        .param-cell__val--danger{ color: #e11d48; }
        .param-cell__unit  { font-size: 8px; color: #94a3b8; margin: 2px 0; }
        .param-cell__label { font-size: 7.5px; text-transform: uppercase; letter-spacing: .07em; color: #64748b; font-weight: bold; }
 
        /* ── FORMULA ── */
        .formula-box {
            background: #f0fdfa;
            border: 1px solid #99f6e4;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 14px;
            font-family: monospace;
            font-size: 9px;
            color: #0d9488;
        }
        .formula-box__label { font-family: sans-serif; font-size: 7.5px; color: #94a3b8; font-weight: bold; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 3px; }
 
        /* ── DRUG TABLE ── */
        .drug-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            border-top: none;
            margin-bottom: 14px;
        }
        .drug-table thead th {
            background: #f1f5f9;
            color: #475569;
            padding: 6px 10px;
            font-size: 8px;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: .07em;
            border-bottom: 1px solid #e2e8f0;
            font-weight: bold;
        }
        .drug-table tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 9.5px;
        }
        .drug-table tbody tr:last-child td { border-bottom: none; }
        .drug-table tfoot td {
            padding: 8px 10px;
            background: #f0fdfa;
            border-top: 2px solid #99f6e4;
            font-weight: bold;
            font-size: 10px;
            color: #0d9488;
        }
        .dose-val { font-weight: bold; font-size: 11px; color: #0d9488; }
 
        /* ── ALLERGY ── */
        .allergy-box {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-left: 3px solid #f97316;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 14px;
            font-size: 9px;
            color: #7c2d12;
        }
        .allergy-box__title { font-weight: bold; color: #9a3412; margin-bottom: 3px; }
 
        /* ── SIGNATURE ── */
        .signature-section {
            margin-top: 28px;
            display: flex;
            justify-content: flex-end;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-box__label {
            font-size: 8.5px;
            color: #64748b;
            margin-bottom: 30px;
        }
        .signature-box__line {
            border-top: 1px solid #1e293b;
            padding-top: 6px;
            font-size: 8px;
            color: #94a3b8;
        }
 
        /* ── FOOTER ── */
        .doc-footer {
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
            font-size: 7.5px;
            color: #94a3b8;
        }
        .doc-footer__warning {
            color: #9a3412;
            font-weight: bold;
        }
 
        /* ── NOTE ── */
        .note-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 14px;
            font-size: 8.5px;
            color: #64748b;
            font-style: italic;
        }
    </style>
</head>
<body>
 
{{-- ── EN-TÊTE ── --}}
<div class="doc-header">
    <div class="doc-header__left">
        <div class="doc-header__republic">République Algérienne Démocratique et Populaire · Ministère de la Santé</div>
        <div class="doc-header__institution">Centre de Lutte Contre le Cancer</div>
        <div class="doc-header__service">Service Pharmacie Oncologie · Chimiothérapie</div>
        <div class="doc-header__address">Draâ Ben Khedda, Tizi-Ouzou, Algérie</div>
    </div>
    <div class="doc-header__right">
        <div class="doc-header__rx">℞</div>
        <div class="doc-header__num">Prescription N° <strong>{{ $prescription->id }}</strong></div>
        <div class="doc-header__date">{{ optional($prescription->date_prescription)->format('d/m/Y') }}</div>
    </div>
</div>
 
{{-- STATUT --}}
<div class="status-line">
    @if($prescription->statut === 'validee')
        <span class="status-badge status-badge--ok">✓ Prescription validée</span>
    @elseif($prescription->statut === 'annulee')
        <span class="status-badge status-badge--cancel">✕ Prescription annulée</span>
    @else
        <span class="status-badge status-badge--wait">⏳ En attente de validation</span>
    @endif
</div>
 
{{-- ALLERGIE --}}
@if($prescription->patient->allergies)
<div class="allergy-box">
    <div class="allergy-box__title">⚠ Allergie signalée</div>
    {{ $prescription->patient->allergies }}
</div>
@endif
 
{{-- PATIENT --}}
<div class="sec-title">Informations patient</div>
<div class="info-block">
    <div class="info-row">
        <div class="info-row__label">Nom complet</div>
        <div class="info-row__value">
            <strong>{{ $prescription->patient->nom }} {{ $prescription->patient->prenom }}</strong>
        </div>
        <div class="info-row__label">N° Dossier</div>
        <div class="info-row__value">{{ $prescription->patient->numero_dossier }}</div>
    </div>
    <div class="info-row">
        <div class="info-row__label">Type de cancer</div>
        <div class="info-row__value">{{ $prescription->patient->type_cancer ?? '—' }}</div>
        <div class="info-row__label">Wilaya</div>
        <div class="info-row__value">{{ $prescription->patient->wilaya ?? '—' }}</div>
    </div>
    <div class="info-row">
        <div class="info-row__label">Médecin</div>
        <div class="info-row__value">{{ $prescription->medecin_nom ?? optional($prescription->medecin)->name ?? '—' }}</div>
        <div class="info-row__label">Cycle</div>
        <div class="info-row__value">Cycle {{ $prescription->cycle }}</div>
    </div>
    <div class="info-row">
        <div class="info-row__label">Protocole</div>
        <div class="info-row__value" colspan="3">{{ optional($prescription->protocole)->nom ?? 'Aucun' }}</div>
    </div>
</div>
 
{{-- PARAMÈTRES CLINIQUES --}}
<div class="sec-title sec-title--teal">Paramètres cliniques — Calcul de dose</div>
<div class="params-grid">
    <div class="param-cell">
        <div class="param-cell__val">{{ $prescription->poids }}</div>
        <div class="param-cell__unit">kg</div>
        <div class="param-cell__label">Poids</div>
    </div>
    <div class="param-cell">
        <div class="param-cell__val">{{ $prescription->taille }}</div>
        <div class="param-cell__unit">cm</div>
        <div class="param-cell__label">Taille</div>
    </div>
    <div class="param-cell">
        <div class="param-cell__val param-cell__val--teal">{{ $prescription->surface_corporelle }}</div>
        <div class="param-cell__unit">m² (Mosteller)</div>
        <div class="param-cell__label">Surface Corporelle</div>
    </div>
    @php $clr = $prescription->clairance_renale; @endphp
    <div class="param-cell">
        @if($clr >= 90)
            <div class="param-cell__val param-cell__val--ok">{{ $clr }}</div>
            <div class="param-cell__unit">ml/min · Normal ≥ 90</div>
        @elseif($clr >= 60)
            <div class="param-cell__val param-cell__val--sky">{{ $clr }}</div>
            <div class="param-cell__unit">ml/min · Léger 60–89</div>
        @elseif($clr >= 30)
            <div class="param-cell__val param-cell__val--warn">{{ $clr }}</div>
            <div class="param-cell__unit">ml/min · Modéré 30–59</div>
        @else
            <div class="param-cell__val param-cell__val--danger">{{ $clr }}</div>
            <div class="param-cell__unit">ml/min · Sévère &lt; 30</div>
        @endif
        <div class="param-cell__label">DFG (Cockroft-Gault)</div>
    </div>
</div>
 
{{-- FORMULE --}}
<div class="formula-box">
    <div class="formula-box__label">Formule de Mosteller appliquée</div>
    SC = √( {{ $prescription->poids }} kg × {{ $prescription->taille }} cm / 3600 ) = <strong>{{ $prescription->surface_corporelle }} m²</strong>
</div>
 
{{-- MÉDICAMENTS --}}
<div class="sec-title sec-title--teal">Traitement prescrit</div>
<table class="drug-table">
    <thead>
        <tr>
            <th>Médicament</th>
            <th style="text-align:right;">Dose calculée</th>
        </tr>
    </thead>
    <tbody>
        @foreach($prescription->details as $detail)
        <tr>
            <td>{{ $detail->medicament->nom }}</td>
            <td style="text-align:right;">
                <span class="dose-val">{{ number_format($detail->dose_calculee, 2) }}</span>
                <span style="font-size:8px;color:#94a3b8;"> mg</span>
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>Dose totale cumulative</td>
            <td style="text-align:right;">{{ number_format($prescription->dose_totale ?? 0, 2) }} mg</td>
        </tr>
    </tfoot>
</table>
 
{{-- NOTE --}}
<div class="note-box">
    Prescription générée par le Système d'Information Oncologique du CLCC Draâ Ben Khedda.
    Les doses ont été calculées automatiquement selon la formule de Mosteller (Surface Corporelle)
    et validées par le pharmacien clinicien.
</div>
 
{{-- SIGNATURE --}}
<div class="signature-section">
    <div class="signature-box">
        <div class="signature-box__label">Signature &amp; cachet du médecin prescripteur</div>
        <div class="signature-box__line">Dr. {{ $prescription->medecin_nom ?? '—' }}</div>
    </div>
</div>
 
{{-- FOOTER --}}
<div class="doc-footer">
    <span>CLCC Draâ Ben Khedda · Pharmacie Oncologie</span>
    <span class="doc-footer__warning">DOCUMENT CONFIDENTIEL — Usage médical exclusif</span>
    <span>Imprimé le {{ now()->format('d/m/Y à H:i') }}</span>
</div>
 
</body>
</html>