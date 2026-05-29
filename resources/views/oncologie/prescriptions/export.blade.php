<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Export Prescriptions</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header {
            background: #264653;
            color: white;
            padding: 16px 20px;
            margin-bottom: 16px;
        }
        .header h1 { margin: 0; font-size: 16px; font-weight: bold; }
        .header p  { margin: 4px 0 0; font-size: 10px; opacity: 0.7; }
        .stats {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
            padding: 0 10px;
        }
        .stat-box {
            flex: 1;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
        }
        .stat-box .val { font-size: 20px; font-weight: bold; }
        .stat-box .lbl { font-size: 10px; opacity: 0.8; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 10px;
        }
        th {
            background: #264653;
            color: white;
            padding: 8px 6px;
            font-size: 9px;
            text-align: left;
            text-transform: uppercase;
        }
        td {
            padding: 7px 6px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 9px;
        }
        tr:nth-child(even) td { background: #f8fafc; }
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        .validee   { background: #dcfce7; color: #166534; }
        .en_attente{ background: #fef3c7; color: #92400e; }
        .annulee   { background: #fee2e2; color: #991b1b; }
        .footer {
            margin-top: 16px;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>📋 Export des Prescriptions — CLCC Draâ Ben Khedda</h1>
    <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Patient</th>
            <th>Médecin</th>
            <th>Protocole</th>
            <th>Date</th>
            <th>SC (m²)</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        @forelse($prescriptions as $p)
        <tr>
            <td><strong>#{{ $p->id }}</strong></td>
            <td>{{ optional($p->patient)->nom }} {{ optional($p->patient)->prenom }}</td>
            <td>{{ $p->medecin_nom ?? '-' }}</td>
            <td>{{ optional($p->protocole)->nom ?? '-' }}</td>
            <td>{{ optional($p->date_prescription)->format('d/m/Y') }}</td>
            <td>{{ $p->surface_corporelle ?? '-' }}</td>
            <td>
                <span class="badge {{ $p->statut }}">
                    {{ match($p->statut) {
                        'validee'    => '✅ Validée',
                        'annulee'    => '❌ Annulée',
                        default      => '⏳ Attente',
                    } }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center;padding:20px;color:#94a3b8;">
                Aucune prescription
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    CLCC Draâ Ben Khedda — Total : {{ $prescriptions->count() }} prescription(s) —
    Document confidentiel
</div>

</body>
</html>