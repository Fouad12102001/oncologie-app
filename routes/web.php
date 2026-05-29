<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Oncologie\AuthOncoController;
use App\Http\Controllers\Oncologie\MedicamentController;
use App\Http\Controllers\Oncologie\LotController;
use App\Http\Controllers\Oncologie\PatientController;
use App\Http\Controllers\Oncologie\PrescriptionController;
use App\Http\Controllers\Oncologie\DispensationController;
use App\Http\Controllers\Oncologie\ProtocoleController;

// ========================
// REDIRECT RACINE
// ========================
Route::get('/', fn() => redirect()->route('oncologie.login'));
Route::get('/login', function () {
    return redirect()->route('oncologie.login');
})->name('login');

// ========================
// ONCOLOGIE — AUTH
// ========================
Route::prefix('oncologie')->name('oncologie.')->group(function () {

    Route::get('/login',   [AuthOncoController::class, 'showLoginForm'])->name('login');
    Route::post('/login',  [AuthOncoController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthOncoController::class, 'logout'])->name('logout');

    // ========================
    // ROUTES PROTÉGÉES
    // ========================
    
Route::post('/register', [AuthOncoController::class, 'register'])
    ->name('register.post');

Route::post('/password/email', [AuthOncoController::class, 'sendResetEmail'])
    ->name('password.email');
    Route::middleware('auth:oncologie')->group(function () {

        // DASHBOARD
        Route::get('/dashboard', fn() => view('oncologie.dashboard'))
            ->name('dashboard');

        // Dans le groupe middleware('auth:oncologie') :

// ========================
// STATISTIQUES
// ========================
Route::get('statistiques', [\App\Http\Controllers\Oncologie\StatistiqueController::class, 'index'])
    ->name('statistiques.index');
Route::get('statistiques/patients', [\App\Http\Controllers\Oncologie\StatistiqueController::class, 'patients'])
    ->name('statistiques.patients');
Route::get('statistiques/prescriptions', [\App\Http\Controllers\Oncologie\StatistiqueController::class, 'prescriptions'])
    ->name('statistiques.prescriptions');
Route::get('statistiques/medicaments', [\App\Http\Controllers\Oncologie\StatistiqueController::class, 'medicaments'])
    ->name('statistiques.medicaments');
Route::get('statistiques/dispensations', [\App\Http\Controllers\Oncologie\StatistiqueController::class, 'dispensations'])
    ->name('statistiques.dispensations');

// ========================
// PARAMÈTRES & ADMINISTRATION
// ========================
Route::prefix('parametres')->name('parametres.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Oncologie\ParametreController::class, 'index'])
        ->name('index');
    Route::get('profil', [\App\Http\Controllers\Oncologie\ParametreController::class, 'profil'])
        ->name('profil');
    Route::put('profil', [\App\Http\Controllers\Oncologie\ParametreController::class, 'updateProfil'])
        ->name('profil.update');
    Route::put('password', [\App\Http\Controllers\Oncologie\ParametreController::class, 'updatePassword'])
        ->name('password.update');
});

// ========================
// ADMINISTRATION (Admin uniquement)
// ========================
Route::prefix('admin')->name('admin.')->middleware('oncologie.admin')->group(function () {
    Route::get('utilisateurs', [\App\Http\Controllers\Oncologie\AdminController::class, 'utilisateurs'])
        ->name('utilisateurs');
    Route::post('utilisateurs', [\App\Http\Controllers\Oncologie\AdminController::class, 'creerUtilisateur'])
        ->name('utilisateurs.store');
    Route::put('utilisateurs/{user}', [\App\Http\Controllers\Oncologie\AdminController::class, 'modifierUtilisateur'])
        ->name('utilisateurs.update');
    Route::patch('utilisateurs/{user}/toggle', [\App\Http\Controllers\Oncologie\AdminController::class, 'toggleActif'])
        ->name('utilisateurs.toggle');
    Route::patch('utilisateurs/{user}/debloquer', [\App\Http\Controllers\Oncologie\AdminController::class, 'debloquer'])
        ->name('utilisateurs.debloquer');
    Route::delete('utilisateurs/{user}', [\App\Http\Controllers\Oncologie\AdminController::class, 'supprimerUtilisateur'])
        ->name('utilisateurs.destroy');
    Route::get('logs', [\App\Http\Controllers\Oncologie\AdminController::class, 'logs'])
        ->name('logs');
    Route::get('referentiels', [\App\Http\Controllers\Oncologie\AdminController::class, 'referentiels'])
        ->name('referentiels');
    Route::post('referentiels/protocole', [\App\Http\Controllers\Oncologie\AdminController::class, 'storeProtocole'])
        ->name('referentiels.protocole.store');
    Route::delete('referentiels/protocole/{protocole}', [\App\Http\Controllers\Oncologie\AdminController::class, 'destroyProtocole'])
        ->name('referentiels.protocole.destroy');
});

        // ========================
        // MÉDICAMENTS
        // ========================
        Route::resource('medicaments', MedicamentController::class);

        Route::post('medicaments/{medicament}/entree',
            [MedicamentController::class, 'entree'])
            ->name('medicaments.entree');

        Route::post('medicaments/{medicament}/sortie',
            [MedicamentController::class, 'sortie'])
            ->name('medicaments.sortie');

        Route::get('medicaments/{medicament}/lots',
            [MedicamentController::class, 'lots'])
            ->name('medicaments.lots');

        // ========================
        // LOTS
        // ========================
        Route::get('lots/export/pdf', [LotController::class, 'exportPdf'])
            ->name('lots.export.pdf');

        Route::resource('lots', LotController::class);

        // ========================
        // PATIENTS
        // ========================
        Route::get('patients/{patient}/export/pdf',
            [PatientController::class, 'exportPdfSingle'])
            ->name('patients.export.pdf.single');

        Route::get('patients/{patient}/export/excel',
            [PatientController::class, 'exportExcelSingle'])
            ->name('patients.export.excel.single');

        Route::resource('patients', PatientController::class);

        // ========================
        // PROTOCOLES (AJAX)
        // ========================
        Route::get('protocoles/{protocole}/medicaments',
            [ProtocoleController::class, 'medicaments'])
            ->name('protocoles.medicaments');

        // ========================
        // PRESCRIPTIONS
        // ========================
        Route::get('prescriptions/export',
            [PrescriptionController::class, 'export'])
            ->name('prescriptions.export');

        Route::get('prescriptions/stats',
            [PrescriptionController::class, 'stats'])
            ->name('prescriptions.stats');

        Route::post('prescriptions/{prescription}/valider',
            [PrescriptionController::class, 'valider'])
            ->name('prescriptions.valider');

        Route::get('prescriptions/{prescription}/pdf',
            [PrescriptionController::class, 'pdf'])
            ->name('prescriptions.pdf');

        Route::resource('prescriptions', PrescriptionController::class);

        // ========================
        // DISPENSATIONS
        // ========================
        Route::get('dispensations/export',
            [DispensationController::class, 'export'])
            ->name('dispensations.export');

        Route::resource('dispensations', DispensationController::class)
            ->only(['index', 'create', 'store', 'show']);
    });
});