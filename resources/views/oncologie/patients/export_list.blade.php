<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste Patients</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h2 { text-align:center; color:#264653; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th { background:#264653; color:white; padding:8px; border:1px solid #ddd; }
        td { padding:7px; border:1px solid #ddd; }
    </style>
</head>
<body>
    <h2>Liste des Patients Oncologie — CLCC Draâ Ben Khedda</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Nom</th><th>Prénom</th><th>Dossier</th>
                <th>Sexe</th><th>Âge</th><th>Wilaya</th><th>Cancer</th><th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->nom }}</td>
                    <td>{{ $p->prenom }}</td>
                    <td>{{ $p->numero_dossier }}</td>
                    <td>{{ $p->sexe }}</td>
                    <td>{{ $p->age }}</td>
                    <td>{{ $p->wilaya }}</td>
                    <td>{{ $p->type_cancer }}</td>
                    <td>{{ $p->statut_vital }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>