<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Oncologie\AuthOncoController;
use App\Http\Controllers\Oncologie\PatientController;
use App\Http\Controllers\Oncologie\PrescriptionController;
use App\Http\Controllers\Oncologie\MedicamentController;
use App\Http\Controllers\Oncologie\LotController;
use App\Http\Controllers\Oncologie\DispensationController;
use App\Http\Controllers\Oncologie\StatistiqueController;
use App\Http\Controllers\Oncologie\ParametreController;
use App\Http\Controllers\Oncologie\AdminController;
use App\Http\Controllers\Oncologie\ProtocoleController;
use App\Http\Controllers\Oncologie\DashboardController;
use App\Http\Controllers\Oncologie\SearchController;
use App\Http\Controllers\Oncologie\AlerteController;

// ═══════════════════════════════════════════════
// AUTHENTIFICATION (public — pas de middleware)
// ═══════════════════════════════════════════════

Route::get('/', function () {
    return redirect()->route('oncologie.login');
});
Route::prefix('oncologie')->name('oncologie.')->group(function () {

    Route::get('/login',  [AuthOncoController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthOncoController::class, 'login']);
    Route::post('/logout',[AuthOncoController::class, 'logout'])->name('logout');
    Route::post('/register', [AuthOncoController::class, 'register'])->name('register');
    Route::get('/forgot-password',  [AuthOncoController::class, 'showForgotForm'])->name('forgot');
    Route::post('/forgot-password', [AuthOncoController::class, 'sendResetEmail']);
    Route::get('/reset-password',   [AuthOncoController::class, 'showResetForm'])->name('reset');
    Route::post('/reset-password',  [AuthOncoController::class, 'resetPassword']);






    // ═══════════════════════════════════════════
    // ZONE PROTÉGÉE
    // ═══════════════════════════════════════════
    Route::middleware(['onco.auth'])->group(function () {

        // ── DASHBOARD ──────────────────────────
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware('onco.rbac:dashboard.view')
            ->name('dashboard');

        Route::post('alertes/dismiss', [AlerteController::class, 'dismiss'])
    ->name('alertes.dismiss');

            Route::get('/liste-ia', function () {
    return Medicament::select('id', 'nom')->get();
});

Route::post('/scan', [MedicamentController::class, 'scanEtRemplir'])
    ->name('medicaments.scan');


        // ── PATIENTS ───────────────────────────
        Route::prefix('patients')->name('patients.')->group(function () {
            Route::get('/', [PatientController::class, 'index'])
                ->middleware('onco.rbac:patients.viewAny')->name('index');

            Route::get('/create', [PatientController::class, 'create'])
                ->middleware('onco.rbac:patients.create')->name('create');

            Route::post('/', [PatientController::class, 'store'])
                ->middleware('onco.rbac:patients.create')->name('store');

            Route::get('/{patient}', [PatientController::class, 'show'])
                ->middleware('onco.rbac:patients.view')->name('show');

            Route::get('/{patient}/edit', [PatientController::class, 'edit'])
                ->middleware('onco.rbac:patients.update')->name('edit');

            Route::put('/{patient}', [PatientController::class, 'update'])
                ->middleware('onco.rbac:patients.update')->name('update');

            Route::delete('/{patient}', [PatientController::class, 'destroy'])
                ->middleware('onco.rbac:patients.delete')->name('destroy');

            Route::get('/{patient}/pdf', [PatientController::class, 'exportPdfSingle'])
                ->middleware('onco.rbac:patients.export')->name('export.pdf.single');

            Route::get('/{patient}/excel', [PatientController::class, 'exportExcelSingle'])
                ->middleware('onco.rbac:patients.export')->name('export.excel.single');
                // ═══ EXPORT PDF LISTE ═══
Route::get('/export/pdf/liste', [PatientController::class, 'exportPdfListe'])
    ->middleware('onco.rbac:patients.export')
    ->name('export.pdf.liste');// ═══ EXPORT PDF LISTE ═══
Route::get('/export/pdf/liste', [PatientController::class, 'exportPdfListe'])
    ->middleware('onco.rbac:patients.export')
    ->name('export.pdf.liste');
        });

        // ── PRESCRIPTIONS ──────────────────────
        Route::prefix('prescriptions')->name('prescriptions.')->group(function () {
            Route::get('/', [PrescriptionController::class, 'index'])
                ->middleware('onco.rbac:prescriptions.viewAny')->name('index');

            Route::get('/create', [PrescriptionController::class, 'create'])
                ->middleware('onco.rbac:prescriptions.create')->name('create');

            Route::post('/', [PrescriptionController::class, 'store'])
                ->middleware('onco.rbac:prescriptions.create')->name('store');

            Route::get('/stats', [PrescriptionController::class, 'stats'])
                ->middleware('onco.rbac:prescriptions.stats')->name('stats');

            Route::get('/export', [PrescriptionController::class, 'export'])
                ->middleware('onco.rbac:prescriptions.export')->name('export');

            Route::get('/{prescription}', [PrescriptionController::class, 'show'])
                ->middleware('onco.rbac:prescriptions.view')->name('show');

            Route::get('/{prescription}/pdf', [PrescriptionController::class, 'pdf'])
                ->middleware('onco.rbac:prescriptions.export')->name('pdf');

            Route::get('/{prescription}/edit', [PrescriptionController::class, 'edit'])
                ->middleware('onco.rbac:prescriptions.update')->name('edit');

            Route::put('/{prescription}', [PrescriptionController::class, 'update'])
                ->middleware('onco.rbac:prescriptions.update')->name('update');

            Route::delete('/{prescription}', [PrescriptionController::class, 'destroy'])
                ->middleware('onco.rbac:prescriptions.delete')->name('destroy');

            Route::post('/{prescription}/valider', [PrescriptionController::class, 'valider'])
                ->middleware('onco.rbac:prescriptions.valider')->name('valider');

            Route::post('/{prescription}/annuler', [PrescriptionController::class, 'annuler'])
                ->middleware('onco.rbac:prescriptions.annuler')->name('annuler');
        });
          Route::get('search', [SearchController::class, 'search'])
    ->name('search');

        // ── MÉDICAMENTS ────────────────────────
        Route::prefix('medicaments')->name('medicaments.')->group(function () {
            Route::get('/', [MedicamentController::class, 'index'])
                ->middleware('onco.rbac:medicaments.viewAny')->name('index');

            Route::get('/create', [MedicamentController::class, 'create'])
                ->middleware('onco.rbac:medicaments.create')->name('create');

            Route::post('/', [MedicamentController::class, 'store'])
                ->middleware('onco.rbac:medicaments.create')->name('store');

            Route::get('/{medicament}', [MedicamentController::class, 'show'])
                ->middleware('onco.rbac:medicaments.view')->name('show');

            Route::get('/{medicament}/edit', [MedicamentController::class, 'edit'])
                ->middleware('onco.rbac:medicaments.update')->name('edit');

            Route::put('/{medicament}', [MedicamentController::class, 'update'])
                ->middleware('onco.rbac:medicaments.update')->name('update');

            Route::delete('/{medicament}', [MedicamentController::class, 'destroy'])
                ->middleware('onco.rbac:medicaments.delete')->name('destroy');

            Route::post('/{medicament}/entree', [MedicamentController::class, 'entree'])
                ->middleware('onco.rbac:medicaments.entree')->name('entree');

            Route::post('/{medicament}/sortie', [MedicamentController::class, 'sortie'])
                ->middleware('onco.rbac:medicaments.sortie')->name('sortie');

            Route::get('/{medicament}/lots', [MedicamentController::class, 'lots'])
                ->middleware('onco.rbac:lots.viewAny')->name('lots');
            Route::get('/liste-ia', function () {
                   return Medicament::select('id', 'nom')->get();});
            Route::post('/scan', [MedicamentIaController::class, 'scan'])
                 ->name('medicaments.scan');
 
             Route::post('/scan-code-barres', [MedicamentController::class, 'scanCodeBarres'])
               ->name('medicaments.scan-code-barres');
 
            Route::get('/{medicament}/prevision-stock', [MedicamentController::class, 'previsionStock'])
            ->name('medicaments.prevision-stock');
 
            Route::get('/{medicament}/detecter-anomalies', [MedicamentController::class, 'detecterAnomalies'])
               ->name('medicaments.detecter-anomalies');
});



        // ── LOTS ───────────────────────────────
        Route::prefix('lots')->name('lots.')->group(function () {
            Route::get('/', [LotController::class, 'index'])
                ->middleware('onco.rbac:lots.viewAny')->name('index');

            Route::get('/create', [LotController::class, 'create'])
                ->middleware('onco.rbac:lots.create')->name('create');

            Route::post('/', [LotController::class, 'store'])
                ->middleware('onco.rbac:lots.create')->name('store');

            Route::get('/{lot}', [LotController::class, 'show'])
                ->middleware('onco.rbac:lots.view')->name('show');

            Route::get('/{lot}/edit', [LotController::class, 'edit'])
                ->middleware('onco.rbac:lots.update')->name('edit');

            Route::put('/{lot}', [LotController::class, 'update'])
                ->middleware('onco.rbac:lots.update')->name('update');

            Route::delete('/{lot}', [LotController::class, 'destroy'])
                ->middleware('onco.rbac:lots.delete')->name('destroy');
        });

        // ── DISPENSATIONS ──────────────────────
        Route::prefix('dispensations')->name('dispensations.')->group(function () {
            Route::get('/', [DispensationController::class, 'index'])
                ->middleware('onco.rbac:dispensations.viewAny')->name('index');

            Route::get('/create', [DispensationController::class, 'create'])
                ->middleware('onco.rbac:dispensations.create')->name('create');

            Route::post('/', [DispensationController::class, 'store'])
                ->middleware('onco.rbac:dispensations.create')->name('store');

            Route::get('/export', [DispensationController::class, 'export'])
                ->middleware('onco.rbac:dispensations.export')->name('export');

            Route::get('/{dispensation}', [DispensationController::class, 'show'])
                ->middleware('onco.rbac:dispensations.view')->name('show');
        });

        // ── STATISTIQUES ───────────────────────
        Route::get('/statistiques', [StatistiqueController::class, 'index'])
            ->middleware('onco.rbac:statistiques.view')
            ->name('statistiques.index');

        // ── PARAMÈTRES ─────────────────────────
        Route::prefix('parametres')->name('parametres.')->group(function () {
            Route::get('/', [ParametreController::class, 'index'])
                ->middleware('onco.rbac:parametres.profil')->name('index');

            Route::put('/profil', [ParametreController::class, 'updateProfil'])
                ->middleware('onco.rbac:parametres.profil')->name('profil.update');

            Route::put('/password', [ParametreController::class, 'updatePassword'])
                ->middleware('onco.rbac:parametres.password')->name('password.update');
        });
      

        // ── AJAX PROTOCOLES ────────────────────
        Route::get('/protocoles/{protocole}/medicaments', [ProtocoleController::class, 'medicaments'])
            ->middleware('onco.rbac:referentiels.view')
            ->name('protocoles.medicaments');

        // ═══════════════════════════════════════
        // ZONE ADMIN
        // ═══════════════════════════════════════
        Route::prefix('admin')->name('admin.')->group(function () {

            // Journal d'activité
            Route::get('/logs', [AdminController::class, 'logs'])
                ->middleware('onco.rbac:logs.view')->name('logs');

            // Utilisateurs
            Route::get('/utilisateurs', [AdminController::class, 'utilisateurs'])
                ->middleware('onco.rbac:utilisateurs.viewAny')->name('utilisateurs');

            Route::post('/utilisateurs', [AdminController::class, 'creerUtilisateur'])
                ->middleware('onco.rbac:utilisateurs.create')->name('utilisateurs.store');

            Route::put('/utilisateurs/{user}', [AdminController::class, 'modifierUtilisateur'])
                ->middleware('onco.rbac:utilisateurs.update')->name('utilisateurs.update');

            Route::delete('/utilisateurs/{user}', [AdminController::class, 'supprimerUtilisateur'])
                ->middleware('onco.rbac:utilisateurs.delete')->name('utilisateurs.destroy');

            Route::patch('/utilisateurs/{user}/debloquer', [AdminController::class, 'debloquer'])
                  ->middleware('onco.rbac:utilisateurs.lock')->name('utilisateurs.debloquer');

            Route::patch('/utilisateurs/{user}/toggle', [AdminController::class, 'toggleActif'])
                  ->middleware('onco.rbac:utilisateurs.lock')->name('utilisateurs.toggle');

            // Référentiels
            Route::get('/referentiels', [AdminController::class, 'referentiels'])
                ->middleware('onco.rbac:referentiels.view')->name('referentiels');

            Route::post('/referentiels/protocoles', [AdminController::class, 'storeProtocole'])
                ->middleware('onco.rbac:referentiels.protocoles.create')->name('referentiels.protocole.store');

            Route::delete('/referentiels/protocoles/{protocole}', [AdminController::class, 'destroyProtocole'])
                ->middleware('onco.rbac:referentiels.protocoles.delete')->name('referentiels.protocole.destroy');
        });
    });
});