<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\TableauDeBordController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Auth\LoginController;

// Routes d'authentification
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Page d'accueil - Redirection vers le tableau de bord moderne
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/admin/tableau-de-bord-moderne');
    }
    return redirect('/login');
});

// Tableau de Bord Moderne Professionnel - Protégé par l'authentification
Route::middleware('auth')->prefix('admin')->group(function () {
    // Route principale du tableau de bord
    Route::get('/tableau-de-bord-moderne', [TableauDeBordController::class, 'index'])
        ->name('admin.tableau-de-bord-moderne');
    
    // Routes pour les pages de détails (nouvelles pages séparées)
    Route::get('/details/chiffre-affaires', function() {
        return view('admin.chiffre-affaires-details');
    })->name('admin.dashboard.chiffre-affaires');
    
    Route::get('/details/stock-rupture', function() {
        return view('admin.stock-rupture-details');
    })->name('admin.dashboard.stock-rupture');
    
    Route::get('/details/top-clients', function() {
        return view('admin.top-clients-details');
    })->name('admin.dashboard.top-clients');
    
    Route::get('/details/performance-horaire', function() {
        return view('admin.performance-horaire-details');
    })->name('admin.dashboard.performance-horaire');
    
    Route::get('/details/modes-paiement', function() {
        return view('admin.modes-paiement-details');
    })->name('admin.dashboard.modes-paiement');
    
    Route::get('/details/etat-tables', function() {
        return view('admin.etat-tables-details');
    })->name('admin.dashboard.etat-tables');
    
    // API endpoint pour les données en temps réel
    Route::get('/api/live-data', [TableauDeBordController::class, 'getLiveData'])
        ->name('admin.live-data');
    
    // Routes pour le système de rapports
    Route::prefix('rapports')->name('admin.reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
        
        Route::get('/complet', [ReportController::class, 'rapportComplet'])->name('complet');
        Route::get('/complet/pdf', [ReportController::class, 'rapportCompletPDF'])->name('complet.pdf');
        Route::get('/rapide', [ReportController::class, 'rapportRapide'])->name('rapide');
    });

    // API routes pour les modals (si nécessaires dans le futur)
    Route::get('/api/chiffre-affaires-details', [TableauDeBordController::class, 'getChiffreAffairesDetails'])
        ->name('admin.api.chiffre-affaires-details');
    Route::get('/api/stock-rupture-details', [TableauDeBordController::class, 'getStockRuptureDetails'])
        ->name('admin.api.stock-rupture-details');
    Route::get('/api/top-clients-details', [TableauDeBordController::class, 'getTopClientsDetails'])
        ->name('admin.api.top-clients-details');
    Route::get('/api/performance-horaire-details', [TableauDeBordController::class, 'getPerformanceHoraireDetails'])
        ->name('admin.api.performance-horaire-details');
    Route::get('/api/modes-paiement-details', [TableauDeBordController::class, 'getModesPaiementDetails'])
        ->name('admin.api.modes-paiement-details');
    Route::get('/api/etat-tables-details', [TableauDeBordController::class, 'getEtatTablesDetails'])
        ->name('admin.api.etat-tables-details');
    
    // Route pour export des données
    Route::get('/api/dashboard-export', [TableauDeBordController::class, 'exportModalData'])
        ->name('admin.dashboard.export');
});

// Redirection pour compatibilité avec les anciens liens
Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        return redirect('/admin/tableau-de-bord-moderne');
    });

    Route::get('/admin/tableau-de-bord', function () {
        return redirect('/admin/tableau-de-bord-moderne');
    });
});
