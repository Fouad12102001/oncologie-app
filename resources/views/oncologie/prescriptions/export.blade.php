<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Export Prescriptions — CLCC</title>
    <style>
        /* ── RESET ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
 
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9.5px;
            color: #1e293b;
            background: #fff;
        }
 
        /* ── HEADER PAGE ── */
        .page-header {
            background: #0b1d35;
            color: #fff;
            padding: 14px 20px 12px;
            margin-bottom: 0;
        }
        .page-header__top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,.15);
            padding-bottom: 10px;
        }
        .page-header__logo-area {}
        .page-header__institution {
            font-size: 9px;
            color: rgba(255,255,255,.5);
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 3px;
        }
        .page-header__title {
            font-size: 15px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 2px;
        }
        .page-header__subtitle {
            font-size: 8.5px;
            color: rgba(255,255,255,.45);
        }
        .page-header__meta {
            text-align: right;
            font-size: 8px;
            color: rgba(255,255,255,.4);
        }
        .page-header__meta strong { color: rgba(255,255,255,.7); }
 
        /* ── KPI STRIP ── */
        .kpi-strip {
            display: flex;
            background: #f1f5f9;
            border-bottom: 2px solid #e2e8f0;
            padding: 10px 20px;
            gap: 0;
        }
        .kpi-item {
            flex: 1;
            text-align: center;
            padding: 0 8px;
            border-right: 1px solid #e2e8f0;
        }
        .kpi-item:last-child { border-right: none; }
        .kpi-item__val {
            font-size: 18px;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 3px;
        }
        .kpi-item__label { font-size: 7.5px; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; }
        .kpi-item--total   .kpi-item__val { color: #0b1d35; }
        .kpi-item--ok      .kpi-item__val { color: #059669; }
        .kpi-item--wait    .kpi-item__val { color: #d97706; }
        .kpi-item--cancel  .kpi-item__val { color: #e11d48; }
 
        /* ── SECTION TITLE ── */
        .section-title {
            font-size: 8px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .1em;
            padding: 10px 20px 6px;
            border-bottom: 1px solid #e2e8f0;
        }
 
        /* ── TABLE ── */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead th {
            background: #0b1d35;
            color: rgba(255,255,255,.85);
            padding: 7px 10px;
            font-size: 7.5px;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: .07em;
            font-weight: bold;
        }
        tbody td {
            padding: 6px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 8.5px;
            color: #1e293b;
            vertical-align: middle;
        }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        tbody tr:hover td { background: #f0fdfa; }
 
        .patient-name { font-weight: bold; font-size: 9px; }
        .patient-dossier { font-size: 7.5px; color: #94a3b8; }
 
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 7.5px;
            font-weight: bold;
        }
        .badge-ok     { background: #dcfce7; color: #166534; }
        .badge-wait   { background: #fef9c3; color: #854d0e; }
        .badge-cancel { background: #ffe4e6; color: #9f1239; }
 
        .sc-val {
            font-family: monospace;
            font-size: 8.5px;
            font-weight: bold;
            color: #0ea5e9;
            background: rgba(14,165,233,.08);
            padding: 1px 5px;
            border-radius: 3px;
        }
 
        /* ── FOOTER ── */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0; right: 0;
            border-top: 1px solid #e2e8f0;
            background: #fff;
            padding: 6px 20px;
            display: flex;
            justify-content: space-between;
            font-size: 7.5px;
            color: #94a3b8;
        }
        .page-footer strong { color: #475569; }
 
        /* ── CONFIDENTIAL BANNER ── */
        .confidential {
            background: #fff7ed;
            border-top: 1px solid #fed7aa;
            padding: 5px 20px;
            font-size: 7.5px;
            color: #9a3412;
            font-weight: bold;
            text-align: center;
            letter-spacing: .06em;
        }
    </style>
</head>
<body>
 
{{-- HEADER --}}
<div class="page-header">
    <div class="page-header__top">
        <div class="page-header__logo-area">
            <div class="page-header__institution">République Algérienne · Ministère de la Santé</div>
            <div class="page-header__title">Export des prescriptions de chimiothérapie</div>
            <div class="page-header__subtitle">Centre de Lutte Contre le Cancer — Draâ Ben Khedda, Tizi-Ouzou</div>
        </div>
        <div class="page-header__meta">
            <div>Généré le <strong>{{ now()->format('d/m/Y') }}</strong></div>
            <div>à <strong>{{ now()->format('H:i') }}</strong></div>
            <div style="margin-top:4px;">Pharmacie Oncologie</div>
        </div>
    </div>
</div>
 
{{-- KPI STRIP --}}
@php
    $total_c   = $prescriptions->count();
    $valid_c   = $prescriptions->where('statut','validee')->count();
    $wait_c    = $prescriptions->where('statut','en_attente')->count();
    $cancel_c  = $prescriptions->where('statut','annulee')->count();
@endphp
<div class="kpi-strip">
    <div class="kpi-item kpi-item--total">
        <div class="kpi-item__val">{{ $total_c }}</div>
        <div class="kpi-item__label">Total</div>
    </div>
    <div class="kpi-item kpi-item--ok">
        <div class="kpi-item__val">{{ $valid_c }}</div>
        <div class="kpi-item__label">Validées</div>
    </div>
    <div class="kpi-item kpi-item--wait">
        <div class="kpi-item__val">{{ $wait_c }}</div>
        <div class="kpi-item__label">En attente</div>
    </div>
    <div class="kpi-item kpi-item--cancel">
        <div class="kpi-item__val">{{ $cancel_c }}</div>
        <div class="kpi-item__label">Annulées</div>
    </div>
</div>
 
<div class="confidential">⚠ DOCUMENT CONFIDENTIEL — Usage médical exclusif — Ne pas diffuser</div>
 
<div class="section-title">Liste des prescriptions</div>
 
{{-- TABLE --}}
<table>
    <thead>
        <tr>
            <th style="width:30px;">N°</th>
            <th>Patient</th>
            <th>Médecin</th>
            <th>Protocole</th>
            <th style="width:60px;">Date</th>
            <th style="width:55px;">SC (m²)</th>
            <th style="width:65px;">Statut</th>
        </tr>
    </thead>
    <tbody>
        @forelse($prescriptions as $p)
        <tr>
            <td style="color:#94a3b8;font-weight:bold;">#{{ $p->id }}</td>
            <td>
                <div class="patient-name">
                    {{ optional($p->patient)->nom }} {{ optional($p->patient)->prenom }}
                </div>
                <div class="patient-dossier">
                    Dossier {{ optional($p->patient)->numero_dossier ?? '—' }}
                </div>
            </td>
            <td>{{ $p->medecin_nom ?? '—' }}</td>
            <td>{{ optional($p->protocole)->nom ?? '—' }}</td>
            <td>{{ optional($p->date_prescription)->format('d/m/Y') }}</td>
            <td>
                @if($p->surface_corporelle)
                    <span class="sc-val">{{ $p->surface_corporelle }} m²</span>
                @else
                    —
                @endif
            </td>
            <td>
                @if($p->statut === 'validee')
                    <span class="badge badge-ok">Validée</span>
                @elseif($p->statut === 'en_attente')
                    <span class="badge badge-wait">En attente</span>
                @else
                    <span class="badge badge-cancel">Annulée</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center;padding:24px;color:#94a3b8;">
                Aucune prescription à exporter
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
 
{{-- FOOTER --}}
<div class="page-footer">
    <span>CLCC Draâ Ben Khedda · Service Pharmacie Oncologie</span>
    <span>{{ $total_c }} prescription(s) · Exporté le {{ now()->format('d/m/Y à H:i') }}</span>
</div>
 
</body>
</html>