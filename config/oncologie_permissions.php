<?php
// config/oncologie_permissions.php

return [

    'roles' => [
        'administrateur' => [
            'label'       => 'Administrateur',
            'icon'        => '⚙️',
            'color'       => '#264653',
            'description' => 'Accès complet au système. Gestion des utilisateurs et des données.',
        ],
        'medecin' => [
            'label'       => 'Médecin',
            'icon'        => '🩺',
            'color'       => '#2a9d8f',
            'description' => 'Création prescriptions, gestion patients, consultation stock.',
        ],
        'pharmacien' => [
            'label'       => 'Pharmacien',
            'icon'        => '💊',
            'color'       => '#6a4c93',
            'description' => 'Gestion stock, lots, dispensation, validation prescriptions.',
        ],
        'infirmier' => [
            'label'       => 'Infirmier',
            'icon'        => '👩‍⚕️',
            'color'       => '#e76f51',
            'description' => 'Consultation uniquement : patients, prescriptions, médicaments.',
        ],
    ],

    // ── MATRICE DES PERMISSIONS ──────────────────────────────────
    // Format : 'permission' => ['role1', 'role2', ...]
    'permissions' => [

        // PATIENTS
        'patients.viewAny' => ['administrateur', 'medecin', 'infirmier'],
        'patients.view'    => ['administrateur', 'medecin', 'infirmier'],
        'patients.create'  => ['medecin'],
        'patients.update'  => ['medecin'],
        'patients.delete'  => ['administrateur'],
        'patients.export'  => ['administrateur', 'medecin'],

        // PRESCRIPTIONS
        'prescriptions.viewAny' => ['administrateur', 'medecin', 'pharmacien', 'infirmier'],
        'prescriptions.view'    => ['administrateur', 'medecin', 'pharmacien', 'infirmier'],
        'prescriptions.create'  => ['medecin'],
        'prescriptions.update'  => ['medecin'],
        'prescriptions.valider' => ['pharmacien'],
        'prescriptions.annuler' => ['medecin', 'administrateur'],
        'prescriptions.delete'  => ['administrateur'],
        'prescriptions.export'  => ['administrateur', 'medecin', 'pharmacien'],
        'prescriptions.stats'   => ['administrateur', 'medecin', 'pharmacien'],

        // MÉDICAMENTS
        'medicaments.viewAny' => ['administrateur', 'medecin', 'pharmacien', 'infirmier'],
        'medicaments.view'    => ['administrateur', 'medecin', 'pharmacien', 'infirmier'],
        'medicaments.create'  => ['pharmacien', 'administrateur'],
        'medicaments.update'  => ['pharmacien', 'administrateur'],
        'medicaments.delete'  => ['administrateur'],
        'medicaments.entree'  => ['pharmacien'],
        'medicaments.sortie'  => ['pharmacien'],
        'medicaments.scan_ia' => ['pharmacien', 'administrateur'],

        // LOTS
        'lots.viewAny' => ['administrateur', 'pharmacien'],
        'lots.view'    => ['administrateur', 'pharmacien'],
        'lots.create'  => ['pharmacien'],
        'lots.update'  => ['pharmacien'],
        'lots.delete'  => ['administrateur'],

        // DISPENSATIONS
        'dispensations.viewAny' => ['administrateur', 'pharmacien', 'medecin'],
        'dispensations.view'    => ['administrateur', 'pharmacien', 'medecin'],
        'dispensations.create'  => ['pharmacien'],
        'dispensations.export'  => ['administrateur', 'pharmacien'],

        // RÉFÉRENTIELS
        'referentiels.view'              => ['administrateur', 'medecin', 'pharmacien'],
        'referentiels.protocoles.create' => ['administrateur', 'medecin'],
        'referentiels.protocoles.delete' => ['administrateur'],

        // STATISTIQUES
        'statistiques.view' => ['administrateur', 'medecin', 'pharmacien'],

        // JOURNAL (Loi 25-11 — accès restreint)
        'logs.view' => ['administrateur'],

        // UTILISATEURS (admin uniquement)
        'utilisateurs.viewAny' => ['administrateur'],
        'utilisateurs.create'  => ['administrateur'],
        'utilisateurs.update'  => ['administrateur'],
        'utilisateurs.delete'  => ['administrateur'],
        'utilisateurs.lock'    => ['administrateur'],

        // PARAMÈTRES (chaque utilisateur peut modifier le sien)
        'parametres.profil'   => ['administrateur', 'medecin', 'pharmacien', 'infirmier'],
        'parametres.password' => ['administrateur', 'medecin', 'pharmacien', 'infirmier'],

        // DASHBOARD
        'dashboard.view' => ['administrateur', 'medecin', 'pharmacien', 'infirmier'],
    ],

    // ── MENUS PAR RÔLE (navigation sidebar) ─────────────────────
    'menus' => [
        'administrateur' => [
            ['icon'=>'🏠', 'label'=>'Tableau de bord',    'route'=>'oncologie.dashboard'],
            ['icon'=>'👥', 'label'=>'Patients',            'route'=>'oncologie.patients.index'],
            ['icon'=>'📋', 'label'=>'Prescriptions',       'route'=>'oncologie.prescriptions.index'],
            ['icon'=>'💊', 'label'=>'Médicaments',         'route'=>'oncologie.medicaments.index'],
            ['icon'=>'📦', 'label'=>'Lots',                'route'=>'oncologie.lots.index'],
            ['icon'=>'🔬', 'label'=>'Dispensations',       'route'=>'oncologie.dispensations.index'],
            ['icon'=>'📚', 'label'=>'Référentiels',        'route'=>'oncologie.admin.referentiels'],
            ['icon'=>'📊', 'label'=>'Statistiques',        'route'=>'oncologie.statistiques.index'],
            ['icon'=>'📋', 'label'=>"Journal d'activité",  'route'=>'oncologie.admin.logs'],
            ['icon'=>'👤', 'label'=>'Utilisateurs',        'route'=>'oncologie.admin.utilisateurs'],
        ],
        'medecin' => [
            ['icon'=>'🏠', 'label'=>'Tableau de bord',    'route'=>'oncologie.dashboard'],
            ['icon'=>'👥', 'label'=>'Mes patients',        'route'=>'oncologie.patients.index'],
            ['icon'=>'📋', 'label'=>'Prescriptions',       'route'=>'oncologie.prescriptions.index'],
            ['icon'=>'💊', 'label'=>'Médicaments',         'route'=>'oncologie.medicaments.index'],
            ['icon'=>'🔬', 'label'=>'Dispensations',       'route'=>'oncologie.dispensations.index'],
            ['icon'=>'📚', 'label'=>'Référentiels',        'route'=>'oncologie.admin.referentiels'],
            ['icon'=>'📊', 'label'=>'Statistiques',        'route'=>'oncologie.statistiques.index'],
        ],
        'pharmacien' => [
            ['icon'=>'🏠', 'label'=>'Tableau de bord',    'route'=>'oncologie.dashboard'],
            ['icon'=>'💊', 'label'=>'Médicaments',         'route'=>'oncologie.medicaments.index'],
            ['icon'=>'📦', 'label'=>'Lots',                'route'=>'oncologie.lots.index'],
            ['icon'=>'🔬', 'label'=>'Dispensations',       'route'=>'oncologie.dispensations.index'],
            ['icon'=>'📋', 'label'=>'Prescriptions',       'route'=>'oncologie.prescriptions.index'],
            ['icon'=>'📚', 'label'=>'Référentiels',        'route'=>'oncologie.admin.referentiels'],
            ['icon'=>'📊', 'label'=>'Statistiques',        'route'=>'oncologie.statistiques.index'],
        ],
        'infirmier' => [
            ['icon'=>'🏠', 'label'=>'Tableau de bord',    'route'=>'oncologie.dashboard'],
            ['icon'=>'👥', 'label'=>'Patients',            'route'=>'oncologie.patients.index'],
            ['icon'=>'📋', 'label'=>'Prescriptions',       'route'=>'oncologie.prescriptions.index'],
            ['icon'=>'💊', 'label'=>'Médicaments',         'route'=>'oncologie.medicaments.index'],
        ],
    ],
];